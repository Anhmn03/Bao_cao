<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

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

    public function updateAttendanceStatus()
{
    // Giả sử 'type' chứa loại (check-in hoặc check-out), và 'time' chứa thời gian
    $checkInTime = Carbon::parse($this->where('type', 'check-in')->first()->time);
    $checkOutTime = Carbon::parse($this->where('type', 'check-out')->first()->time);

    // Điều kiện hợp lệ: check-in < 08:00 và check-out > 17:00
    if ($checkInTime->lt(Carbon::createFromTime(8, 0)) && $checkOutTime->gt(Carbon::createFromTime(17, 0))) {
        $this->status = true; // Hợp lệ
    } else {
        $this->status = false; // Không hợp lệ
    }

    // Lưu thay đổi
    $this->save();
}

    
}
