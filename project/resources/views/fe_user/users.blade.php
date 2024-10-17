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
        @include('fe.slidebar') <!-- Sidebar -->

        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                @include('fe.topbar') <!-- Topbar -->

             
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
                        </div>
                    
                        <!-- Card chứa Import và Export -->
                        <div class="card mb-4">
                            <div class="card-body">
                                <div class="row">
                                    <!-- Form Nhập Dữ Liệu Từ Excel -->
                                    <div class="col-md-6">
                                        <form action="{{ route('users.import') }}" method="POST" enctype="multipart/form-data">
                                            @csrf
                                            <div class="input-group">
                                                <input type="file" name="import_file" class="form-control" required>
                                                <div class="input-group-append">
                                                    <button type="submit" class="btn btn-primary">
                                                        <i class="fas fa-file-import"></i> Nhập từ Excel
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                    <div  class="col-md-3 text-right">
                                        <a href="{{ route('users.create') }}" class="btn btn-success">
                                        <i class="fas fa-user-plus"></i> Thêm Nhân Viên
                                        </a>
                                     </div>
                                    
                                    <!-- Form Xuất Dữ Liệu -->
                                    <div class="col-md-3 text-right">
                                        <form action="{{ route('users.export') }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-info">
                                                <i class="fas fa-file-export"></i> Xuất Dữ Liệu
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    
                        <!-- Danh sách người dùng -->
                        <div class="table-responsive">
                            <table class="table table-bordered w-100">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>ID</th>
                                        <th>Tên</th>
                                        <th>Email</th>
                                        <th>Số điện thoại</th>
                                        <th>Chức vụ</th>
                                        <th>Phòng ban</th>
                                        <th>Hành động</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($users as $user)
                                        <tr>
                                            <td>{{ $user->id }}</td>
                                            <td>{{ $user->name }}</td>
                                            <td>{{ $user->email }}</td>
                                            <td>{{ $user->phone_number }}</td>
                                            <td>{{ $user->position }}</td>
                                            <td>{{ $user->department->name ?? 'Chưa xác định' }}</td>
                                            <td>
                                                <!-- Nút Sửa -->
                                                <button class="btn btn-warning" data-toggle="modal" data-target="#editUserModal" 
                                                    data-id="{{ $user->id }}" 
                                                    data-name="{{ $user->name }}" 
                                                    data-email="{{ $user->email }}" 
                                                    data-phone="{{ $user->phone_number }}" 
                                                    data-position="{{ $user->position }}"
                                                    data-department="{{ $user->department->name ?? 'Chưa xác định' }}">
                                                    Cập nhật
                                                </button>
                    
                                                <!-- Nút Xóa -->
                                                <form action="{{ route('users.destroy', $user->id) }}" method="POST" 
                                                      style="display:inline;" id="delete-form-{{ $user->id }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-danger" 
                                                            onclick="confirmDelete({{ $user->id }})">
                                                        Xóa
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center">Không có người dùng nào.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
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
    <script>
        $(document).ready(function() {
            $('#editUserModal').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget); // Nút Sửa được nhấn
                var id = button.data('id');
                var name = button.data('name');
                var email = button.data('email');
                var phone = button.data('phone');
                var position = button.data('position');
                var department = button.data('department');

                // Điền dữ liệu vào modal
                var modal = $(this);
                modal.find('#userId').val(id);
                modal.find('#userName').val(name);
                modal.find('#userEmail').val(email);
                modal.find('#userPhone').val(phone);
                modal.find('#userPosition').val(position);
                modal.find('#userDepartment').val(department);

                // Cập nhật hành động form
                $('#editUserForm').attr('action', '/users/' + id); // Cập nhật action của form
            });
        });

        // Xử lý sự kiện gửi form
        $('#editUserForm').on('submit', function (e) {
    e.preventDefault(); // Prevent default form submission

    $.ajax({
        url: $(this).attr('action'),
        method: 'PUT', // Use PUT explicitly here
        data: $(this).serialize(),
        success: function (response) {
            alert('Người dùng đã được cập nhật.');
            location.reload(); // Reload page on success
        },
        error: function (xhr) {
            alert('Có lỗi xảy ra.');
        }
    });
});
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