<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Thống kê nhân sự</title>

    <!-- Font và CSS -->
    <link href="/fe-access/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,300,400,600,700,800,900" rel="stylesheet">
    <link href="/fe-access/css/sb-admin-2.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>

    <style>
        /* Đảm bảo các phần tử chứa biểu đồ có viền */
        .chart-container {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 40px;
            padding: 20px;
            background-color: #fff;
            border: 2px solid #ddd; /* Viền cho khung ngoài */
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1); /* Bóng đổ cho khung ngoài */
        }

        .row {
            display: flex;
            justify-content: space-between;
        }

        /* Căn chỉnh các biểu đồ bên trong để chúng có chiều rộng hợp lý */
        .col-md-6 {
            width: 48%; /* Mỗi biểu đồ chiếm 50% chiều rộng của khung */
            margin-bottom: 20px;
            position: relative;
        }

        /* Đảm bảo canvas không có viền */
        canvas {
            max-width: 100%;
            height: 400px;
            margin: 0 auto;
            border: none; /* Loại bỏ viền trong canvas */
        }

        .chart-title {
            text-align: center;
            margin-bottom: 20px;
        }

        
        /* Căn chỉnh trục X cho các biểu đồ */
        .chart-container .row {
            display: flex;
            justify-content: space-between;
            align-items: stretch;
        }

        .col-md-6 canvas {
            max-width: 100%;
            height: 350px; /* Đảm bảo chiều cao giống nhau */
        }
    </style>
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
                <div class="container-fluid">
                   

                    <!-- Biểu đồ số lượng nhân viên và chú thích -->
                    <div class="chart-container">
                        <div class="pie-chart-container">
                            <h2 class="mt-4 mb-4">Thống kê nhân sự</h2>
                            <p class="text-muted">Biểu đồ thể hiện số lượng nhân viên và phân bổ độ tuổi trong các phòng ban.</p>
                            <p class="text-muted">Ngày cập nhật: {{ now()->format('d/m/Y') }}</p>
                            <canvas id="employeeChart"></canvas>
                        </div>
                        <div class="chart-legend" id="employeeLegend"></div>
                    </div>

                    <!-- Biểu đồ độ tuổi và giới tính -->
                    <div class="chart-container">
                        <div class="row">
                            <!-- Biểu đồ độ tuổi -->
                            <div class="col-md-6">
                                <h5 class="text-center">Phân bổ độ tuổi giữa các phòng ban</h5>
                                <canvas id="ageChart"></canvas>
                                <p class="text-center mt-2 text-muted">Biểu đồ dạng cột xếp chồng thể hiện sự so sánh độ tuổi giữa các phòng ban.</p>
                            </div>

                            <!-- Biểu đồ giới tính -->
                            <div class="col-md-6">
                                <h5 class="text-center">Thống kê giới tính nhân viên theo phòng ban</h5>
                                <canvas id="genderChart"></canvas>
                                <p class="text-center mt-2 text-muted">Biểu đồ thể hiện sự phân bổ giới tính trong các phòng ban.</p>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- <canvas id="genderChart"></canvas> --}}
                <div class="chart-container">
                    <div class="pie-chart-container">
                        <canvas id="seniorityChart"></canvas>
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

        <!-- JS -->
        <script src="/fe-access/vendor/jquery/jquery.min.js"></script>
        <script src="/fe-access/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
        <script src="/fe-access/vendor/jquery-easing/jquery.easing.min.js"></script>
        <script src="/fe-access/js/sb-admin-2.min.js"></script>
    </div>

    
    
  
        <script>
    Chart.register(ChartDataLabels);

    // Dữ liệu biểu đồ số lượng nhân viên (Pie Chart)
    const employeeLabels = @json($labels);
    const employeeData = @json($employeeData);
    const employeeColors = @json($colors);

    // Biểu đồ số lượng nhân viên (Pie Chart)
    const employeeChart = new Chart(document.getElementById('employeeChart'), {
        type: 'pie',
        data: {
            labels: employeeLabels,
            datasets: [{
               label: 'Số lượng nhân viên',
                data: employeeData,
                backgroundColor: employeeColors,
                borderWidth: 0 // Loại bỏ khung viền của biểu đồ
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false }, // Ẩn chú thích mặc định
                datalabels: {
                    formatter: (value, ctx) => {
                        let total = ctx.chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
                        if (value === 0) {
                            return ''; // Không hiển thị phần trăm nếu giá trị là 0
                        }
                        let percentage = ((value / total) * 100).toFixed(1) + "%";
                        return percentage; // Hiển thị phần trăm
                    },
                    color: '#fff',
                    font: { size: 14 }
                },
               // title: { display: true, text: 'Số lượng nhân viên trong các phòng ban' }
            },
            layout: {
                padding: 0 // Loại bỏ padding dư thừa
            }
        }
    });
    

    // // Tạo chú thích tùy chỉnh cho biểu đồ pie
    // const employeeLegend = document.getElementById('employeeLegend');
    // employeeLegend.style.display = "flex";
    // employeeLegend.style.flexDirection = "column"; // Căn chỉnh theo cột
    // employeeLegend.style.alignItems = "start";
    // employeeLegend.style.marginTop = "10px";

    // employeeLabels.forEach((label, index) => {
    //     const legendItem = document.createElement('div');
    //     legendItem.style.display = "flex";
    //     legendItem.style.alignItems = "center";
    //     legendItem.style.marginBottom = "5px"; // Khoảng cách nhỏ giữa các chú thích

    //     const colorBox = document.createElement('span');
    //     colorBox.style.width = "12px";
    //     colorBox.style.height = "12px";
    //     colorBox.style.backgroundColor = employeeColors[index];
    //     colorBox.style.display = "inline-block";
    //     colorBox.style.marginRight = "8px";
    //     colorBox.style.borderRadius = "50%"; // Làm tròn hộp màu

    //     const labelText = document.createElement('span');
    //     labelText.style.fontSize = "14px";
    //     labelText.style.color = "#333";
    //     labelText.innerText = `${label}: ${employeeData[index]}`;

    //     legendItem.appendChild(colorBox); // Thêm hộp màu
    //     legendItem.appendChild(labelText); // Thêm nhãn
    //     employeeLegend.appendChild(legendItem); // Thêm vào container chú thích
    // });

    // Tạo chú thích tùy chỉnh cho biểu đồ pie
