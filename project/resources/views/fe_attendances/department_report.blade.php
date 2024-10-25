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
            background-color: #f8f9fc; /* Màu nền sáng */
        }
        h2 {
            color: #4e73df; /* Màu tiêu đề */
        }
        table {
            width: 100%; /* Đặt chiều rộng bảng 100% */
            border-collapse: collapse; /* Kết hợp biên bảng */
            margin-top: 20px; /* Khoảng cách trên bảng */
        }
        th, td {
            text-align: center; /* Căn giữa nội dung */
            padding: 12px; /* Khoảng cách trong ô */
            border: 1px solid #dddddd; /* Biên mờ cho ô */
        }
        th {
            background-color: #4e73df; /* Màu nền tiêu đề bảng */
            color: white; /* Màu chữ tiêu đề bảng */
        }
        tr:nth-child(even) {
            background-color: #f2f2f2; /* Màu nền hàng chẵn */
        }
        tr:hover {
            background-color: #e9ecef; /* Màu nền khi di chuột qua hàng */
        }
        .form-group {
            margin-bottom: 20px; /* Khoảng cách dưới các nhóm input */
        }
        .form-control {
            width: auto; /* Chiều rộng tự động cho input */
            display: inline-block; /* Hiển thị inline */
            margin-right: 10px; /* Khoảng cách giữa các input */
        }
        .btn-submit {
            padding: 10px 20px; /* Khoảng cách nút */
            background-color: #4e73df; /* Màu nền nút */
            color: white; /* Màu chữ nút */
            border: none; /* Không có biên */
            border-radius: 5px; /* Bo góc cho nút */
            cursor: pointer; /* Con trỏ chuột khi hover */
        }
        .btn-submit:hover {
            background-color: #2e59d9; /* Màu nền khi hover */
        }
        .filter-form {
            margin-bottom: 20px; /* Khoảng cách dưới form lọc */
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

                    <!-- Hiển thị thông báo nếu có -->
                    @if(session('message'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('message') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <!-- Bộ lọc theo phòng ban -->
                    <div class="filter-form d-flex align-items-center mb-4">
                        <form action="{{ route('department.report') }}" method="GET" class="d-flex align-items-center">
                            <div class="form-group me-3">
                                <label for="department_ids" class="form-label">Chọn phòng ban cha:</label>
                                <select name="department_ids[]" id="department_ids" class="form-control" onchange="toggleSubDepartments()">
                                    <option value="">Chọn phòng ban cha</option>
                                    @foreach ($departments as $department)
                                        <option value="{{ $department->id }}" {{ in_array($department->id, $selectedDepartmentIds) ? 'selected' : '' }}>
                                            {{ $department->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="form-group me-3" id="sub_department_container" style="display: none;">
                                <label for="sub_department_id" class="form-label">Chọn tổ ban:</label>
                                <select name="sub_department_id" id="sub_department_id" class="form-control">
                                    <option value="">Tất cả</option>
                                    @foreach ($subDepartments as $subDepartment)
                                        <option value="{{ $subDepartment->id }}" {{ $selectedSubDepartment == $subDepartment->id ? 'selected' : '' }}>
                                            {{ $subDepartment->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <button type="submit" class="btn btn-primary btn-submit">Lọc</button>
                        </form>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th rowspan="2">Họ và tên</th>
                                    <th rowspan="2">Chức vụ</th>
                                    <th colspan="31">Ngày trong tháng</th>
                                    <th rowspan="2">Tổng số giờ làm trong tháng</th>
                                </tr>
                                <tr>
                                    @for ($i = 1; $i <= 31; $i++)
                                        <th>{{ $i }}</th>
                                    @endfor
                                </tr>
                            </thead>
                            <tbody id="report-body" style="display: none;"> <!-- Ẩn tbody mặc định -->
                                @if (!empty($monthlyReport) && count($monthlyReport) > 0)
                                    @foreach ($monthlyReport as $userId => $report)
                                        <tr>
                                            <td>{{ $report['name'] }}</td>
                                            <td>{{ $report['position'] ?? 'N/A' }}</td>
                    
                                            {{-- Hiển thị giờ làm việc cho từng ngày trong tháng --}}
                                            @for ($i = 1; $i <= 31; $i++)
                                            @php
                                                $day = sprintf('%02d', $i);
                                                $dateKey = now()->format('Y-m-') . $day;
                                                $hoursWorked = isset($report['dailyHours'][$dateKey][0]) 
                                                    ? $report['dailyHours'][$dateKey][0]['hours'] 
                                                    : 0;
                                            @endphp
                                            <td>{{ $hoursWorked }}</td>
                                        @endfor
                                            <td>{{ $report['totalHours'] }} giờ</td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="34" class="text-center">Không có dữ liệu đi làm cho phòng ban đã chọn.</td>
                                    </tr>
                                @endif
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
    
    <script>
        function toggleSubDepartments() {
            const departmentSelect = document.getElementById('department_ids');
            const subDepartmentContainer = document.getElementById('sub_department_container');

            // Kiểm tra xem có phòng ban cha nào được chọn hay không
            if (departmentSelect.value) {
                subDepartmentContainer.style.display = 'block'; // Hiển thị danh sách phòng ban con
            } else {
                subDepartmentContainer.style.display = 'none'; // Ẩn danh sách phòng ban con
            }
        }

        // Hiện thị bảng khi có phòng ban được chọn
        const reportBody = document.getElementById('report-body');
        const departmentSelect = document.getElementById('department_ids');

        departmentSelect.addEventListener('change', function() {
            if (this.value) {
                reportBody.style.display = 'table-row-group'; // Hiển thị tbody
            } else {
                reportBody.style.display = 'none'; // Ẩn tbody
            }
        });
    </script>
    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Bootstrap core JavaScript-->
    <script src="fe-access/vendor/jquery/jquery.min.js"></script>
    <script src="fe-access/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="fe-access/vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="fe-access/js/sb-admin-2.min.js"></script>
</body>
</html>