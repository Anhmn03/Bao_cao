<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function edit()
    {
        $checkInTime = Setting::where('key', 'check_in_time')->value('value');
        $checkOutTime = Setting::where('key', 'check_out_time')->value('value');

        return view('fe_attendances/setting', compact('checkInTime', 'checkOutTime'));
    }

    public function update(Request $request)
    {
        Setting::where('key', 'check_in_time')->update(['value' => $request->input('check_in_time')]);
        Setting::where('key', 'check_out_time')->update(['value' => $request->input('check_out_time')]);

        return redirect()->back()->with('message', 'Giờ check-in và check-out đã được cập nhật!');
    }}
