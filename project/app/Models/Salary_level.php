<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Salary_level extends Model
{
    use HasFactory;
    protected $table = 'salary_level';
    protected $fillable = [
        'name',
        'monthly_salary',
        'daily_salary',
        'status',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
    ];
    protected $casts = [
        'created_at' => 'datetime',  // Sử dụng Carbon cho các cột datetime
        'updated_at' => 'datetime',
    ];

    // Quan hệ với người tạo cấp bậc lương
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Quan hệ với người cập nhật cấp bậc lương
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

}
