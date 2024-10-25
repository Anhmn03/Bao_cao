<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Department;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class DepartmentController extends Controller
{
    public function allDepartment()
    {
        if (Auth::user()->role == '2') {
            return redirect()->route('login')->with('error', 'Bạn không có quyền truy cập vào trang này.');
        }
        $departments = Department::where('parent_id', 0)
            ->with('children')
            ->get();
        return view('fe_department/departments', compact('departments'));
    }
    public function showMembers($id)
    {
       // Lấy phòng ban hiện tại và các phòng ban con
    $department = Department::with('children')->findOrFail($id);

    // Lấy tất cả ID của phòng ban hiện tại và các phòng ban con
    $departmentIds = collect([$department->id])->merge(
        $department->children->pluck('id')
    );

    // Lấy tất cả người dùng thuộc các phòng ban đó
    $users = User::whereIn('department_id', $departmentIds)->get();

    // Trả về view với dữ liệu phòng ban và danh sách người dùng
    return view('fe_department/department_members', compact('department', 'users'));
    }
    public function create()
    {
        $departments = Department::all();
        $childDepartments = []; // Lấy danh sách tổ con dựa trên phòng ban cha nếu cần
        return view('fe_department/create_department', compact('departments', 'childDepartments'));
    }


    // Store the new department
    public function store(Request $request)
{
    // Validate the incoming request data
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'parent_id' => 'nullable|exists:departments,id',
        'status' => 'required|boolean',
    ]);

    // Check if there is a parent department and it is inactive
    if ($validated['parent_id']) {
        $parentDepartment = Department::find($validated['parent_id']);
        if ($parentDepartment && !$parentDepartment->status) {
            // Redirect back if the parent department is inactive
            return redirect()->back()->with('error', 'Không thể thêm phòng ban con vào một phòng ban không hoạt động.');
        }
    }

    $currentTime = now()->format('Y-m-d H:i:s');

    Department::create([
        'name' => $validated['name'],
        // Nếu không có parent_id thì gán về 0
        'parent_id' => $validated['parent_id'] ?? 0, // Gán parent_id là 0 nếu không có
        'status' => $validated['status'],
        'created_by' => auth()->id(),
        'updated_by' => auth()->id(),
        'created_at' => $currentTime,
        'updated_at' => $currentTime,
    ]);

    return redirect()->route('departments.create')->with('success', 'Department added successfully.');
}

    public function destroy($id)
    {
        $department = Department::findOrFail($id);
        $department->delete();

        return redirect()->route('departments.all')->with('success', 'Department deleted successfully.');
    }

    public function updateStatus(Request $request, $id)
{
    // Tìm phòng ban dựa trên ID, nếu không có thì trả về lỗi 404.
    $department = Department::findOrFail($id);

    // Xác thực dữ liệu đầu vào từ request.
    $validated = $request->validate([
        'status' => 'required|boolean',
    ]);

    // Cập nhật trạng thái của phòng ban.
    $department->status = $validated['status'];
    $department->updated_by = auth()->id(); // Cập nhật user thực hiện thay đổi.
    $department->updated_at = now(); // Cập nhật thời gian thay đổi.
    
    // Cập nhật trạng thái của người dùng trong phòng ban nếu trạng thái phòng ban bị tắt
    if (!$validated['status']) { // Nếu trạng thái phòng ban bị tắt
        $department->users()->update(['status' => false]); // Giả sử có mối quan hệ users() trong model Department
    }
    
    $department->save();

    // Trả về thông báo thành công.
    return redirect()
        ->route('departments.show', $id)
        ->with('success', 'Trạng thái phòng ban đã được cập nhật!');
}
    public function showSubDepartments($id)
{
    // Tìm phòng ban theo ID
    $department = Department::with('children')->findOrFail($id); // Giả sử 'children' là mối quan hệ trong model Department

    // Trả về view với thông tin phòng ban và các tổ con
    return view('fe_department/sub_departments', compact('department'));
}

/*************  ✨ Codeium Command ⭐  *************/
    /**
     * Hiển thị chi tiết phòng ban và người dùng trong phòng ban.
     *
     * @param int $id ID của phòng ban
     * @return \Illuminate\Http\Response
     */
/******  9c9fcda7-79d7-4339-b7a9-6b2b0cab570c  *******/
public function show($id){
    $department = Department::with('users')->findOrFail($id);
    return view('fe_department/department', compact('department'));
}
public function search(Request $request){
    $validated = $request->validate([
        'query' => 'required|string|max:255',
    ]);

    // Tìm kiếm phòng ban theo tên
    $departments = Department::where('name', 'LIKE', '%' . $validated['query'] . '%')
        ->with('children') // Bao gồm các phòng ban con nếu cần
        ->get();

    return view('fe_department/departments', compact('departments'));

}
}
