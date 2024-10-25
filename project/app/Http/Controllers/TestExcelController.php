<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class TestExcelController extends Controller
{
    public function index()
    {
        if (class_exists(Excel::class)) {
            return "<h1>Import Maatwebsite Excel thành công!</h1>";
        } else {
            return "<h1>Import thất bại. Kiểm tra lại cấu hình!</h1>";
        }
    }

}
