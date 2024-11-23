<?php

namespace App\Http\Controllers;

use App\Models\CaculateSalary;
use App\Models\Salary;
use App\Models\User;
use App\Models\User_attendance;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Caculate_Salary extends Controller
{
    // Get salary info for a specific user
    public function getSalaryInfo($userId)
    {
        $user = User::findOrFail($userId);
        $salary = Salary::where('department_id', $user->department_id)->first();

        $workdays = $this->calculateWorkdays($userId);
        $validWorkdays = $workdays['valid_workdays'];
        $invalidWorkdays = $workdays['invalid_workdays'];

        if ($salary) {
            $data = [
                'userName' => $user->name,
                'department' => $user->department->name ?? 'Không rõ',
                'salaryCoefficient' => $salary->salaryCoefficient,
                'monthlySalary' => $salary->monthlySalary,
                'validWorkdays' => $validWorkdays,
                'invalidWorkdays' => $invalidWorkdays,
                'salaryAmount' => $this->calculateSalaryAmount($salary->salaryCoefficient, $salary->monthlySalary, $validWorkdays, $invalidWorkdays),
            ];

            return view('fe_salary.salary_user', $data);
        } else {
            return redirect()->back()->with('error', 'Không tìm thấy thông tin lương.');
        }
    }

    // Calculate and save salary information
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

        $month = Carbon::now()->format('Y-m');
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

        return redirect()->route('admin.salary')->with('success', 'Tính lương thành công!');
    }

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
        $salaryAmount = ($validWorkdaysAmount + $invalidWorkdaysAmount) ;
        $salaryAmount = round($salaryAmount, 2);

        // Format the salary amount as currency with commas and VND suffix
        return number_format($salaryAmount, 0, ',', '.') ;

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
   // Calculate workdays based on attendance data
public function calculateWorkdays($userId)
{
    // Lấy tất cả các bản ghi attendance trong tháng này của người dùng
    $attendances = User_attendance::where('user_id', $userId)
        ->whereYear('time', Carbon::now()->year)
        ->whereMonth('time', Carbon::now()->month)
        ->get();

    $validWorkdays = 0;
    $invalidWorkdays = 0;

    // Nhóm các bản ghi attendance theo ngày
    $attendancesGroupedByDay = $attendances->groupBy(function ($attendance) {
        return Carbon::parse($attendance->time)->format('Y-m-d'); // Nhóm theo ngày (Y-m-d)
    });

    // Duyệt qua từng nhóm ngày
    foreach ($attendancesGroupedByDay as $date => $attendancesForDay) {
        $isValidDay = true; // Giả sử là một ngày công hợp lệ

        foreach ($attendancesForDay as $attendance) {
            // Kiểm tra nếu check-in không hợp lệ (type == 'in' và status không phải true)
            $checkInValid = ($attendance->type == 'in' && $attendance->status === true);
            // Kiểm tra nếu check-out không hợp lệ (type == 'out' và status không phải true)
            $checkOutValid = ($attendance->type == 'out' && $attendance->status === true);

            // Nếu có bất kỳ checkin hoặc checkout nào không hợp lệ, đánh dấu là ngày công không hợp lệ
            if (!$checkInValid || !$checkOutValid) {
                $isValidDay = false;
                break; // Nếu đã xác định là không hợp lệ thì thoát ra khỏi vòng lặp
            }
        }

        // Nếu tất cả checkin/check-out trong ngày đó hợp lệ
        if ($isValidDay) {
            $validWorkdays++;
        } else {
            $invalidWorkdays++;
        }
    }

    // Trả về số ngày công hợp lệ và không hợp lệ
    return ['valid_workdays' => $validWorkdays, 'invalid_workdays' => $invalidWorkdays];
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
