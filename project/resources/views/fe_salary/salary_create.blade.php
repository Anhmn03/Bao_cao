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
                       
                        {{-- Chọn Phòng Ban --}}
                        <div class="form-group">
                            <label for="department_id">Phòng ban</label>
                            <select name="department_id" id="department_id" class="form-control" required>
                                <option value="">Chọn phòng ban</option>
                                @foreach ($departments as $department)
                                    <option value="{{ $department->id }}">{{ $department->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Bậc Lương --}}
                        <div class="form-group">
                            <label for="name">Bậc Lương</label>
                            <input type="text" name="name" id="name" class="form-control" required>
                        </div>

                        {{-- Hệ số lương --}}
                        <div class="form-group">
                            <label for="salaryCoefficient">Hệ số lương</label>
                            <input type="number" name="salaryCoefficient" id="salaryCoefficient" class="form-control" step="0.01" min="0" max="99.99" required>
                        </div>

                        {{-- Lương tháng --}}
                        <div class="mb-3">
                            <label for="monthlySalary" class="form-label">Lương Tháng:</label>
                            <input type="text" name="monthlySalary" id="monthlySalary"
                                   class="form-control money-input" placeholder="Nhập lương tháng" required>
                            <small id="monthly_display" class="form-text text-muted">
                                Bạn đã nhập: 0 ₫
                            </small>
                        </div>

                        {{-- Nút submit --}}
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

    <!-- Initialize Cleave.js -->
    <script>
        const cleaveMonthly = new Cleave('#monthlySalary', {
            numeral: true,
            numeralThousandsGroupStyle: 'thousand',
            numeralDecimalScale: 2,
            noImmediatePrefix: true
        });
    
        const monthlyInput = document.getElementById('monthlySalary');
        const monthlyDisplay = document.getElementById('monthly_display');
    
        monthlyInput.addEventListener('input', () => {
            monthlyDisplay.textContent = `Bạn đã nhập: ${monthlyInput.value}`;
        });
    
        document.querySelector('form').addEventListener('submit', function (event) {
            const cleanedSalary = monthlyInput.value.replace(/[^0-9.]/g, ''); 
            monthlyInput.value = cleanedSalary;
        });
    </script>
    
</body>
</html>
