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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.8.1/font/bootstrap-icons.min.css">

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
                    
                        @if (session('duplicate_emails'))
                        <div class="alert alert-warning">
                            <strong>Cảnh báo!</strong> Những email sau đã tồn tại và không được nhập lại:
                            <ul>
                                @foreach (session('duplicate_emails') as $email)
                                    <li>{{ $email }}</li>
                                @endforeach
                            </ul>
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
                                <thead class="thead-dark">
                                    <tr>
                                        <th><input type="checkbox" id="select-all"></th>
                                        <th>STT</th>
                                        <th>Tên</th>
                                        <th>Email</th>
                                        <th>Số điện thoại</th>
                                        <th>Chức vụ</th>
                                        <th>Phòng ban</th>
                                        <th>Chi tiết</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($users as $user)
                                    <tr>
                                        <td><input type="checkbox" name="user_ids[]" value="{{ $user->id }}"></td>
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
                                        <td>
                                            <a href="{{ route('users.detail', $user->id) }}" class="btn btn-primary">
                                                <i class="fas fa-info-circle"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="8" class="text-center">Không có người dùng nào.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </form>
                    </div>

                    <div class="d-flex justify-content-center mt-3">
                        {{ $users->links() }}
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
        document.getElementById('importDataBtn').addEventListener('click', function() {
            document.getElementById('importFile').click();
        });

        document.getElementById('importFile').addEventListener('change', function() {
            document.getElementById('importForm').submit();
        });

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
                form.submit();
            }
        }
    </script>

    <!-- Bootstrap core JavaScript-->
    <script src="fe-access/vendor/jquery/jquery.min.js"></script>
    <script src="fe-access/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="fe-access/vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="fe-access/js/sb-admin-2.min.js"></script>
</body>

</html>
