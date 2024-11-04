<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Admin - Danh sách bậc lương</title>

    <!-- Custom fonts and styles-->
    <link href="/fe-access/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,300,400,600,700,800,900" rel="stylesheet">
    <link href="/fe-access/css/sb-admin-2.min.css" rel="stylesheet">

    <style>
        .input-group {
            border-radius: 50px;
            overflow: hidden;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .input-group input {
            border: none;
            padding: 15px;
            font-size: 16px;
        }

        .input-group input:focus {
            outline: none;
            box-shadow: none;
        }

        .input-group .btn {
            background-color: #4e73df;
            color: white;
            border: none;
            border-radius: 0;
        }

        .input-group .btn:hover {
            background-color: #2e59d9;
        }

        .action-btns a, .action-btns button {
            margin-right: 5px;
        }

        .action-btns .btn {
            border-radius: 5px;
        }
    </style>
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

                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">Danh sách Bậc Lương</h1>
                        <div class="d-flex align-items-center">
                            <a href="{{ route('salary.create') }}" class="btn btn-primary mr-2">Thêm cấp bậc lương</a>
                    
                            <form method="GET" action="{{ route('salary') }}" class="form-inline">
                                <div class="form-group mb-2">
                                    <label for="search_salary" class="sr-only">Nhập tên cấp bậc:</label>
                                    <input type="text" class="form-control mr-2" id="search_salary" name="search_salary" value="{{ request()->input('search_salary') }}" placeholder="Nhập tên cấp bậc">
                                </div>
                                <button type="submit" class="btn btn-primary mb-2">
                                    <i class="fas fa-search"></i> <!-- Thay đổi icon ở đây -->
                                </button>
                            </form>
                        </div>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="thead-dark">
                                <tr>
                                    <th>STT</th>
                                    <th>Bậc</th>
                                    <th>Lương Tháng</th>
                                    <th>Lương Ngày</th>
                                    <th>Chi tiết </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($salarylevels as $salaryLevel)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $salaryLevel->name }}</td>
                                        <td>{{ number_format($salaryLevel->monthly_salary, 0, ',', '.') }} VND</td>
                                        <td>{{ number_format($salaryLevel->daily_salary, 0, ',', '.') }} VND</td>
                                        <td class="action-btns">
                                            <a href="{{ route('salary.show', $salaryLevel->id) }}" class="btn btn-primary">
                                                <i class="fas fa-info-circle"></i>
                                            </a>
                                            {{-- <form action="{{ route('salary_levels.destroy', $salaryLevel) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <i class="fas fa-trash-alt"></i> Xóa
                                                </button>
                                            </form> --}}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
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

    <script>
        function formatCurrency(input) {
            let value = input.value.replace(/\D/g, ''); // Chỉ giữ lại số
            input.value = new Intl.NumberFormat('vi-VN').format(value); // Định dạng theo VN
        }
    </script>

    <!-- Bootstrap core JavaScript-->
    <script src="fe-access/vendor/jquery/jquery.min.js"></script>
    <script src="fe-access/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="fe-access/vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="fe-access/js/sb-admin-2.min.js"></script>
</body>

</html>
