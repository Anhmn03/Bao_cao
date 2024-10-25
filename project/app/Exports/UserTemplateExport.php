<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
class UserTemplateExport implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        // Trả về một collection rỗng, chỉ để tạo file mẫu.
        return collect([]);
    }

    /**
     * Đặt tiêu đề cho file mẫu.
     */
    public function headings(): array
    {
        return [
            'Tên',           // Tên của người dùng
            'Email',         // Địa chỉ email
            'Mật khẩu',      // Mật khẩu (sẽ mã hóa khi lưu)
            'Số điện thoại', // Số điện thoại
            'Phòng ban',     // Tên phòng ban
            'Chức vụ'         // Vị trí công tác
        ];
    }
}
