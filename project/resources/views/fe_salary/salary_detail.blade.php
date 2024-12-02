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
                        <h1 class="h3 mb-0 text-gray-800">Chi tiết Bậc Lương</h1>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            Thông tin cấp bậc
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered">
                                <tr>
                                    <th>Tên Bậc Lương:</th>
                                    <td>{{ $salaryLevel->name ?? 'Chưa có tên' }}</td>
                                </tr>
                                <tr>
                                    <th>Hệ Số Lương:</th>
                                    <td>{{ isset($salaryLevel->salaryCoefficient) ? number_format($salaryLevel->salaryCoefficient, 2) : 'Chưa có hệ số lương' }}</td>
                                </tr>
                                <tr>
                                    <th>Lương Hàng Tháng:</th>
                                    <td>{{ isset($salaryLevel->monthlySalary) ? number_format($salaryLevel->monthlySalary, 0, ',', '.') . ' VND' : 'Chưa có lương tháng' }}</td>
                                </tr>
                                <tr>
                                    <th>Lương Hàng Ngày:</th>
                                    <td>{{ isset($salaryLevel->dailySalary) ? number_format($salaryLevel->dailySalary, 0, ',', '.') . ' VND' : 'Chưa có lương ngày' }}</td>
                                </tr>
                                <tr>
                                    <th>Phòng Ban:</th>
                                    <td>{{ $salaryLevel->department->name ?? 'Chưa có phòng ban' }}</td>
                                </tr>
                                {{-- <tr>
                                    <th>Trạng thái:</th>
                                    <td>
                                        @if ($salaryLevel->status == 1)
                                            Hoạt động
                                        @elseif ($salaryLevel->status == 0)
                                            Vô hiệu hóa
                                        @else
                                            Chưa xác định
                                        @endif
                                    </td>
                                </tr> --}}
                                <tr>
                                    <th>Người Tạo:</th>
                                    <td>{{ $creator->name ?? 'Không rõ' }}</td>
                                </tr>
                                <tr>
                                    <th>Người Cập Nhật Gần Nhất:</th>
                                    <td>{{ $updater->name ?? 'Không rõ' }}</td>
                                </tr>
                                <tr>
                                    <th>Ngày Tạo:</th>
                                    <td>{{ $salaryLevel->created_at ? $salaryLevel->created_at->format('d/m/Y H:i') : 'Chưa có ngày tạo' }}</td>
                                </tr>
                                <tr>
                                    <th>Ngày Cập Nhật:</th>
                                    <td>{{ $salaryLevel->updated_at ? $salaryLevel->updated_at->format('d/m/Y H:i') : 'Chưa có ngày cập nhật' }}</td>
                                </tr>
                            </table>

                            <!-- Người sử dụng bậc lương -->
                            <h5>Người sử dụng bậc lương</h5>
                            <ul>
                                @foreach ($users as $user)
                                    <li>{{ $user->email }}</li>
                                @endforeach
                            </ul>

                        </div>
                    </div>

                    <!-- Nút Quay Lại và Chỉnh Sửa -->
                    <div class="mt-4 d-flex justify-content-between">
                        <a href="{{ route('salary') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Quay Lại
                        </a>
                        <button class="btn btn-primary" data-toggle="modal" data-target="#editModal">
                            <i class="fas fa-edit"></i> Chỉnh sửa
                        </button>
                    </div>

                    <!-- Modal Chỉnh sửa -->
                    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="editModalLabel">Chỉnh sửa bậc lương</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <form action="{{ route('salary.update', $salaryLevel->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="form-group">
                                            <label for="name">Tên cấp bậc</label>
                                            <input type="text" class="form-control" id="name" name="name" value="{{ $salaryLevel->name }}" required>
                                        </div>

                                        <div class="form-group">
                                            <label for="salaryCoefficient">Hệ số lương</label>
                                            <input type="number" class="form-control" id="salaryCoefficient" name="salaryCoefficient" value="{{ $salaryLevel->salaryCoefficient }}" step="0.01" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="monthlySalary">Lương tháng</label>
                                            <input type="text" class="form-control" id="monthlySalary" name="monthlySalary" value="{{ number_format($salaryLevel->monthlySalary, 0, '.', ',') }}" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="dailySalary">Lương tháng</label>
                                            <input type="text" class="form-control" id="dailySalary" name="dailySalary" value="{{ number_format($salaryLevel->dailySalary, 0, '.', ',') }}" required>
                                        </div>
                                        {{-- <div class="form-group">
                                            <label for="status">Trạng thái</label>
                                            <select class="form-control" id="status" name="status" required>
                                                <option value="1" {{ $salaryLevel->status == 1 ? 'selected' : '' }}>Hoạt động</option>
                                                <option value="0" {{ $salaryLevel->status == 0 ? 'selected' : '' }}>Vô hiệu hóa</option>
                                            </select>
                                        </div> --}}
                                        <button type="submit" class="btn btn-primary">Cập nhật</button>
                                    </form>
                                </div>
                            </div>
                        </div>
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

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>
    {{-- <script>
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
    </script> --}}
    <script>
        // Cleave.js initialization for Monthly Salary
        const cleaveMonthly = new Cleave('#monthlySalary', {
            numeral: true,
            numeralThousandsGroupStyle: 'thousand',
            numeralDecimalScale: 2,
            noImmediatePrefix: true
        });
    
        // Cleave.js initialization for Daily Salary
        const cleaveDaily = new Cleave('#dailySalary', {
            numeral: true,
            numeralThousandsGroupStyle: 'thousand',
            numeralDecimalScale: 2,
            noImmediatePrefix: true
        });
    
        // Monthly Salary input change event listener
        const monthlyInput = document.getElementById('monthlySalary');
        const monthlyDisplay = document.getElementById('monthly_display');
        monthlyInput.addEventListener('input', () => {
            monthlyDisplay.textContent = `Bạn đã nhập: ${monthlyInput.value}`;
        });
    
        // Daily Salary input change event listener
        const dailyInput = document.getElementById('dailySalary');
        const dailyDisplay = document.getElementById('daily_display');
        dailyInput.addEventListener('input', () => {
            dailyDisplay.textContent = `Bạn đã nhập: ${dailyInput.value}`;
        });
    
        // Form submit event to clean up the inputs before submission
        document.querySelector('form').addEventListener('submit', function (event) {
            // Clean the monthly salary input value
            const cleanedMonthlySalary = monthlyInput.value.replace(/[^0-9.]/g, '');
            monthlyInput.value = cleanedMonthlySalary;
    
            // Clean the daily salary input value
            const cleanedDailySalary = dailyInput.value.replace(/[^0-9.]/g, '');
            dailyInput.value = cleanedDailySalary;
        });
    </script>
    
    <!-- Bootstrap core JavaScript-->
    <script src="fe-access/vendor/jquery/jquery.min.js"></script>
    <script src="fe-access/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="fe-access/vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
    <script src="fe-access/js/sb-admin-2.min.js"></script>
</body>

</html>
