<?php

namespace App\Http\Controllers;

use App\Models\CaculateSalary;
use App\Models\Department;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChartController extends Controller
{
    
    public function userDepChart()
{
    // Lấy danh sách phòng ban cha
    $parentDepartments = Department::where('parent_id', 0)->get();
    
    // Biến dữ liệu dùng chung
    $labels = []; // Tên phòng ban
    $employeeData = []; // Số lượng nhân viên trong phòng ban
    $colors = []; // Màu sắc của từng phòng ban
    
    // Dữ liệu cho Stacked Bar Chart (độ tuổi)
    $ageDatasets = []; 
    
    // Dữ liệu cho Grouped Bar Chart (giới tính)
    $genderDatasets = [
        'male' => [],
        'female' => []
    ];

    foreach ($parentDepartments as $parent) {
        // Lấy tất cả các ID phòng ban liên quan đến phòng ban cha này
        $allDepartmentIds = $this->getAllDepartmentIds($parent->id);

        // 1. Tạo dữ liệu số lượng nhân viên
        $employeeCount = User::whereIn('department_id', $allDepartmentIds)->count();
        $labels[] = $parent->name;
        $employeeData[] = $employeeCount;

        // Tạo màu ngẫu nhiên cho phòng ban
        $color = $this->randomColor();
        $colors[] = $color;

        // 2. Tạo dữ liệu nhóm độ tuổi
        $ages = User::whereIn('department_id', $allDepartmentIds)
        ->get()
        ->map(function ($user) {
            // Tính tuổi dựa trên ngày sinh (cột age lưu dưới dạng date)
            return Carbon::parse($user->age)->diffInYears(Carbon::now()); // 'age' là cột lưu ngày sinh
        })->toArray();
        // Nhóm độ tuổi (20-30, 31-40, ...)
        $ageGroups = $this->groupAges($ages);

        foreach ($ageGroups as $range => $count) {
            if (!isset($ageDatasets[$range])) {
                $ageDatasets[$range] = [
                    'label' => $range,
                    'data' => [],
                    'backgroundColor' => $this->randomColor(),
                ];
            }
            $ageDatasets[$range]['data'][] = $count;
        }

        // 3. Thống kê giới tính
        $maleCount = User::whereIn('department_id', $allDepartmentIds)->where('gender', 'male')->count();
        $femaleCount = User::whereIn('department_id', $allDepartmentIds)->where('gender', 'female')->count();

        // Thêm vào dữ liệu giới tính
        $genderDatasets['male'][] = $maleCount;
        $genderDatasets['female'][] = $femaleCount;
    }

    // Chuyển datasets cho Chart.js
    $ageDatasets = array_values($ageDatasets);
    $seniorityData = $this->categorizeEmployeesBySeniority();

    
    // Trả về dữ liệu vào view
    return view('fe_chart.user_department', compact('labels', 'employeeData', 'colors', 'ageDatasets', 'genderDatasets', 'seniorityData'));
}

/**
 * Nhóm các độ tuổi theo khoảng (20-30, 31-40, ...)
 */
private function groupAges(array $ages)
{
    $groups = [
        '20-30' => 0,
        '31-40' => 0,
        '41-50' => 0,
        '51+' => 0,
    ];

    foreach ($ages as $age) {
        if ($age >= 20 && $age <= 30) {
            $groups['20-30']++;
        } elseif ($age >= 31 && $age <= 40) {
            $groups['31-40']++;
        } elseif ($age >= 41 && $age <= 50) {
            $groups['41-50']++;
        } else {
            $groups['51+']++;
        }
    }

    return $groups;
}

/**
 * Tạo màu ngẫu nhiên cho các nhóm.
 */
private function randomColor()
{
    return sprintf(
        'rgba(%d, %d, %d, 0.7)',
        rand(50, 200),
        rand(50, 200),
        rand(50, 200)
    );
}

/**
 * Lấy danh sách ID của tất cả các phòng ban con, bao gồm chính phòng ban cha.
 */
private function getAllDepartmentIds($parentId)
{
    $childDepartments = Department::where('parent_id', $parentId)->pluck('id')->toArray();
    $allDepartmentIds = [$parentId];

    foreach ($childDepartments as $childId) {
        $allDepartmentIds = array_merge($allDepartmentIds, $this->getAllDepartmentIds($childId));
    }

    return $allDepartmentIds;
}
public function calculateYearsOfService($created_at)
{
    return Carbon::now()->diffInYears(Carbon::parse($created_at));
}
public function categorizeEmployeesBySeniority()
{
    $users = User::all();

    $categories = [
        'dưới 1 năm' => 0,
        '1-3 năm' => 0,
        '3-5 năm' => 0,
        'trên 5 năm' => 0,
    ];

    foreach ($users as $user) {
        $years = $this->calculateYearsOfService($user->created_at);

        if ($years < 1) {
            $categories['dưới 1 năm']++;
        } elseif ($years <= 3) {
            $categories['1-3 năm']++;
        } elseif ($years <= 5) {
            $categories['3-5 năm']++;
        } else {
            $categories['trên 5 năm']++;
        }
    }

    return $categories;
}

public function workDays(){
        // Get total valid and invalid workdays by year and month
        $totals = CaculateSalary::selectRaw('SUM(valid_workdays) as total_valid_workdays, SUM(invalid_workdays) as total_invalid_workdays, YEAR(created_at) as year, MONTH(created_at) as month')
            ->groupBy(DB::raw('YEAR(created_at), MONTH(created_at)'))  // Group by year and month
            ->orderBy('year', 'asc')   // Order by year ascending
            ->orderBy('month', 'asc')  // Order by month ascending
            ->get();
    
        // Get total salary by year and month
        $salaryStats = CaculateSalary::selectRaw('SUM(salary_amount) as total_salary, MONTH(created_at) as month, YEAR(created_at) as year')
            ->groupBy(DB::raw('YEAR(created_at), MONTH(created_at)'))  // Group by year and month
            ->orderBy('year', 'desc')  // Order by year descending
            ->orderBy('month', 'desc')  // Order by month descending
            ->get();
    
        // Return data to the view
        return view('fe_chart.work_days', compact('totals', 'salaryStats'));
 
    

  // Trả dữ liệu về view
  return view('fe_chart.work_days', compact('totals', 'salaryStats'));
}
// public function salaryStats(){
//     // Lấy tổng lương thực nhận theo tháng từ bảng cacu_salaries
//     $salaryStats = CaculateSalary::selectRaw('SUM(salary) as total_salary, MONTH(created_at) as month, YEAR(created_at) as year')
//         ->groupBy('year', 'month')
//         ->orderBy('year', 'desc')
//         ->orderBy('month', 'desc')
//         ->get();

//     // Trả dữ liệu về view
//     return view('fe_chart.work_days', compact('salaryStats'));
// }

}

