<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CaculateSalary extends Model
{
    use HasFactory;
    protected $table = 'cacu_salaries';
    protected $fillable = [
        'user_id',
        'valid_workdays',
        'invalid_workdays',
        'salary_amount',
        'month',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',

    ];
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }


}
