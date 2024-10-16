<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function login(){
        return view('fe/login');
    }
    public function loginPost(Request $request){
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'password' => 'required'
        ]);
       $data = $request->only('email', 'password');
       if(auth()->attempt($data)){
        return redirect()->route('departments');
       }
        return redirect()->back()->with('error', 'Đăng nhập thể vui lý');
    }
    public function logout(){
        auth::logout();
        return redirect()->route('index')->with('success', 'Đăng xuất này');
    }
}
