<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đơn xin nghỉ phép bị từ chối</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 90%;
            max-width: 650px;
            margin: 20px auto;
            border: 1px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .header {
            background-color: #FF5733;
            color: white;
            text-align: center;
            padding: 20px;
        }
        .header h1 {
            margin: 0;
        }
        .banner {
            display: block;
            width: 100%;
        }
        .content {
            padding: 20px;
        }
        .content h2 {
            color: #FF5733;
            font-size: 1.5em;
            margin-bottom: 10px;
        }
        .content p {
            margin-bottom: 15px;
            line-height: 1.8;
        }
        .footer {
            background-color: #f9f9f9;
            text-align: center;
            padding: 15px;
            font-size: 0.9em;
            color: #777;
            border-top: 1px solid #e0e0e0;
        }
        .footer p {
            margin: 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Công ty TNHH ABC</h1>
        </div>
        <img src="https://via.placeholder.com/650x150?text=Thông+Báo+Đơn+Xin+Nghỉ+Bị+Từ+Chối" alt="Banner" class="banner">
        <div class="content">
            <h2>Kính gửi {{ $leaveRequest->user->name }},</h2>
            <p>Chúng tôi rất tiếc phải thông báo rằng đơn xin nghỉ phép của bạn từ ngày <strong>{{ $leaveRequest->start_date }}</strong> đến ngày <strong>{{ $leaveRequest->end_date }}</strong> đã bị <strong>từ chối</strong>.</p>
            {{-- <p>Lý do từ chối: <strong>{{ $leaveRequest->reason }}</strong></p> --}}
            <p>Vui lòng liên hệ với quản lý trực tiếp hoặc phòng nhân sự nếu cần thêm thông tin.</p>
            <br>
            <p>Trân trọng,</p>
            <p><strong>Ban Quản Lý Nhân Sự</strong></p>
        </div>
        <div class="footer">
            <p>&copy; 2024 Công ty TNHH ABC. Mọi quyền được bảo lưu.</p>
            <p>Địa chỉ: 123 Đường ABC, Quận XYZ, TP. HCM</p>
            <p>Email: <a href="mailto:hr@company.com">hr@company.com</a> | Hotline: 0123 456 789</p>
        </div>
    </div>
</body>
</html>
