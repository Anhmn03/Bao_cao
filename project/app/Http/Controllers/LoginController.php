<?php

namespace App\Http\Controllers;

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
            'password' => 'required'
        ]);

        // Lấy thông tin từ request
        $credentials = $request->only('email', 'password');

        // Nếu đăng nhập thành công
        if (auth()->attempt($credentials)) {
            $user = auth()->user(); // Lấy thông tin người dùng đã đăng nhập

            // Kiểm tra role của người dùng và chuyển hướng
            if ($user->role == 1) {
                return redirect()->route('departments')->with('success', 'Đăng nhập thành công');
            } elseif ($user->role == 2) {
                return redirect()->route('attendance')->with('success', 'Đăng nhập thành công');
            }

            // Nếu role không xác định
            return redirect()->back()->with('error', 'Vai trò không hợp lệ');
        }

        // Nếu đăng nhập thất bại
        return redirect()->back()->with('error', 'Đăng nhập không thành công');
    }   
     public function logout(){
        auth::logout();
        return redirect()->route('login')->with('success', 'Đăng xuất này');
    }

   
}
