<?php

namespace App\Http\Controllers;

use App\Exports\UsersExport;
use App\Imports\UsersImport;
use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\Routing\Annotation\Route;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Validator;




class UserController extends Controller
{

    public function index(Request $request)
    {
        $search = $request->input('search'); // Lấy từ khóa tìm kiếm từ request
        $query = User::with('department');  // Khởi tạo query
    
        if ($search) {
            // Thêm điều kiện tìm kiếm theo tên, email hoặc chức vụ
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('position', 'LIKE', "%{$search}%");
            });
        }
    
        $users = $query->paginate(2); // Phân trang kết quả
        $departments = Department::all(); // Lấy tất cả phòng ban
    
        return view('fe_user/users', compact('users', 'departments', 'search'));
    }


    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return redirect()->route('users')->with('success', 'User deleted successfully.');
    }
  
    public function create()
    {
        $departments = Department::all(); // Lấy tất cả phòng ban
        return view('fe_user/create_user', compact('departments')); // Đảm bảo truyền biến ở đây
    }

    public function store(Request $request)
    {
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email', // Kiểm tra email duy nhất
            'password' => 'required|string|min:8|confirmed', // Kiểm tra mật khẩu
            'phone_number' => 'required|string|max:15', // Kiểm tra số điện thoại
            'position' => 'required|string|max:100',
            'department_id' => 'required|exists:departments,id', // Kiểm tra department_id tồn tại
        ]);
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
    
       
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password), // Mã hóa mật khẩu
            'phone_number' => $request->phone_number,
            'department_id' => $request->department_id,
            'position' => $request->position, // Lưu chức vụ người dùng
            'status' => 1,
            'created_by' => Auth::id(), // Ghi nhận ID của người tạo
            'updated_by' => Auth::id(), // Ghi nhận ID của người cập nhật
        ]);

      
        return redirect()->route('users')->with('success', 'User created successfully.');
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $departments = Department::all();

        return view('fe_user/edit_user', compact('user', 'departments'));
    }

    // Cập nhật thông tin người dùng
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $id,
            'phone_number' => 'required|string|max:15',
            'position' => 'required|string|max:100',
            'department_id' => 'required|exists:departments,id',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $user->update($request->only([
            'name', 'email', 'phone_number', 'position', 'department_id'
        ]) + ['updated_by' => Auth::id()]);

        return redirect()->route('users')->with('success', 'User updated successfully.');
    }

    public function importPost(Request $request){
        $request->validate([
            'import_file' =>[
                'required',
                'file',
                'mimes:xls,xlsx'
            ],
        ]);
        Excel::import(new UsersImport, $request->file('import_file'));

        return redirect()->back()->with('success', 'Import successfully.');
    }
    
    public function export(){
        $filename = "users.xlsx";
        return Excel::download(new UsersExport, $filename);
    }
}
