<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nhắc nhở chấm công Check-out</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.8;
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
            <h2>Nhắc nhở chấm công Check-out</h2>
            <p>Kính gửi: <strong>{{ $user->name }}</strong>,</p>
            <p>Ban Hành chính - Nhân sự kính nhắc bạn thực hiện chấm công <strong>Check-out</strong> vào cuối giờ làm việc hôm nay. Việc này giúp chúng tôi quản lý dữ liệu chấm công chính xác và duy trì sự chuyên nghiệp trong hệ thống làm việc.</p>
            <p>Hướng dẫn:</p>
            <ul>
                <li>Truy cập vào hệ thống quản lý chấm công qua ứng dụng hoặc website công ty.</li>
                <li>Chọn tính năng <strong>Check-out</strong> và xác nhận thông tin.</li>
            </ul>
            <p>Nếu bạn gặp khó khăn hoặc cần hỗ trợ, vui lòng liên hệ với phòng Hành chính - Nhân sự qua các thông tin dưới đây.</p>
            <div class="signature">
                <p>Trân trọng,</p>
                <p><strong>Phòng Hành chính - Nhân sự</strong></p>
                <p>Công ty TNHH ABC</p>
                <p>Địa chỉ: 123 Đường ABC, Phường XYZ, Quận H, TP. Hà Nội</p>
                <p>Email: <a href="mailto:support@abc.com">support@abc.com</a> | Điện thoại: 0123 456 789</p>
            </div>
            <a href="mailto:support@abc.com" class="button">Liên hệ hỗ trợ</a>
        </div>
        <div class="footer">
            <p>&copy; 2024 Công ty TNHH ABC. Tất cả các quyền được bảo lưu.</p>
        </div>
    </div>
</body>
</html>
