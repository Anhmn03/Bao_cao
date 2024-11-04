<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\User;
use App\Models\User_attendance;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class User_attendanceController extends Controller
{
    // Phương thức hiển thị lịch sử chấm công của người dùng
    public function index()
    {
        $attendances = User_attendance::where('user_id', Auth::id())->get();
        return view('fe_attendances.users_attendance', compact('attendances'));
    }

    // Phương thức check in
    public function checkIn()
    {
        $user = Auth::user();
        $latestAttendance = User_attendance::where('user_id', $user->id)
            ->orderBy('time', 'desc')
            ->first();

        if (!$latestAttendance || $latestAttendance->type === 'out') {
            $attendance = User_attendance::create([
                'time' => now()->timezone('Asia/Ho_Chi_Minh'),
                'type' => 'in',
                'user_id' => $user->id,
                'status' => true,
                'justification' => '',
                'created_by' => $user->id,
                'updated_by' => $user->id,
            ]);

            // Check validity
            $attendance->checkValidity();

            return redirect()->back()->with('message', 'Check in thành công.');
        } else {
            return redirect()->back()->with('message', 'Bạn đã Check In rồi. Vui lòng Check Out trước khi Check In lại!');
        }
    }

    public function checkOut()
    {
        $user = Auth::user();
        $latestAttendance = User_attendance::where('user_id', $user->id)
            ->orderBy('time', 'desc')
            ->first();

        if ($latestAttendance && $latestAttendance->type === 'in') {
            $attendance = User_attendance::create([
                'time' => now()->timezone('Asia/Ho_Chi_Minh'),
                'type' => 'out',
                'user_id' => $user->id,
                'status' => '1',
                'justification' => '',
                'created_by' => $user->id,
                'updated_by' => $user->id,
            ]);

            // Check validity
            $attendance->checkValidity();

            return redirect()->back()->with('message', 'Check out thành công.');
        } else {
            return redirect()->back()->with('message', 'Bạn chưa Check In, không thể Check Out!');
        }
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