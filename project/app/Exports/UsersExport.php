<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class UsersExport implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return User::with('department') // Load thông tin phòng ban
        ->get()
        ->map(function ($user) {
            return [
                'name' => $user->name,
                'email' => $user->email,
                'phone_number' => $user->phone_number,
                'position' => $user->position,
                'department' => $user->department ? $user->department->name : 'N/A', // Hiển thị tên phòng ban
                'status' => $user->status ? 'Hoạt động' : 'Không hoạt động',
                
            ];
        });
}

/**
 * Cung cấp tiêu đề cho từng cột trong file Excel.
 */
public function headings(): array
{
    return [
        'Họ và tên',
        'Email',
        'Số điện thoại',
        'Chức vụ',
        'Phòng ban', // Hiển thị tên phòng ban
        'Trạng thái',
        
        
    ];
}
}


