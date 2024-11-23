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

class Salary_caculate extends Controller
{
    public function showCaculate(Request $request)
    {
        $cacu_salaries = CaculateSalary::with('user.department')->get();
        return view('fe_user.salary_form', compact('cacu_salaries'));
    }
    /**
     * Display a listing of all calculated salaries.
     *
     * @return \Illuminate\View\View
     */
    // public function showForm(Request $request)
    // {
    //     // Lấy các phòng ban có parent_id = 0
    //     $departments = Department::where('parent_id', 0)->get();
    //     return view('fe_salary.salary_department', compact('departments'));
    // }
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

    public function index(Request $request)
    {
        // Lấy danh sách các phòng ban cấp cao nhất
        $departments = Department::where('parent_id', 0)->get();

        // Khai báo các biến để truyền vào view
        $employees = [];
        $user = null;
        $salaryAmount = null;
        $validWorkdays = 0;
        $invalidWorkdays = 0;

        // Hàm đệ quy để lấy tất cả phòng ban con
        $getAllSubDepartments = function ($parentId) use (&$getAllSubDepartments) {
            $subDepartments = Department::where('parent_id', $parentId)->get();
            $result = $subDepartments->toArray(); // Lưu danh sách phòng ban con

            foreach ($subDepartments as $subDepartment) {
                // Đệ quy gọi lại để lấy các phòng ban con của phòng ban hiện tại
                $result = array_merge($result, $getAllSubDepartments($subDepartment->id));
            }
            return $result;
        };

        // Kiểm tra nếu người dùng đã chọn phòng ban
        if ($request->has('department_id') && $request->input('department_id') != '') {
            $departmentId = $request->input('department_id');

            // Lấy tất cả phòng ban con của phòng ban đã chọn
            $allDepartments = $getAllSubDepartments($departmentId);

            // Lấy danh sách nhân viên theo tất cả phòng ban con và phòng ban cha
            $allDepartmentIds = array_column($allDepartments, 'id'); // Lấy tất cả id của các phòng ban con
            $allDepartmentIds[] = $departmentId; // Thêm id của phòng ban cha vào danh sách

            // Lấy tất cả nhân viên thuộc các phòng ban này
            $employees = User::whereIn('department_id', $allDepartmentIds)
                ->where('role', '!=', 1)
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

                // Lấy thông tin lương
                $salary = Salary::where('department_id', $user->department_id)->first();

                if ($salary) {
                    $salaryCoefficient = $salary->salaryCoefficient;
                    $monthlySalary = $salary->monthlySalary;

                    // Gọi phương thức calculateSalaryAmount để tính lương
                    $salaryAmount = $this->calculateSalaryAmount($salaryCoefficient, $monthlySalary, $validWorkdays, $invalidWorkdays);
                }
            }
        }

