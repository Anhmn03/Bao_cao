<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Danh sách người dùng</title>

    <!-- Font và CSS -->
    <link href="/fe-access/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,300,400,600,700,800,900" rel="stylesheet">
    <link href="/fe-access/css/sb-admin-2.min.css" rel="stylesheet">
</head>

<body id="page-top">
    <div id="wrapper">
        @include('fe_user.slidebar') <!-- Thanh bên -->

        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                @include('fe_admin/topbar') <!-- Thanh trên -->

                <div class="container-fluid">
                    <div class="form-group">
                        <label for="name">Họ và tên</label>
                        <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                    </div>

                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                    </div>

                    <div class="form-group">
                        <label for="phone_number">Số điện thoại</label>
                        <input type="text" name="phone_number" id="phone_number" class="form-control" value="{{ old('phone_number', $user->phone_number) }}" required>
                    </div>
                    <div class="form-group">
                        <label for="department">Phòng ban</label>
                        @php
                            // Khởi tạo biến để chứa giá trị của phòng ban
                            $departmentValue = 'Chưa xác định'; // Giá trị mặc định
                    
                            if ($user->department) {
                                if ($user->department->parent) {
                                    $departmentValue = $user->department->name . ' - ' . $user->department->parent->name;
                                } else {
                                    $departmentValue = $user->department->name;
                                }
                            }
                        @endphp
                    
                        <input type="text" name="department" id="department" class="form-control" value="{{ old('department', $departmentValue) }}" required>
                    </div>
                    <div class="form-group">
                        <label for="position">Chức vụ</label>
                        <input type="text" name="position" id="position" class="form-control" value="{{ old('position', $user->position) }}">
                    </div>

                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#editUserModal">
                        Chỉnh sửa
                    </button>
                </div>
            </div>

            <!-- Modal chỉnh sửa thông tin người dùng -->
            <div class="modal fade" id="editUserModal" tabindex="-1" role="dialog" aria-labelledby="editUserModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editUserModalLabel">Sửa Thông Tin Người Dùng</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                                <form id="editUserForm" action="{{ route('users.quickUpdate', $user->id) }}" method="POST">
                                    @csrf
                                    <div class="form-group">
                                        <label for="name">Name</label>
                                        <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                                    </div>
                            
                                    <div class="form-group">
                                        <label for="email">Email</label>
                                        <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                                    </div>
                            
                                    <div class="form-group">
                                        <label for="phone_number">Phone Number</label>
                                        <input type="text" name="phone_number" class="form-control" value="{{ old('phone_number', $user->phone_number) }}" required>
                                    </div>
                            
                                    <div class="form-group">
                                        <label for="position">Position</label>
                                        <input type="text" name="position" class="form-control" value="{{ old('position', $user->position) }}" required>
                                    </div>
                            
                                    {{-- <div>
                                        <label for="department_id">Phòng ban:</label>
                                        <select name="department_id" required>
                                            @foreach($departments as $department)
                                                @if($department->parent_id == 0) <!-- Chỉ hiển thị phòng ban không có parent -->
                                                    <option value="{{ $department->id }}" {{ $user->department_id == $department->id ? 'selected' : '' }}>
                                                        {{ $department->name }}
                                                    </option>
                                                @endif
                                            @endforeach
                                        </select>
                                        @error('department_id')
                                            <span>{{ $message }}</span>
                                        @enderror
                                    </div> --}}
                                    <div class="form-group">
                                        <label for="department_id">Phòng ban:</label>
                                        <select name="department_id" id="department_id" class="form-control" required>
                                            <option value="">Chọn phòng ban</option>
                                            @foreach($departments as $department)
                                                @if($department->parent_id == 0) <!-- Chỉ hiển thị phòng ban không có parent -->
                                                    <option value="{{ $department->id }}" {{ $user->department_id == $department->id ? 'selected' : '' }}>
                                                        {{ $department->name }}
                                                    </option>
                                                @endif
                                            @endforeach
                                        </select>
                                        @error('department_id')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    
                                    <div id="subDepartmentWrapper" class="form-group" style="display: none;">
                                        <label for="sub_department_id">Phòng ban con:</label>
                                        <select name="sub_department_id" id="sub_department_id" class="form-control">
                                            <option value="">Chọn phòng ban con</option>
                                        </select>
                                    </div>
                                    <div class="form-group mt-3">
                                        <button type="submit" class="btn btn-primary">Cập nhật</button>
                                        <a href="{{ route('attendance') }}" class="btn btn-secondary">Hủy</a>
                                    </div>
                                </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <footer class="sticky-footer bg-white mt-auto">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>© {{ date('Y') }} Your Company. All Rights Reserved.</span>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <!-- Scroll to Top Button -->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>
    <script>
        document.getElementById('department_id').addEventListener('change', function() {
            const selectedDepartmentId = this.value;
            const subDepartmentWrapper = document.getElementById('subDepartmentWrapper');
            const subDepartmentSelect = document.getElementById('sub_department_id');
    
            // Hiện thị phòng ban con chỉ khi có phòng ban chính được chọn
            if (selectedDepartmentId) {
                subDepartmentWrapper.style.display = 'block';
    
                // Xóa các lựa chọn trước đó trong danh sách phòng ban con
                subDepartmentSelect.innerHTML = '<option value="">Chọn phòng ban con</option>';
    
                // Biến để kiểm tra xem có phòng ban con nào không
                let hasSubDepartments = false;
    
                // Thêm các phòng ban con tương ứng với phòng ban cha đã chọn
                @foreach($departments as $department)
                    @if($department->parent_id != 0) // Chỉ các phòng ban con
                        if (selectedDepartmentId == '{{ $department->parent_id }}') {
                            const option = document.createElement('option');
                            option.value = '{{ $department->id }}';
                            option.textContent = '{{ $department->name }}';
                            subDepartmentSelect.appendChild(option);
                            hasSubDepartments = true; // Đánh dấu có phòng ban con
                        }
                    @endif
                @endforeach
    
                // Nếu không có phòng ban con nào, ẩn wrapper
                if (!hasSubDepartments) {
                    subDepartmentWrapper.style.display = 'none';
                }
            } else {
                subDepartmentWrapper.style.display = 'none';
            }
        });
    </script>
    <script src="/fe-access/vendor/jquery/jquery.min.js"></script>
    <script src="/fe-access/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="/fe-access/vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="/fe-access/js/sb-admin-2.min.js"></script>
</body>

</html>