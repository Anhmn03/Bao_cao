<?php

namespace App\Http\Controllers;

use App\Models\CaculateSalary;
use App\Models\User;
use Illuminate\Http\Request;

class CaculateSalaryController extends Controller
{
    public function index()
    {
        // Lấy tất cả nhân viên
        $employees = User::with('salary')->get();
        return view('fe_caculate.caculate_salary(1)', compact('employees'));
    }

}
