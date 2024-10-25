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
        $dataRows = $rows->skip(1);

        foreach ($dataRows as $row) {
            try {
                // Tìm department_id dựa vào tên phòng ban
                $department = Department::where('name', $row[4])->first();

                if (!$department) {
                    throw new \Exception("Department not found: " . $row[4]);
                }

                // Tìm người dùng dựa trên email
                $user = User::where('email', $row[1])->first();

                // Lấy ID của người cập nhật (giả sử từ session)
                $updatedById = auth()->id();

                if ($user) {
                    // Cập nhật thông tin người dùng nếu đã tồn tại
                    $user->update([
                        'name' => $row[0],  // Tên
                        'email' => $row[1],  // Email
                        'password' => bcrypt($row[2]),  // Mật khẩu
                        'phone_number' => $row[3],  // Số điện thoại
                        'position' => $row[5],  // Vị trí
                        'department_id' => $department->id,  // ID phòng ban
                        'status' => 1,  // Trạng thái mặc định là 1
                        'role' => 2,  // Role mặc định là 2
                        'updated_at' => now(),  // Thời gian cập nhật
                        'updated_by' => $updatedById,  // ID người cập nhật
                    ]);
                } else {
                    // Tạo người dùng mới
                    User::create([
                        'name' => $row[0],
                        'email' => $row[1],
                        'password' => bcrypt($row[2]),
                        'phone_number' => $row[3],
                        'department_id' => $department->id,
                        'position' => $row[5],
                        'status' => 1,
                        'role' => 2,
                        'created_at' => now(),
                        'created_by' => $updatedById,
                    ]);
                }
            } catch (\Exception $e) {
                // Ghi log lỗi hoặc xử lý tiếp
                // \Log::error("Lỗi khi import dòng: " . json_encode($row) . " - " . $e->getMessage());
                continue;
            }
        }
    }
}
