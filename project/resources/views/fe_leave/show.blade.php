<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Cấu hình ngày nghỉ phép</title>

    <!-- Custom fonts for this template -->
    <link href="fe-access/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,300,400,600,700,800,900" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Custom styles for this template -->
    <link href="fe-access/css/sb-admin-2.min.css" rel="stylesheet">

    <style>
        .card-header {
            background-color: #f8f9fc;
            font-weight: bold;
            font-size: 1.2rem;
        }

        .card-body p {
            margin-bottom: 0.5rem;
        }

        .btn-primary {
            background-color: #4e73df;
            border-color: #4e73df;
        }

        .btn-primary:hover {
            background-color: #2e59d9;
            border-color: #2653d4;
        }

        .row .card {
            margin-bottom: 0;
        }

        .modal-content {
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
                    @if(session('warning'))
    <div class="alert alert-warning">
        {{ session('warning') }}
    </div>
@endif

                    @if (session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif
                    @if(session('success'))
                    <div class="alert alert-success mt-3">
                        {{ session('success') }}
                    </div>
                    @endif
                    <!-- Nội dung chính -->
                    <h1 class="h3 mb-4 text-gray-800">Cấu hình ngày nghỉ phép</h1>
                    

                    <!-- Phần song song -->
                    <div class="row mt-4">
                        <!-- Thông tin nhân viên -->
                        <div class="col-md-5">
                            <div class="card">
                                <div class="card-header">Thông tin nhân viên</div>
                                <div class="card-body">
                                    <p><strong>Tên:</strong> {{ $user->name }}</p>
                                    <p><strong>Email:</strong> {{ $user->email }}</p>
                                    <p><strong>Phòng:</strong> {{ $user->department->name }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Thông tin ngày nghỉ -->
                        <div class="col-md-7">
                            <div class="card">
                                <div class="d-flex justify-content-between align-items-center card-header">
                                    <span>Thông tin ngày nghỉ phép</span>
                                    <!-- Button trigger modal -->
                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#leaveRequestModal">
                                        Đơn xin nghỉ
                                    </button>
                                </div>
                                <div class="card-body">
                                    <p><strong>Số ngày nghỉ phép trong năm:</strong> {{ $maxLeaveDays }} ngày</p>
                                    <p><strong>Số ngày nghỉ đã sử dụng:</strong> {{ $usedLeaveDays }} ngày</p>
                                    <p><strong>Số ngày nghỉ còn lại:</strong> {{ $remainingLeaveDays }} ngày</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Danh sách đơn xin nghỉ -->
                    <div class="card mt-4">
                        <div class="card-header">
                            <h5>Danh sách đơn xin nghỉ</h5>
                        </div>
                        <div class="card-body">
                            @if ($leaveRequests->isEmpty())
                                <p>Không có đơn xin nghỉ nào.</p>
                            @else
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>STT</th>
                                            <th>Lý do</th>
                                            <th>Ngày bắt đầu</th>
                                            <th>Ngày kết thúc</th>
                                            <th>Trạng thái</th>
                                            <th>Ngày gửi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($leaveRequests as $index => $request)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>
                                                    @if($request->reason == 'Khác')
                                                        {{ $request->other_reason }} <!-- Hiển thị lý do người dùng nhập -->
                                                    @else
                                                        {{ $request->reason }} <!-- Hiển thị lý do đã chọn -->
                                                    @endif
                                                </p></td>
                                                <td>{{ $request->start_date }}</td>
                                                <td>{{ $request->end_date }}</td>
                                                <td>
                                                    @if ($request->status === '0')
                                                        <span class="badge bg-warning">Đang chờ</span>
                                                    @elseif ($request->status === '1')
                                                        <span class="badge bg-success">Đã duyệt</span>
                                                        @elseif ($request->status === '2')
                                                        <span class="badge bg-danger">Từ chối</span>
                                                    @endif
                                                </td>
                                                <td>{{ $request->created_at->format('d/m/Y') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @endif
                        </div>
                    </div>

                    <!-- Modal for Leave Request -->
                    <div class="modal fade" id="leaveRequestModal" tabindex="-1" aria-labelledby="leaveRequestModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="leaveRequestModalLabel">Đơn xin nghỉ phép</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <form action="{{ route('leave.store') }}" method="POST">
                                    @csrf
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label for="reason" class="form-label">Lý do nghỉ</label>
                                            <select id="reason" name="reason" class="form-control" required>
                                                <option value="">Chọn lý do</option>
                                                <option value="Nghỉ thai sản">Nghỉ thai sản</option>
                                                <option value="Nghỉ kết hôn">Nghỉ kết hôn</option>
                                                <option value="Sức khỏe không ổn định">Sức khỏe không ổn định</option>
                                                <option value="Khác">Khác</option>
                                            </select>
                                        </div>

                                        <!-- Ô text ẩn, chỉ hiện khi chọn "Khác" -->
                                        {{-- <div id="otherReasonContainer" class="mb-3" style="display: none;">
                                            <label for="custom_reason" class="form-label">Nhập lý do khác</label>
                                            <input type="text" id="custom_reason" name="custom_reason" class="form-control">
                                        </div> --}}
                                        <div class="form-group" id="other-reason-group" style="display:none;">
                                            <label for="other-reason">Lý do khác:</label>
                                            <input type="text" class="form-control" id="other-reason" name="other_reason">
                                        </div>
                                        {{-- <div id="otherReasonContainer" style="display: none;">
                                            <label for="other_reason" class="label">Lý do khác:</label>
                                            <input id="other_reason" name="other_reason" placeholder="Nhập lý do khác tại đây..." rows="4" class="input-textarea"></input>
                                        </div> --}}

                                        <div class="mb-3">
                                            <label for="start_date" class="form-label">Ngày bắt đầu</label>
                                            <input type="date" id="start_date" name="start_date" class="form-control" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="end_date" class="form-label">Ngày kết thúc</label>
                                            <input type="date" id="end_date" name="end_date" class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-primary">Gửi đơn</button>
                                    </div>
                                </form>
                            </div>
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

{{-- <script>
document.addEventListener('DOMContentLoaded', function () {
    const reasonSelect = document.getElementById('reason');
    const otherReasonContainer = document.getElementById('otherReasonContainer');
    const otherReasonInput = document.getElementById('other_reason');
    const startDateInput = document.getElementById('start_date');
    const endDateInput = document.getElementById('end_date');

    reasonSelect.addEventListener('change', function () {
        const reason = this.value;
        const startDate = new Date(startDateInput.value);

        if (reason === 'Nghỉ thai sản' || reason === 'Nghỉ kết hôn') {
            if (startDateInput.value) {
                let endDate = new Date(startDate);
                if (reason === 'Nghỉ thai sản') {
                    // Nghỉ thai sản: +180 ngày
                    endDate.setDate(endDate.getDate() + 180);
                } else if (reason === 'Nghỉ kết hôn') {
                    // Nghỉ kết hôn: +3 ngày
                    endDate.setDate(endDate.getDate() + 3);
                }
                // Cập nhật ngày kết thúc tự động
                endDateInput.value = endDate.toISOString().split('T')[0];
                endDateInput.readOnly = true; // Không cho phép chỉnh sửa ngày kết thúc
            } else {
                // Nếu ngày bắt đầu chưa được chọn
                alert('Vui lòng chọn ngày bắt đầu trước.');
                reasonSelect.value = ''; // Reset lựa chọn lý do
            }
        } else if (reason === 'Khác') {
            // Hiển thị ô nhập lý do khác khi chọn "Khác"
            otherReasonContainer.style.display = 'block';
            otherReasonInput.required = true;
        } else {
            // Ẩn ô nhập lý do khác nếu không chọn "Khác"
            otherReasonContainer.style.display = 'none';
            otherReasonInput.required = false;

            // Kiểm tra nếu người dùng không chọn lý do nghỉ thì không cho phép tiếp tục
            if (!startDateInput.value || !endDateInput.value) {
                alert("Vui lòng chọn ngày bắt đầu và ngày kết thúc.");
            }
        }
    });

    startDateInput.addEventListener('change', function () {
        if (new Date(startDateInput.value) > new Date(endDateInput.value)) {
            alert("Ngày kết thúc phải lớn hơn ngày bắt đầu!");
        }
    });
});


</script> --}}

{{-- <script>
    // Hàm xử lý thay đổi lựa chọn lý do
    function handleReasonChange() {
        const reasonSelect = document.getElementById('reason');
        const otherReasonContainer = document.getElementById('otherReasonContainer');

        if (reasonSelect.value === "Khác") {
            otherReasonContainer.style.display = "block"; // Hiển thị ô nhập lý do khác
        } else {
            otherReasonContainer.style.display = "none"; // Ẩn ô nhập lý do khác
        }
    }

    // Gắn sự kiện khi tài liệu đã sẵn sàng
    document.addEventListener('DOMContentLoaded', function () {
        const reasonSelect = document.getElementById('reason');
        reasonSelect.addEventListener('change', handleReasonChange); // Gọi hàm khi thay đổi lựa chọn lý do
        handleReasonChange(); // Gọi hàm ngay khi trang load, để kiểm tra nếu lý do đã được chọn là "Khác"
    });
</script> --}}

<script>
    // function handleReasonChange() {
    //     const reasonSelect = document.getElementById('reason');
    //     const otherReasonContainer = document.getElementById('otherReasonContainer');

    //     if (reasonSelect.value === "Khác") {
    //         otherReasonContainer.style.display = "block";
    //     } else {
    //         otherReasonContainer.style.display = "none";
    //     }
    // }

    // document.addEventListener('DOMContentLoaded', function () {
    //     const reasonSelect = document.getElementById('reason');
    //     reasonSelect.addEventListener('change', handleReasonChange);
    //     handleReasonChange(); // Kiểm tra giá trị khi tải trang
    // });
    // Khi lý do được thay đổi
document.getElementById('reason').addEventListener('change', function () {
    var reason = this.value;
    var otherReasonGroup = document.getElementById('other-reason-group');

    // Kiểm tra nếu lý do là "Khác"
    if (reason === 'Khác') {
        otherReasonGroup.style.display = 'block';  // Hiển thị ô nhập lý do khác
    } else {
        otherReasonGroup.style.display = 'none';  // Ẩn ô nhập lý do khác
    }
});

// Kiểm tra nếu đã điền lý do "Khác" khi load trang
window.onload = function () {
    var reason = document.getElementById('reason').value;
    if (reason === 'Khác') {
        document.getElementById('other-reason-group').style.display = 'block';
    }
};

</script>

</body>
</html>
