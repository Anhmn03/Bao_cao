
    <!-- Font và CSS -->
    <link href="/fe-access/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,300,400,600,700,800,900" rel="stylesheet">
    <link href="/fe-access/css/sb-admin-2.min.css" rel="stylesheet">
</head>
<body id="page-top">
    <div id="wrapper">
        @include('fe_admin.slidebar') <!-- Thanh bên -->

        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                @include('fe_admin.topbar') <!-- Thanh trên -->

                <div class="container-fluid">
                    <button onclick="window.history.back();" class="btn btn-secondary mt-4">
                        <i class="fas fa-arrow-left"></i> Quay lại
                    </button>
                    <h1 class="mb-4">Tính Lương Nhân Viên</h1>
                     <form method="GET" action="{{ route('salary.calculate') }}" id="salary-form">
                        <!-- Chọn phòng ban -->
                        <select name="department_id" id="department_id" required>
                            <option value="">Chọn phòng ban</option>
                            @foreach($departments as $department)
                                <option value="{{ $department->id }}" {{ request('department_id') == $department->id ? 'selected' : '' }}>
                                    {{ $department->name }}
                                </option>
                            @endforeach
                        </select>
                        
                        <!-- Nhân viên (Hiển thị khi phòng ban đã chọn) -->
                        <div id="employee-container">
                            @if(isset($employees) && count($employees) > 0)
                                <select name="user_id" id="user_id" required>
                                    <option value="">Chọn nhân viên</option>
                                    @foreach($employees as $employee)
                                        <option value="{{ $employee->id }}" {{ request('user_id') == $employee->id ? 'selected' : '' }}>
                                            {{ $employee->name }}
                                        </option>
                                    @endforeach
                                </select>
                            @else
                                <p>Chưa có nhân viên nào trong phòng ban này.</p>
                            @endif
                        </div>

                        <button type="submit">Tính lương</button>
                    </form>
                    
                    
                   
                
                    <!-- Hiển thị thông tin lương đã tính -->
                    @if($user && $salaryAmount !== null)
                        <h2>Thông Tin Lương</h2>
                        <p>Nhân viên: {{ $user->name }}</p>
                        <p>Lương Tháng: {{ $salaryAmount }} VND</p>
                        <p>Ngày công hợp lệ: {{ $validWorkdays }}</p>
                        <p>Ngày công không hợp lệ: {{ $invalidWorkdays }}</p>
                
                        <!-- Nút Lưu Lương -->
                        <form method="POST" action="{{ route('salary.save') }}">
                            @csrf
                            <input type="hidden" name="user_id" value="{{ $user->id }}">
                            <input type="hidden" name="valid_workdays" value="{{ $validWorkdays }}">
                            <input type="hidden" name="invalid_workdays" value="{{ $invalidWorkdays }}">
                            <input type="hidden" name="salary_amount" value="{{ $salaryAmount }}">
                            <button type="submit" class="btn btn-success">Lưu Lương</button>
                        </form>
                    @endif
                </div>

                    <footer class="sticky-footer bg-white">
                        <div class="container my-auto">
                            <div class="copyright text-center my-auto">
                                <span>© {{ date('Y') }} Your Company. All Rights Reserved.</span>
                            </div>
                        </div>
                    </footer>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Sử dụng AJAX để lấy danh sách nhân viên khi chọn phòng ban
        $(document).ready(function () {
    // Lắng nghe sự kiện thay đổi phòng ban
    $('#department_id').on('change', function () {
        var departmentId = $(this).val();
        var employeeContainer = $('#employee-container');
        var userSelect = $('#user_id');

        if (departmentId) {
            $.ajax({
                url: '/get-employees/' + departmentId, // Gửi request đến route xử lý
                method: 'GET',
                success: function (data) {
                    userSelect.empty(); // Xóa các option cũ
                    userSelect.append('<option value="">Chọn nhân viên</option>'); // Thêm option mặc định

                    if (data.length > 0) {
                        // Thêm các nhân viên mới vào select
                        $.each(data, function (index, employee) {
                            userSelect.append('<option value="' + employee.id + '">' + employee.name + '</option>');
                        });
                        employeeContainer.show(); // Hiển thị danh sách nhân viên
                    } else {
                        userSelect.append('<option value="">Không có nhân viên nào</option>');
                        employeeContainer.show(); // Hiển thị nhưng thông báo không có nhân viên
                    }
                },
                error: function () {
                    alert('Đã xảy ra lỗi khi tải danh sách nhân viên.');
                }
            });
        } else {
            employeeContainer.hide(); // Ẩn danh sách nhân viên nếu không chọn phòng ban
        }
    });
});

    </script>
        <!-- Bootstrap core JavaScript-->
        <script src="/fe-access/vendor/jquery/jquery.min.js"></script>
        <script src="/fe-access/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    
        <!-- Core plugin JavaScript-->
        <script src="/fe-access/vendor/jquery-easing/jquery.easing.min.js"></script>
    
        <!-- Custom scripts for all pages-->
        <script src="/fe-access/js/sb-admin-2.min.js"></script>
    </body>

    </html>
    