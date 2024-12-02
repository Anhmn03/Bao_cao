<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Tính lương nhân viên</title>
    <link href="/fe-access/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,300,400,600,700,800,900" rel="stylesheet">
    <link href="/fe-access/css/sb-admin-2.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Nunito', sans-serif;
            background-color: #f8f9fc;
            margin: 0;
            padding: 0;
        }

        .container-fluid {
            margin-top: 20px;
        }

        h1 {
            font-size: 2rem;
            font-weight: 700;
            text-align: center;
            margin-bottom: 20px;
        }
/* 
        .btn {
            font-size: 1rem;
            padding: 10px 20px;
            border-radius: 8px;
            width: 100%;
            display: inline-flex;
            justify-content: center;
            align-items: center;
            text-transform: uppercase;
            letter-spacing: 1px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease-in-out;
        } */

        .btn-primary {
            background-color: #4e73df;
            border-color: #4e73df;
            color: #fff;
        }

        .btn-primary:hover {
            background-color: #2e59d9;
            border-color: #2e59d9;
        }

        .btn-secondary {
            background-color: #6c757d;
            border-color: #6c757d;
            color: #fff;
        }

        .btn-secondary:hover {
            background-color: #5a6268;
            border-color: #545b62;
        }

        .btn i {
            margin-right: 8px;
        }

        .form-control {
            font-size: 1rem;
            padding: 10px;
            border-radius: 8px;
            border: 1px solid #ddd;
            margin-bottom: 10px;
        }

        .form-control:focus {
            border-color: #4e73df;
            box-shadow: 0 0 5px rgba(78, 115, 223, 0.5);
        }

        .row.mb-4 {
            margin-bottom: 20px;
        }

        .row {
            margin-left: 0;
            margin-right: 0;
        }

        .col-md-4 {
            padding-left: 0;
            padding-right: 0;
        }

        .table-container {
            margin: 30px auto;
            max-width: 1200px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            padding: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            background-color: #4e73df;
            color: #fff;
        }

        thead th {
            padding: 12px;
            text-align: center;
        }

        tbody tr {
            background-color: #f9f9f9;
            border-bottom: 1px solid #ddd;
        }

        tbody tr:hover {
            background-color: #e9ecef;
            cursor: pointer;
        }

        td {
            padding: 12px;
            text-align: center;
            color: #333;
        }

        .salary-cell {
            font-weight: bold;
            color: #4e73df;
        }

        .month-cell {
            font-style: italic;
        }
    </style>
</head>
<body id="page-top">
    <div id="wrapper">
        @include('fe_admin.slidebar') <!-- Thanh bên -->

        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                @include('fe_admin.topbar') <!-- Thanh trên -->

                <div class="container-fluid">
                    {{-- <h1>Tính lương nhân viên</h1> --}}

                    <!-- Back Button -->
                    <div class="row mb-4">
                        <!-- Quay lại Button -->
                        <div class="col-md-2">
                            <button onclick="window.history.back();" class="btn btn-secondary w-100">
                                <i class="fas fa-arrow-left"></i> Quay lại
                            </button>
                        </div>
                    
                        <!-- Tính lương Button -->
                        <div class="col-md-3">
                            <a href="{{ route('salary.calculate') }}" class="btn btn-primary w-100">
                                <i class="fas fa-calculator"></i> Tính lương
                            </a>
                        </div>
                        
                        <!-- Tính lương toàn bộ nhân viên Button -->
                        <div class="col-md-4">
                            <form action="{{ route('caculate.all') }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-calculator"></i> Tính lương toàn bộ nhân viên
                                </button>
                            </form>
                        </div>
                    </div>
                    
                    <!-- Search Form -->
                    <form method="GET" action="{{ route('salary_caculate') }}">
                        <div class="row mb-4">
                            <!-- Search by Name -->
                            <div class="col-md-3 " >
                                <input type="text" name="search_name" class="form-control"style="margin-bottom: 0px" placeholder="Tìm kiếm theo tên" value="{{ request('search_name') }}">
                            </div>
                    
                            <!-- Select Department -->
                            <div class="col-md-3">
                                <div class="form-group">
                                    <select name="search_department" id="search_department" class="form-control">
                                        <option value="">-- Chọn phòng ban --</option>
                                        @foreach($departments as $department)
                                            <option 
                                                value="{{ $department->name }}" 
                                                {{ request('search_department') == $department->name ? 'selected' : '' }}
                                            >
                                                {{ $department->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            
                    
                            <!-- Search Button -->
                            <div class="col-md-2" >
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-search h-70"></i> Tìm kiếm
                                </button>
                            </div>
                        </div>
                    </form>
               


                    <h1>Tính Lương Nhân Viên</h1>

                    <table class="table">
                        <thead>
                            <tr>
                                <th>STT</th>
                                <th>Tên nhân viên</th>
                                <th>Phòng ban</th>
                                <th>Hệ số lương</th>
                                <th>Số ngày làm việc hợp lệ</th>
                                <th>Số ngày làm việc không hợp lệ</th>
                                <th>Số tiền lương</th>
                                <th>Tháng</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($cacu_salaries as $salary)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $salary->user->name ?? 'N/A' }}</td>
                                <td>{{ $salary->user->department->name ?? 'N/A' }}</td>
                                <td>{{ $salary->user->salary->salaryCoefficient ?? 'N/A' }}</td>
                                <td>{{ $salary->valid_workdays }}</td>
                                <td>{{ $salary->invalid_workdays }}</td>
                                <td>{{ number_format($salary->salary_amount) }}</td>

                                <td>{{ $salary->month }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="d-flex justify-content-center">
                        {{ $cacu_salaries->links() }}
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
        <script src="fe-access/js/sb-admin-2.min.js"></script>
        
    </body>
</html>
