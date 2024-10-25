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
    <link href="fe-access/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="fe-access/css/sb-admin-2.min.css" rel="stylesheet">
</head>

<body id="page-top">
    <div id="wrapper">
        @include('fe_admin.slidebar') <!-- Sidebar -->

        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                @include('fe_admin.topbar') <!-- Topbar -->

             
                    <div class="container-fluid">
                        <div class="d-sm-flex align-items-center justify-content-between mb-4">
                            <h1 class="h3 mb-0 text-gray-800">Danh sách người dùng</h1>
                            
                            <form action="{{ route('users') }}" method="GET" class="mb-3">
                                <div class="input-group">
                                    <input type="text" name="search" value="{{ $search ?? '' }}" 
                                           placeholder="Nhập tên, email hoặc chức vụ" class="form-control">
                                    <button type="submit" class="btn btn-primary">Tìm kiếm</button>
                                </div>

                            </form>
                            @if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif
                        </div>
                    
                        <!-- Card chứa Import và Export -->
                        <div class="card mb-4">
                            <div class="card-body">
                                <div class="row mb-3 align-items-center">
                                    <!-- Nút Nhập Dữ Liệu -->
                                    <div class="col-md-3">
                                        <form action="{{ route('users.import') }}" method="POST" enctype="multipart/form-data" id="importForm">
                                            @csrf
                                            <div class="input-group">
                                                <input type="file" name="import_file" class="form-control" id="importFile" style="display: none;" required>
                                                <div class="input-group-append">
                                                    <button type="submit" class="btn btn-primary" id="submitBtn" style="display: none;">
                                                        <i class="fas fa-file-import"></i> Nhập từ Excel
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                        <button class="btn btn-primary" id="importDataBtn">
                                            <i class="fas fa-file-import"></i> Nhập dữ liệu
                                        </button>
                                        <a href="{{ route('export.template') }}" class="btn btn-primary">Tải Mẫu Excel</a>
                                    </div>
                                    

                                    <!-- Nút Xuất Dữ Liệu -->
                                    <div class="col-md-3 text-center">
                                        <form action="{{ route('users.export') }}" method="GET">
                                            <button type="submit" class="btn btn-info w-100">
                                                <i class="fas fa-file-export"></i> Xuất Dữ Liệu
                                            </button>
                                        </form>
                                    </div>
                        
                                    <!-- Nút Thêm Nhân Viên -->
                                    <div class="col-md-3 text-center">
                                        <a href="{{ route('users.create') }}" class="btn btn-success w-100">
                                            <i class="fas fa-user-plus"></i> Thêm Nhân Viên
                                        </a>
                                    </div>
                        
                                    <!-- Nút Xóa Người Dùng Đã Chọn -->
                                    <div class="col-md-3 text-center">
                                        <button type="button" class="btn btn-danger w-100" onclick="confirmBulkDelete()">
                                            <i class="fas fa-trash"></i> Xóa Người Dùng Đã Chọn
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    
                        <!-- Danh sách người dùng -->
                        <div class="table-responsive">
                            <form id="deleteUsersForm" method="POST" action="{{ route('users.destroy') }}">
                                @csrf
                                <table class="table table-bordered w-100">
                                    {{-- <div class="d-flex justify-content-between mt-3">
                                        <button type="button" class="btn btn-danger" onclick="confirmBulkDelete()">Xóa Người Dùng Đã Chọn</button>
                                    </div> --}}
                                   
                                    <thead class="thead-dark">
                                        <tr>
                                            <th>
                                                <input type="checkbox" id="select-all">
                                            </th>
                                            <th>STT</th>
                                            <th>Tên</th>
                                            <th>Email</th>
                                            <th>Số điện thoại</th>
                                            <th>Chức vụ</th>
                                            <th>Phòng ban</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($users as $user)
                                            <tr>
                                                <td>
                                                    <input type="checkbox" name="user_ids[]" value="{{ $user->id }}">
                                                </td>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $user->name }}</td>
                                                <td>{{ $user->email }}</td>
                                                <td>{{ $user->phone_number }}</td>
                                                <td>{{ $user->position }}</td>
                                                <td>
                                                    @if ($user->department)
                                                        {{ $user->department->name }} 
                                                        @if ($user->department->parent_id)
                                                            - {{ $user->department->parent->name ?? 'Chưa xác định' }}
                                                        @endif
                                                    @else
                                                        Chưa xác định
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center">Không có người dùng nào.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                                
                                
                            </form>
                        </div>
                        </div>
                    
                        <div class="d-flex justify-content-center mt-3">
                            {{ $users->links() }}
                        </div>
                    </div>

            <!-- Modal sửa người dùng -->
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
                            <form id="editUserForm" method="POST"  action="{{ route('users.update', $user->id) }}">
                                @csrf
        <div class="form-group">
            <label>Tên</label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
        </div>

        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
        </div>

        <div class="form-group">
            <label>Số điện thoại</label>
            <input type="text" name="phone_number" class="form-control" value="{{ old('phone_number', $user->phone_number) }}" required>
        </div>

        <div class="form-group">
            <label>Chức vụ</label>
            <input type="text" name="position" class="form-control" value="{{ old('position', $user->position) }}" required>
        </div>

        <div class="form-group">
            <label>Phòng ban</label>
            <select name="department_id" class="form-control" required>
                @foreach($departments as $department)
                    <option value="{{ $department->id }}" 
                        {{ $department->id == $user->department_id ? 'selected' : '' }}>
                        {{ $department->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Cập nhật</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>© {{ date('Y') }} Your Company. All Rights Reserved.</span>
                    </div>
                </div>
            </footer>
        </div>
        
    </div>

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>
    <script>
        function confirmDelete(userId) {
            if (confirm('Are you sure you want to delete this user?')) {
                document.getElementById('delete-form-' + userId).submit();
            }
        }
        </script>
    
</script>
<script>
    document.getElementById('importDataBtn').addEventListener('click', function() {
        // Kích hoạt ô input file
        document.getElementById('importFile').click();
    });

    // Thêm sự kiện lắng nghe cho ô input file
    document.getElementById('importFile').addEventListener('change', function() {
        // Tự động gửi form khi chọn file
        document.getElementById('importForm').submit();
    });
</script>
<script>
    document.getElementById('select-all').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('input[name="user_ids[]"]');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });

    function confirmBulkDelete() {
        const form = document.getElementById('deleteUsersForm');
        const selectedCheckboxes = form.querySelectorAll('input[name="user_ids[]"]:checked');

        if (selectedCheckboxes.length === 0) {
            alert('Vui lòng chọn ít nhất một người dùng để xóa.');
            return;
        }

        if (confirm('Bạn có chắc chắn muốn xóa những người dùng đã chọn?')) {
            form.submit(); // Gửi form
        }
    }
</script>
   

    <!-- Bootstrap core JavaScript-->
    <script src="fe-access/vendor/jquery/jquery.min.js"></script>
    <script src="fe-access/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="fe-access/vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="fe-access/js/sb-admin-2.min.js"></script>
</body>
</html>