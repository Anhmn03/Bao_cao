<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Salary_parent extends Model
{
    protected $fillable = ['name', 'category', 'description', 'created_at',
    'created_by',
    'updated_at',
    'updated_by',];

    public function salaryLevels()
    {
        return $this->hasMany(Salary_level::class);
    }
}
