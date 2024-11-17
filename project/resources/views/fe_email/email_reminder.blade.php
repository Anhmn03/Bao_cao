<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nhắc nhở chấm công Check-in</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            padding: 0;
            margin: 0;
        }
        .email-container {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f9f9f9;
        }
        .header {
            text-align: center;
            padding-bottom: 20px;
        }
        .header img {
            max-width: 150px;
            margin-bottom: 10px;
        }
        .content {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .footer {
            margin-top: 20px;
            font-size: 0.9em;
            color: #777;
            text-align: center;
        }
        .signature {
            margin-top: 20px;
        }
        .button {
            display: inline-block;
            margin-top: 10px;
            padding: 10px 20px;
            color: #fff;
            background-color: #4CAF50;
            text-decoration: none;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <img src="https://www.example.com/logo.png" alt="Company Logo">
            <h2 style="color: #333;">Công ty TNHH ABC</h2>
        </div>
        <div class="content">
            <h2 style="color: #333;">Nhắc nhở chấm công Check-in</h2>
            <p>Kính gửi <strong>{{ $user->name }}</strong>,</p>
            <p>Chúng tôi muốn nhắc nhở bạn vui lòng thực hiện chấm công <strong>Check-in</strong> khi đến văn phòng hôm nay. Điều này giúp chúng tôi quản lý và ghi nhận chính xác thời gian làm việc của bạn.</p>
            <p>Nếu có bất kỳ thắc mắc hoặc cần hỗ trợ thêm, xin vui lòng liên hệ với phòng Hành chính - Nhân sự của chúng tôi.</p>
            <p>Cảm ơn sự hợp tác của bạn!</p>
            <div class="signature">
                Trân trọng,<br><br>
                <strong>Phòng Hành chính - Nhân sự</strong><br>
                Công ty TNHH ABC<br>
                Địa chỉ: 123 Đường ABC, Phường XYZ, Quận H, TP. Hà Nội<br>
                Email: support@abc.com | SĐT: 0123 456 789
            </div>
            <a href="mailto:support@abc.com" class="button">Liên hệ hỗ trợ</a>
        </div>
        <div class="footer">
            <p>&copy; 2024 Công ty TNHH ABC. Tất cả các quyền được bảo lưu.</p>
        </div>
    </div>
</body>
</html>