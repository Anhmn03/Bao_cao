<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Admin - Danh sách bậc lương</title>

    <!-- Custom fonts for this template-->
    <link href="/fe-access/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,300,400,600,700,800,900" rel="stylesheet">
    <link href="/fe-access/css/sb-admin-2.min.css" rel="stylesheet">

    <!-- Include Cleave.js -->
    <script src="https://cdn.jsdelivr.net/npm/cleave.js"></script>
</head>

<body id="page-top">
    <div id="wrapper">
        @include('fe_admin.slidebar')

        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                @include('fe_admin.topbar')

                <div class="container-fluid">
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <h2 class="text-center">Thông tin lương của nhân viên</h2>
        <table class="table table-bordered mt-4">
            <tr>
                <th>Tên nhân viên</th>
                <td>{{ $userName }}</td>
            </tr>
            <tr>
                <th>Phòng ban</th>
                <td>{{ $department }}</td>
            </tr>
            <tr>
                <th>Hệ số lương</th>
                <td>{{ $salaryCoefficient }}</td>
            </tr>
            <tr>
                <th>Ngày công hợp lệ</th>
                <td>{{ $validWorkdays }}</td>
            </tr>
            <tr>
                <th>Ngày công kh hợp lệ </th>
                <td>{{ $invalidWorkdays }}</td>
            </tr>
            <tr>
                <th>Lương hàng tháng</th>
                <td>{{$salaryAmount}} VND</td>
            </tr>
        </table>
        <div class="text-center">
            <a href="{{ url()->previous() }}" class="btn btn-secondary">Quay lại</a>
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
<script src="fe-access/vendor/jquery/jquery.min.js"></script>
<script src="fe-access/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="fe-access/vendor/jquery-easing/jquery.easing.min.js"></script>
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
<script src="fe-access/js/sb-admin-2.min.js"></script>
</body>

</html>
