<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Admin - Danh sách bậc lương</title>

    <link href="/fe-access/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,300,400,600,700,800,900" rel="stylesheet">
    <link href="/fe-access/css/sb-admin-2.min.css" rel="stylesheet">

    <style>
        /* Styling for department titles */
        .department-title {
            margin-top: 20px;
            font-size: 22px;
            font-weight: bold;
            color: #0056b3;
            cursor: pointer;
            transition: color 0.3s ease;
        }

        .department-title:hover {
            color: #003366; /* Darker color on hover */
        }

        /* Smooth animation for showing and hiding tables */
        .salary-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #ddd;
            display: none;
            opacity: 0;
            transition: opacity 0.4s ease;
            margin-bottom: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Subtle shadow for depth */
        }

        .salary-table.show {
            display: table;
            opacity: 1;
        }

        /* Header styling with dark gray background */
        .salary-table thead th {
            color: #000; /* Black text color for the first row */
            padding: 12px;
            text-align: center;
            font-weight: bold;
            border: 1px solid #ddd;
        }

        /* First column styling with dark gray background */
        .salary-table tbody th {
            font-weight: bold;
            color: #333;
            padding: 10px;
            text-align: center;
            border: 1px solid #ddd;
        }

        /* Table body styling */
        .salary-table tbody td {
            padding: 10px;
            text-align: center;
            border: 1px solid #ddd;
            background-color: #ffffff;
        }

        /* Row hover effect */
        .salary-table tbody tr:hover {
            background-color: #f2f7ff;
        }

        /* Specific styling for top-left cell */
        .salary-table thead th:first-child {
            background-color: #ffffff; /* Set top-left cell to white */
        }

        /* Transition for toggle effect */
        .toggle-icon {
            margin-left: 8px;
            transition: transform 0.3s ease;
        }

        .rotate {
            transform: rotate(90deg);
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
                                    <i class="fas fa-search"></i>
                                </button>
                            </form>
                        </div>
                    </div>

                    <div class="table-responsive">
                        @foreach ($departments as $department)
                            <h2 class="department-title" onclick="toggleTable('{{ $department->id }}')">
                                {{ $department->name }}
                                <i class="fas fa-chevron-right toggle-icon" id="icon-{{ $department->id }}"></i>
                            </h2>

                            <!-- Salary table with levels in the first row and salary info in the first column -->
                            <table class="salary-table" id="table-{{ $department->id }}">
                                <thead>
                                    <tr>
                                        <th></th>
                                        @foreach ($salaries as $salary)
                                            @if ($salary->department_id == $department->id)
                                                <th><a href="{{ route('salary.show', ['id' => $salary->id]) }}">{{ $salary->name }}</a></th>
                                            @endif
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <th>Hệ Số Lương</th>
                                        @foreach ($salaries as $salary)
                                            @if ($salary->department_id == $department->id)
                                                <td>{{ $salary->salaryCoefficient }}</td>
                                            @endif
                                        @endforeach
                                    </tr>
                                    <tr>
                                        <th>Lương Tháng (nghìn đồng)</th>
                                        @foreach ($salaries as $salary)
                                            @if ($salary->department_id == $department->id)
                                                <td>{{ number_format($salary->monthlySalary / 1000, 0, ',', '.') }}</td>
                                            @endif
                                        @endforeach
                                    </tr>
                                </tbody>
                            </table>
                        @endforeach
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

    <script src="fe-access/vendor/jquery/jquery.min.js"></script>
    <script src="fe-access/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="fe-access/vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="fe-access/js/sb-admin-2.min.js"></script>

    <script>
        function toggleTable(departmentId) {
            const table = document.getElementById('table-' + departmentId);
            const icon = document.getElementById('icon-' + departmentId);
            if (table.classList.contains('show')) {
                table.classList.remove('show');
                icon.classList.remove('rotate');
            } else {
                table.classList.add('show');
                icon.classList.add('rotate');
            }
        }
    </script>
</body>

</html>
