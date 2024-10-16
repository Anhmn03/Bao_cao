<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User_attendance extends Model
{
    use HasFactory;
    protected $table = 'user_attendances';
    protected $fillable = [
        'time',
        'type',
        'user_id',
        'create_at',
        'create_by',
        'update_at',
        'update_by',
    ];
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    protected $casts = [
        'time' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
