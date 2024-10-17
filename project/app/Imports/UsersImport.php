<?php

namespace App\Imports;

use App\Models\Department;
use App\Models\User;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class UsersImport implements ToCollection
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            try {
                // Tìm department_id dựa vào tên phòng ban
                $department = Department::where('name', $row[5])->firstOrFail();

                // Tạo user mới với thông tin từ file Excel
                User::create([
                    'name' => $row[0],
                    'email' => $row[1],
                    'password' => bcrypt($row[2]),
                    'phone_number' => $row[3], 
                    'position' => $row[4],
                    'department_id' => $department->id, // Lấy ID của phòng ban
                ]);
            } catch (\Exception $e) {
                // Xử lý ngoại lệ nếu không tìm thấy phòng ban
                // \Log::error("Import thất bại cho dòng: " . json_encode($row));
                continue; // Bỏ qua dòng lỗi và tiếp tục import
            }
        }
    }
}
