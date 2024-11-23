<?php

namespace App\Http\Controllers;

use App\Mail\AttendanceApproved;
use App\Mail\JustificationRejected;
use App\Mail\JustificationSubmitted;
use App\Models\CaculateSalary;
use App\Models\Department;
use App\Models\Setting;
use App\Models\User;
use App\Models\User_attendance;
use Carbon\Carbon;
use Hamcrest\Core\Set;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class User_attendanceController extends Controller


{
    // Phương thức hiển thị lịch sử chấm công của người dùng
    public function index(Request $request)
    {
        $query = User_attendance::where('user_id', Auth::id());

        // Kiểm tra nếu có tham số tìm kiếm theo ngày
        if ($request->has('search_date') && $request->input('search_date') !== '') {
            $searchDate = $request->input('search_date');
            $query->whereDate('time', $searchDate); // Tìm kiếm theo ngày
        }

        $attendances = $query->orderBy('time', 'desc') // Sắp xếp theo thời gian mới nhất
            ->paginate(5); // Phân trang, mỗi trang có 5 bản ghi

        return view('fe_attendances.users_attendance', compact('attendances'));
    }

    // public function checkIn(Request $request)
    // {
    //     $user = Auth::user();
    //     $today = Carbon::today()->toDateString();  // Lấy ngày hôm nay

    //     // Kiểm tra xem ngày hôm qua có bản ghi check-out chưa
    //     $this->checkOutYesterday($user, $today, $request);

    //     // Kiểm tra bản ghi check-in hiện tại của người dùng
    //     $latestAttendance = User_attendance::where('user_id', $user->id)
    //         ->orderBy('time', 'desc')
    //         ->first();

    //     // Debug: Log bản ghi check-in mới nhất để kiểm tra
    //     Log::info('Latest attendance: ' . json_encode($latestAttendance));

    //     // Nếu chưa có bản ghi check-in hoặc đã check-out, tiến hành check-in mới
    //     if (!$latestAttendance || ($latestAttendance->type === 'out' && $latestAttendance->status)) {
    //         return $this->handleCheckIn($user, $request);
    //     } else {
    //         // Nếu đã check-in rồi và chưa check-out, thông báo lỗi
    //         return redirect()->back()->with('message', 'Bạn đã Check In rồi. Vui lòng Check Out trước khi Check In lại!');
    //     }
    // }

    // // Phương thức kiểm tra và tạo bản ghi check-out cho ngày hôm qua
    // public function checkOutYesterday($user, $today, $request)
    // {
    //     $yesterday = Carbon::yesterday()->toDateString();

    //     // Kiểm tra xem ngày hôm qua đã có bản ghi check-out chưa
    //     $yesterdayCheckOut = User_attendance::where('user_id', $user->id)
    //         ->whereDate('time', $yesterday)
    //         ->where('type', 'out')
    //         ->first();

    //     if (!$yesterdayCheckOut) {
    //         // Nếu chưa có check-out, tạo bản ghi check-out tự động lúc 17:05
    //         $checkOutTime = Carbon::parse($yesterday . ' 17:05:00')->timezone('Asia/Ho_Chi_Minh');

    //         // Debug: In ra thời gian để kiểm tra
    //         Log::info("message");;

    //         // Tạo bản ghi check-out tự động
    //         User_attendance::create([
    //             'time' => $checkOutTime->timestamp,
    //             'type' => 'out',
    //             'user_id' => $user->id,
    //             'status' => false,
    //             'justification' => $request->input('justification', ''),
    //             'created_by' => $user->id,
    //             'updated_by' => $user->id,
    //             'created_at' => now(),  // Dùng Carbon cho thời gian hiện tại
    //             'updated_at' => now(),  // Dùng Carbon cho thời gian hiện tại
    //         ]);
    //     } else {
    //         // Nếu đã có bản ghi check-out, có thể cần log hoặc thông báo gì đó
    //         Log::info('Check-out already exists for yesterday.');
    //     }
    // }

    // /**
    //  * Handle check-in for the current day
    //  * 
    //  * @param App\Models\User $user
    //  * @param Illuminate\Http\Request $request
    //  * @return Illuminate\Http\RedirectResponse
    //  */
    // private function handleCheckIn($user, $request)
    // {
    //     $checkInTime = now()->timezone('Asia/Ho_Chi_Minh');
    //     $checkInTimeAllowed = Setting::where('key', 'check_in_time')->value('value');
    //     $validStatus = $checkInTime->format('H:i') < $checkInTimeAllowed;

    //     // Tạo bản ghi check-in mới
    //     $attendance = User_attendance::create([
    //         'time' => $checkInTime->timestamp, // UNIX timestamp
    //         'type' => 'in',
    //         'user_id' => $user->id,
    //         'status' => $validStatus,
    //         'justification' => $validStatus ? '' : $request->input('justification', ''),
    //         'created_by' => $user->id,
    //         'updated_by' => $user->id,
    //         'created_at' => now(), // Dùng Carbon cho thời gian hiện tại
    //         'updated_at' => now(), // Dùng Carbon cho thời gian hiện tại
    //     ]);

    //     // Debug: Log thời gian check-in để kiểm tra
    //     Log::info('Check-in created for user ' . $user->id . ' at ' . $checkInTime);

    //     // Nếu check-in không hợp lệ và chưa có lý do giải trình, yêu cầu giải trình
    //     if (!$validStatus && !$request->input('justification')) {
    //         return redirect()->back()->with('message', 'Check in không hợp lệ vào lúc ' . $checkInTime->format('H:i') . '. Vui lòng cung cấp lý do giải trình.')->withInput();
    //     }

    //     return redirect()->back()->with('message', 'Check in thành công lúc ' . $checkInTime->format('H:i') . '.');
    // }

    // public function checkIn(Request $request)
    // {
    //     $user = Auth::user();
    //     $today = Carbon::today();
    //     $yesterday = Carbon::yesterday();

    //     // Lấy giới hạn thời gian check-in từ bảng Setting
    //     $checkInLimit = Setting::where('key', 'check_in_time')->value('value');
    //     $checkInLimit = Carbon::createFromFormat('H:i', $checkInLimit);

    //     // Kiểm tra hôm qua có quên check-out không
    //     $missedCheckout = User_attendance::where('user_id', $user->id)
    //         ->whereDate('time', $yesterday)
    //         ->where('type', 'in')
    //         ->doesntHave('relatedCheckout')  // Bỏ dòng này, không cần nữa
    //         ->first();

    //     if ($missedCheckout) {
    //         // Thêm bản ghi check-out tự động cho hôm qua
    //         User_attendance::create([
    //             'time' => $yesterday->endOfDay(),
    //             'type' => 'out',
    //             'user_id' => $user->id,
    //             'status' => 0, // Đánh dấu cần giải trình
    //             'justification' => 'Tự động check-out do quên hôm trước.',
    //             'created_by' => $user->id,
    //             'updated_by' => $user->id,
    //         ]);
    //     }


    //     // Kiểm tra hôm nay đã check-in chưa
    //     $alreadyCheckedInToday = User_attendance::where('user_id', $user->id)
    //         ->whereDate('time', $today)
    //         ->where('type', 'in')
    //         ->exists();

    //     if ($alreadyCheckedInToday) {
    //         return redirect()->back()->with('error', 'Bạn đã check-in hôm nay rồi!');
    //     }

    //     // Kiểm tra xem thời gian check-in có vượt quá giới hạn không
    //     $status = now()->gt($checkInLimit) ? 0 : 1;

    //     // Thêm bản ghi check-in hôm nay
    //     User_attendance::create([
    //         'time' => now()->timezone('Asia/Ho_Chi_Minh'),
    //         'type' => 'in',
    //         'user_id' => $user->id,
    //         'status' => $status, // Nếu check-in sau giờ giới hạn thì status = 0
    //         'justification' => null,
    //         'created_by' => $user->id,
    //         'updated_by' => $user->id,
    //     ]);

    //     $message = $status === 0 ? 'Bạn đã check-in sau giờ giới hạn, trạng thái đã được đánh dấu là cần giải trình.' : 'Check-in thành công!';

    //     return redirect()->back()->with('message', $message);
    // }
    public function checkIn(Request $request)
    {
        $user = Auth::user();
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();
    
        // Lấy giới hạn thời gian check-in từ bảng Setting
        $checkInLimit = Setting::where('key', 'check_in_time')->value('value');
        $checkInLimit = Carbon::createFromFormat('H:i', $checkInLimit);
    
        // Kiểm tra hôm qua có quên check-out không
        $missedCheckout = User_attendance::where('user_id', $user->id)
            ->whereDate('time', $yesterday)
            ->where('type', 'in')
            ->doesntHave('relatedCheckout')
            ->first();
    
        if ($missedCheckout) {
            $justificationReason = $request->input('justification_reason');
            if ($justificationReason === 'Other') {
                $justificationReason = $request->input('other_justification');
            }
    
            $autoCheckoutTime = $yesterday->copy()->setTime(17, 0, 0);
    
            // Thêm bản ghi check-out tự động cho hôm qua
            User_attendance::create([
                'time' => $autoCheckoutTime,
                'type' => 'out',
                'user_id' => $user->id,
                'status' => 0, // Đánh dấu cần giải trình
                'justification' => $justificationReason,
                'created_by' => $user->id,
                'updated_by' => $user->id,
            ]);
        }
    
        // Kiểm tra hôm nay đã có check-in chưa
        $latestCheckIn = User_attendance::where('user_id', $user->id)
            ->whereDate('time', $today)
            ->where('type', 'in')
            ->latest()
            ->first();
    
        if ($latestCheckIn) {
            // Kiểm tra xem lần check-in cuối cùng đã có check-out tương ứng chưa
            $hasCheckoutAfterLastCheckIn = User_attendance::where('user_id', $user->id)
                ->where('type', 'out')
                ->where('time', '>', $latestCheckIn->time)
                ->exists();
    
            if (!$hasCheckoutAfterLastCheckIn) {
                return redirect()->back()->with('error', 'Bạn không thể check-in liên tiếp mà không có check-out!');
            }
        }
    
        // Kiểm tra xem thời gian check-in có vượt quá giới hạn không
        $status = now()->gt($checkInLimit) ? 0 : 1;
    
        // Thêm bản ghi check-in hôm nay
        User_attendance::create([
            'time' => now()->timezone('Asia/Ho_Chi_Minh'),
            'type' => 'in',
            'user_id' => $user->id,
            'status' => $status, // Nếu check-in sau giờ giới hạn thì status = 0
            'justification' => null,
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);
    
        $message = $status === 0 ? 'Bạn đã check-in sau giờ giới hạn, trạng thái đã được đánh dấu là cần giải trình.' : 'Check-in thành công!';
    
        return redirect()->back()->with('message', $message);
    }
    
    public function checkOut(Request $request)
    {
        $user = Auth::user();
        $today = Carbon::today();
        $checkOutLimit = Carbon::createFromFormat('H:i', Setting::where('key', 'check_out_time')->value('value'));

        $latestCheckIn = User_attendance::where('user_id', $user->id)
            ->whereDate('time', $today)
            ->where('type', 'in')
            ->latest()
            ->first();

        if (!$latestCheckIn) {
            return redirect()->back()->with('error', 'Bạn chưa check-in hôm nay!');
        }

        if (User_attendance::where('user_id', $user->id)
            ->whereDate('time', $today)
            ->where('type', 'out')
            ->where('time', '>', $latestCheckIn->time)
            ->exists()
        ) {
            return redirect()->back()->with('error', 'Bạn đã check-out rồi sau khi check-in!');
        }

        $status = now()->lt($checkOutLimit) ? 0 : 1;

        User_attendance::create([
            'time' => now()->timezone('Asia/Ho_Chi_Minh'),
            'type' => 'out',
            'user_id' => $user->id,
            'status' => $status,
            'justification' => null,
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);

        $message = $status === 0
            ? 'Bạn đã check-out trước giờ giới hạn, trạng thái đã được đánh dấu là cần giải trình.'
            : 'Check-out thành công!';

        return redirect()->back()->with('message', $message);
    }


   
    public function addJustification(Request $request, $attendanceId)
{
    // Tìm bản ghi attendance
    $attendance = User_attendance::findOrFail($attendanceId);
    $attendance->timestamps = false;

    // Xác định lý do giải trình (từ danh sách hoặc người dùng nhập)
    $justificationReason = $request->justification_reason == 'Other' 
        ? $request->other_justification 
        : $request->justification_reason;

    // Lưu lý do giải trình vào cơ sở dữ liệu
    $attendance->justification = $justificationReason;

    // Cập nhật trạng thái lý do giải trình
    $attendance->status = 0; // Đặt trạng thái là "chờ xét duyệt"
    $attendance->save();

    // Lấy thông tin người dùng từ bản ghi attendance
    $user = $attendance->user;

    // Gửi email đến admin với lý do giải trình
    $adminEmail = env('MAIL_FROM_ADDRESS');
    Mail::to($adminEmail)->send(new JustificationSubmitted($user, $justificationReason));

    // Trả về thông báo thành công và chuyển hướng
    return redirect()->route('attendance')->with('message', 'Giải trình đã được gửi thành công.');
}
    public function approveAttendance($id)
    {
        // Tìm bản ghi chấm công
        $attendance = User_attendance::findOrFail($id);

        // Kiểm tra nếu không có lý do giải trình
        if (!$attendance->justification) {
            return redirect()->back()->with('error', 'Không thể thay đổi trạng thái vì không có lý do giải trình.');
        }
        $checkin = $attendance->time;
        $checkout = $attendance->time;
        // Chỉ cập nhật trạng thái mà không thay đổi thời gian
        $attendance->timestamps = false; // Tắt cập nhật timestamps
        $attendance->status = 1; // Trạng thái "Đã chấp nhận lý do giải trình"
        $attendance->saveQuietly(); // Lưu trạng thái mới mà không cập nhật thời gian

        $attendance->time = $checkin;
        $attendance->time = $checkout;
        $attendance->save();

        $user = $attendance->user;

        Mail::to($user->email)->send(new AttendanceApproved($user));

        // Thông báo thành công
        return redirect()->back()->with('message', 'Trạng thái đã được thay đổi thành "Chấp nhận lý do giải trình".');
    }

    public function rejectAttendance($id)
    {
        // Tìm bản ghi chấm công
        $attendance = User_attendance::findOrFail($id);

        // Kiểm tra nếu không có lý do giải trình
        if (!$attendance->justification) {
            return redirect()->back()->with('error', 'Không thể thay đổi trạng thái vì không có lý do giải trình.');
        }

        // Lý do từ chối được nhập bởi admin (Giả sử bạn lấy lý do từ request)
        $rejectionReason = request('rejection_reason');

        // Nếu lý do từ chối là "Khác" thì lấy lý do từ trường custom_rejection_reason
        if ($rejectionReason === "Khác") {
            $rejectionReason = request('custom_rejection_reason');
        }

        // Kiểm tra xem có lý do từ chối không (không được để trống)
        if (!$rejectionReason) {
            return redirect()->back()->with('error', 'Vui lòng chọn lý do từ chối.');
        }

        // Cập nhật trạng thái "Từ chối lý do giải trình" mà không thay đổi thời gian
        $attendance->timestamps = false; // Tắt cập nhật timestamps
        $attendance->status = 3; // Trạng thái "Từ chối lý do giải trình"
        // $attendance->rejection_reason = $rejectionReason; // Lưu lý do từ chối
        $attendance->saveQuietly(); // Lưu trạng thái mới mà không cập nhật thời gian

        // Gửi email thông báo cho người dùng về lý do từ chối
        $user = $attendance->user;
        Mail::to($user->email)->send(new JustificationRejected($user, $attendance, $rejectionReason));

        // Thông báo thành công
        return redirect()->back()->with('message', 'Lý do giải trình đã bị từ chối và người dùng đã nhận được thông báo.');
    }
    public function manageInvalidAttendances()
    {
        // Lấy tất cả các bản ghi không hợp lệ và sắp xếp theo cái mới nhất lên đầu
        $invalidAttendances = User_attendance::where('status', false)
            ->orderBy('created_at', 'desc') // Sắp xếp theo thời gian mới nhất
            ->paginate(10); // Bạn có thể thay đổi số lượng trang

        return view('fe_attendances.attendance_management', compact('invalidAttendances'));
    }

    // Phương thức báo cáo hàng tháng
    public function monthlyReport(Request $request)
    {
        $userId = Auth::id();
        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);

        $employeeData = [
            'name' => User::find($userId)->name,
            'position' => User::find($userId)->position,
            'attendance' => [],
        ];

        $daysInMonth = Carbon::create($year, $month)->daysInMonth;

        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date = Carbon::create($year, $month, $day);
            $attendances = User_attendance::where('user_id', $userId)
                ->whereDate('time', $date)
                ->orderBy('time')
                ->get();

            if ($attendances->isNotEmpty()) {
                foreach ($attendances as $attendance) {
                    $type = $attendance->type;

                    if (!isset($employeeData['attendance'][$date->toDateString()])) {
                        $employeeData['attendance'][$date->toDateString()] = [
                            'checkIn' => null,
                            'checkOut' => null,
                            'hours' => 0,
                        ];
                    }

                    if ($type === 'in') {
                        $employeeData['attendance'][$date->toDateString()]['checkIn'] = $attendance->time;
                    } elseif ($type === 'out') {
                        $checkInTime = $employeeData['attendance'][$date->toDateString()]['checkIn'];
                        if ($checkInTime !== null) {
                            $checkOutTime = $attendance->time;
                            $hoursWorked = Carbon::parse($checkInTime)->diffInHours(Carbon::parse($checkOutTime));
                            $employeeData['attendance'][$date->toDateString()]['checkOut'] = $checkOutTime;
                            $employeeData['attendance'][$date->toDateString()]['hours'] = $hoursWorked;
                        }
                    }
                }
            }
        }

        return view('fe_attendances.monthly_report', compact('employeeData', 'month', 'year'));
    }

    public function getDepartments($parentId = 0)
    {
        $departments = Department::where('parent_id', $parentId)->where('status', 1)->get();

        // Đệ quy để lấy các phòng ban con
        foreach ($departments as $department) {
            $department->children = $this->getDepartments($department->id);
        }
    
        return $departments;
    }


    public function departmentReport(Request $request)
    {
        // Lấy danh sách phòng ban cấp 1 (parent_id = 0)
        $departments = Department::where('parent_id', 0)->get();

        // Lấy danh sách phòng ban đã chọn từ request và các phòng ban con
        $selectedDepartmentIds = $this->getDepartmentIds($request);

        // Lọc theo ngày
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $singleDate = $request->input('single_date', now()->toDateString());

        // Tạo query với điều kiện lọc theo phòng ban
        $query = User_attendance::with('user')
            ->whereHas('user', function ($query) use ($selectedDepartmentIds) {
                $query->whereIn('department_id', $selectedDepartmentIds);
            });

        // Áp dụng lọc ngày
        $attendanceDataQuery = $this->applyDateFilter($query, $startDate, $endDate, $singleDate);

        // Phân trang dữ liệu, chú ý đảm bảo không có lỗi trong query
        $attendanceData = $attendanceDataQuery->orderBy('time', 'asc')->paginate(10)->withQueryString(); // Chia thành 10 bản ghi mỗi trang và giữ lại tham số tìm kiếm

        // Logic tạo báo cáo hàng tháng
        $monthlyReport = $this->generateMonthlyReport($attendanceData);

        // Trả về view với phân trang dữ liệu
        return view('fe_attendances.department_report', compact(
            'attendanceData',
            'departments',
            'monthlyReport',
            'startDate',
            'endDate',
            'singleDate',
            'selectedDepartmentIds' // Truyền biến này vào view
        ));
    }

    // Hàm đệ quy để lấy tất cả các phòng ban con
    private function getDepartmentIds(Request $request)
    {
        // Lấy danh sách phòng ban đã chọn từ request
        $selectedDepartmentIds = $request->input('department_ids', []);

        // Kiểm tra xem người dùng có chọn "Tất cả" không (ID = 0)
        if (in_array(0, $selectedDepartmentIds)) {
            // Nếu chọn "Tất cả", lấy tất cả các phòng ban
            return Department::pluck('id')->toArray();
        } else {
            // Lấy tất cả các phòng ban con nhiều cấp
            return $this->getAllSubDepartmentIds($selectedDepartmentIds);
        }
    }

    // Hàm đệ quy để lấy tất cả các phòng ban con
    private function getAllSubDepartmentIds($departmentIds)
    {
        $allDepartmentIds = $departmentIds;
        $subDepartments = Department::whereIn('parent_id', $departmentIds)->pluck('id')->toArray();

        if (!empty($subDepartments)) {
            $allDepartmentIds = array_merge($allDepartmentIds, $this->getAllSubDepartmentIds($subDepartments));
        }

        return $allDepartmentIds;
    }

    // Hàm áp dụng bộ lọc ngày
    private function applyDateFilter($query, $startDate, $endDate, $singleDate)
    {
        if ($singleDate) {
            // Nếu chọn ngày duy nhất, lọc theo ngày đó
            $query->whereDate('time', Carbon::parse($singleDate)->format('Y-m-d'));
        } else {
            // Lọc theo khoảng ngày
            if ($startDate) {
                $query->whereDate('time', '>=', Carbon::parse($startDate)->format('Y-m-d'));
            }
            if ($endDate) {
                $query->whereDate('time', '<=', Carbon::parse($endDate)->format('Y-m-d'));
            }
        }
        return $query;
    }

    // thử 


    // Hàm tạo báo cáo hàng tháng
    private function generateMonthlyReport($attendanceData)
    {
        $monthlyReport = [];

        foreach ($attendanceData as $attendance) {
            $userId = $attendance->user_id;
            $date = $attendance->time->format('Y-m-d');
            $type = $attendance->type;

            if (!isset($monthlyReport[$userId])) {
                $monthlyReport[$userId] = [
                    'name' => $attendance->user->name,
                    'position' => $attendance->user->position ?? 'N/A',
                    'dailyHours' => [],
                    'totalHours' => 0,
                ];
            }

            if (!isset($monthlyReport[$userId]['dailyHours'][$date])) {
                $monthlyReport[$userId]['dailyHours'][$date] = [];
            }

            if ($type === 'in') {
                $monthlyReport[$userId]['dailyHours'][$date][] = [
                    'checkIn' => $attendance->time,
                    'checkOut' => null,
                    'hours' => 0,
                ];
            } elseif ($type === 'out') {
                $lastIndex = count($monthlyReport[$userId]['dailyHours'][$date]) - 1;
                if ($lastIndex >= 0 && !$monthlyReport[$userId]['dailyHours'][$date][$lastIndex]['checkOut']) {
                    $checkInTime = Carbon::parse($monthlyReport[$userId]['dailyHours'][$date][$lastIndex]['checkIn']);
                    $checkOutTime = Carbon::parse($attendance->time);

                    $hoursWorked = $checkInTime->diffInHours($checkOutTime);
                    $monthlyReport[$userId]['dailyHours'][$date][$lastIndex]['checkOut'] = $attendance->time;
                    $monthlyReport[$userId]['dailyHours'][$date][$lastIndex]['hours'] = $hoursWorked;

                    $monthlyReport[$userId]['totalHours'] += $hoursWorked;
                }
            }
        }

        // Tính tổng giờ làm việc cho mỗi người
        foreach ($monthlyReport as &$report) {
            foreach ($report['dailyHours'] as $dateHours) {
                foreach ($dateHours as $day) {
                    $report['totalHours'] += $day['hours'];
                }
            }
            $report['monthlyTotalHours'] = $report['totalHours'];
        }

        return $monthlyReport;
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validate the input data
        $validatedData = $request->validate([
            'user_id' => 'required|exists:users,id',
            'time' => 'required|date_format:H:i',
            'type' => 'required|in:in,out',
            'status' => 'required|boolean',
            'justification' => 'nullable|string',
        ]);

        // Create a new attendance record
        $attendance = User_attendance::create([
            'user_id' => $validatedData['user_id'],
            'time' => strtotime($validatedData['time']),
            'type' => $validatedData['type'],
            'status' => false, // Mặc định là không hợp lệ
            'justification' => $validatedData['justification'],
            'created_by' => auth()->id(),
        ]);

        // Check the validity of the attendance record after creating
        $attendance->checkValidity();

        return redirect()->back()->with('success', 'Đã ghi log thành công.');
    }

    // Phương thức lọc theo ngày
    protected function filterByDate($query, $startDate, $endDate, $singleDate = null)
    {
        if ($singleDate) {
            // Nếu có ngày đơn lẻ, lọc theo ngày đó
            $query->whereDate('time', $singleDate);
        } else {
            // Nếu có khoảng thời gian, lọc theo khoảng thời gian
            if ($startDate) {
                $query->where('time', '>=', $startDate);
            }
            if ($endDate) {
                $query->where('time', '<=', $endDate);
            }
        }
        return $query;
    }
}
