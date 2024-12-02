<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Salary_histories extends Model
{
    use HasFactory;
    protected $table = 'salary_histories';
    protected $fillable = [
       'user_id',
        'salary_id',
        'old_salaryCoefficient',
        'old_monthlySalary',
        'changed_at',
        'changed_by',
    ];
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Định nghĩa mối quan hệ với Salary
    public function salary()
    {
        return $this->belongsTo(Salary::class, 'salary_id');
    }
}