const employeeLegend = document.getElementById('employeeLegend');
employeeLegend.style.display = "flex";
employeeLegend.style.flexDirection = "column"; // Căn chỉnh theo cột
employeeLegend.style.alignItems = "flex-start"; // Căn chỉnh sang trái
employeeLegend.style.marginTop = "15px"; // Khoảng cách phía trên
employeeLegend.style.padding = "10px"; // Thêm khoảng đệm
employeeLegend.style.backgroundColor = "#f9f9f9"; // Nền sáng
employeeLegend.style.borderRadius = "8px"; // Bo góc
employeeLegend.style.boxShadow = "0 4px 8px rgba(0, 0, 0, 0.1)"; // Bóng đổ

employeeLabels.forEach((label, index) => {
    const legendItem = document.createElement('div');
    legendItem.style.display = "flex";
    legendItem.style.alignItems = "center";
    legendItem.style.marginBottom = "10px"; // Tăng khoảng cách giữa các chú thích

    const colorBox = document.createElement('span');
    colorBox.style.width = "20px"; // Tăng kích thước hộp màu
    colorBox.style.height = "20px";
    colorBox.style.backgroundColor = employeeColors[index];
    colorBox.style.display = "inline-block";
    colorBox.style.marginRight = "12px"; // Tăng khoảng cách giữa hộp màu và nhãn
    colorBox.style.borderRadius = "50%"; // Làm tròn hộp màu

    const labelText = document.createElement('span');
    labelText.style.fontSize = "16px"; // Tăng kích thước chữ
    labelText.style.fontWeight = "bold"; // Tăng độ đậm của chữ
    labelText.style.color = "#333";
    labelText.innerText = `${label}: ${employeeData[index]}`;

    legendItem.appendChild(colorBox); // Thêm hộp màu
    legendItem.appendChild(labelText); // Thêm nhãn
    employeeLegend.appendChild(legendItem); // Thêm vào container chú thích
});


    // Dữ liệu cho biểu đồ độ tuổi (Stacked Bar Chart)
    // Dữ liệu cho biểu đồ độ tuổi (Group Bar Chart)
