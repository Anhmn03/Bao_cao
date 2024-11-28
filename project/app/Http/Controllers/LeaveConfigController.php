<?php

namespace App\Http\Controllers;

use App\Mail\LeaveRequestNotification;
use App\Models\Leave_config;
use App\Models\Leave_request;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class LeaveConfigController extends Controller
{
    public function show()
    {
        $user = Auth::user();
    
        // Tính số ngày nghỉ tối đa dựa trên thâm niên
        $maxLeaveDays = $this->calculateMaxLeaveDay($user);
    
        // Tìm hoặc tạo mới cấu hình nghỉ phép
        $config = Leave_config::firstOrCreate(
            ['user_id' => $user->id], // Điều kiện tìm kiếm
            ['used_leave_days' => 0]  // Nếu không tìm thấy, tạo mới với giá trị mặc định
        );
    
        // Cập nhật max_leave_days nếu cần thiết
        if ($config->max_leave_days !== $maxLeaveDays) {
            $config->update(['max_leave_days' => $maxLeaveDays]);
        }
    
        // Tính số ngày nghỉ còn lại
        $remainingLeaveDays = max($config->max_leave_days - $config->used_leave_days, 0);
    
        // Lấy danh sách các yêu cầu nghỉ phép
        $leaveRequests = Leave_request::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();
    
        // Trả về view với dữ liệu
        return view('fe_leave.show', [
            'user' => $user,
            'maxLeaveDays' => $config->max_leave_days,
            'usedLeaveDays' => $config->used_leave_days,
            'remainingLeaveDays' => $remainingLeaveDays,
            'leaveRequests' => $leaveRequests,
        ]);
    }
    

    private function calculateMaxLeaveDay(User $user)
    {
        // Lấy ngày bắt đầu làm việc của nhân viên
        $startDate = $user->created_at;
    
        // Tính thâm niên làm việc
        $yearsWorked = $startDate->diffInYears(now());
    
        // Dựa vào thâm niên để tính số ngày nghỉ phép
        if ($yearsWorked < 1) {
            return 10; // Dưới 1 năm: 10 ngày nghỉ phép
        } elseif ($yearsWorked >= 1 && $yearsWorked < 3) {
            return 12; // Từ 1 đến 3 năm: 12 ngày nghỉ phép
        } elseif ($yearsWorked >= 3 && $yearsWorked < 5) {
            return 15; // Từ 3 đến 5 năm: 15 ngày nghỉ phép
        } else {
            return 18; // Trên 5 năm: 18 ngày nghỉ phép
        }
    }


    public function store(Request $request)
    {
        $request->validate([
            'reason' => 'required|string|max:255',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'custom_reason' => 'nullable|string|max:255', // Trường lý do nếu chọn "Khác"
        ]);
    
        $user = Auth::user();
    
        // Nếu chọn "Khác", sử dụng lý do người dùng nhập
        $reason = $request->input('reason') === 'other' ? $request->input('custom_reason') : $request->input('reason');
    
        // Tạo đơn xin nghỉ
        $leaveRequest = Leave_request::create([
            'user_id' => $user->id,
            'reason' => $reason,
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
            'status' => 'pending',
            'custom_reason' => $request->input('custom_reason'), // Lưu lý do "khác" nếu có
        ]);
    // Lấy thông tin admin (ở đây giả sử bạn lấy email của admin từ bảng User, bạn có thể điều chỉnh lại nếu cần)
    // $admin = User::where('role', 'admin')->first();

    // // Gửi email thông báo cho admin
    // if ($admin) {
    //     Mail::to($admin->email)->send(new LeaveRequestNotification($leaveRequest));
        
    // } 
    // $adminEmail = env('MAIL_FROM_ADDRESS');
    // Mail::to($adminEmail)->send(new LeaveRequestNotification($leaveRequest));
    $admin = User::where('role', '1')->first();

    // Gửi email thông báo cho admin
    if ($admin) {
        Mail::to($admin->email)->send(new LeaveRequestNotification($leaveRequest)); // Truyền đối tượng Leave_request vào đây
    }
        return redirect()->back()->with('success', 'Đơn xin nghỉ đã được gửi.');
    }
    
}
