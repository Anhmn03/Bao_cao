<?php

namespace App\Http\Controllers;

use App\Mail\AcceptLeaveRequestMail;
use App\Mail\RejectionNotification;
use App\Models\Leave_config;
use App\Models\Leave_request;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class Leave_requestController extends Controller
{
   public function index(){
    $leaveRequests = Leave_request::with('user') // Lấy thông tin nhân viên
    return view('fe_leave.admin_view', compact('leaveRequests'));
   }
   // Chấp nhận đơn xin nghỉ phép
public function acceptLeaveRequest($id)
{
    $leaveRequest = Leave_request::find($id);

    if (!$leaveRequest) {
        return redirect()->route('leave_requests.index')->with('error', 'Đơn xin nghỉ không tồn tại.');
    }

    // Cập nhật trạng thái của đơn xin nghỉ phép
    $leaveRequest->status = '1';
    $leaveRequest->save();

    // Cập nhật số ngày nghỉ phép đã sử dụng của nhân viên
    $user = $leaveRequest->user;

    // Tính số ngày nghỉ phép của đơn xin nghỉ
    $startDate = \Carbon\Carbon::parse($leaveRequest->start_date);
    $endDate = \Carbon\Carbon::parse($leaveRequest->end_date);
    $leaveDays = $endDate->diffInDays($startDate) + 1; // Bao gồm ngày bắt đầu

    return redirect()->route('leave_admin')->with('success', 'Đơn xin nghỉ phép đã được chấp nhận.');
}
public function reject(Request $request, $id)
{
    // Tìm đơn xin nghỉ dựa trên ID
    $leaveRequest = Leave_request::find($id);

    // Kiểm tra nếu đơn xin nghỉ không tồn tại
    if (!$leaveRequest) {
        return redirect()->back()->with('error', 'Đơn xin nghỉ không tồn tại.');
    }

    return redirect()->back()->with('success', 'Đơn xin nghỉ đã bị từ chối.');
}
}
