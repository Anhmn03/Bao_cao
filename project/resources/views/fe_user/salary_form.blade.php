<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Thêm Người Dùng</title>
    <link href="/fe-access/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,300,400,600,700,800,900" rel="stylesheet">
    <link href="/fe-access/css/sb-admin-2.min.css" rel="stylesheet">

    <style>
        body {
            font-family: 'Nunito', sans-serif;
        }

        h1 {
            font-size: 2rem;
            font-weight: 700;
            margin: 20px 0;
            text-align: center;
        }

        .btn {
            font-size: 1rem;
            padding: 10px 20px;
            border-radius: 5px;
        }

        .btn-secondary {
            margin-right: 10px;
        }

        .btn-primary {
            background-color: #4e73df;
            border-color: #4e73df;
            color: #fff;
        }

        .btn-primary:hover {
            background-color: #2e59d9;
        }

        .table-container {
            margin: 20px auto;
            max-width: 1000px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 0;
        }

        thead {
            background-color: #4e73df;
            color: #fff;
        }

        thead th {
            font-weight: 700;
            padding: 15px;
            text-align: center;
        }

        tbody tr {
            background-color: #f9f9f9;
            border-bottom: 1px solid #ddd;
        }

        tbody tr:nth-child(even) {
            background-color: #f1f1f1;
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
                    <button onclick="window.history.back();" class="btn btn-secondary mt-4">
                        <i class="fas fa-arrow-left"></i> Quay lại
                    </button>
                    <a href="{{ route('salary_cacu') }}" class="btn btn-primary mt-4">
                        <i class="fas fa-calculator"></i> Tính lương
                    </a>

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
                                <td>{{ $salary->salary_amount }}</td>
                                <td>{{ $salary->month }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <script src="fe-access/vendor/jquery/jquery.min.js"></script>
        <script src="fe-access/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
        <script src="fe-access/vendor/jquery-easing/jquery.easing.min.js"></script>
        <script src="fe-access/js/sb-admin-2.min.js"></script>
    </body>
</html>
