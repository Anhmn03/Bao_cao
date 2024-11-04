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
</head>

<body id="page-top">
    <div id="wrapper">
        @include('fe_user.slidebar') <!-- Sidebar -->

        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                @include('fe_admin.topbar') <!-- Topbar -->

                <div class="container-fluid">
                   


<div class="container">
    <h1>Báo cáo tháng</h1>
    
    <form action="{{ route('attendance.monthlyReport') }}" method="GET">
        <div class="form-row align-items-center">
            <div class="col-auto">
                <label for="month">Tháng:</label>
                <select name="month" id="month" class="form-control">
                    @for ($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ $m == $month ? 'selected' : '' }}>
                            {{ $m }}
                        </option>
                    @endfor
                </select>
            </div>
            <div class="col-auto">
                <label for="year">Năm:</label>
                <select name="year" id="year" class="form-control">
                    @for ($y = now()->year - 5; $y <= now()->year + 5; $y++)
                        <option value="{{ $y }}" {{ $y == $year ? 'selected' : '' }}>
                            {{ $y }}
                        </option>
                    @endfor
                </select>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary">Xem báo cáo</button>
            </div>
        </div>
    </form>

    <h2>Tên: {{ $employeeData['name'] }}</h2>
    <h3>Chức vụ: {{ $employeeData['position'] }}</h3>

    <table class="table table-bordered mt-3">
        <thead>
            <tr>
                <th>Ngày</th>
                <th>Giờ Check-in</th>
                <th>Giờ Check-out</th>
                <th>Số giờ làm việc</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($employeeData['attendance'] as $date => $attendance)
                <tr>
                    <td>{{ $date }}</td>
                    <td>{{ $attendance['checkIn'] ? $attendance['checkIn']->format('H:i') : 'Chưa check-in' }}</td>
                    <td>{{ $attendance['checkOut'] ? $attendance['checkOut']->format('H:i') : 'Chưa check-out' }}</td>
                    <td>{{ $attendance['hours'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

