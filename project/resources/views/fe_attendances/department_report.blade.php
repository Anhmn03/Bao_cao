<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Báo cáo chấm công cho phòng ban">
    <meta name="author" content="Công ty của bạn">

    <title>Báo cáo chấm công</title>

    <!-- Fonts & Styles -->
    <link href="/fe-access/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,300,400,600,700,800,900" rel="stylesheet">
    <link href="/fe-access/css/sb-admin-2.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #f8f9fc;
        }

        h1 {
            color: #4e73df;
            font-weight: bold;
        }

        .form-group label {
            font-weight: bold;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th {
            background-color: #4e73df;
            color: #fff;
        }

        th, td {
            text-align: center;
            padding: 10px;
            border: 1px solid #ddd;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        tr:hover {
            background-color: #e9ecef;
        }

        .form-control {
            display: inline-block;
            margin-right: 10px;
            width: auto;
        }

        .btn-submit {
            background-color: #4e73df;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            padding: 10px 20px;
        }

        .btn-submit:hover {
            background-color: #2e59d9;
        }

        .filter-form {
            margin-bottom: 20px;
        }

        .pagination-wrapper {
            text-align: center;
        }

        .pagination-wrapper .pagination {
            display: inline-flex;
            margin: 0 auto;
        }

        .child-department {
            display: none;
            margin-left: 20px;
        }

        .toggle-btn {
            cursor: pointer;
            color: #007bff;
            background: none;
            border: none;
        }

        .alert {
            margin-bottom: 20px;
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
                    <form method="GET" action="{{ route('department.report') }}" class="border rounded bg-light p-4">
                        @if(session('message'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('message') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        @endif

                        <h1 class="mb-4">Báo cáo Chấm công</h1>

                        <div class="form-group mb-3">
                            <label>Chọn phòng ban:</label>
                            <div id="departmentList" class="border p-3 rounded bg-white" style="max-height: 300px; overflow-y: auto;">
                                <ul>
                                    <li>
                                        <span class="toggle-btn" data-target="0">+</span>
                                        <input type="checkbox" name="department_ids[]" value="0" 
                                               {{ in_array(0, $selectedDepartmentIds) ? 'checked' : '' }}> Tất cả
                                    </li>
                                    <ul>
                                        @foreach ($departments as $department)
                                            <li>
                                                <span class="toggle-btn" data-target="{{ $department->id }}">+</span>
                                                <input type="checkbox" name="department_ids[]" value="{{ $department->id }}" 
                                                       {{ in_array($department->id, $selectedDepartmentIds) ? 'checked' : '' }}>
                                                {{ $department->name }}
                                                @if ($department->children->count() > 0)
                                                    <ul class="child-department" data-parent="{{ $department->id }}">
                                                        @include('fe_attendances.department_tree', ['departments' => $department->children])
                                                    </ul>
                                                @endif
                                            </li>
                                        @endforeach
                                    </ul>
                                </ul>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="single_date">Chọn ngày:</label>
                            <input type="date" name="single_date" id="single_date" value="{{ $singleDate }}" class="form-control">
                        </div>

                        <button type="submit" class="btn-submit">Lọc</button>
                    </form>

                    @if ($monthlyReport && count($monthlyReport) > 0)
                    <div class="table-responsive">
                        <table>
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
                                @foreach ($monthlyReport as $userId => $report)
                                    @foreach ($report['dailyHours'] as $date => $records)
                                        @if ($date == $singleDate)
                                            @foreach ($records as $record)
                                                <tr>
                                                    <td>{{ $report['name'] }}</td>
                                                    <td>{{ $report['position'] }}</td>
                                                    <td>{{ $date }}</td>
                                                    <td>{{ $record['checkIn'] }}</td>
                                                    <td>{{ $record['checkOut'] ?? 'N/A' }}</td>
                                                    <td>{{ $record['hours'] }}</td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    @endforeach
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center mt-3">
                        <p>Không có dữ liệu đi làm cho ngày đã chọn.</p>
                    </div>
                    @endif

                    <div class="pagination-wrapper mt-3">
                        {{ $attendanceData->links() }}
                    </div>
                </div>

                <footer class="sticky-footer bg-white">
                    <div class="container my-auto">
                        <div class="text-center my-auto">
                            <span>© {{ date('Y') }} Công ty của bạn. Bảo lưu mọi quyền.</span>
                        </div>
                    </div>
                </footer>
            </div>
        </div>

        <a class="scroll-to-top rounded" href="#page-top">
            <i class="fas fa-angle-up"></i>
        </a>

        <!-- Scripts -->
        <script src="/fe-access/vendor/jquery/jquery.min.js"></script>
        <script src="/fe-access/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
        <script src="/fe-access/js/sb-admin-2.min.js"></script>
        <script>
            document.querySelectorAll('.toggle-btn').forEach(btn => {
                btn.addEventListener('click', function () {
                    const targetId = this.getAttribute('data-target');
                    const childList = document.querySelector(`.child-department[data-parent="${targetId}"]`);
                    if (childList) {
                        const isHidden = childList.style.display === 'none' || !childList.style.display;
                        childList.style.display = isHidden ? 'block' : 'none';
                        this.textContent = isHidden ? '-' : '+';
                    }
                });
            });
        </script>
    </div>
</body>

</html>
