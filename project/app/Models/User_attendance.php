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
        'time' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    
}
