

<?php

use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\User_attendanceController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\TestExcelController;
use App\Exports\UsersExport;
use App\Http\Controllers\Caculate_Salary;
use App\Http\Controllers\ChartController;
use App\Http\Controllers\Leave_requestController;
use App\Http\Controllers\LeaveConfigController;
use App\Http\Controllers\Salary_caculate;
use App\Http\Controllers\SalaryController;
use App\Http\Controllers\SettingController;
use App\Models\Salary;
use App\Models\User;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Redirect root to login
Route::get('/', function () {
    return redirect()->route('login');
});

// Authentication routes
Route::middleware('web')->group(function () {
    Route::get('/login', [LoginController::class, 'login'])->name('login');
    Route::post('/login', [LoginController::class, 'loginPost']);
    Route::get('/logout', [LoginController::class, 'logout'])->name('logout');

    // Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])
    //     ->name('password.request');
    // Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])
    //     ->name('password.email');
});
Route::post('users/import/', [UserController::class, 'importPost'])->name('users.import');
Route::get('/export-users', [UserController::class, 'export'])->name('users.export');
Route::get('/export-template', [UserController::class, 'exportTemplate'])->name('export.template');

// Department routes (role = 1)
Route::middleware('auth')->group(function () {
    Route::get('/departments', [DepartmentController::class, 'allDepartment'])->name('departments');
    Route::get('/departments/create', [DepartmentController::class, 'create'])->name('departments.create');
    Route::post('/departments', [DepartmentController::class, 'store'])->name('departments.store');
    Route::get('/departments/{id}/members', [DepartmentController::class, 'showMembers'])->name('departments.show');
    Route::patch('/departments/{id}/update-status', [DepartmentController::class, 'updateStatus'])
        ->name('departments.updateStatus');
    Route::get('/departments/{id}/sub-departments', [DepartmentController::class, 'showSubDepartments'])->name('departments.subDepartments');
    Route::get('/departments/search', [DepartmentController::class, 'search'])->name('departments.search');
    Route::get('/departments/tree', [DepartmentController::class, 'getDepartments'])->name('departments.tree');
    Route::get('/fetch-sub-departments/{parentId}', [DepartmentController::class, 'fetchSubDepartments']);

});

// User management routes
Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/users', [UserController::class, 'index'])->name('users');
    Route::get('/users/search', [UserController::class, 'search'])->name('users.search');
    Route::post('/users/destroy', [UserController::class, 'destroy'])->name('users.destroy');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/users/store', [UserController::class, 'store'])->name('users.store');
    Route::post('/users/{id}/quick-update', [UserController::class, 'update'])->name('users.quickUpdate');
    Route::get('/users/{id}', [UserController::class, 'show'])->name('users.show');
    Route::get('/users/{id}/detail', [UserController::class, 'showDetail'])->name('users.detail');
    Route::get('/users/{id}/edit', [UserController::class, 'editUser'])->name('users.edit');
    Route::post('/users/{id}/update-detail', [UserController::class, 'updateUser'])->name('users.updatedetail');
    Route::get('/reminder-settings', [UserController::class, 'showReminderForm'])->name('reminder.settings');
    Route::post('/reminder-settings', [UserController::class, 'saveReminderSettings'])->name('reminder.save');
    Route::get('/setting/edit',[SettingController::class, 'edit'])->name('setting.edit');
    Route::post('/setting/update',[SettingController::class, 'updateCheckInOutTime'])->name('setting.update');
    Route::post('/settings/update-reminder-time-checkout', [SettingController::class, 'updateReminderTimeCheckout'])->name('setting.updateReminderTimeCheckout');
    Route::get('/departments/{parentId}/children', [UserController::class, 'fetchSubDepartments']);
    Route::get('/salaries/{departmentId}', [UserController::class, 'getSalaries']);


});

