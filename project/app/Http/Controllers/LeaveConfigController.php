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


    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'reason' => 'required|string|max:255',
    //         'other_reason' => 'nullable|string', // Nếu lý do là "Khác", có thể có thêm lý do điền vào
    //         'start_date' => 'required|date|after_or_equal:today',
    //         'end_date' => 'required|date|after_or_equal:start_date',
    //     ]);
    
    //     $user = Auth::user();
    
    //     // Nếu chọn "Khác", sử dụng lý do người dùng nhập
    //     if ($request['reason'] == 'Khác' && !empty($request['other_reason'])) {
    //         $reason = $request['other_reason'];  // Sử dụng lý do người dùng nhập
    //     } else {
    //         $reason = $request['reason'];  // Sử dụng lý do được chọn từ danh sách
    //     }    
    //     // Tạo đơn xin nghỉ
    //     $leaveRequest = Leave_request::create([
    //         'user_id' => $user->id,
    //         'reason' => $reason,
    //         'start_date' => $request->input('start_date'),
    //         'end_date' => $request->input('end_date'),
    //         'status' => '0',
    //     ]);
    // // Lấy thông tin admin (ở đây giả sử bạn lấy email của admin từ bảng User, bạn có thể điều chỉnh lại nếu cần)
    // // $admin = User::where('role', 'admin')->first();

    // // // Gửi email thông báo cho admin
    // // if ($admin) {
    // //     Mail::to($admin->email)->send(new LeaveRequestNotification($leaveRequest));
        
    // // } 
    // $adminEmail = env('MAIL_FROM_ADDRESS');
    // Mail::to($adminEmail)->send(new LeaveRequestNotification($leaveRequest));
    // // $admin = User::where('role', '1')->first();

    // // // Gửi email thông báo cho admin
    // // if ($admin) {
    // //     Mail::to($admin->email)->send(new LeaveRequestNotification($leaveRequest)); // Truyền đối tượng Leave_request vào đây
    // // }
    //     return redirect()->back()->with('success', 'Đơn xin nghỉ đã được gửi.');
    // }

    public function store(Request $request)
{
    $request->validate([
        'reason' => 'required|string|max:255',
        'other_reason' => 'nullable|string',
        'start_date' => 'required|date|after_or_equal:today',
        'end_date' => 'required|date|after_or_equal:start_date',
    ]);

    $user = Auth::user();

    // Tính số ngày nghỉ mới
    $startDate = \Carbon\Carbon::parse($request->input('start_date'));
    $endDate = \Carbon\Carbon::parse($request->input('end_date'));
    $leaveDaysRequested = $endDate->diffInDays($startDate) + 1; // Bao gồm ngày nghỉ cuối cùng

    // Lấy cấu hình nghỉ phép của người dùng
    $config = Leave_config::firstOrCreate(
        ['user_id' => $user->id],
        ['used_leave_days' => 0]
    );
    
    // Tính tổng số ngày nghỉ đã sử dụng
    $totalUsedLeaveDays = $config->used_leave_days + $leaveDaysRequested;

    // Kiểm tra xem số ngày nghỉ có vượt quá số ngày tối đa không
    if ($totalUsedLeaveDays > $config->max_leave_days) {
        // Tính số ngày nghỉ vượt quá
        $exceedingDays = $totalUsedLeaveDays - $config->max_leave_days;
        
        // Trả về thông báo cảnh báo cho người dùng
        return redirect()->back()->with('warning', "Số ngày nghỉ bạn yêu cầu vượt quá số ngày nghỉ tối đa. Bạn sẽ phải nghỉ không lương {$exceedingDays} ngày.");
    }

    // Nếu không vượt quá số ngày nghỉ, thực hiện tạo đơn xin nghỉ
    // Nếu chọn "Khác", sử dụng lý do người dùng nhập
    if ($request['reason'] == 'Khác' && !empty($request['other_reason'])) {
        $reason = $request['other_reason'];
    } else {
        $reason = $request['reason'];
    }

    // Tạo đơn xin nghỉ phép
    $leaveRequest = Leave_request::create([
        'user_id' => $user->id,
        'reason' => $reason,
        'start_date' => $request->input('start_date'),
        'end_date' => $request->input('end_date'),
        'status' => '0', // Chưa duyệt
    ]);

    // Gửi email thông báo cho admin
    $adminEmail = env('MAIL_FROM_ADDRESS');
    Mail::to($adminEmail)->send(new LeaveRequestNotification($leaveRequest));

    return redirect()->back()->with('success', 'Đơn xin nghỉ đã được gửi.');
}

    
}
