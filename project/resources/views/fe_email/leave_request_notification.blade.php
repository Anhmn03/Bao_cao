<!DOCTYPE html>
<html>
<head>
    <title>Thông báo đơn xin nghỉ phép</title>
</head>
<body>
    <p>Kính gửi: Quản lý</p>
    
    <p><strong>Thông tin nhân viên:</strong></p>
    <p><strong>Họ tên:</strong> {{ $leaveRequest->user->name }}</p>
    <p><strong>Phòng ban:</strong> {{ $leaveRequest->user->department->name }}</p> <!-- Phòng ban có thể truy vấn từ bảng Department nếu có -->
    
    <p><strong>Lý do nghỉ:</strong> 
        @if($leaveRequest->reason === 'other')
            {{ $leaveRequest->custom_reason }} <!-- Nếu người dùng chọn "khác" và điền lý do riêng -->
        @else
            {{ $leaveRequest->reason }} <!-- Các lý do sẵn có -->
        @endif
    </p>
    
    <p><strong>Ngày bắt đầu nghỉ:</strong> {{ $leaveRequest->start_date }}</p>
    <p><strong>Ngày kết thúc nghỉ:</strong> {{ $leaveRequest->end_date }}</p>

    <p><strong>Trạng thái:</strong> {{ ucfirst($leaveRequest->status) }}</p>

    <p>Vui lòng kiểm tra và xử lý yêu cầu này.</p>

    <p>Trân trọng,</p>
    <p><strong>Hệ thống quản lý nghỉ phép</strong></p>
</body>
</html>
