<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Department;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class DepartmentController extends Controller
{
    public function allDepartment()
    {
        $departments = Department::all();
        return view('fe_department/departments', compact('departments'));
    }
    public function showMembers($id)
    {
        $department = Department::with('users')->findOrFail($id); // Assuming users() is the relationship in Department model
        return view('fe_department/department_members', compact('department'));
    }
    public function create()
    {
        $departments = Department::all();  // Get all departments to display in parent dropdown
        return view('fe_department/create_department', compact('departments'));
    }

    // Store the new department
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:departments,id',
            'status' => 'required|boolean',
        ]);
        $currentTime = now()->format('Y-m-d H:i:s'); // Định dạng lại nếu cần

        Department::create([
            'name' => $validated['name'],
            'parent_id' => $validated['parent_id'] ?? null,
            'status' => $validated['status'],
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
            'created_at' =>  $currentTime,
            'updated_at' =>  $currentTime,

        ]);

        return redirect()->route('departments.create')->with('success', 'Department added successfully.');
    }
    
    public function destroy($id)
    {
        $department = Department::findOrFail($id);
        $department->delete();

        return redirect()->route('departments.all')->with('success', 'Department deleted successfully.');
    }
    
}
