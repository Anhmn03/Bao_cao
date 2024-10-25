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
    public function index()
    {
        // Lấy lịch sử check in/check out của người dùng
        $attendances = User_attendance::where('user_id', Auth::id())->get();
        return view('fe_attendances/users_attendance', compact('attendances'));
    }


    public function checkIn()
{
    $user = Auth::user();

    // Kiểm tra xem người dùng đã Check In chưa
    $latestAttendance = User_attendance::where('user_id', $user->id)
                                        ->orderBy('time', 'desc')
                                        ->first();

    // Nếu không có lịch sử Check In hoặc có lịch sử Check Out gần đây thì cho phép Check In
    if (!$latestAttendance || $latestAttendance->type === 'out') {
        User_attendance::create([
            'time' => now()->timezone('Asia/Ho_Chi_Minh'), // Đặt múi giờ Việt Nam
            'type' => 'in',
            'user_id' => $user->id,
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);

        return redirect()->back()->with('message', 'Check in thành công.');
    } else {
        return redirect()->back()->with('message', 'Bạn đã Check In rồi. Vui lòng Check Out trước khi Check In lại!');
    }
}

public function checkOut()
{
    $user = Auth::user();

    // Kiểm tra xem người dùng đã Check In chưa (tuỳ chọn)
    $latestAttendance = User_attendance::where('user_id', $user->id)
                                        ->orderBy('time', 'desc')
                                        ->first();

    // Thực hiện Check Out chỉ khi có lịch sử Check In
    if ($latestAttendance && $latestAttendance->type === 'in') {
        User_attendance::create([
            'time' => now()->timezone('Asia/Ho_Chi_Minh'), // Đặt múi giờ Việt Nam
            'type' => 'out',
            'user_id' => $user->id,
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);

        return redirect()->back()->with('message', 'Check out thành công.');
    } else {
        return redirect()->back()->with('message', 'Bạn chưa Check In, không thể Check Out!');
    }
}


public function monthlyReport($month = null, $year = null)
{
    $userId = Auth::id();
    $month = $month ?? now()->month;
    $year = $year ?? now()->year;

    $attendanceData = []; 
    $user = User::find($userId);

    $employeeData = [
        'name' => $user->name,
        'position' => $user->position,
        'attendance' => [],
    ];

    for ($day = 1; $day <= Carbon::create($year, $month)->daysInMonth; $day++) {
        $date = Carbon::create($year, $month, $day);
        $attendances = User_attendance::where('user_id', $userId)
            ->whereDate('time', $date)
            ->orderBy('time')
            ->get();

        $totalMinutes = 0;
        $checkInTime = null;

        foreach ($attendances as $attendance) {
            if ($attendance->type === 'in') {
                $checkInTime = Carbon::parse($attendance->time);
            } elseif ($attendance->type === 'out' && $checkInTime) {
                $checkOutTime = Carbon::parse($attendance->time);
                $totalMinutes += $checkInTime->diffInMinutes($checkOutTime);
                $checkInTime = null; // Reset để xử lý lần check-in tiếp theo
            }
        }

        $totalHours = intdiv($totalMinutes, 60);

        $employeeData['attendance'][$day] = $totalHours;
    }

    $attendanceData = $employeeData;

    return view('fe_attendances.monthly_report', compact('attendanceData', 'month', 'year'));
}
public function departmentReport(Request $request)
{
    $departments = Department::where('parent_id', 0)->get();
    $selectedDepartmentIds = $request->input('department_ids', []);
    $subDepartments = $selectedDepartmentIds 
        ? Department::whereIn('parent_id', $selectedDepartmentIds)->get() 
        : [];

    $selectedSubDepartment = $request->input('sub_department_id', '');

    $attendanceData = collect();

    if ($selectedDepartmentIds || $selectedSubDepartment) {
        $attendanceData = User_attendance::with('user')
            ->whereHas('user', function ($query) use ($selectedDepartmentIds, $selectedSubDepartment) {
                if ($selectedDepartmentIds) {
                    $query->whereIn('department_id', $selectedDepartmentIds);
                }
                if ($selectedSubDepartment) {
                    $query->orWhere('department_id', $selectedSubDepartment);
                }
            })
            ->orderBy('time', 'asc')
            ->get();
    } else {
        $attendanceData = User_attendance::with('user')->orderBy('time', 'asc')->get();
    }

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
        $report['monthlyTotalHours'] = array_sum(array_column($report['dailyHours'], 'hours'));
    }

    return view('fe_attendances.department_report', compact(
        'attendanceData', 
        'departments', 
        'subDepartments', 
        'selectedDepartmentIds', 
        'selectedSubDepartment', 
        'monthlyReport'
    ));
}
    // Báo cáo tất cả nhân viên với phân quyền và phân trang
    public function reportAllUsers()
    {
        // Kiểm tra quyền truy cập (chỉ admin mới được phép)
        if (Auth::user()->role == '2') {
            return redirect()->route('login')->with('error', 'Bạn không có quyền truy cập vào trang này.');
        }

        // Phân trang với 10 nhân viên mỗi trang
        $users = User::paginate(10);

        // Dữ liệu báo cáo cho từng nhân viên
        $reportData = $users->map(function ($user) {
            $attendances = User_attendance::where('user_id', $user->id)
                ->whereMonth('time', now()->month)
                ->orderBy('time')
                ->get();

            $dailyWork = $attendances->groupBy(function ($attendance) {
                return $attendance->time->format('Y-m-d');
            })->map(function ($records) {
                $checkIn = $records->where('type', 'in')->first();
                $checkOut = $records->where('type', 'out')->first();

                return $checkIn && $checkOut
                    ? $checkOut->time->diffInHours($checkIn->time)
                    : 0;
            });

            $daysWithFullHours = $dailyWork->filter(fn($hours) => $hours >= 8)->count();

            return [
                'name' => $user->name,
                'totalDays' => $dailyWork->count(),
                'daysWithFullHours' => $daysWithFullHours,
            ];
        });

        // Trả về view với dữ liệu và phân trang
        return view('fe_attendances/all_users_report', [
            'reportData' => $reportData,
            'users' => $users, // Dùng cho phân trang
        ]);
    }
}