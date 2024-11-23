<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Quản lý các trường hợp không hợp lệ">
    <meta name="author" content="Your Company">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Quản lý các trường hợp không hợp lệ</title>

    <!-- Fonts and Styles -->
    <link href="/fe-access/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,300,400,600,700,800,900" rel="stylesheet">
    <link href="/fe-access/css/sb-admin-2.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #f8f9fc;
        }

        h3 {
            color: #4e73df;
        }

        .table th {
            background-color: #4e73df;
            color: white;
        }

        .btn-success, .btn-danger {
            margin: 0 5px;
        }

        .btn-submit {
            background-color: #4e73df;
            color: #fff;
        }

        .btn-submit:hover {
            background-color: #2e59d9;
        }

        .alert-dismissible .btn-close {
            padding: 1rem;
        }
    </style>
</head>

<body id="page-top">
    <!-- Wrapper -->
    <div id="wrapper">
        <!-- Sidebar -->
        @include('fe_admin.slidebar')

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">
            <!-- Main Content -->
            <div id="content">
                <!-- Topbar -->
                @include('fe_admin.topbar')

                <!-- Page Content -->
                <div class="container-fluid">
                    @if (session('message'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('message') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <h3 class="mb-4">Quản lý các trường hợp không hợp lệ</h3>

                    <!-- Table -->
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>STT</th>
                                <th>Nhân viên</th>
                                <th>Thời gian</th>
                                <th>Loại</th>
                                <th>Lý do giải trình</th>
                                <th>Thay đổi trạng thái</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($invalidAttendances as $attendance)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $attendance->user->name }}</td>
                                    <td>{{ $attendance->time }}</td>
                                    <td>{{ ucfirst($attendance->type) }}</td>
                                    <td>{{ $attendance->justification ?? 'Chưa có giải trình' }}</td>
                                    <td>
                                        @if ($attendance->justification)
                                            <!-- Approve Button -->
                                            <form action="{{ route('admin.approveAttendance', $attendance->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-success">Chấp nhận</button>
                                            </form>

                                            <!-- Reject Button -->
                                            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal-{{ $attendance->id }}">
                                                Từ chối
                                            </button>

                                            <!-- Modal -->
                                            <div class="modal fade" id="rejectModal-{{ $attendance->id }}" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="rejectModalLabel">Từ chối giải trình của {{ $attendance->user->name }}</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <form action="{{ route('admin.rejectAttendance', $attendance->id) }}" method="POST">
                                                            @csrf
                                                            <div class="modal-body">
                                                                <div class="form-group mb-3">
                                                                    <label for="rejection_reason">Chọn lý do từ chối:</label>
                                                                    <select name="rejection_reason" id="rejection_reason" class="form-select" onchange="toggleCustomReason()">
                                                                        <option value="">Chọn lý do</option>
                                                                        <option value="Không đến làm việc đúng giờ">Không đến làm việc đúng giờ</option>
                                                                        <option value="Nghỉ phép không thông báo">Nghỉ phép không thông báo</option>
                                                                        <option value="Vi phạm nội quy công ty">Vi phạm nội quy công ty</option>
                                                                        <option value="Khác">Khác</option>
                                                                    </select>
                                                                </div>
                                                                <div class="form-group" id="custom-reason-container" style="display: none;">
                                                                    <label for="custom_rejection_reason">Lý do khác:</label>
                                                                    <textarea name="custom_rejection_reason" id="custom_rejection_reason" class="form-control" rows="3"></textarea>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                                                                <button type="submit" class="btn btn-danger">Từ chối</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <button class="btn btn-secondary" disabled>Không có lý do</button>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <!-- Pagination -->
                    {{ $invalidAttendances->links() }}
                    <footer class="sticky-footer bg-white">
                        <div class="container my-auto text-center">
                            <span>&copy; {{ date('Y') }} Your Company. All Rights Reserved.</span>
                        </div>
                    </footer>
                </div>
            </div>
        </div>

        <!-- Footer -->

    </div>
    
    <!-- Scroll to Top -->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- JavaScript -->
    <script src="fe-access/vendor/jquery/jquery.min.js"></script>
<script src="fe-access/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="fe-access/vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.4/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="fe-access/js/sb-admin-2.min.js"></script>

    <script>
        function toggleCustomReason() {
            const reasonSelect = document.getElementById("rejection_reason");
            const customReasonContainer = document.getElementById("custom-reason-container");
            customReasonContainer.style.display = reasonSelect.value === "Khác" ? "block" : "none";
        }
    </script>
</body>

</html>
