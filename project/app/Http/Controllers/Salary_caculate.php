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
    public function showCaculate(Request $request){
        $cacu_salaries = CaculateSalary::with('user.department')->get();
        return view('fe_user.salary_form', compact('cacu_salaries'));
    }
    /**
     * Display a listing of all calculated salaries.
     *
     * @return \Illuminate\View\View
     */
    public function showForm(Request $request)
    {
        // Lấy các phòng ban có parent_id = 0
        $departments = Department::where('parent_id', 0)->get();
        return view('fe_salary.salary_department', compact('departments'));
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
     
        
     
         // Kiểm tra nếu người dùng đã chọn phòng ban
         if ($request->has('department_id') && $request->input('department_id') != '') {
             $departmentId = $request->input('department_id');
             
          
     
             // Lấy danh sách nhân viên theo tất cả phòng ban con và phòng ban cha
          
             $allDepartmentIds[] = $departmentId; // Thêm id của phòng ban cha vào danh sách
     
             // Lấy tất cả nhân viên thuộc các phòng ban này
             $employees = User::whereIn('department_id', $allDepartmentIds)->get();
     
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

        $month = Carbon::now()->startOfMonth()->format('Y-m'); // Sử dụng định dạng 'Y-m-d'
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
            return Carbon::parse($attendance->time)->format('Y-m-d'); // Nhóm theo ngày
        });
    
        // Duyệt qua từng nhóm ngày
        foreach ($attendancesGroupedByDay as $date => $attendancesForDay) {
            $checkIns = $attendancesForDay->where('type', 'in'); // Lọc các bản ghi check-in
            $checkOuts = $attendancesForDay->where('type', 'out'); // Lọc các bản ghi check-out
    
            // Nếu không đủ cặp check-in/out thì không thay đổi gì, chỉ tiếp tục
            if ($checkIns->isEmpty() || $checkOuts->isEmpty()) {
                continue; // Không thay đổi số ngày công hợp lệ hay không hợp lệ
            }
    
            // Kiểm tra trạng thái của từng cặp check-in và check-out
            $isValidDay = false;
            foreach ($checkIns as $checkIn) {
                foreach ($checkOuts as $checkOut) {
                    // Kiểm tra trạng thái hợp lệ của cặp check-in và check-out
                    if ($checkIn->status === true && $checkOut->status === true && 
                        Carbon::parse($checkIn->time)->lessThan(Carbon::parse($checkOut->time))) {
                        $isValidDay = true;
                        break; // Chỉ cần một cặp hợp lệ là đủ
                    }
                }
                if ($isValidDay) break;
            }
    
           
        }
    
        // Trả về số ngày công hợp lệ và không hợp lệ
        return ['valid_workdays' => $validWorkdays, 'invalid_workdays' => $invalidWorkdays];
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
        $month = Carbon::now()->format('Y-m-d');
    
        // Kiểm tra nếu đã tồn tại bản ghi lương cho tháng này
        $existingSalary = CaculateSalary::where('user_id', $userId)->where('month', $month)->first();
    
        if ($existingSalary) {
            // Cập nhật lương
            $existingSalary->update([
                'valid_workdays' => $validWorkdays,
                'invalid_workdays' => $invalidWorkdays,
                'salary_amount' => $salaryAmount,
                'salary_coefficient' => $salaryCoefficient,
                'updated_by' => auth()->id(),
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

  
}


