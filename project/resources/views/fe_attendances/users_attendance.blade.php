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
    <link href="fe-access/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,300,400,600,700,800,900" rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="fe-access/css/sb-admin-2.min.css" rel="stylesheet">
</head>

<body id="page-top">
    <div id="wrapper">
        @include('fe_user.slidebar') <!-- Sidebar -->

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

                    <!-- Form Check In / Check Out -->
                    {{-- <div class="d-flex justify-content-center gap-3 mb-4">
                        <form action="{{ route('attendance.checkin') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-sign-in-alt"></i> Check In
                            </button>
                        </form>

                        <form action="{{ route('attendance.checkout') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-danger btn-lg">
                                <i class="fas fa-sign-out-alt"></i> Check Out
                            </button>
                        </form>
                    </div> --}}
                 
                    <!-- Lịch sử Check In/Out -->
                    <div class="d-flex justify-content-between mb-4">
                        <h3 class="text-center">Lịch sử Check In/Out</h3>
                        <div class="d-flex justify-content-center gap-3 mb-4">
                            <form action="{{ route('attendance.checkin') }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-sign-in-alt"></i> Check In
                                </button>
                            </form>
    
                            <form action="{{ route('attendance.checkout') }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-danger btn-lg">
                                    <i class="fas fa-sign-out-alt"></i> Check Out
                                </button>
                            </form>
                        </div>
                        <h4 class="text-center">Ngày {{ date('d/m/Y') }}</h4>
                    </div>
                  
                    <table class="table table-striped table-bordered text-center">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>Nhân viên</th>
                                <th>Hoạt động</th>
                                <th>Thời gian</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($attendances as $attendance)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $attendance->user->name }}</td>
                                <td>{{ ucfirst($attendance->type) }}</td> <!-- Checkin/Checkout -->
                                <td>{{ $attendance->time->format('H:i d/m/Y') }}</td>

                              </tr>
                            @endforeach
                        </tbody>
                    </table>
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

    <!-- Core plugin JavaScript-->
    <script src="fe-access/vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="fe-access/js/sb-admin-2.min.js"></script>
</body>

</html>