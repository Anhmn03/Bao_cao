<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class UsersExport implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return User::join('departments', 'users.department_id', '=', 'departments.id')
        ->select(
            'users.id', 
            'users.name', 
            'users.email', 
            'users.phone_number', 
            'users.status', 
            'users.position', 
            'departments.name as department_name',  // Lấy tên phòng ban
            'users.created_at', 
            'users.updated_at', 
            'users.created_by', 
            'users.updated_by'
        )->get();    }
    public function headings(): array
    {
        return [
            'Stt',
            'name',
            'email',
            'phone_number',
            'status',
            'position',
            'department',
            'created_at',
            'update_at',
            'created_by',
            'update_by',
        ];
    }
}
