<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User_attendance extends Model
{
    use HasFactory;
    protected $table = 'user_attendance';
    protected $fillable = [
      'time',
    'type',
    'user_id',
    'status',
    'justification',
    'created_at',
    'created_by',
    'updated_at',
    'updated_by',
    ];
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    protected $casts = [
        'status' => 'boolean',
        'time' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    public function checkValidity()
{
    $today = now()->startOfDay()->timestamp;

    // Tìm bản ghi check-in và check-out gần nhất của người dùng
    $checkIn = User_attendance::where('user_id', $this->user_id)
        ->where('type', 'in')
        ->orderBy('time', 'desc')
        ->first();

    $checkOut = User_attendance::where('user_id', $this->user_id)
        ->where('type', 'out')
        ->orderBy('time', 'desc')
        ->first();

    // Kiểm tra nếu cả hai bản ghi check-in và check-out tồn tại
    if ($checkIn && $checkOut) {
        $checkInTime = $checkIn->time->format('H:i');
        $checkOutTime = $checkOut->time->format('H:i');

        // Kiểm tra điều kiện thời gian hợp lệ
        if ($checkInTime < '08:00' && $checkOutTime > '17:00') {
            $this->status = 1; // Hợp lệ
        } else {
            $this->status = 0; // Không hợp lệ
        }

        // Lưu trạng thái đã cập nhật vào cơ sở dữ liệu
        $this->save();
    }
}
    
}
