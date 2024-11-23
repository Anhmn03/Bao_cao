<!DOCTYPE html>
<html lang="en">

<head>
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
                    <!-- Nút quay lại -->
                    <div class="d-flex justify-content-start mt-4">
                        <button onclick="window.history.back();" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Quay lại
                        </button>
                    </div>

                    <!-- Tiêu đề -->
                    <h1 class="text-center my-4">Tính Lương Toàn Bộ Nhân Viên</h1>

                    <!-- Thông báo -->
                    @if(session('success'))
                        <div class="alert alert-success text-center my-3">
                            {{ session('success') }}
                        </div>
                    @endif

                    <!-- Bảng thông tin -->
                    <div class="table-responsive">
                        <table class="table table-bordered text-center">
                            <thead class="thead-dark">
                                <tr>
                                    <th>Tên nhân viên</th>
                                    <th>Ngày công hợp lệ</th>
                                    <th>Ngày công không hợp lệ</th>
                                    <th>Hệ số lương</th>
                                    <th>Lương tạm tính</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($calculatedSalaries as $salary)
                                    <tr>
                                        <td>{{ $salary['user_name'] }}</td>
                                        <td>{{ $salary['valid_workdays'] }}</td>
                                        <td>{{ $salary['invalid_workdays'] }}</td>
                                        <td>{{ $salary['salary_coefficient'] }}</td>
                                        <td>{{ $salary['salary_amount'], 0, ',', '.' }} VND</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Nút lưu trữ -->
                    <div class="text-center mt-4">
                        <form method="POST" action="{{ route('save.salary') }}">
                            @csrf
                            <button type="submit" class="btn btn-success px-4 py-2">
                                <i class="fas fa-save"></i> Lưu Lương
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>


        <!-- Footer -->
        
    </div>
    <footer class="sticky-footer bg-white">
        <div class="container my-auto">
            <div class="copyright text-center my-auto">
                <span>© {{ date('Y') }} Your Company. All Rights Reserved.</span>
            </div>
        </div>
    </footer>

    <!-- Bootstrap core JavaScript -->
    <script src="/fe-access/vendor/jquery/jquery.min.js"></script>
    <script src="/fe-access/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript -->
    <script src="/fe-access/vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages -->
    <script src="/fe-access/js/sb-admin-2.min.js"></script>
</body>

</html>
