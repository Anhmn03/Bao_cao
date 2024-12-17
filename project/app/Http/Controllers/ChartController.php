<?php

namespace App\Http\Controllers;

use App\Console\Commands\CalculateSalary;
use App\Models\CaculateSalary;
use App\Models\Department;
use App\Models\User;
use App\Models\User_attendance;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ChartController extends Controller
{
    
    public function userDepChart()
    {
        // Lấy danh sách phòng ban cha
        $parentDepartments = Department::where('parent_id', 0)->get();

        // Dữ liệu dùng chung
        $labels = [];
        $employeeData = [];
        $colors = ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF']; // Màu mặc định
        $ageDatasets = [];
        $genderDatasets = [
            'male' => [],
            'female' => []
        ];

        foreach ($parentDepartments as $index => $parent) {
            $allDepartmentIds = $this->getAllDepartmentIds($parent->id);

            // 1. Dữ liệu số lượng nhân viên
            $employeeCount = User::whereIn('department_id', $allDepartmentIds)->count();
            $labels[] = $parent->name;
            $employeeData[] = $employeeCount;

            // 2. Dữ liệu độ tuổi
            $ages = User::whereIn('department_id', $allDepartmentIds)
                ->whereNotNull('age') // Bỏ qua nếu không có ngày tháng năm sinh
                ->get()
                ->map(function ($user) {
                    return Carbon::parse($user->age)->diffInYears(Carbon::now());
                })
                ->toArray();

            $ageGroups = $this->groupAges($ages);

            foreach ($ageGroups as $range => $count) {
                if (!isset($ageDatasets[$range])) {
                    $ageDatasets[$range] = [
                        'label' => $range,
                        'data' => [],
                        // Gán màu cho từng nhóm độ tuổi từ danh sách $colors
                        'backgroundColor' => $colors[array_search($range, array_keys($ageGroups)) % count($colors)],
                    ];
                }
                $ageDatasets[$range]['data'][] = $count;
            }
            

            // 3. Dữ liệu giới tính
            $maleCount = User::whereIn('department_id', $allDepartmentIds)->where('gender', 'male')->count();
            $femaleCount = User::whereIn('department_id', $allDepartmentIds)->where('gender', 'female')->count();

            $genderDatasets['male'][] = $maleCount;
            $genderDatasets['female'][] = $femaleCount;
        }

        $ageDatasets = array_values($ageDatasets);
        $seniorityData = $this->categorizeEmployeesBySeniority();

        return view('fe_chart.user_department', compact('labels', 'employeeData', 'colors', 'ageDatasets', 'genderDatasets', 'seniorityData'));
    }

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

// public function workDays(){
//         // Get total valid and invalid workdays by year and month
//         // $totals = CaculateSalary::selectRaw('SUM(valid_workdays) as total_valid_workdays, SUM(invalid_workdays) as total_invalid_workdays, YEAR(created_at) as year, MONTH(created_at) as month')
//         //     ->groupBy(DB::raw('YEAR(created_at), MONTH(created_at)'))  // Group by year and month
//         //     ->orderBy('year', 'asc')   // Order by year ascending
//         //     ->orderBy('month', 'asc')  // Order by month ascending
//         //     ->get();
    
//         // Get total salary by year and month
//         $salaryStats = CaculateSalary::selectRaw('SUM(salary_amount) as total_salary, MONTH(created_at) as month, YEAR(created_at) as year')
//             ->groupBy(DB::raw('YEAR(created_at), MONTH(created_at)'))  // Group by year and month
//             ->orderBy('year', 'desc')  // Order by year descending
//             ->orderBy('month', 'desc')  // Order by month descending
//             ->get();
    
//         // Return data to the view
//         return view('fe_chart.work_days', compact('totals', 'salaryStats'));
 
    

//   // Trả dữ liệu về view
//   return view('fe_chart.work_days', compact('totals', 'salaryStats'));
// }
public function calculateWorkdays($userId, $selectedYear, $selectedMonth)
{
    // Lấy tất cả các bản ghi attendance trong tháng này của người dùng
    // $attendances = User_attendance::where('user_id', $userId)
    //     ->whereYear('time', Carbon::now()->year)
    //     ->whereMonth('time', Carbon::now()->month)
    //     ->get();
    $attendances = User_attendance::where('user_id', $userId)
    ->whereYear('time', $selectedYear)
    ->whereMonth('time', $selectedMonth)
    ->get();

    $validWorkdays = 0;  // Số ngày công hợp lệ
    $invalidWorkdays = 0; // Số ngày công không hợp lệ

    // Nhóm các bản ghi attendance theo ngày
    $attendancesGroupedByDay = $attendances->groupBy(function ($attendance) {
        return Carbon::parse($attendance->time)->format('Y-m-d'); // Nhóm theo ngày
    });

    // Duyệt qua từng ngày
    foreach ($attendancesGroupedByDay as $date => $attendancesForDay) {
        $checkIns = $attendancesForDay->where('type', 'in')->sortBy('time')->values(); // Lấy các bản ghi check-in, sắp xếp theo thời gian
        $checkOuts = $attendancesForDay->where('type', 'out')->sortBy('time')->values(); // Lấy các bản ghi check-out, sắp xếp theo thời gian
        if ($checkIns->isEmpty() || $checkOuts->isEmpty()) {
            continue; // Không tăng invalidWorkdays, chỉ bỏ qua
        }
        
        // Nếu không đủ cặp check-in/out thì ngày đó không hợp lệ
        if ($checkIns->count() !== $checkOuts->count()) {
            $invalidWorkdays++;
            continue;
        }

        $isValidDay = true; // Giả định ngày hợp lệ ban đầu

        // Kiểm tra từng cặp check-in và check-out theo thứ tự
        for ($i = 0; $i < $checkIns->count(); $i++) {
            $checkIn = $checkIns[$i];
            $checkOut = $checkOuts[$i];

            // Kiểm tra trạng thái và thời gian của từng cặp
            if (
                $checkIn->status != 1 ||
                $checkOut->status != 1 ||
                Carbon::parse($checkIn->time)->greaterThanOrEqualTo(Carbon::parse($checkOut->time))
            ) {
                $isValidDay = false; // Nếu bất kỳ cặp nào không hợp lệ, toàn bộ ngày không hợp lệ
                break;
            }
        }

        // Phân loại ngày công
        if ($isValidDay) {
            $validWorkdays++;
        } else {
            $invalidWorkdays++;
        }
    }

    // Trả về kết quả
    return ['valid_workdays' => $validWorkdays, 'invalid_workdays' => $invalidWorkdays];
}

public function calculateTotalWorkdays($selectedYear, $selectedMonth, $users)
{
    // Nếu không có người dùng, trả về tổng ngày công là 0
    if ($users->isEmpty()) {
        return [
            'total_valid_workdays' => 0,
            'total_invalid_workdays' => 0
        ];
    }

    $totalValidWorkdays = 0;  // Tổng số ngày công hợp lệ
    $totalInvalidWorkdays = 0; // Tổng số ngày công không hợp lệ

    // Duyệt qua tất cả người dùng và tính tổng số ngày công hợp lệ và không hợp lệ
    foreach ($users as $user) {
        $workdays = $this->calculateWorkdays($user->id, $selectedYear, $selectedMonth); // Truyền năm và tháng vào
        $totalValidWorkdays += $workdays['valid_workdays'];  // Cộng dồn số ngày công hợp lệ
        $totalInvalidWorkdays += $workdays['invalid_workdays']; // Cộng dồn số ngày công không hợp lệ
    }

    // Trả về kết quả tổng hợp
    return [
        'total_valid_workdays' => $totalValidWorkdays,
        'total_invalid_workdays' => $totalInvalidWorkdays
    ];
}

 

public function showWorkdaysChart(Request $request)
{
    // Ghi log request đầu vào
    Log::info('Request Data:', $request->all());

    // Lấy tháng, năm và phòng ban được chọn
    $selectedMonth = $request->has('month') ? $request->input('month') : Carbon::now()->month;
    $selectedYear = $request->has('year') ? $request->input('year') : Carbon::now()->year;
    $departmentId = $request->input('department_id', null);

    // Ghi log thông tin đã chọn
    Log::info("Selected Month: $selectedMonth");
    Log::info("Selected Year: $selectedYear");
    Log::info("Selected Department ID: " . ($departmentId ?: 'None'));

    // Lấy danh sách phòng ban cha
    $departments = Department::where('parent_id', 0)->get();
    Log::info('Parent Departments:', $departments->toArray());

    // Lấy danh sách phòng ban con hoặc tất cả các phòng ban con
    if ($departmentId) {
        $childDepartments = Department::where('parent_id', $departmentId)->get();
        $allDepartments = $departments->where('id', $departmentId)->merge($childDepartments);
    } else {
        $childDepartments = Department::whereIn('parent_id', $departments->pluck('id'))->get();
        $allDepartments = $departments->merge($childDepartments);
    }

    // Ghi log danh sách phòng ban
    Log::info('Child Departments:', $childDepartments->toArray());
    Log::info('All Departments:', $allDepartments->toArray());

    // Lấy danh sách người dùng thuộc các phòng ban này
    $users = User::whereIn('department_id', $allDepartments->pluck('id'))->get();
    Log::info('Users in Departments:', $users->toArray());

    // Tính tổng số ngày công hợp lệ và không hợp lệ
    $workdays = $this->calculateTotalWorkdays($selectedYear, $selectedMonth, $users);
    Log::info('Workdays Data:', $workdays);

    // Lấy dữ liệu lương từ bảng CaculateSalary
   // Truy vấn bảng CaculateSalary để lấy tổng lương của nhân viên trong các phòng ban có parent_id = 0
//    $salaries = CaculateSalary::join('users', 'users.id', '=', 'cacu_salaries.user_id')
//    ->join('departments', 'departments.id', '=', 'users.department_id')
//    ->where('departments.parent_id', 0) // Lọc các phòng ban cha
//    ->whereMonth('cacu_salaries.month', $selectedMonth)
//    ->whereYear('cacu_salaries.month', $selectedYear)
//    ->select('users.department_id', DB::raw('SUM(cacu_salaries.salary_amount) as total_salary'))
//    ->groupBy('users.department_id')
//    ->get();

// $salaries = CaculateSalary::join('departments', 'departments.id', '=', 'cacu_salaries.department_id') // Join với bảng departments
// ->where('departments.parent_id', 0) // Lọc các phòng ban có parent_id = 0
// ->selectRaw('AVG(cacu_salaries.salary_amount) as average_salary, MONTH(cacu_salaries.created_at) as month, YEAR(cacu_salaries.created_at) as year, departments.name as department_name')
// ->groupBy(DB::raw('YEAR(cacu_salaries.created_at), MONTH(cacu_salaries.created_at), departments.id')) // Nhóm theo tháng, năm và phòng ban
// ->orderBy('year', 'asc')  // Sắp xếp theo năm tăng dần
// ->orderBy('month', 'asc')  // Sắp xếp theo tháng tăng dần
// ->get();
// Truy vấn dữ liệu mức lương
// Lấy dữ liệu lương từ cơ sở dữ liệu

// đang đúng 
// $salaries = CaculateSalary::join('users', 'users.id', '=', 'cacu_salaries.user_id')
//     ->join('departments', 'departments.id', '=', 'users.department_id')
//     ->where('departments.parent_id', 0)  // Chỉ lấy các phòng ban cha (parent_id = 0)
//     ->selectRaw('AVG(cacu_salaries.salary_amount) as average_salary, MONTH(cacu_salaries.created_at) as month, YEAR(cacu_salaries.created_at) as year, departments.id as department_id, departments.name as department_name')
//     ->groupBy(DB::raw('YEAR(cacu_salaries.created_at), MONTH(cacu_salaries.created_at), departments.id, departments.name'))
//     ->orderBy('year', 'desc')
//     ->orderBy('month', 'desc')
//     ->get();

$salaries = CaculateSalary::join('users', 'users.id', '=', 'cacu_salaries.user_id')
->join('departments', 'departments.id', '=', 'users.department_id')
->where('departments.parent_id', 0)  // Chỉ lấy các phòng ban cha (parent_id = 0)
->selectRaw('AVG(cacu_salaries.salary_amount) as average_salary, MONTH(cacu_salaries.created_at) as month, YEAR(cacu_salaries.created_at) as year, departments.id as department_id, departments.name as department_name')
->groupBy(DB::raw('YEAR(cacu_salaries.created_at), MONTH(cacu_salaries.created_at), departments.id, departments.name'))
->orderBy('year', 'asc')
->orderBy('month', 'asc')
->get();

// Ghi log dữ liệu lương
Log::info('Salaries Data:', $salaries->toArray());

// Khởi tạo mảng lưu trữ dữ liệu cho biểu đồ
$chartData = [];
$allMonths = range(1, 12); // Tạo mảng từ tháng 1 đến tháng 12

// Danh sách màu sắc cho các phòng ban
$colors = [
    'rgba(75, 192, 192, 0.2)',  // Màu cho phòng ban 1
    'rgba(255, 99, 132, 0.2)',  // Màu cho phòng ban 2
    'rgba(255, 159, 64, 0.2)',  // Màu cho phòng ban 3
    'rgba(153, 102, 255, 0.2)', // Màu cho phòng ban 4
    'rgba(54, 162, 235, 0.2)',  // Màu cho phòng ban 5
    'rgba(255, 206, 86, 0.2)',  // Màu cho phòng ban 6
    // Bạn có thể thêm màu sắc khác ở đây nếu có nhiều hơn 6 phòng ban
];

foreach ($departments as $index => $department) {
    // Lọc mức lương của từng phòng ban dựa trên department_id
    $departmentSalaries = $salaries->where('department_id', $department->id);

    $monthlySalaries = array_fill(1, 12, 0); // Khởi tạo mảng với tất cả các tháng có giá trị 0

    foreach ($departmentSalaries as $salary) {
        $month = (int) Carbon::parse($salary->month)->format('m'); // Lấy tháng từ dữ liệu
        $monthlySalaries[$month] = $salary->average_salary; // Gán mức lương cho tháng
    }

    // Thêm dữ liệu vào biểu đồ, gán màu sắc cho từng phòng ban
    $chartData[] = [
        'label' => $department->name,
        'data' => array_values($monthlySalaries), // Chuyển mảng tháng sang mảng giá trị
        'backgroundColor' => $colors[$index % count($colors)], // Lặp qua danh sách màu sắc
        'borderColor' => $colors[$index % count($colors)], // Lặp qua danh sách màu sắc
        'borderWidth' => 2,
    ];
    Log::info("Chart Data for Department [{$department->name}]:", $chartData);

}

    // Ghi log dữ liệu biểu đồ của phòng ban


// Lấy tháng hiện tại dưới dạng số (tháng và năm)
$currentMonth = Carbon::createFromDate($selectedYear, $selectedMonth, 1)->format('m Y');
Log::info("Current Month: $currentMonth");

// Trả về dữ liệu cho view
$response = [
    'totalValidWorkdays' => $workdays['total_valid_workdays'],
    'totalInvalidWorkdays' => $workdays['total_invalid_workdays'],
    'currentMonth' => $currentMonth,
    'selectedMonth' => $selectedMonth,
    'selectedYear' => $selectedYear,
    'departmentId' => $departmentId,
    'departments' => $departments, // Danh sách phòng ban cha
    'childDepartments' => $childDepartments, // Danh sách phòng ban con (nếu có)
    'chartData' => json_encode($chartData), // Chuyển dữ liệu sang JSON
];

// Ghi log dữ liệu trả về
Log::info('Response Data:', $response);

// Trả về view với dữ liệu
return view('fe_chart.work_days', $response);
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

