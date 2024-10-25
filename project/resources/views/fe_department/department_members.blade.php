<!DOCTYPE html>
<html lang="vi">

<head>
    <style>
        table.table tbody tr:hover {
            background-color: #f2f2f2; /* Màu khi hover */
            transition: background-color 0.3s ease-in-out;
        }
    
        table.table tbody tr.selected {
            background-color: #d1ecf1; /* Màu highlight khi được chọn */
        }
    
        .btn {
            transition: transform 0.2s;
        }
    
        .btn:hover {
            transform: scale(1.05); /* Phóng to nhẹ khi hover */
        }
    </style>
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
        @include('fe_admin.slidebar') <!-- Thanh bên -->

        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                @include('fe_admin.topbar') <!-- Thanh trên -->

                <div class="container-fluid">
                    <button onclick="window.history.back();" class="btn btn-secondary mt-4">
                        <i class="fas fa-arrow-left"></i> Quay lại
                    </button>
                    @if($department)
                    <h1 class="mt-4">Danh sách thành viên: {{ $department->name }}</h1>
                
                    <div class="mb-3">
                        <span>Trạng thái phòng ban: </span>
                        <span class="badge {{ $department->status ? 'bg-success' : 'bg-secondary' }}">
                            {{ $department->status ? 'Hoạt động' : 'Không hoạt động' }}
                        </span>
                        <!-- Form thay đổi trạng thái -->
                        <form action="{{ route('departments.updateStatus', $department->id) }}" method="POST" class="d-inline-block">
                            @csrf
                            @method('PATCH')  <!-- Thêm method PATCH -->
                        
                            <input type="hidden" name="status" value="{{ $department->status ? 0 : 1 }}">
                            <button type="submit" class="btn btn-sm {{ $department->status ? 'btn-danger' : 'btn-success' }}">
                                {{ $department->status ? 'Tắt' : 'Bật' }}
                            </button>
                        </form>
                    </div>
                
                    <div class="card mt-4">
                        <div class="card-body table-responsive">
                            <table class="table table-bordered table-hover align-middle">
                                <thead class="thead-light">
                                    <tr>
                                        <th>STT</th>
                                        <th>Tên</th>
                                        <th>Email</th>
                                        <th>Số điện thoại</th>
                                        <th>Chức vụ</th>
                                        <th>Tổ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($users as $user)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>{{ $user->phone_number }}</td>
                                        <td>{{ $user->position }}</td>
                                        <td>            @if ($user->department)
                                            {{ $user->department->name }} 
                                        @else
                                            Chưa xác định
                                        @endif
                                      </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">Không có thành viên nào trong các phòng ban này.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            </table>
                        </div>
                    </div>
                    @else
                        <div class="alert alert-danger mt-4">
                            Phòng ban không tồn tại hoặc đã bị xóa.
                        </div>
                    @endif
                </div>
            </div>
            <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="deleteModalLabel">Xác nhận xóa</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            Bạn có chắc chắn muốn xóa thành viên này không?
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                            <button type="button" class="btn btn-danger">Xóa</button>
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

    <!-- JavaScript -->
    <script src="fe-access/vendor/jquery/jquery.min.js"></script>
    <script src="fe-access/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="fe-access/vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="fe-access/js/sb-admin-2.min.js"></script>
</body>
</html>