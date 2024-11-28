<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đơn xin nghỉ phép đã được chấp nhận</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .email-container {
            width: 100%;
            max-width: 650px;
            margin: 30px auto;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            border: 1px solid #e0e0e0;
        }
        .header {
            background-color: #4CAF50;
            color: #fff;
            text-align: center;
            padding: 20px;
        }
        .header img {
            max-width: 120px;
            margin-bottom: 10px;
        }
        .content {
            padding: 20px;
        }
        .content h2 {
            color: #4CAF50;
            font-size: 1.5em;
            margin-bottom: 10px;
        }
        .content p {
            margin-bottom: 15px;
            line-height: 1.8;
        }
        .signature {
            margin-top: 20px;
        }
        .signature p {
            margin: 5px 0;
        }
        .footer {
            text-align: center;
            padding: 15px;
            background-color: #f9f9f9;
            font-size: 0.9em;
            color: #777;
            border-top: 1px solid #e0e0e0;
        }
        .button {
            display: inline-block;
            margin-top: 10px;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: #fff;
            text-decoration: none;
            font-size: 1em;
            border-radius: 5px;
        }
        .button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <img src="https://www.example.com/logo.png" alt="Company Logo">
            <h1>Công ty TNHH ABC</h1>
        </div>
        <div class="content">
            <h2>Kính gửi {{ $leaveRequest->user->name }},</h2>
            <p>Chúng tôi xin thông báo rằng đơn xin nghỉ phép của bạn từ ngày <strong>{{ $leaveRequest->start_date }}</strong> đến ngày <strong>{{ $leaveRequest->end_date }}</strong> đã được <strong>chấp nhận</strong>.</p>
            <p>Hãy đảm bảo rằng các nhiệm vụ của bạn đã được chuyển giao đầy đủ trước thời gian nghỉ.</p>
            <p>Nếu cần thêm thông tin hoặc hỗ trợ, vui lòng liên hệ với quản lý trực tiếp hoặc phòng nhân sự.</p>
            <p>Chúc bạn có một kỳ nghỉ vui vẻ!</p>
            <div class="signature">
                <p>Trân trọng,</p>
                <p><strong>Ban Quản Lý Nhân Sự</strong></p>
                <p>Công ty TNHH ABC</p>
                <p>Địa chỉ: 123 Đường ABC, Phường XYZ, Quận H, TP. Hà Nội</p>
                <p>Email: <a href="mailto:hr@company.com">hr@company.com</a> | Điện thoại: 0123 456 789</p>
            </div>
            <a href="mailto:hr@company.com" class="button">Liên hệ hỗ trợ</a>
        </div>
        <div class="footer">
            <p>&copy; 2024 Công ty TNHH ABC. Tất cả các quyền được bảo lưu.</p>
        </div>
    </div>
</body>
</html>
