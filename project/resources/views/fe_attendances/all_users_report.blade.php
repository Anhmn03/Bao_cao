
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Danh sách người dùng</title>

    <!-- Custom fonts -->
    <link href="/fe-access/vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,300,400,600,700,800,900" rel="stylesheet">

    <!-- Custom styles -->
    <link href="/fe-access/css/sb-admin-2.min.css" rel="stylesheet">
</head>

<body id="page-top">
    <div id="wrapper">
        @include('fe_admin.slidebar') <!-- Sidebar -->

        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                @include('fe_admin/topbar') <!-- Topbar -->

                <div class="container-fluid">
                    <h1 class="text-center mb-5 text-primary font-weight-bold">
                        Báo cáo chấm công của tất cả nhân viên
                    </h1>

                    <!-- Thông báo lỗi -->
                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    <!-- Bảng báo cáo -->
                    <form action="{{ route('reportAllUsers') }}" method="GET">
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="department" class="form-label">Phòng ban</label>
                                <select id="department" name="department" class="form-select">
                                    <option value="">Tất cả</option>
                                    <!-- Giả sử bạn có danh sách phòng ban -->
                                    @foreach($departments as $department)
                                        <option value="{{ $department->id }}">{{ $department->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="start_date" class="form-label">Từ ngày</label>
                                <input type="date" id="start_date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                            </div>
                            <div class="col-md-4">
                                <label for="end_date" class="form-label">Đến ngày</label>
                                <input type="date" id="end_date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Lọc</button>
                    </form>
                
                    <!-- Thông báo lỗi nếu có -->
                    @if(session('error'))
                        <div class="alert alert-danger mt-3">{{ session('error') }}</div>
                    @endif
                
                    <!-- Bảng báo cáo -->
                    <table class="table table-bordered mt-4">
                        <thead>
                            <tr>
                                <th>Họ và tên</th>
                                <th>Chức vụ</th>
                                <th>Ngày tháng</th>
                                <th>Check-In</th>
                                <th>Check-Out</th>
                                <th>Số giờ làm việc</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($reportData as $data)
                                @foreach($data['attendances'] as $attendance)
                                    <tr>
                                        <td>{{ $data['name'] }}</td>
                                        <td>{{ $data['position'] }}</td> <!-- Chức vụ -->
                                        <td>{{ $attendance->time->format('Y-m-d') }}</td>
                                        <td>{{ $attendance->type == 'in' ? $attendance->time->format('H:i') : '-' }}</td>
                                        <td>{{ $attendance->type == 'out' ? $attendance->time->format('H:i') : '-' }}</td>
                                        <td>
                                            @if($attendance->type == 'out' && isset($data['checkInTime']) && isset($data['checkOutTime']))
                                                {{ $attendance->time->diffInHours($data['checkInTime']) }} giờ
                                            @else
                                                0 giờ
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
                
                    <!-- Phân trang -->
                    <div class="mt-4">
                        {{ $users->links() }}
                    </div>
            </div>

            <!-- Footer -->
            <footer class="sticky-footer bg-white mt-auto">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>© {{ date('Y') }} Your Company. All Rights Reserved.</span>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <!-- Scroll to Top Button -->
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