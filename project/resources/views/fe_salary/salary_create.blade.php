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
                        <h1 class="h3 mb-0 text-gray-800">Danh sách Bậc Lương</h1>
                    </div>
                    
                    <h1>Thêm</h1>
                    
                    <form action="{{ route('salary.store') }}" method="POST" class="p-4 bg-white shadow rounded">
                        @csrf
                        <div class="mb-3">
                            <label for="name" class="form-label">Cấp Bậc:</label>
                            <input type="text" name="name" id="name" class="form-control"
                                   placeholder="Nhập tên bậc lương" required>
                        </div>

                        <div class="mb-3">
                            <label for="monthly_salary" class="form-label">Lương Tháng:</label>
                            <input type="text" name="monthly_salary" id="monthly_salary"
                                   class="form-control money-input" placeholder="Nhập lương tháng" required>
                            <small id="monthly_display" class="form-text text-muted">
                                Bạn đã nhập: 0 ₫
                            </small>
                        </div>

                        <div class="mb-3">
                            <label for="daily_salary" class="form-label">Lương Ngày:</label>
                            <input type="text" name="daily_salary" id="daily_salary"
                                   class="form-control money-input" placeholder="Nhập lương ngày" required>
                            <small id="daily_display" class="form-text text-muted">
                                Bạn đã nhập: 0 ₫
                            </small>
                        </div>

                        <!-- Nút Quay Lại và Thêm ở cùng một hàng -->
                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('salary') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Quay Lại
                            </a>
                            <button type="submit" class="btn btn-primary">Thêm</button>
                        </div>
                    </form>
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
    <script src="fe-access/js/sb-admin-2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/cleave.js"></script>

    <!-- Initialize Cleave.js and Update Display -->
    <script>
        const cleaveMonthly = new Cleave('#monthly_salary', {
            numeral: true,
            numeralThousandsGroupStyle: 'thousand',
            numeralDecimalScale: 0,
            suffix: ' ₫'
        });

        const cleaveDaily = new Cleave('#daily_salary', {
            numeral: true,
            numeralThousandsGroupStyle: 'thousand',
            numeralDecimalScale: 0,
            suffix: ' ₫'
        });

        const monthlyInput = document.getElementById('monthly_salary');
        const dailyInput = document.getElementById('daily_salary');
        const monthlyDisplay = document.getElementById('monthly_display');
        const dailyDisplay = document.getElementById('daily_display');

        monthlyInput.addEventListener('input', () => {
            monthlyDisplay.textContent = `Bạn đã nhập: ${monthlyInput.value}`;
        });

        dailyInput.addEventListener('input', () => {
            dailyDisplay.textContent = `Bạn đã nhập: ${dailyInput.value}`;
        });
    </script>
</body>
</html>
