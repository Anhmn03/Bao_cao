<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Department;
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $table = 'users';

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone_number',
        'status',
        'position',
        'department_id',
        'salary_level_id',
        'role',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
  
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    public function department() {
        return $this->belongsTo(Department::class, 'department_id', 'id');
    }
    public function isActive(){
        return $this->status === 1;
    }
}
