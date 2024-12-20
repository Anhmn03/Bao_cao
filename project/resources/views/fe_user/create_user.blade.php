<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Thêm Người Dùng</title>

    <!-- Font và CSS -->
    <link href="/fe-access/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,300,400,600,700,800,900" rel="stylesheet">
    <link href="/fe-access/css/sb-admin-2.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js"></script>
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

                    <div class="card mt-4">
                        <div class="card-body">
                            <h4 class="card-title mb-4">Thêm Người Dùng Mới</h4>
                            

                            <form id="createUserForm" action="{{ route('users.store') }}" method="POST">
                                @csrf
                                
                             
                                <div class="form-group">
                                    <label for="department_id">Phòng Ban:</label>
                                    <select id="department_id" name="department_id" class="form-control select2" required>
                                        <option value="">Chọn phòng ban</option>
                                        @foreach ($departments as $department)
                                            <option value="{{ $department->id }}">{{ $department->name }}</option>
                                            @if (count($department->children))
                                                @include('fe_department.department_children', ['children' => $department->children, 'prefix' => '--'])
                                            @endif
                                        @endforeach
                                    </select>
                                    @error('department_id')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="position">Chức vụ:</label>
                                    <input type="text" name="position" id="position" class="form-control" 
                                           placeholder="Nhập chức vụ" value="{{ old('position') }}" required>
                                    @error('position') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>


                                <div class="form-group">
                                    <label for="name">Tên:</label>
                                    <input type="text" name="name" id="name" class="form-control" 
                                           placeholder="Nhập tên người dùng" value="{{ old('name') }}" required>
                                    @error('name') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>

                                <div class="form-group">
                                    <label for="email">Email:</label>
                                    <input type="email" name="email" id="email" class="form-control" 
                                           placeholder="Nhập địa chỉ email" value="{{ old('email') }}" required>
                                    @error('email') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>

                                <div class="form-group">
                                    <label for="password">Mật khẩu:</label>
                                    <input type="password" name="password" id="password" class="form-control" 
                                           placeholder="Nhập mật khẩu" required minlength="6">
                                    @error('password') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>

                                <div class="form-group">
                                    <label for="password_confirmation">Xác nhận mật khẩu:</label>
                                    <input type="password" name="password_confirmation" id="password_confirmation" 
                                           class="form-control" placeholder="Xác nhận mật khẩu" required>
                                    @error('password_confirmation') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>

                                <div class="form-group">
                                    <label for="phone_number">Số điện thoại:</label>
                                    <input type="tel" name="phone_number" id="phone_number" class="form-control" 
                                           placeholder="Nhập số điện thoại" value="{{ old('phone_number') }}">
                                    @error('phone_number') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>

                                <div class="form-group">
                                    <label for="role">Vai trò:</label>
                                    <select name="role" id="role" class="form-control" required>
                                        <option value="">Chọn vai trò</option>
                                        <option value="1">Admin</option>
                                        <option value="4">Trưởng phòng</option>
                                        <option value="3">Tổ trưởng</option>
                                        <option value="2">Nhân viên</option>
                                    </select>
                                    @error('role') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>




                                <div class="d-flex justify-content-between mt-4">
                                    <button onclick="window.history.back();" class="btn btn-secondary btn-sm">
                                        <i class="fas fa-arrow-left"></i> Quay lại
                                    </button>
                                    <button type="submit" class="btn btn-primary btn-sm">Thêm Người Dùng</button>
                                </div>                            </form>
                        </div>
                    </div>
                </div>
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

    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>
    <style>
        .btn-sm {
            padding: 0.375rem 0.75rem;
            font-size: 0.875rem;
        }
        .mt-4 {
            margin-top: 1.5rem;
        }
    </style>

    <script src="fe-access/vendor/jquery/jquery.min.js"></script>
    <script src="fe-access/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="fe-access/vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="fe-access/js/sb-admin-2.min.js"></script>
  
  {{-- <div class="form-group">
    <label for="department_id">Phòng Ban:</label>
    <select id="department_id" name="department_id" class="form-control select2" required>
        <option value="">Chọn phòng ban</option>
        @foreach ($departments as $department)
            <option value="{{ $department->id }}">{{ $department->name }}</option>
            @if (count($department->children))
                @include('fe_department.department_children', ['children' => $department->children, 'prefix' => '--'])
            @endif
        @endforeach
    </select>
    @error('department_id')
        <span class="text-danger">{{ $message }}</span>
    @enderror
</div> --}}
{{-- <script>
    $(document).ready(function() {
        $('.select2').select2({
            placeholder: "Chọn phòng ban",
            allowClear: true,
            minimumInputLength: 1, // Số ký tự tối thiểu để kích hoạt tìm kiếm
            width: '100%' // Giữ chiều rộng của select box
        });
    });
</script> --}}
<script>
    $('.select2').select2({
    placeholder: "Chọn phòng ban",
    allowClear: true,
    minimumInputLength: 1,
    width: '200px' // Đặt chiều rộng tại đây
});
</script>

</body>
</html> 