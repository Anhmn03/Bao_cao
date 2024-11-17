{{-- <!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Thêm Người Dùng</title>

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
                    <form method="GET" action="{{ route('salary.calculate') }}">
                        <!-- Chọn phòng ban -->
                        <label for="department_id">Chọn phòng ban:</label>
                        <select name="department_id" id="department_id" required>
                            @foreach($departments as $department)
                                <option value="{{ $department->id }}" {{ request('department_id') == $department->id ? 'selected' : '' }}>
                                    {{ $department->name }}
                                </option>
                            @endforeach
                        </select>
                    
                        <!-- Chọn nhân viên -->
                        <label for="user_id">Chọn nhân viên:</label>
                        <select name="user_id" id="user_id" required>
                            @if(request('department_id'))
                                @php
                                    $employees = \App\Models\User::where('department_id', request('department_id'))->get();
                                @endphp
                                @foreach($employees as $employee)
                                    <option value="{{ $employee->id }}" {{ request('user_id') == $employee->id ? 'selected' : '' }}>
                                        {{ $employee->name }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    
                        <button type="submit">Tính lương</button>
                    </form>
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
    
        <!-- Bootstrap core JavaScript-->
        <script src="/fe-access/vendor/jquery/jquery.min.js"></script>
        <script src="/fe-access/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    
        <!-- Core plugin JavaScript-->
        <script src="/fe-access/vendor/jquery-easing/jquery.easing.min.js"></script>
    
        <!-- Custom scripts for all pages-->
        <script src="/fe-access/js/sb-admin-2.min.js"></script>
    </body>

    </html>
     --}}