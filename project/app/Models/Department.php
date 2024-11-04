<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;
    protected $table = 'departments';
    protected $fillable = [
        'name',
        'parent_id',
        'status',
        
        'created_by',
        
        'updated_by',
    ];
    // Mối quan hệ với phòng ban cha
    public function parent()
    {
        return $this->belongsTo(Department::class, 'parent_id');
    }

    // Mối quan hệ với các phòng ban con
   
        // Phương thức để kiểm tra trạng thái của phòng ban
    public function isActive(){
        return $this->status === 1;
    }

    // Phương thức để lấy thời gian tạo phòng ban
    protected $casts = [
        'created_at' => 'datetime',  // Sử dụng Carbon cho các cột datetime
        'updated_at' => 'datetime',
    ];

    public function children()
    {
        return $this->hasMany(Department::class, 'parent_id')->with('children');
    }
    public function users(){
        return $this->hasMany(User::class);
    }
}
