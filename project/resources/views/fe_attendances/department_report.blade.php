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
            background-color: #f8f9fc;
        }

        h2 {
            color: #4e73df;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            text-align: center;
            padding: 12px;
            border: 1px solid #dddddd;
        }

        th {
            background-color: #4e73df;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        tr:hover {
            background-color: #e9ecef;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-control {
            width: auto;
            display: inline-block;
            margin-right: 10px;
        }

        .btn-submit {
            padding: 10px 20px;
            background-color: #4e73df;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .btn-submit:hover {
            background-color: #2e59d9;
        }

        .filter-form {
            margin-bottom: 20px;
        }
    </style>
</head>

<body id="page-top">
    <div id="wrapper">
        @include('fe_admin.slidebar') <!-- Sidebar -->

        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                @include('fe_admin.topbar') <!-- Topbar -->

                <div class="container-fluid">
                    @if(session('message'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('message') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form method="GET" action="{{ route('department.report') }}" class="mb-4 filter-form">
                        <div class="row">
                            <div class="col-md-6">
                                <label for="department_ids">Select Department(s)</label>
                                <select name="department_ids[]" id="department_ids" class="form-control" multiple>
                                    @foreach ($departments as $department)
                                        <option value="{{ $department->id }}" 
                                            {{ in_array($department->id, $selectedDepartmentIds) ? 'selected' : '' }}>
                                            {{ $department->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                    
                            <div class="col-md-6">
                                <label for="sub_department_id">Select Sub-Department</label>
                                <select name="sub_department_id" id="sub_department_id" class="form-control">
                                    <option value="">-- Select Sub-Department --</option>
                                    @foreach ($subDepartments as $subDepartment)
                                        <option value="{{ $subDepartment->id }}" 
                                            {{ $selectedSubDepartment == $subDepartment->id ? 'selected' : '' }}>
                                            {{ $subDepartment->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group mt-3">
                            <label for="start_date">Từ ngày:</label>
                            <input type="date" name="start_date" value="{{ $startDate }}" class="form-control" style="display: inline-block; width: auto;">

                            <label for="end_date">Đến ngày:</label>
                            <input type="date" name="end_date" value="{{ $endDate }}" class="form-control" style="display: inline-block; width: auto;">
                        </div>

                        <div class="form-group mt-3">
                            <label for="single_date">Ngày đơn lẻ:</label>
                            <input type="date" name="single_date" value="{{ $singleDate }}" class="form-control" style="display: inline-block; width: auto;">
                        </div>

                        <button type="submit" class="btn btn-primary mt-3">Lọc</button>
                    </form>

                    @if ($monthlyReport && count($monthlyReport) > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>Họ và tên</th>
                                        <th>Chức vụ</th>
                                        <th>Ngày tháng</th>
                                        <th>Check-In</th>
                                        <th>Check-Out</th>
                                        <th>Số giờ làm việc</th>
                                        <th>Trạng thái</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($monthlyReport as $userId => $report)
                                        @foreach ($report['dailyHours'] as $date => $records)
                                            @if (empty($singleDate) || $date == $singleDate) <!-- Filter by single date -->
                                                @foreach ($records as $record)
                                                    <tr>
                                                        <td>{{ $report['name'] }}</td>
                                                        <td>{{ $report['position'] }}</td>
                                                        <td>{{ $date }}</td>
                                                        <td>{{ $record['checkIn'] }}</td>
                                                        <td>{{ $record['checkOut'] ?? 'N/A' }}</td>
                                                        <td>{{ $record['hours'] }}</td>
                                                        <td></td>
                                                    </tr>
                                                @endforeach
                                            @endif
                                        @endforeach
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="pagination-wrapper text-center mt-3">
                                {{ $attendanceData->links() }}
                            </div>
                        </div>
                    @else
                        <div class="text-center mt-3">
                            <p>Không có dữ liệu đi làm cho phòng ban đã chọn.</p>
                        </div>
                    @endif

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

        <a class="scroll-to-top rounded" href="#page-top">
            <i class="fas fa-angle-up"></i>
        </a>

        <script src="fe-access/vendor/jquery/jquery.min.js"></script>
        <script src="fe-access/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
        <script src="fe-access/vendor/jquery-easing/jquery.easing.min.js"></script>
        <script src="fe-access/js/sb-admin-2.min.js"></script>
    </div>
</body>
</html>