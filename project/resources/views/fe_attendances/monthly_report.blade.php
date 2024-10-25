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
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,300,400,600,700,800,900" rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="/fe-access/css/sb-admin-2.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fc; /* Màu nền sáng */
        }
        h2 {
            color: #4e73df; /* Màu tiêu đề */
        }
        table {
            width: 100%; /* Đặt chiều rộng bảng 100% */
            border-collapse: collapse; /* Kết hợp biên bảng */
            margin-top: 20px; /* Khoảng cách trên bảng */
        }
        th, td {
            text-align: center; /* Căn giữa nội dung */
            padding: 12px; /* Khoảng cách trong ô */
            border: 1px solid #dddddd; /* Biên mờ cho ô */
        }
        th {
            background-color: #4e73df; /* Màu nền tiêu đề bảng */
            color: white; /* Màu chữ tiêu đề bảng */
        }
        tr:nth-child(even) {
            background-color: #f2f2f2; /* Màu nền hàng chẵn */
        }
        tr:hover {
            background-color: #e9ecef; /* Màu nền khi di chuột qua hàng */
        }
        .form-group {
            margin-bottom: 20px; /* Khoảng cách dưới các nhóm input */
        }
        .form-control {
            width: auto; /* Chiều rộng tự động cho input */
            display: inline-block; /* Hiển thị inline */
            margin-right: 10px; /* Khoảng cách giữa các input */
        }
        .btn-submit {
            padding: 10px 20px; /* Khoảng cách nút */
            background-color: #4e73df; /* Màu nền nút */
            color: white; /* Màu chữ nút */
            border: none; /* Không có biên */
            border-radius: 5px; /* Bo góc cho nút */
            cursor: pointer; /* Con trỏ chuột khi hover */
        }
        .btn-submit:hover {
            background-color: #2e59d9; /* Màu nền khi hover */
        }
        .table-responsive {
            overflow-x: auto; /* Thêm thanh cuộn ngang */
            margin-top: 20px; /* Khoảng cách trên bảng */
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
                    <h2 style="text-align: center;">Báo cáo giờ làm việc</h2>

                    <form action="{{ route('attendance.monthlyReport', ['month' => '', 'year' => '']) }}" method="GET" style="text-align: center; margin-bottom: 20px;">
                        <div class="form-group">
                            <label for="month">Tháng:</label>
                            <input type="number" id="month" name="month" class="form-control" min="1" max="12" value="{{ $month ?? now()->month }}">
                            
                            <label for="year">Năm:</label>
                            <input type="number" id="year" name="year" class="form-control" value="{{ $year ?? now()->year }}">
                            
                            <button type="submit" class="btn-submit">Xem báo cáo</button>
                        </div>
                    </form>
                
                    <div class="table-responsive"> <!-- Bọc bảng trong div có class table-responsive -->
                        <table>
                            <thead>
                                <tr>
                                    <th rowspan="2">Họ và tên</th>
                                    <th rowspan="2">Chức vụ</th>
                                    <th colspan="31">Ngày trong tháng</th>
                                </tr>
                                <tr>
                                    @for ($i = 1; $i <= 31; $i++)
                                        <th>{{ $i }}</th>
                                    @endfor
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>{{ $attendanceData['name'] }}</td>
                                    <td>{{ $attendanceData['position'] }}</td>
                                    @for ($day = 1; $day <= 31; $day++)
                                        @php
                                            // Kiểm tra xem nhân viên có giờ làm việc cho ngày này không
                                            $hours = isset($attendanceData['attendance'][$day]) ? $attendanceData['attendance'][$day] : null;
                                        @endphp
                                        <td>{{ $hours !== null ? $hours . ' giờ' : '' }}</td>
                                    @endfor
                                </tr>
                            </tbody>
                        </table>
                    </div> <!-- Kết thúc div table-responsive -->

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

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Bootstrap core JavaScript-->
    <script src="fe-access/vendor/jquery/jquery.min.js"></script>
    <script src="fe-access/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="fe-access/vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="fe-access/js/sb-admin-2.min.js"></script>
</body>

</html>