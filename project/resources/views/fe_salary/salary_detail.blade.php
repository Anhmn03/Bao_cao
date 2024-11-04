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
                <div class="d-flex justify-content-between">
                    <a href="{{ route('salary') }}" class="btn btn-secondary">Quay Lại</a>
                </div>
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

                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">Chi tiết Bậc Lương</h1>
                        <!-- Nút chỉnh sửa mở modal -->
                        <button class="btn btn-primary" data-toggle="modal" data-target="#editModal">Chỉnh sửa</button>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            Thông tin cấp bậc
                        </div>
                        <div class="card-body">
                            <h5 class="card-title">Tên cấp bậc: {{ $salaryLevel->name }}</h5>
                            <p class="card-text">Lương tháng: {{ number_format($salaryLevel->monthly_salary, 0, '.', ',') }} VND</p>
                            <p class="card-text">Lương ngày: {{ number_format($salaryLevel->daily_salary, 0, '.', ',') }} VND</p>
                            <p class="card-text">Trạng thái: 
                                {{ $salaryLevel->status == 1 ? 'Hoạt động' : 'Vô hiệu hóa' }}
                            </p>                            <p class="card-text">Ngày tạo: {{ $salaryLevel->created_at->format('d/m/Y') }}</p>
                            <p class="card-text">Người tạo: {{ $salaryLevel->creator ? $salaryLevel->creator->name : 'N/A' }}</p>
                            <p class="card-text">Ngày cập nhật: {{ $salaryLevel->updated_at->format('d/m/Y') }}</p>
                            <p class="card-text">Người cập nhật: {{ $salaryLevel->updater ? $salaryLevel->updater->name : 'N/A' }}</p>
                        </div>
                    </div>

                    <!-- Modal Chỉnh sửa -->
                    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="editModalLabel">Chỉnh sửa bậc lương</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <form action="{{ route('salary.update', $salaryLevel->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="form-group">
                                            <label for="name">Tên cấp bậc</label>
                                            <input type="text" class="form-control" id="name" name="name" value="{{ $salaryLevel->name }}" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="monthly_salary">Lương tháng</label>
                                            <input type="text" class="form-control" id="monthly_salary" name="monthly_salary" value="{{ number_format($salaryLevel->monthly_salary, 0, '.', ',') }}" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="daily_salary">Lương ngày</label>
                                            <input type="text" class="form-control" id="daily_salary" name="daily_salary" value="{{ number_format($salaryLevel->daily_salary, 0, '.', ',') }}" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="status">Trạng thái</label>
                                            <select name="status" class="form-control" required>
                                                <option value="1">Hoạt động</option>
                                                <option value="0">Vô hiệu hóa</option>
                                            </select>
                                        </div>
                                        <button type="submit" class="btn btn-primary">Cập nhật</button>
                                    </form>
                                </div>
                            </div>
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

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Bootstrap core JavaScript-->
    <script src="fe-access/vendor/jquery/jquery.min.js"></script>
    <script src="fe-access/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="fe-access/vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <!-- Bootstrap core JavaScript-->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
    <script src="fe-access/js/sb-admin-2.min.js"></script>
</body>

</html>
