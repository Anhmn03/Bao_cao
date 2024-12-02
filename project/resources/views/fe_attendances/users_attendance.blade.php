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
    {{-- <script src="fe-access/vendor/jquery/jquery.min.js"></script> --}}
    {{-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script> --}}
    <!-- Custom fonts for this template-->
    {{-- <link href="fe-access/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css"> --}}
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,300,400,600,700,800,900" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="fe-access/css/sb-admin-2.min.css" rel="stylesheet">

    <style>
        .badge {
        font-size: 1rem; /* Kích thước chữ đồng nhất với các cột khác */
        padding: 0.5rem 0.75rem; /* Điều chỉnh padding để phù hợp với chiều cao của các hàng */
        display: inline-block;
    }
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
                    @if (session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif

                    <!-- Form tìm kiếm theo ngày -->
                    <!-- Form tìm kiếm theo ngày và Đặt thời gian trên cùng một dòng -->
                    <div class="d-flex align-items-center mb-4" >
                        <form method="GET" action="{{ route('attendance') }}" class="d-flex align-items-center">
                            <div class="form-group me-2"style="margin-bottom: 0px">
                                <label for="search_date" class="d-block">Tìm kiếm theo ngày:</label>
                                <input type="date" name="search_date" class="form-control" id="search_date" value="{{ request('search_date') }}">
                            </div>
                            
                            <button type="submit" class="btn btn-primary me-2 align-self-end">Tìm kiếm</button>
                        </form>
                    
                        <!-- Đặt thời gian button -->
                        <button type="button" class="btn btn-primary align-self-end" data-bs-toggle="modal" data-bs-target="#reminderModal">
                            Đặt thời gian
                        </button>
                        
                    </div>
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
                                <td>{{ $attendance->time }}</td>
                                <td>
                                    @if ($attendance->status == 0)
                                        <span class="badge badge-danger">Không hợp lệ</span>
                                    @elseif ($attendance->status == 1)
                                        <span class="badge badge-success">Hợp lệ</span>
                                    @elseif ($attendance->status == 2)
                                        <span class="badge badge-info">Đang xử lý</span>
                                    @elseif ($attendance->status == 3)
                                        <span class="badge badge-warning">Không hợp lệ</span>
                                      
                                    @endif
                                    {{-- @if($attendance->status == 0)
    <span class="badge bg-warning">Chờ xét duyệt</span>
@elseif($attendance->status == 1)
    <span class="badge bg-primary">Hợp lệ</span>
@elseif($attendance->status == 2)
    <span class="badge bg-success">Hợp lệ</span>
@elseif($attendance->status == 3)
    <span class="badge bg-danger">Không hợp lệ</span>
@endif --}}
                                    {{-- {{ dd($attendance->status) }} --}}
                                </td>
                                <td>
                                    @if ($attendance->justification)
                                        <!-- Display the existing justification if it already exists -->
                                        <p id="justificationDisplay-{{ $attendance->id }}">{{ $attendance->justification }}</p>
                                    @else
                                        <!-- If status is invalid and no justification exists, allow user to add one -->
                                        @if ($attendance->status == 0)
                                            <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#justificationModal-{{ $attendance->id }}">
                                                Giải trình
                                            </button>
                                
                                            <!-- Modal for adding justification -->
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
                                                                    <label for="justificationReason">Chọn lý do giải trình:</label>
                                                                    <select name="justification_reason" id="justificationReason-{{ $attendance->id }}" class="form-control" required>
                                                                        <option value="">Chọn lý do</option>
                                                                        <option value="Hôm nay tôi xin phép đến muộn">Đến muộn</option>
                                                                        <option value="Lý do xin về sớm vì nhà có công việc đột xuất.">Về sớm</option>
                                                                        <option value="Other">Khác</option>
                                                                    </select>
                                                                    
                                                                    <div class="form-group" id="otherJustification" style="display: none;">
                                                                        <label for="otherJustificationTextarea">Lý do khác:</label>
                                                                        <textarea name="other_justification" id="otherJustificationTextarea" class="form-control" placeholder="Nhập lý do khác"></textarea>
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
                                        @endif
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
            <script>
         document.addEventListener("DOMContentLoaded", function () {
    // Gắn event listener cho tất cả các dropdown trong modal
    document.querySelectorAll('[id^="justificationReason"]').forEach(function (selectElement) {
        selectElement.addEventListener("change", function () {
            // Lấy modal chứa dropdown hiện tại
            var modal = this.closest(".modal");

            // Lấy phần 'Other' justification
            var otherJustificationDiv = modal.querySelector(".form-group#otherJustification");
            var otherJustificationTextarea = modal.querySelector("textarea#otherJustificationTextarea");

            // Kiểm tra giá trị đã chọn
            if (this.value === "Other") {
                otherJustificationDiv.style.display = "block"; // Hiển thị textarea
                otherJustificationTextarea.required = true;    // Đặt yêu cầu nhập
            } else {
                otherJustificationDiv.style.display = "none";  // Ẩn textarea
                otherJustificationTextarea.required = false;   // Xóa yêu cầu nhập
            }
        });
    });
});


            </script>
           
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
    // Kiểm tra nếu có attendanceId trong session
    if ("{{ session('attendanceId') }}") {
        var modalId = "{{ session('attendanceId') }}";
        var modal = new bootstrap.Modal(document.getElementById('justificationModal-' + modalId));
        modal.show();
        
        // Đặt nội dung giải trình vào modal hoặc hiển thị ở bảng nếu cần
        var justificationText = "{{ session('justification') }}";
        document.getElementById('justificationDisplay-' + modalId).textContent = justificationText;
    }
});
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
    var reminderModal = document.getElementById("reminderModal");
    if (!reminderModal) {
        console.error("Modal 'reminderModal' không tồn tại.");
    }
});
    </script>
</body>

</html>