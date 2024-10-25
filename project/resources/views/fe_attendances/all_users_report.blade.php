
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
                    <table class="table table-bordered table-hover shadow-sm">
                        <thead class="thead-dark">
                            <tr>
                                <th>Tên nhân viên</th>
                                <th>Tổng số ngày làm việc</th>
                                <th>Số ngày đủ 8 tiếng</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($reportData as $data)
                                <tr>
                                    <td>{{ $data['name'] }}</td>
                                    <td>{{ $data['totalDays'] }}</td>
                                    <td>{{ $data['daysWithFullHours'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <!-- Điều hướng phân trang -->
                    <div class="d-flex justify-content-center mt-4">
                        {{ $users->links() }}
                    </div>
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