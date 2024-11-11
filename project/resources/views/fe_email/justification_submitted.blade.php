<!DOCTYPE html>
<html>
<head>
    <title>Lý do giải trình mới</title>
</head>
<body>
    <h1>Lý do giải trình mới</h1>
    <p><strong>Nhân viên:</strong> {{ $attendance->user->name }}</p>
    <p><strong>Hoạt động:</strong> {{ ucfirst($attendance->type) }} lúc {{ $attendance->time->format('H:i d/m/Y') }}</p>
    <p><strong>Trạng thái:</strong> {{ $attendance->status ? 'Hợp lệ' : 'Không hợp lệ' }}</p>
    <p><strong>Lý do giải trình:</strong> {{ $attendance->justification }}</p>
</body>
</html>
