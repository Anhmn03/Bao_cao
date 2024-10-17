<?php

use App\Http\Controllers\DepartmentController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\UserController;
use App\Models\User;

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

Route::get('/', [HomeController::class, 'index'])->name('index');

Route::middleware('web')->group(function () {
    Route::get('/login', [LoginController::class, 'login'])->name('login');
    Route::post('/login', [LoginController::class, 'loginPost']);
    Route::get('/logout', [LoginController::class, 'logout'])->name('logout');
    // Route::get('/register', [LoginController::class, 'register'])->name('register');

});

// department
Route::get('/departments', [DepartmentController::class, 'allDepartment'])->name('departments');
Route::get('/departments/create', [DepartmentController::class, 'create'])->name('departments.create');
Route::post('/departments', [DepartmentController::class, 'store'])->name('departments.store');
Route::get('/departments/{id}/members', [DepartmentController::class, 'showMembers'])->name('departments.show');


// user 
Route::group(['middleware' => ['web']], function () {
Route::get('/users', [UserController::class, 'index'])->name('users');
Route::get('/users/search', [UserController::class, 'search'])->name('users.search');

Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');
// Route để hiển thị form thêm người dùng
Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
// Route để lưu người dùng mới
Route::post('/users/store', [UserController::class, 'store'])->name('users.store');
Route::post('/users/update/{id}', [UserController::class, 'update'])->name('users.update');

});

// import dữ liệu 
Route::post('users/import',[UserController::class,'importPost'])->name('users.import');

//export dữ liệu 
Route::get('users/export', [UserController::class, 'export'])->name('users.export');