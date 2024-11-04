<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class UsersExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        $sheets = [];
        $chunkSize = 5; // Kích thước mỗi chunk là 15 bản ghi
        $totalUsers = User::count(); // Đếm tổng số bản ghi
        $sheetsCount = ceil($totalUsers / $chunkSize); // Tính số lượng sheet

        for ($i = 0; $i < $sheetsCount; $i++) {
            $sheets[] = new UserSheet($i * $chunkSize, $chunkSize);
        }

        return $sheets;
    }
}

class UserSheet implements FromCollection, WithHeadings
{
    protected $offset;
    protected $limit;

    public function __construct($offset, $limit)
    {
        $this->offset = $offset;
        $this->limit = $limit;
    }

/*************  ✨ Codeium Command ⭐  *************/
/******  096c995e-4e18-477d-8c36-7450f3c78deb  *******/
    public function collection()
    {
        return User::with('department')
            ->offset($this->offset)
            ->limit($this->limit)
            ->get()
            ->map(function ($user) {
                return [
                    'Họ và tên' => $user->name,
                    'Email' => $user->email,
                    'Số điện thoại' => $user->phone_number,
                    'Chức vụ' => $user->position,
                    'Phòng ban' => $user->department ? $user->department->name : 'N/A',
                    'Trạng thái' => $user->status ? 'Hoạt động' : 'Không hoạt động',
                ];
            });
    }

    public function headings(): array
    {
        return [
            'Họ và tên',
            'Email',
            'Số điện thoại',
            'Chức vụ',
            'Phòng ban',
            'Trạng thái',
        ];
    }
}