// Attendance routes (role = 2)
Route::middleware('auth')->group(function () {
    Route::get('/attendance', [User_attendanceController::class, 'index'])->name('attendance');
    Route::post('/check-in', [User_attendanceController::class, 'checkIn'])->name('attendance.checkin');
    Route::post('/check-out', [User_attendanceController::class, 'checkOut'])->name('attendance.checkout');
    Route::get('/attendance/monthly-report', [User_attendanceController::class, 'monthlyReport'])
        ->name('attendance.monthlyReport');
    Route::get('/attendance/allUser', [User_attendanceController::class, 'reportAllUsers'])->name('attendance.all');
    Route::get('/attendance/department-report', [User_attendanceController::class, 'departmentReport'])->name('department.report');
    Route::get('/attendance/search', [User_attendanceController::class, 'searchByDepartment'])->name('attendance.search');
    Route::post('/attendance/{id}/justification', [User_attendanceController::class, 'addJustification'])->name('attendance.addJustification');
    Route::get('/admin/manage-attendances', [User_attendanceController::class, 'manageInvalidAttendances'])->name('admin.manageAttendances');
    Route::post('/admin/approve-attendance/{id}', [User_attendanceController::class, 'approveAttendance'])->name('admin.approveAttendance');
    Route::post('/admin/reject-attendance/{id}', [User_attendanceController::class, 'rejectAttendance'])->name('admin.rejectAttendance');
    Route::get('leave',[LeaveConfigController::class, 'show'])->name('leave');
    Route::post('/leave/store',[LeaveConfigController::class, 'store'])->name('leave.store');
});

// Route::get('/users/export', function () {
//     dd('Export route is called'); // Kiểm tra xem route có được gọi hay không
//     return Excel::download(new UsersExport, 'users.xlsx');
// });

Route::middleware('auth')->group(function () {
   Route::get('/salary',[SalaryController::class, 'index'])->name('salary');
   Route::get('/salary/create',[SalaryController::class, 'create'])->name('salary.create');
   Route::post('/salary',[SalaryController::class, 'store'])->name('salary.store');
   Route::get('/salary/{id}',[SalaryController::class, 'show'])->name('salary.show');
   Route::get('/salary/{id}/edit',[SalaryController::class, 'edit'])->name('salary.edit');
   Route::put('/salary/{id}/update',[SalaryController::class, 'update'])->name('salary.update');
});

// 

Route::middleware('auth')->group(function () {
    Route::get('/salary_caculate/{userId}', [Caculate_Salary::class, 'getSalaryInfo'])->name('salary_user');
});

Route::middleware('auth')->group(function () {
    Route::get('/salary_cacu', [Salary_caculate::class, 'showCaculate'])->name('salary_caculate');
    Route::get('/salary-calculate', [Salary_caculate::class, 'index'])->name('salary.calculate');
    Route::post('/salary_cacu/save', [Salary_caculate::class, 'saveSalary'])->name('salary.save'); // Lưu thông tin tính lương
    Route::get('get-employees/{departmentId}', [Salary_caculate::class, 'getEmployeesByDepartment']);
    Route::post('/salary/save', [Salary_caculate::class, 'saveSalary'])->name('salary.save');
    Route::post('/caculateAll', [Salary_caculate::class, 'calculateSalariesForAllEmployees'])->name('caculate.all');
    Route::post('save_salary', [Salary_caculate::class, 'saveAllSalaries'])->name('save.salary');
    Route::post('/setting/updatecalculate',[SettingController::class, 'updateSalaryCalculationTime'])->name('setting.updatecalculate');
});

Route::middleware('auth')->group(function () {
    Route::get('leave',[LeaveConfigController::class, 'show'])->name('leave');
    Route::post('/leave/store',[LeaveConfigController::class, 'store'])->name('leave.store');
});

Route::middleware('auth')->group(function () {
    Route::get('/leave_application',[Leave_requestController::class, 'index'])->name('leave_admin');
    Route::post('leave_request/accept/{id}',[Leave_requestController::class,'acceptLeaveRequest'])-> name('leave_accept');
    Route::post('/leave-request/reject/{id}', [Leave_requestController::class, 'reject'])->name('leave_reject');

    // Route::post('/leave/store',[LeaveConfigController::class, 'store'])->name('leave.store');
});

Route::middleware('auth')->group(function () {
    Route::get('/api/userDepChart',[ChartController::class, 'userDepChart'])->name('userDepChart');
});
Route::get('/chart', [App\Http\Controllers\ChartController::class, 'index']);