const ageLabels = @json($labels); // Nhãn trục X (các phòng ban)
const ageDatasets = @json($ageDatasets); // Dữ liệu cho các nhóm độ tuổi

// Biểu đồ độ tuổi (Group Bar Chart)
new Chart(document.getElementById('ageChart'), {
    type: 'bar',
    data: {
        labels: ageLabels, // Nhãn trục X (phòng ban)
        datasets: ageDatasets // Các nhóm dữ liệu cho độ tuổi
    },
    options: {
        responsive: true,
        plugins: {
            legend: { position: 'top' }, // Hiển thị chú thích phía trên
            title: { 
                display: true, 
                text: 'Phân bổ độ tuổi giữa các phòng ban' // Tiêu đề biểu đồ
            },
            datalabels: { display: false } // Tắt hiển thị dữ liệu trên các cột
        },
        scales: {
            x: {
        beginAtZero: true,
        grid: { display: false }, // Ẩn lưới trục X
        ticks: {
            padding: 5, // Tăng khoảng cách giữa nhãn trục X và các cột
        },
        barPercentage: 1.0, // Đảm bảo các cột đầy đủ chiều rộng
        categoryPercentage: 1.0, // Giảm khoảng trống giữa các nhóm cột
    },
    y: {
        beginAtZero: true,
        ticks:{
            stepSize: 1,
        }
    }
        }
    }
});


    // Dữ liệu cho biểu đồ giới tính (Bar Chart)
    const genderLabels = @json($labels);
    const maleData = @json($genderDatasets['male']);
    const femaleData = @json($genderDatasets['female']);

    // Biểu đồ giới tính (Bar Chart)
    new Chart(document.getElementById('genderChart'), {
    type: 'bar',
    data: {
        labels: genderLabels,
        datasets: [
            {
                label: 'Nam',
                data: maleData,
                backgroundColor: '#3498db',
            },
            {
                label: 'Nữ',
                data: femaleData,
                backgroundColor: '#e74c3c',
            }
        ]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { position: 'top' },
            title: { display: true, text: 'Thống kê giới tính nhân viên theo phòng ban' },
            datalabels: { display: false } // Tắt hiển thị dữ liệu trên các cột
        },
        scales: {
    x: {
        beginAtZero: true,
        grid: { display: false }, // Ẩn lưới trục X
        ticks: {
            padding: 5, // Tăng khoảng cách giữa nhãn trục X và các cột
        },
        barPercentage: 1.0, // Đảm bảo các cột đầy đủ chiều rộng
        categoryPercentage: 1.0, // Giảm khoảng trống giữa các nhóm cột
    },
    y: {
        beginAtZero: true,
        ticks: {
            stepSize: 1, // Giải đoạn dữ liệu giữa cơ trúc trên biểu đồ
        }
    }
}
    }
});
 // Biểu đồ thâm niên (Bar Chart)
 const ctx = document.getElementById('seniorityChart').getContext('2d');
            const seniorityData = @json(array_values($seniorityData));
            const labels = @json(array_keys($seniorityData));

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Số lượng nhân viên',
                        data: seniorityData,
                        backgroundColor: ['#4CAF50', '#FFC107', '#FF5722', '#2196F3'],
                        borderWidth: 1,
                    }],
                },
                options: {
                    responsive: true,
                    plugins: {
                        title: { display: true, text: 'Thống kê thâm niên nhân viên' },
                        datalabels: {
                            align: 'end',
                            color: 'white',
                            font: { weight: 'bold' },
                            formatter: (value) => value + ' nhân viên',
                        }
                    },
                    scales: {
    x: {
        beginAtZero: true,
        grid: { display: false }, // Ẩn lưới trục X
        ticks: {
            padding: 5, // Tăng khoảng cách giữa nhãn trục X và các cột
        },
        barPercentage: 1.0, // Đảm bảo các cột đầy đủ chiều rộng
        categoryPercentage: 1.0, // Giảm khoảng trống giữa các nhóm cột
    },
    y: {
        beginAtZero: true
    }
}
                }
            });
</script>
   
</body>

</html>        