        return view('fe_user.salary_caculate', compact('departments', 'employees', 'user', 'salaryAmount', 'validWorkdays', 'invalidWorkdays'));
    }


    /**
     * Lưu thông tin lương tính toán.
     *
     * @return \Illuminate\View\View
     */

    // public function calculateSalariesForAllEmployees()
    // {
    //     $users = User::where('role', '!=', 1)
    //         ->where('status', '!=', 0)
    //         ->get(); // Lấy tất cả nhân viên trừ role = 1 và status = 0         $calculatedSalaries = []; // Lưu lương tạm

    //     foreach ($users as $user) {
    //         $salary = Salary::where('department_id', $user->department_id)->first();

    //         if (!$salary) {
    //             continue; // Bỏ qua nếu không tìm thấy thông tin lương cho nhân viên
    //         }

    //         $workdays = $this->calculateWorkdays($user->id);
    //         $validWorkdays = $workdays['valid_workdays'];
    //         $invalidWorkdays = $workdays['invalid_workdays'];

    //         $salaryCoefficient = $salary->salaryCoefficient;
    //         $monthlySalary = $salary->monthlySalary;
    //         $salaryAmount = $this->calculateSalaryAmount($salaryCoefficient, $monthlySalary, $validWorkdays, $invalidWorkdays);

    //         // Lưu tạm vào mảng
    //         $calculatedSalaries[] = [
    //             'user_id' => $user->id,
    //             'user_name' => $user->name,
    //             'valid_workdays' => $validWorkdays,
    //             'invalid_workdays' => $invalidWorkdays,
    //             'salary_amount' => $salaryAmount,
    //             'salary_coefficient' => $salaryCoefficient,
    //         ];
    //     }

    //     // Lưu vào session để hiển thị trên giao diện
    //     session(['calculated_salaries' => $calculatedSalaries]);
    //     return view('fe_user/salary_allcaculate', ['calculatedSalaries' => $calculatedSalaries]);
    // }
    public function calculateSalariesForAllEmployees()
{
    $users = User::where('role', '!=', 1) // Loại trừ role = 1 (quản trị)
        ->where('status', '!=', 0) // Chỉ tính lương cho nhân viên đang hoạt động
        ->get();

    foreach ($users as $user) {
        $salary = Salary::where('department_id', $user->department_id)->first();

        if (!$salary) {
            continue; // Bỏ qua nếu không tìm thấy thông tin lương cho nhân viên
        }

        // Tính số ngày công hợp lệ và không hợp lệ
        $workdays = $this->calculateWorkdays($user->id);
        $validWorkdays = $workdays['valid_workdays'];
        $invalidWorkdays = $workdays['invalid_workdays'];

        // Tính toán số tiền lương
        $salaryCoefficient = $salary->salaryCoefficient;
        $monthlySalary = $salary->monthlySalary;
        $salaryAmount = $this->calculateSalaryAmount($salaryCoefficient, $monthlySalary, $validWorkdays, $invalidWorkdays);

        // Kiểm tra nếu lương tháng hiện tại đã tồn tại
        $month = Carbon::now()->format('Y-m');
        $existingSalary = CaculateSalary::where('user_id', $user->id)
            ->where('month', $month)
            ->first();

        if ($existingSalary) {
            // Nếu đã có, cập nhật bản ghi
            $existingSalary->update([
                'valid_workdays' => $validWorkdays,
                'invalid_workdays' => $invalidWorkdays,
                'salary_amount' => str_replace('.', '', $salaryAmount),
                'salary_coefficient' => $salaryCoefficient,
                'updated_by' => auth()->id(),
            ]);
        } else {
            // Nếu chưa có, tạo mới bản ghi lương
            CaculateSalary::create([
                'user_id' => $user->id,
                'valid_workdays' => $validWorkdays,
                'invalid_workdays' => $invalidWorkdays,
                'salary_amount' => str_replace('.', '', $salaryAmount),
                'salary_coefficient' => $salaryCoefficient,
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

        $salaryCoefficient = $salary->salaryCoefficient;
        $monthlySalary = $salary->monthlySalary;
        $validWorkdays = $request->input('valid_workdays');
        $invalidWorkdays = $request->input('invalid_workdays');

        $salaryAmount = $this->calculateSalaryAmount($salaryCoefficient, $monthlySalary, $validWorkdays, $invalidWorkdays);

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
     * Tính lương cho tất cả nhân viên
     *
     * @return \Illuminate\Http\Response
     */



    // Calculate the salary amount based on workdays
    private function calculateSalaryAmount($salaryCoefficient, $monthlySalary, $validWorkdays, $invalidWorkdays)
    {
        // Get the current month and year
        $month = Carbon::now()->month;
        $year = Carbon::now()->year;

        // Calculate the total workdays in the current month (Monday to Friday, optionally including Saturday)
        $totalWorkdaysInMonth = $this->getTotalWorkdaysInMonth($year, $month);

        // Calculate salary based on valid workdays
        $validWorkdaysAmount = ($validWorkdays / $totalWorkdaysInMonth) * $monthlySalary;

        // Calculate salary for invalid workdays (50% of the monthly salary)
        $invalidWorkdaysAmount = ($invalidWorkdays / $totalWorkdaysInMonth) * ($monthlySalary * 0.5);

        // Total salary = (valid workdays salary + invalid workdays salary) * salary coefficient
        // $salaryAmount = ($validWorkdaysAmount + $invalidWorkdaysAmount) * $salaryCoefficient;
        $salaryAmount = ($validWorkdaysAmount + $invalidWorkdaysAmount);
        $salaryAmount = round($salaryAmount, 2);

        // Format the salary amount as currency with commas and VND suffix
        return number_format($salaryAmount, 0, ',', '.');

        return $salaryAmount;
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

        if ($existingSalary) {
            // Cập nhật lương
            $existingSalary->update([
                'valid_workdays' => $validWorkdays,
                'invalid_workdays' => $invalidWorkdays,
                'salary_amount' => str_replace('.', '', $salaryAmount), // Ensure no thousand separators
                'salary_coefficient' => $salaryCoefficient,
                'updated_by' => auth()->id(),
                'updated_at' => now(), // Automatically handles timestamp formatting
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
    public function calculateAndSaveWorkdays(Request $request)
    {
        $user = Auth::user();

        $workdays = $this->calculateWorkdays($user->id);
        $validWorkdays = $workdays['valid_workdays'];
        $invalidWorkdays = $workdays['invalid_workdays'];

        $this->saveOrUpdateSalary($user->id, $validWorkdays, $invalidWorkdays);

        return redirect()->back()->with('message', 'Đã tính toán và lưu số ngày công cho tháng này.');
    }
}
