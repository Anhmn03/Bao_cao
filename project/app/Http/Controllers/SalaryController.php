<?php

namespace App\Http\Controllers;

use App\Models\Salary_level;
use Illuminate\Http\Request;

class SalaryController extends Controller
{
    public function index(Request $request)
{
    // Lấy giá trị tìm kiếm từ request
    $searchSalary = $request->input('search_salary');

    // Tạo truy vấn cơ bản
    $query = Salary_level::query();

    // Nếu có giá trị tìm kiếm, tìm trong tên cấp bậc
    if ($searchSalary) {
        $query->where('name', 'LIKE', '%' . $searchSalary . '%');
    }

    // Lấy kết quả
    $salarylevels = $query->get();

    return view('fe_salary.salary', compact('salarylevels'));
}

    public function create()
    {
        return view('fe_salary.salary_create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:50|unique:salary_level,name',
            'monthly_salary' => 'required|string|unique:salary_level,monthly_salary',
            'daily_salary' => 'required|string|unique:salary_level,daily_salary',
        ], [
            'name.required' => 'Tên cấp bậc là bắt buộc.',
            'name.unique' => 'Cấp bậc đã tồn tại.',
            'monthly_salary.required' => 'Lương tháng là bắt buộc.',
            'daily_salary.required' => 'Lương ngày là bắt buộc.',
        ]);

        $monthlySalary = $this->parseSalary($request->monthly_salary);
        $dailySalary = $this->parseSalary($request->daily_salary);

        if ($monthlySalary < 1000000) {
            return redirect()->back()->withErrors(['monthly_salary' => 'Lương tháng phải ít nhất là 1,000,000 VND.']);
        }

        if ($dailySalary < 100000) {
            return redirect()->back()->withErrors(['daily_salary' => 'Lương ngày phải ít nhất là 100,000 VND.']);
        }

        $existingSalaryLevel = Salary_level::where('monthly_salary', $monthlySalary)
            ->where('daily_salary', $dailySalary)
            ->first();

        if ($existingSalaryLevel) {
            return redirect()->back()->withErrors([
                'monthly_salary' => 'Lương tháng và lương ngày đã tồn tại cho cấp bậc này.',
                'daily_salary' => 'Lương tháng và lương ngày đã tồn tại cho cấp bậc này.'
            ]);
        }

        $userId = auth()->user()->id;

        Salary_level::create([
            'name' => $request->name,
            'monthly_salary' => $monthlySalary,
            'daily_salary' => $dailySalary,
            'status' => 1,
            'created_at' => now(),
            'created_by' => $userId,
            'updated_at' => now(),
            'updated_by' => $userId,
        ]);

        return redirect()->route('salary')->with('success', 'Thêm mới thành công');
    }

        /**
         * Parse a salary string into an integer
         *
         * @param string $salary salary string, e.g. "1,000,000 VND"
         * @return int the parsed salary integer
         */
    private function parseSalary($salary)
    {
        return intval(str_replace(['₫', '.', ','], '', $salary));
    }

    public function show($id)
    {
        // Tìm mức lương theo ID
        $salaryLevel = Salary_level::with(['creator', 'updater'])->findOrFail($id);

        return view('fe_salary.salary_detail', compact('salaryLevel'));
    }
    public function edit($id)
    {
        // Tìm mức lương theo ID
        $salaryLevel = Salary_level::findOrFail($id);

        return view('fe_salary.salary_detail', compact('salaryLevel'));
    }

    public function update(Request $request, $id)
    {
        // Tìm mức lương theo ID
        $salaryLevel = Salary_level::findOrFail($id);

        // Xác thực dữ liệu đầu vào
        $request->validate([
            'name' => 'required|string|max:50|unique:salary_level,name,' . $salaryLevel->id,
            'monthly_salary' => 'required|string|unique:salary_level,monthly_salary,' . $salaryLevel->id,
            'daily_salary' => 'required|string|unique:salary_level,daily_salary,' . $salaryLevel->id,
        ], [
            'name.required' => 'Tên cấp bậc là bắt buộc.',
            'name.unique' => 'Cấp bậc đã tồn tại.',
            'monthly_salary.required' => 'Lương tháng là bắt buộc.',
            'daily_salary.required' => 'Lương ngày là bắt buộc.',
        ]);

        $monthlySalary = $this->parseSalary($request->monthly_salary);
        $dailySalary = $this->parseSalary($request->daily_salary);

        if ($monthlySalary < 1000000) {
            return redirect()->back()->withErrors(['monthly_salary' => 'Lương tháng phải ít nhất là 1,000,000 VND.']);
        }

        if ($dailySalary < 100000) {
            return redirect()->back()->withErrors(['daily_salary' => 'Lương ngày phải ít nhất là 100,000 VND.']);
        }

        $existingSalaryLevel = Salary_level::where('monthly_salary', $monthlySalary)
            ->where('daily_salary', $dailySalary)
            ->where('id', '!=', $salaryLevel->id) // Đảm bảo không kiểm tra bản ghi hiện tại
            ->first();

        if ($existingSalaryLevel) {
            return redirect()->back()->withErrors([
                'monthly_salary' => 'Lương tháng và lương ngày đã tồn tại cho cấp bậc này.',
                'daily_salary' => 'Lương tháng và lương ngày đã tồn tại cho cấp bậc này.'
            ]);
        }

        // Cập nhật thông tin mức lương
        $salaryLevel->update([
            'name' => $request->name,
            'monthly_salary' => $monthlySalary,
            'daily_salary' => $dailySalary,
            'status' => $request->status,
            'updated_at' => now(),
            'updated_by' => auth()->user()->id,
        ]);

        return redirect()->route('salary')->with('success', 'Cập nhật thành công');
    }

}
