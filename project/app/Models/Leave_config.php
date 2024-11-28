<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Leave_config extends Model
{
    use HasFactory;
    protected $table = 'leave_configs';
    protected $fillable = [
        'user_id',
        'start_date',
        'max_leave_days',
        'user_leave_days',
        'created_by',
        'updated_by',
    ];
    protected $casts = [
        'created_at' => 'datetime',  // Sử dụng Carbon cho các cột datetime
        'updated_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

}
