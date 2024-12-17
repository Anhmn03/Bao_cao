<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Thống kê nhân sự</title>
    <style>
         .filter-form .form-control {
        height: 40px; /* Đặt chiều cao cho tất cả các ô input và select */
        line-height: 1.5; /* Đảm bảo văn bản bên trong căn giữa */
        padding: 5px 10px; /* Điều chỉnh khoảng cách bên trong */
        font-size: 14px; /* Điều chỉnh kích thước chữ để đồng bộ */
    }

    .filter-form .btn {
        height: 40px; /* Chiều cao đồng bộ với các ô input/select */
        line-height: 1.5; /* Đảm bảo văn bản căn giữa */
    }
        .card {
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); /* Thêm bóng nhẹ */
            border-radius: 15px; /* Bo góc mềm mại */
        }
    
        .card-header {
            background-color: #f8f9fc; /* Màu nền nhẹ */
            border-bottom: 1px solid #e3e6f0; /* Đường viền */
        }
    
        canvas {
            max-height: 250px; /* Giới hạn chiều cao biểu đồ */
        }

        .filter-form {
            display: flex;
            justify-content: space-between;
            gap: 15px;
        }

        .filter-form .form-group {
            flex: 1;
            margin-right: 10px;
        }

        .filter-form .form-group select {
            width: 100%;
        }

        .form-row {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }
    </style>
    
    <!-- Font và CSS -->
    <link href="/fe-access/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,300,400,600,700,800,900" rel="stylesheet">
    <link href="/fe-access/css/sb-admin-2.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
</head>

<body id="page-top">
    <div id="wrapper">
        <!-- Thanh bên -->
        @include('fe_admin.slidebar')

        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <!-- Thanh trên -->
                @include('fe_admin.topbar')

                <!-- Nội dung chính -->
                <!-- Biểu đồ ngày công -->
<div class="card mb-4">
    <div class="card-header">
        <h2>Biểu đồ Ngày Công - Tháng {{ $currentMonth }}</h2>
        <form action="{{ route('workingDayChart') }}" method="get" class="filter-form">
            <div class="form-group">
                <label for="month">Tháng:</label>
                <select name="month" id="month" class="form-control" required>
                    @for ($i = 1; $i <= 12; $i++)
                        <option value="{{ $i }}" {{ $i == $selectedMonth ? 'selected' : '' }}>{{ $i }}</option>
                    @endfor
                </select>
            </div>
        
            <div class="form-group">
                <label for="year">Năm:</label>
                <select name="year" id="year" class="form-control" required>
                    <option value="{{ $selectedYear }}" selected>{{ $selectedYear }}</option>
                </select>
            </div>
        
            <div class="form-group">
                <label for="department_id">Phòng ban:</label>
                <select name="department_id" id="department_id" class="form-control">
                    <option value="">Tất cả phòng ban</option>
                    @foreach ($departments as $department)
                        <option value="{{ $department->id }}" {{ $department->id == $departmentId ? 'selected' : '' }}>
                            {{ $department->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        
            <button type="submit" class="btn btn-primary" style="margin-top:  30px">Lọc</button>
        </form>
        
    </div>
    <div class="card-body">
        <canvas id="workdaysChart" width="400" height="200"></canvas>
    </div>

    

</div>

<!-- Biểu đồ lương -->
<div class="card mb-4">
    <div class="card-header">
        <h6 class="font-weight-bold text-primary">Biểu đồ Lương</h6>
    </div>
    <div class="card-body">
        <canvas id="salaryChart" width="400" height="200"></canvas>
    </div>
</div>

                <!-- Footer -->
                <footer class="sticky-footer bg-white mt-4">
                    <div class="container my-auto">
                        <div class="copyright text-center my-auto">
                            <span>© {{ date('Y') }} Your Company. All Rights Reserved.</span>
                        </div>
                    </div>
                </footer>
            </div>
        </div>

        <!-- Nút cuộn lên đầu trang -->
        <a class="scroll-to-top rounded" href="#page-top">
            <i class="fas fa-angle-up"></i>
        </a>

        <script>
    var ctx = document.getElementById('workdaysChart').getContext('2d');
    var workdaysChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Ngày công hợp lệ', 'Ngày công không hợp lệ'],
            datasets: [{
                label: 'Ngày công hợp lệ',
                data: [{{ $totalValidWorkdays }}, 0],
                backgroundColor: '#4CAF50',
                borderColor: '#388E3C',
                borderWidth: 1
            }, {
                label: 'Ngày công không hợp lệ',
                data: [0, {{ $totalInvalidWorkdays ?? 0 }}],
                backgroundColor: '#F44336',
                borderColor: '#D32F2F',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                x: {
                    stacked: false,
                    title: {
                        display: true,
                        text: 'Loại ngày công'
                    }
                },
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Số ngày công'
                    }
                }
            },
            plugins: {
                legend: {
                    position: 'top'
                },
                datalabels: {
                    display: true,
                    color: '#fff',
                    font: {
                        weight: 'bold'
                    },
                    formatter: function(value) {
                        return value;
                    }
                }
            }
        }
    });
        </script>
<script>
   var ctx = document.getElementById('salaryChart').getContext('2d');
var chartData = {!! $chartData !!};  // Nhúng dữ liệu từ controller vào JS
var months = ['01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12'];

var salaryChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: months, // Nhãn tháng
        datasets: chartData.map(function(department) {
            return {
                label: department.label, // Tên phòng ban
                data: department.data,  // Dữ liệu mức lương của từng tháng
                backgroundColor: department.backgroundColor, // Màu nền cho đường
                borderColor: department.borderColor, // Màu viền cho đường
                borderWidth: 2,
                fill: false // Không tô màu dưới đường biểu đồ
            };
        })
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            x: {
                title: {
                    display: true,
                    text: 'Tháng' // Tiêu đề cho trục x
                }
            },
            y: {
                title: {
                    display: true,
                    text: 'Mức Lương (VNĐ)' // Tiêu đề cho trục y
                },
                beginAtZero: true
            }
        }
    }
});


</script>
        <!-- JS -->
        <script src="/fe-access/vendor/jquery/jquery.min.js"></script>
        <script src="/fe-access/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
        <script src="/fe-access/vendor/jquery-easing/jquery.easing.min.js"></script>
        <script src="/fe-access/js/sb-admin-2.min.js"></script>
    </div>
</body>

</html>


