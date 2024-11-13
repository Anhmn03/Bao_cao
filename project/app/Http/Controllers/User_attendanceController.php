<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Setting;
use App\Models\User;
use App\Models\User_attendance;
use Carbon\Carbon;
use Hamcrest\Core\Set;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
    
        $attendances = $query->orderBy('created_at', 'desc') // Sắp xếp theo thời gian mới nhất
            ->paginate(5); // Phân trang, mỗi trang có 5 bản ghi
    
        return view('fe_attendances.users_attendance', compact('attendances'));
    }
    

    // Phương thức check in
   

    public function checkIn(Request $request)
    {
        $user = Auth::user();
    
        // Lấy bản ghi check in mới nhất
        $latestAttendance = User_attendance::where('user_id', $user->id)
            ->orderBy('time', 'desc')
            ->first();
    
        // Kiểm tra trạng thái của bản ghi check in gần nhất
        if (!$latestAttendance || ($latestAttendance->type === 'out' || $latestAttendance->status)) {
            // Nếu không có bản ghi check in, hoặc bản ghi gần nhất là check out hoặc đã được chấp nhận
    
            // Kiểm tra xem hôm qua có quên check out không
            $yesterday = Carbon::yesterday()->toDateString();
            $yesterdayAttendance = User_attendance::where('user_id', $user->id)
                ->whereDate('time', $yesterday)
                ->where('type', 'in')
                ->whereNull('justification')
                ->first();
    
            // Nếu có bản ghi check in nhưng chưa có lý do giải trình, yêu cầu người dùng giải trình lý do
            if ($yesterdayAttendance) {
                return redirect()->back()->with('message', 'Bạn chưa checkout hôm qua, vui lòng giải trình lý do.')->withInput();
            }
    
            // Nếu không có vấn đề gì, thực hiện check-in
            $checkInTimeAllowed = Setting::where('key', 'check_in_time')->value('value');
            $checkInTime = now()->timezone('Asia/Ho_Chi_Minh');
            $validStatus = $checkInTime->format('H:i') < $checkInTimeAllowed;
    
            // Lưu bản ghi check-in
            $attendance = User_attendance::create([
                'time' => $checkInTime,
                'type' => 'in',
                'user_id' => $user->id,
                'status' => $validStatus,
                'justification' => $validStatus ? '' : $request->input('justification', ''),
                'created_by' => $user->id,
                'updated_by' => $user->id,
            ]);
    
            // Nếu không hợp lệ và chưa có lý do giải trình, yêu cầu nhập lý do
            if (!$validStatus && !$request->input('justification')) {
                return redirect()->back()->with('message', 'Check in không hợp lệ vào lúc ' . $checkInTime->format('H:i') . '. Vui lòng cung cấp lý do giải trình.')->withInput();
            }
    
            return redirect()->back()->with('message', 'Check in thành công lúc ' . $checkInTime->format('H:i') . '.');
        } else {
            return redirect()->back()->with('message', 'Bạn đã Check In rồi. Vui lòng Check Out trước khi Check In lại!');
        }
    }
    


    public function checkOut(Request $request)
    {
        $user = Auth::user();
        $latestAttendance = User_attendance::where('user_id', $user->id)
            ->orderBy('time', 'desc')
            ->first();
    
        if ($latestAttendance && $latestAttendance->type === 'in') {
            // Lấy thời gian check out
            $checkOutTimeAllowed = Setting::where('key', 'check_out_time')->value('value');
            $checkOutTime = now()->timezone('Asia/Ho_Chi_Minh');
            $validStatus = $checkOutTime->format('H:i') >= $checkOutTimeAllowed;
    
            // Lưu bản ghi check-out bất kể hợp lệ hay không
            $attendance = User_attendance::create([
                'time' => $checkOutTime,
                'type' => 'out',
                'user_id' => $user->id,
                'status' => $validStatus,
                'justification' => $request->input('justification', ''), // Lưu lý do giải trình nếu có
                'created_by' => $user->id,
                'updated_by' => $user->id,
            ]);
    
            // Nếu không hợp lệ và chưa có lý do giải trình, yêu cầu nhập lý do
            if (!$validStatus && !$request->input('justification')) {
                return redirect()->back()->with('message', 'Check out không hợp lệ vào lúc ' . $checkOutTime->format('H:i') . '. Vui lòng cung cấp lý do giải trình.')->withInput();
            }
    
            // Thông báo cho người dùng về thời gian check-out
            return redirect()->back()->with('message', $validStatus ? 'Check out thành công lúc ' . $checkOutTime->format('H:i') . '.' : 'Check out không hợp lệ nhưng đã được ghi nhận với lý do giải trình.');
        } else {
            return redirect()->back()->with('message', 'Bạn chưa Check In, không thể Check Out!');
        }
    }
    
    public function addJustification(Request $request, $attendanceId)
{
    $attendance = User_attendance::findOrFail($attendanceId);
    
    // Lưu lý do giải trình
    if ($request->justification_reason == 'Other') {
        $attendance->justification = $request->other_justification;
    } else {
        $attendance->justification = $request->justification_reason;
    }
    
    // Cập nhật trạng thái lý do giải trình
    $attendance->status = 0; // Lý do được chấp nhận
    $attendance->save();
    
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
    
    // Chỉ thay đổi trạng thái khi có lý do giải trình
    $attendance->status = 1; // Trạng thái "Đã chấp nhận lý do giải trình"
    $attendance->save();
    
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
    
    // Chỉ thay đổi trạng thái khi có lý do giải trình
    $attendance->status = 3; // Trạng thái "Từ chối lý do giải trình"
    $attendance->save();
    
    // Thông báo thành công
    return redirect()->back()->with('message', 'Lý do giải trình đã bị từ chối ');
}

    public function manageInvalidAttendances() {
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



    // Phương thức báo cáo cho phòng ban
    public function departmentReport(Request $request)
    {
        $departments = Department::where('parent_id', 0)->get();
        $selectedDepartmentIds = $request->input('department_ids', []);
        $selectedSubDepartment = $request->input('sub_department_id', '');

        $subDepartments = $selectedDepartmentIds
            ? Department::whereIn('parent_id', $selectedDepartmentIds)->get()
            : [];

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $singleDate = $request->input('single_date');

        $query = User_attendance::with('user')
            ->whereHas('user', function ($query) use ($selectedDepartmentIds, $selectedSubDepartment) {
                if ($selectedDepartmentIds) {
                    $query->whereIn('department_id', $selectedDepartmentIds);
                }
                if ($selectedSubDepartment) {
                    $query->orWhere('department_id', $selectedSubDepartment);
                }
            });

        // Áp dụng lọc ngày và phân trang
        $attendanceData = $this->filterByDate($query, $startDate, $endDate, $singleDate)
            ->orderBy('time', 'asc')
            ->paginate(10); // Chia thành 10 bản ghi mỗi trang
        // Rest of the monthly report logic
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

        foreach ($monthlyReport as &$report) {
            foreach ($report['dailyHours'] as $dateHours) {
                foreach ($dateHours as $day) {
                    $report['totalHours'] += $day['hours'];
                }
            }
            $report['monthlyTotalHours'] = $report['totalHours'];
        }

        return view('fe_attendances.department_report', compact(
            'attendanceData',
            'departments',
            'subDepartments',
            'selectedDepartmentIds',
            'selectedSubDepartment',
            'monthlyReport',
            'startDate',
            'endDate',
            'singleDate'
        ));
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