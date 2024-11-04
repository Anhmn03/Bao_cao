<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Danh sách người dùng</title>

    <!-- Custom fonts for this template-->
    <link href="/fe-access/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="/fe-access/css/sb-admin-2.min.css" rel="stylesheet">
</head>

<body id="page-top">
    <div id="wrapper">
        @include('fe_admin.slidebar') <!-- Sidebar -->

        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                @include('fe_admin.topbar') <!-- Topbar -->
                <div class="container-fluid">
                    <div class="card-footer">
                        <a href="{{ route('users') }}" class="btn btn-secondary mt-3">
                            <i class="fas fa-arrow-left"></i>  <!-- Icon chữ "i" trong vòng tròn -->
                            Quay lại danh sách
                        </a>
                    </div>
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h2 class="h3 mb-0 text-gray-800">Thông tin chi tiết của người dùng</h2>
                    </div>
                    @if (session('success'))
    <div class="alert alert-success" role="alert">
        {{ session('success') }}
    </div>
@endif
@if (session('error'))
    <div class="alert alert-danger" role="alert">
        {{ session('error') }}
    </div>
@endif
<div class="card">
    <div class="card-header">
        <strong>{{ $user->name }}</strong>
    </div>
    <div class="card-body">
        <!-- Hiển thị thông tin chi tiết -->
        <div class="container-fluid">
            

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
                <input type="text" name="position" id="position" class="form-control" 
                       value="{{ old('position', $user->position ? 'Admin' : ($user->position === 2 ? 'Tổ trưởng' : 'Nhân viên')) }}" readonly>
            </div>
            <div class="form-group">
                <label for="status">Trạng thái</label>
                <input type="text" name="status" id="status" class="form-control" 
                       value="{{ old('status', $user->status ? 'Hoạt động' : 'Vô hiệu hóa') }}" required readonly>
            </div>
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#updateModal">
                Chỉnh sửa
            </button>
        </div>
    </div>
            
                            <!-- Nút để hiển thị modal cập nhật -->
                            
                                               
                </div>

            </div>
            <div class="modal fade" id="updateModal" tabindex="-1" role="dialog" aria-labelledby="updateModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="updateModalLabel">Cập nhật thông tin người dùng</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form method="post" action="{{ route('users.updatedetail', ['id' => $user->id]) }}">
                            @csrf
                            <div class="modal-body">
                                <div class="form-group">
                                    <label for="email">Email:</label>
                                    <input type="email" class="form-control" id="email" name="email" value="{{ $user->email }}" required>
                                </div>
                                <div class="form-group">
                                    <label for="phone_number">Số điện thoại:</label>
                                    <input type="text" class="form-control" id="phone_number" name="phone_number" value="{{ $user->phone_number }}" required>
                                </div>
                                <div class="form-group">
                                    <label for="position">Chức vụ:</label>
                                    <select class="form-control" id="position" name="position" required>
                                        <option value="0" {{ $user->position == '0' ? 'selected' : '' }}>Admin</option>
                                        <option value="1" {{ $user->position == '1' ? 'selected' : '' }}>Tổ trưởng</option>
                                        <option value="2" {{ $user->position == '2' ? 'selected' : '' }}>Nhân viên</option>
                                        {{-- <option value="3" {{ $user->position == '3' ? 'selected' : '' }}></option>
                                        <option value="4" {{ $user->position == '4' ? 'selected' : '' }}>Kinh doanh</option> --}}
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="department">Phòng ban:</label>
                                    <select class="form-control" id="department_id" name="department_id" required>
                                        @foreach($departments as $department)
                                            <option value="{{ $department->id }}" {{ $user->department_id == $department->id ? 'selected' : '' }}>
                                                {{ $department->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="status">Trạng thái:</label>
                                    <select class="form-control" id="status" name="status" required>
                                        <option value="1" {{ $user->status ? 'selected' : '' }}>Hoạt động</option>
                                        <option value="0" {{ !$user->status ? 'selected' : '' }}>Vô hiệu hóa</option>
                                    </select>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                                <button type="submit" class="btn btn-success">Lưu thay đổi</button>
                            </div>
                        </form>
                        @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
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

    <!-- Modal cập nhật -->
   
    <!-- Bootstrap core JavaScript-->
    <script src="/fe-access/vendor/jquery/jquery.min.js"></script>
    <script src="/fe-access/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="/fe-access/vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="/fe-access/js/sb-admin-2.min.js"></script>
</body>
</html>
