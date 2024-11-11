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
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,300,400,600,700,800,900" rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="fe-access/css/sb-admin-2.min.css" rel="stylesheet">

    <style>
        /* Styles cho modal */
        .modal-dialog {
            max-width: 600px;
            margin: 1.75rem auto;
        }
        .modal-header, .modal-footer {
            display: flex;
            justify-content: space-between;
        }
        .modal-body {
            padding: 20px;
        }
    </style>
</head>

<body id="page-top">
    <div id="wrapper">
        @include('fe_user.slidebar') <!-- Sidebar -->

        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                @include('fe_admin.topbar') <!-- Topbar -->

                <div class="container-fluid">
                    <!-- Hiển thị thông báo nếu có -->
                    @if(session('message'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('message') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <!-- Form tìm kiếm theo ngày -->
                    <form method="GET" action="{{ route('attendance') }}">
                        <div class="form-group">
                            <label for="search_date">Tìm kiếm theo ngày:</label>
                            <input type="date" name="search_date" class="form-control" id="search_date" value="{{ request('search_date') }}">
                        </div>
                        <button type="submit" class="btn btn-primary">Tìm kiếm</button>
                    </form>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#reminderModal">
                        Đặt thời gian
                    </button>

                    <!-- Lịch sử Check In/Out -->
                    <div class="d-flex justify-content-between mb-4">
                        <h3 class="text-center">Lịch sử Check In/Out</h3>
                        <div class="d-flex justify-content-center gap-3 mb-4">
                            <form action="{{ route('attendance.checkin') }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-sign-in-alt"></i> Check In
                                </button>
                            </form>
    
                            <form action="{{ route('attendance.checkout') }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-danger btn-lg">
                                    <i class="fas fa-sign-out-alt"></i> Check Out
                                </button>
                            </form>
                        </div>
                        <h4 class="text-center">Ngày {{ date('d/m/Y') }}</h4>
                    </div>

                    <table class="table table-striped table-bordered text-center">
                        <thead class="table-dark">
                            <tr>
                                <th>STT</th>
                                <th>Nhân viên</th>
                                <th>Hoạt động</th>
                                <th>Thời gian</th>
                                <th>Trạng thái</th> <!-- New Status Column -->
                                <th>Lý do giải trình</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($attendances as $attendance)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $attendance->user->name }}</td>
                                <td>{{ ucfirst($attendance->type) }}</td> <!-- Checkin/Checkout -->
                                <td>{{ $attendance->time->format('H:i d/m/Y') }}</td>
                                <td>
                                    @if ($attendance->status == 0)
                                        <span class="badge badge-danger">Không hợp lệ</span>
                                    @elseif ($attendance->status == 1)
                                        <span class="badge badge-success">Hợp lệ</span>
                                    @elseif ($attendance->status == 2)
                                        <span class="badge badge-info">Lý do đã được chấp nhận</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($attendance->status == 0)
                                        <!-- Button to trigger modal -->
                                        <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#justificationModal-{{ $attendance->id }}">
                                            Giải trình
                                        </button>
                                        
                                        <!-- Modal for justification -->
                                        <div class="modal fade" id="justificationModal-{{ $attendance->id }}" tabindex="-1" aria-labelledby="justificationModalLabel" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="justificationModalLabel">Giải trình cho nhân viên {{ $attendance->user->name }}</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <form action="{{ route('attendance.addJustification', $attendance->id) }}" method="POST">
                                                        @csrf
                                                        <div class="modal-body">
                                                            <div class="form-group">
                                                                <label for="justificationTextarea">Lý do giải trình</label>
                                                                <textarea name="justification" id="justificationTextarea" class="form-control" placeholder="Nhập lý do giải trình" required></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                                                            <button type="submit" class="btn btn-primary">Gửi</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @elseif ($attendance->status == 2)
                                        <!-- Hiển thị lý do đã được chấp nhận -->
                                        <p>{{ $attendance->justification }}</p>
                                    @else
                                        --
                                    @endif
                                </td>
                                
                              </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{ $attendances->links('pagination::bootstrap-4') }}

                </div>
            </div>
            <div class="modal fade" id="reminderModal" tabindex="-1" aria-labelledby="reminderModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="reminderModalLabel">Chỉnh Sửa Thời Gian Nhắc Nhở</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <!-- Form nhắc nhở -->
                            <form action="{{ route('reminder.save') }}" method="POST">
                                @csrf
                                @method('POST')
                            
                                <div class="form-group">
                                    <label for="reminder_time">Thời gian nhắc nhở:</label>
                                    <input type="time" name="reminder_time" id="reminder_time" class="form-control" 
                                           value="{{ old('reminder_time', \Carbon\Carbon::parse(Auth::user()->reminder_time)->format('H:i')) }}" required>
                            
                                    @error('reminder_time')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                                    <button type="submit" class="btn btn-success">Lưu</button>
                                </div>
                            </form>
                            
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
    
    <!-- Bootstrap core JavaScript-->
    <script src="fe-access/vendor/jquery/jquery.min.js"></script>
    <script src="fe-access/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="fe-access/vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="fe-access/js/sb-admin-2.min.js"></script>

    <!-- Script to auto-show modal after submission -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            if (window.location.href.indexOf("justification-submitted=true") > -1) {
                var modalId = "{{ session('attendanceId') }}"; // Attendance ID saved after submission
                var modal = new bootstrap.Modal(document.getElementById("justificationModal-" + modalId));
                modal.show();
            }
        });
    </script>
</body>

</html>
