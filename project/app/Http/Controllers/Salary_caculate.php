<?php

namespace App\Http\Controllers;

use App\Models\CaculateSalary;
use App\Models\Department;
use App\Models\Salary;
use App\Models\User;
use App\Models\User_attendance;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class Salary_caculate extends Controller
{
    

public function showCaculate(Request $request)
{
    // Lấy danh sách các phòng ban
    $departments = Department::all();
    
    // Khởi tạo query cho CaculateSalary
    $query = CaculateSalary::with('user.department')
        ->whereHas('user', function ($query) {
            $query->where('role', '!=', 1); // Loại trừ role = 1
        });
    

    // Trả về view với dữ liệu cần thiết
    return view('fe_salary.salary_form', compact('cacu_salaries', 'departments'));
}



    /**
     * Display a listing of all calculated salaries.
     *
     * @return \Illuminate\View\View
     */

    public function getEmployeesByDepartment(Request $request)
    {
        // Kiểm tra giá trị đầu vào
        $departmentId = $request->input('department_id');
        if (!$departmentId) {
            return response()->json([
                'status' => 'error',
                'message' => 'Phòng ban không hợp lệ.',
            ], 400);
        }

        // Lấy danh sách nhân viên thuộc phòng ban đã chọn
        $employees = User::where('department_id', $departmentId)->get();

        // Kiểm tra nếu không có nhân viên nào
        if ($employees->isEmpty()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Không có nhân viên nào trong phòng ban này.',
                'employees' => [],
            ]);
        }

        // Trả về danh sách nhân viên
        return response()->json([
            'status' => 'success',
            'message' => 'Danh sách nhân viên được lấy thành công.',
            'employees' => $employees,
        ]);
    }


    /**
     * Tính lương cho nhân viên khi đã chọn phòng ban và nhân viên.
     *
     * @return \Illuminate\View\View
     */

   

    /**
     * Lưu thông tin lương tính toán.
     *
     * @return \Illuminate\View\View
     */

      public function index(Request $request)
{
    // Retrieve the top-level departments (parent_id = 0)
    $departments = Department::where('parent_id', 0)->get();

    // Declare variables to pass to the view
    $employees = [];
    $user = null;
    $salaryAmount = null;
    $validWorkdays = 0;
    $invalidWorkdays = 0;

    /**
     * Recursive function to get all sub-departments
     * 
     * @param int $parentId
     * @return array
     */
    // Hàm đệ quy để lấy tất cả phòng ban con
    $getAllSubDepartments = function ($parentId) use (&$getAllSubDepartments) {
        // Lấy tất cả các phòng ban con trực tiếp
        $subDepartments = Department::where('parent_id', $parentId)->get();
        $result = $subDepartments->toArray(); // Lưu danh sách phòng ban con

        foreach ($subDepartments as $subDepartment) {
            // Đệ quy gọi lại để lấy các phòng ban con của phòng ban hiện tại
            $result = array_merge($result, $getAllSubDepartments($subDepartment->id));
        }

        return $result; // Trả về tất cả các phòng ban con
    };

    

    // Kiểm tra nếu người dùng đã chọn phòng ban
    if ($request->has('department_id') && $request->input('department_id') != '') {
        $departmentId = $request->input('department_id');

        // Lấy tất cả phòng ban con của phòng ban đã chọn
        $allDepartments = $getAllSubDepartments($departmentId);

        // Lấy danh sách các phòng ban con và phòng ban cha
        $allDepartmentIds = array_column($allDepartments, 'id'); // Lấy tất cả id của các phòng ban con
        $allDepartmentIds[] = $departmentId; // Thêm id của phòng ban cha vào danh sách

        // Lấy tất cả nhân viên thuộc các phòng ban này
        $employees = User::whereIn('department_id', $allDepartmentIds)
            ->where('role', '!=', 1) // Giả sử role = 1 là admin hoặc không tính lương
            ->get();

        // Kiểm tra nếu người dùng đã chọn nhân viên
        if ($request->has('user_id') && $request->input('user_id') != '') {
            $userId = $request->input('user_id');

            // Lấy thông tin chi tiết nhân viên đã chọn
            $user = User::with('salary')->findOrFail($userId);

            // Tính số ngày công hợp lệ và không hợp lệ
            $workdays = $this->calculateWorkdays($userId);
            $validWorkdays = $workdays['valid_workdays'];
            $invalidWorkdays = $workdays['invalid_workdays'];

         

            // 

            if ($salary) {
                $dailySalary = $salary->dailySalary; // Lấy lương theo ngày từ bảng salaries
                $monthlySalary = $salary->monthlySalary; // Lấy lương theo tháng
            
                
        }
    }

    return view('fe_salary.salary_caculate', compact('departments', 'employees', 'user', 'salaryAmount', 'validWorkdays', 'invalidWorkdays'));
}
}
    

    public function calculateSalariesForAllEmployees()
{
    
    // Lấy tất cả nhân viên không phải quản trị và đang hoạt động
    $users = User::where('role', '!=', 1) // Loại trừ role = 1 (quản trị)
        ->where('status', '!=', 0) // Chỉ tính lương cho nhân viên đang hoạt động
        ->get();

        foreach ($users as $user) {
        
            // Tính số ngày công hợp lệ và không hợp lệ
            $workdays = $this->calculateWorkdays($user->id);
            $validWorkdays = $workdays['valid_workdays'];
            $invalidWorkdays = $workdays['invalid_workdays'];
        
            // Kiểm tra vai trò của nhân viên và chọn cách tính lương phù hợp
            $role = $user->role; // Lấy vai trò từ bảng User
            $dailySalary = $salary->dailySalary ?? 0; // Lấy lương ngày (nếu có)
            $monthlySalary = $salary->monthlySalary ?? 0; // Lấy lương tháng (nếu có)
        
            // Tính toán lương dựa trên vai trò
            if ($role == 3) {
                // Lương theo ngày
                $salaryAmount = $this->calculateSalaryAmount($role, $dailySalary, 0, $validWorkdays, $invalidWorkdays);
            } else {
                // Lương theo tháng
                $salaryAmount = $this->calculateSalaryAmount($role, 0, $monthlySalary, $validWorkdays, $invalidWorkdays);
            }
        
            // Kiểm tra nếu lương tháng hiện tại đã tồn tại
            $month = Carbon::now()->format('Y-m');
            $existingSalary = CaculateSalary::where('user_id', $user->id)
                ->where('month', $month)
                ->first();
        
                $salaryAmount = str_replace('.', '', $salaryAmount); // Xóa dấu phân cách hàng nghìn
                $salaryAmount = str_replace(' VND', '', $salaryAmount); // Xóa hậu tố " VND"
                
                // Chuyển đổi sang kiểu số (float hoặc int)
                $salaryAmount = floatval($salaryAmount); // Chuyển thành float
                
                // Kiểm tra xem có bản ghi lương hay không và cập nhật
                if ($existingSalary) {
                    $existingSalary->update([
                        'valid_workdays' => $validWorkdays,
                        'invalid_workdays' => $invalidWorkdays,
                        'salary_amount' => $salaryAmount, // Lưu giá trị lương như số
                        'salary_coefficient' => $salary->salaryCoefficient,
                        'updated_by' => auth()->id(),
                    ]);
                
            } else {
                // Nếu chưa có, tạo mới bản ghi lương
                CaculateSalary::create([
                    'user_id' => $user->id,
                    'valid_workdays' => $validWorkdays,
                    'invalid_workdays' => $invalidWorkdays,
                    'salary_amount' => $salaryAmount,
                    'salary_coefficient' => $salary->salaryCoefficient,
                    'month' => $month,
                    'created_by' => auth()->id(),
                    'updated_by' => auth()->id(),
                ]);
            }
    }

    // Trả về view thông báo hoàn thành
    return redirect()->route('salary_caculate')->with('success', 'Lương của tất cả nhân viên đã được tính và lưu thành công!');
}



    public function calculateSalary(Request $request)
{
    $userId = $request->input('user_id');
    $user = User::findOrFail($userId);

    $salary = Salary::where('department_id', $user->department_id)->first();

    if (!$salary) {
        return back()->with('error', 'Thông tin lương không tìm thấy.');
    }

    $role = $user->role; // Giả sử vai trò người dùng được lưu trong cột 'role'
    $dailySalary = $salary->dailySalary; // Lấy lương theo ngày (nếu có)
    $monthlySalary = $salary->monthlySalary; // Lấy lương tháng
    $validWorkdays = $request->input('valid_workdays');
    $invalidWorkdays = $request->input('invalid_workdays');

    // Gọi hàm calculateSalaryAmount với đầy đủ tham số
    $salaryAmount = $this->calculateSalaryAmount($role, $dailySalary, $monthlySalary, $validWorkdays, $invalidWorkdays);

    $month = Carbon::now()->startOfMonth()->format('Y-m-d'); // Sử dụng định dạng 'Y-m-d'
    $existingSalary = CaculateSalary::where('user_id', $userId)->where('month', $month)->first();

    if ($existingSalary) {
        $existingSalary->update([
            'valid_workdays' => $validWorkdays,
            'invalid_workdays' => $invalidWorkdays,
            'salary_amount' => $salaryAmount,
        ]);
    } else {
        CaculateSalary::create([
            'user_id' => $userId,
            'valid_workdays' => $validWorkdays,
            'invalid_workdays' => $invalidWorkdays,
            'salary_amount' => $salaryAmount,
            'month' => $month,
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);
    }

    return redirect()->route('salary_caculate')->with('success', 'Tính lương thành công!');
}

    /**
     * 
     * Tính lương cho tất cả nhân viên
     *
     * @return \Illuminate\Http\Response
     */
    private function calculateSalaryAmount($role, $dailySalary, $monthlySalary, $validWorkdays, $invalidWorkdays)
    {
        // Get the current month and year
        $month = Carbon::now()->month;
        $year = Carbon::now()->year;
    
        // Initialize salary amount
        $salaryAmount = 0;
    
        if ($role == 2) {
            // Role = 2: Calculate monthly salary
            $totalWorkdaysInMonth = $this->getTotalWorkdaysInMonth($year, $month);
    
            // Calculate salary based on valid workdays
            $validWorkdaysAmount = ($validWorkdays / $totalWorkdaysInMonth) * $monthlySalary;
    
            // Calculate salary for invalid workdays (50% of the monthly salary)
            $invalidWorkdaysAmount = ($invalidWorkdays / $totalWorkdaysInMonth) * ($monthlySalary * 0.5);
    
            // Total salary for role 2
            $salaryAmount = $validWorkdaysAmount + $invalidWorkdaysAmount;
    
        } elseif ($role == 3) {
            // Role = 3: Calculate daily salary
            $validWorkdaysAmount = $validWorkdays * $dailySalary;
            $invalidWorkdaysAmount = $invalidWorkdays * $dailySalary * 0.5;
    
            // Total salary for role 3
            $salaryAmount = $validWorkdaysAmount + $invalidWorkdaysAmount;
        }
    
        // Round salary to 2 decimal places
        $salaryAmount = round($salaryAmount, 2);
    
        // Format the salary amount as currency with commas and VND suffix
        return number_format($salaryAmount, 0, ',', '.') . ' VND';
    }
    

    // Calculate the total number of workdays in the month (Monday to Friday, with optional Saturday work)
    private function getTotalWorkdaysInMonth($year, $month)
    {
        $startDate = Carbon::createFromDate($year, $month, 1); // Start of the month
        $endDate = $startDate->copy()->endOfMonth(); // End of the month

        $workdays = 0;

        // Loop through each day in the month
        while ($startDate <= $endDate) {
            // If it's a weekday (Monday to Friday)
            if ($startDate->isWeekday()) {
                $workdays++;
            } elseif ($startDate->isSaturday()) {
                // Optionally, if Saturday is considered a workday
                $workdays++;
            }

            // Move to the next day
            $startDate->addDay();
        }

        return $workdays;
    }


    // Calculate workdays based on attendance data
    public function calculateWorkdays($userId)
    {
        // Lấy tất cả các bản ghi attendance trong tháng này của người dùng
        $attendances = User_attendance::where('user_id', $userId)
            ->whereYear('time', Carbon::now()->year)
            ->whereMonth('time', Carbon::now()->month)
            ->get();
//            
    
        $validWorkdays = 0; // Số ngày công hợp lệ
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
            Log::debug('Valid workdays: ' . $validWorkdays . ' Invalid workdays: ' . $invalidWorkdays);

        }
    
        // Kiểm tra kết quả trả về
    
        // Trả về kết quả
        return ['valid_workdays' => $validWorkdays, 'invalid_workdays' => $invalidWorkdays];
    }


    public function saveAllSalaries()
    {
        $calculatedSalaries = session('calculated_salaries', []);

        foreach ($calculatedSalaries as $salary) {
            $month = Carbon::now()->format('Y-m'); // Lấy tháng hiện tại

            // Kiểm tra nếu đã tồn tại bản ghi lương
            $existingSalary = CaculateSalary::where('user_id', $salary['user_id'])
                ->where('month', $month)
                ->first();

            if ($existingSalary) {
                // Cập nhật lương
                $existingSalary->update([
                    'valid_workdays' => $salary['valid_workdays'],
                    'invalid_workdays' => $salary['invalid_workdays'],
                    'salary_amount' => str_replace('.', '', $salary['salary_amount']), // Ensure no thousand separators

                    // 'salary_amount' => $salary['salary_amount'],
                    'salary_coefficient' => $salary['salary_coefficient'],
                    'updated_by' => auth()->id(),
                ]);
            } else {

                // Tạo mới bản ghi lương
                CaculateSalary::create([
                    'user_id' => $salary['user_id'],
                    'valid_workdays' => $salary['valid_workdays'],
                    'invalid_workdays' => $salary['invalid_workdays'],
                    'salary_amount' => $salary['salary_amount'],
                    'salary_coefficient' => $salary['salary_coefficient'],
                    'month' => $month,
                    'created_by' => auth()->id(),
                    'updated_by' => auth()->id(),
                ]);
            }
        }

        // Xóa session sau khi lưu
        session()->forget('calculated_salaries');

        return redirect()->route('salary_caculate')->with('success', 'Lương đã được lưu thành công!');
    }
    public function saveSalary(Request $request)
    {
        // Lấy dữ liệu từ request
        $userId = $request->input('user_id');
        $validWorkdays = $request->input('valid_workdays');
        $invalidWorkdays = $request->input('invalid_workdays');
        $salaryAmount = $request->input('salary_amount');
        $salaryCoefficient = $request->input('salary_coefficient');

        // Lấy tháng hiện tại
        $month = Carbon::now()->format('Y-m');  // This ensures the month is in 'YYYY-MM' format

        // Kiểm tra nếu đã tồn tại bản ghi lương cho tháng này
        $existingSalary = CaculateSalary::where('user_id', $userId)->where('month', $month)->first();
        $salaryAmount = str_replace(['.', 'VNĐ'], '', $salaryAmount);

        if ($existingSalary) {
            // Cập nhật lương
            $existingSalary->update([
                'valid_workdays' => $validWorkdays,
                'invalid_workdays' => $invalidWorkdays,
                // Lưu giá trị số mà không có hậu tố "VND"
                'salary_amount' => floatval(str_replace('.', '', $salaryAmount)), // Đảm bảo là giá trị số
                'salary_coefficient' => $salaryCoefficient,
                'updated_by' => auth()->id(),
                'updated_at' => now(),
            ]);
            
        } else {
            // Tạo mới bản ghi lương
            CaculateSalary::create([
                'user_id' => $userId,
                'valid_workdays' => $validWorkdays,
                'invalid_workdays' => $invalidWorkdays,
                'salary_amount' => $salaryAmount,
                'salary_coefficient' => $salaryCoefficient,
                'month' => $month,
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
            ]);
        }

        return redirect()->route('salary_caculate')->with('success', 'Lương đã được lưu thành công!');
    }
    // Save or update salary information
    public function saveOrUpdateSalary($userId, $validWorkdays, $invalidWorkdays)
    {
        $month = Carbon::now()->format('Y-m');

        $existingSalaryRecord = CaculateSalary::where('user_id', $userId)
            ->where('month', $month)
            ->first();

        if ($existingSalaryRecord) {
            $existingSalaryRecord->update([
                'valid_workdays' => $validWorkdays,
                'invalid_workdays' => $invalidWorkdays,
                'updated_by' => $userId,
            ]);
        } else {
            CaculateSalary::create([
                'user_id' => $userId,
                'valid_workdays' => $validWorkdays,
                'invalid_workdays' => $invalidWorkdays,
                'salary_amount' => 0.00,
                'month' => $month,
                'created_by' => $userId,
                'updated_by' => $userId,
            ]);
        }
    }

    // Calculate and save workdays for the current user
    
}