
    <!-- Font và CSS -->
    <link href="/fe-access/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,300,400,600,700,800,900" rel="stylesheet">
    <link href="/fe-access/css/sb-admin-2.min.css" rel="stylesheet">
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
                    <h1 class="mb-4">Tính Lương Nhân Viên</h1>
                     <form method="GET" action="{{ route('salary_caculate') }}">
                        <div class="form-group">
                            <label for="department_id">Chọn Phòng Ban:</label>
                            <select id="department_id" name="department_id" class="form-control">
                                <option value="">-- Chọn --</option>
                                @foreach($departments as $department)
                                    <option value="{{ $department->id }}" {{ request('department_id') == $department->id ? 'selected' : '' }}>
                                        {{ $department->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                
                        <div class="form-group">
                            <label for="user_id">Chọn Nhân Viên:</label>
                            <select id="user_id" name="user_id" class="form-control">
                                <option value="">-- Chọn --</option>
                                @foreach($employees as $employee)
                                    <option value="{{ $employee->id }}" {{ request('user_id') == $employee->id ? 'selected' : '' }}>
                                        {{ $employee->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                
                        <button type="submit" class="btn btn-primary">Tính Lương</button>
                    </form>
                    
                    
                    <!-- Hiển thị kết quả nếu đã chọn phòng ban và nhân viên -->
                     @if($user)
                        <h3>Thông tin nhân viên: {{ $user->name }}</h3>
                        <p>Lương tính được: {{ number_format($salaryAmount, 0, ',', '.') }} VND</p>
                        <p>Số ngày công hợp lệ: {{ $validWorkdays }}</p>
                        <p>Số ngày công không hợp lệ: {{ $invalidWorkdays }}</p>
                    @endif
                    <button type="submit" name="action" value="save" class="btn btn-primary">Lưu trữ tính lương</button> 
                  

                    <footer class="sticky-footer bg-white">
                        <div class="container my-auto">
                            <div class="copyright text-center my-auto">
                                <span>© {{ date('Y') }} Your Company. All Rights Reserved.</span>
                            </div>
                        </div>
                    </footer>
                </div>
            </div>
        </div>
    </div>

    
        <!-- Bootstrap core JavaScript-->
        <script src="/fe-access/vendor/jquery/jquery.min.js"></script>
        <script src="/fe-access/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    
        <!-- Core plugin JavaScript-->
        <script src="/fe-access/vendor/jquery-easing/jquery.easing.min.js"></script>
    
        <!-- Custom scripts for all pages-->
        <script src="/fe-access/js/sb-admin-2.min.js"></script>
    </body>

    </html>
    