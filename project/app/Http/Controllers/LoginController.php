<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function login(){
        return view('fe_admin/login');
    }
    public function loginPost(Request $request)
{
    // Validate email và password
    $request->validate([
        'email' => 'required|email|exists:users,email',
        'password' => 'required',
    ]);

    // Lấy thông tin từ request
    $credentials = $request->only('email', 'password');

    // Lấy thông tin người dùng từ email để kiểm tra status
    $user = User::where('email', $credentials['email'])->first();

    // Kiểm tra nếu người dùng đã bị vô hiệu hóa (status = 0)
    if ($user->status == 0) {
        return redirect()->back()->with('error', 'Tài khoản của bạn đã bị vô hiệu hóa. Vui lòng liên hệ quản trị viên.');
    }

    // Thực hiện đăng nhập nếu thông tin hợp lệ và không bị vô hiệu hóa
    if (auth()->attempt($credentials)) {
        $user = auth()->user(); // Lấy thông tin người dùng đã đăng nhập

        // Chuyển hướng dựa trên vai trò của người dùng
        if ($user->role == 1) {
            return redirect()->route('departments')->with('success', 'Đăng nhập thành công');
        } elseif ($user->role == 2) {
            return redirect()->route('attendance')->with('success', 'Đăng nhập thành công');
        }

        // Nếu vai trò không xác định
        return redirect()->back()->with('error', 'Vai trò không hợp lệ.');
    }

    // Nếu đăng nhập thất bại
    return redirect()->back()->with('error', 'Email hoặc mật khẩu không đúng.');
}

     public function logout(){
        auth::logout();
        return redirect()->route('login')->with('success', 'Đăng xuất này');
    }

   
}
