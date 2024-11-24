<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thông Báo Từ Chối Giải Trình</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            padding: 20px;
            background-color: #f9f9f9;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            padding-bottom: 20px;
        }
        .content {
            padding: 20px;
        }
        .footer {
            margin-top: 30px;
            font-size: 0.9em;
            color: #777;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Thông Báo Chấp Nhận Giải Trình</h2>
        </div>
        <div class="content">
            <p>Kính gửi: <strong>{{ $user->name }}</strong>,</p>
            <p>Trạng thái chấm công của bạn đã được thay đổi thành "Chấp nhận lý do giải trình".</p>
            <p>Cảm ơn bạn đã cung cấp lý do hợp lệ.</p>
            <p>Xin chân thành cảm ơn sự hợp tác của bạn.</p>
        </div>
        <div class="footer">
            Trân trọng,<br>
            Ban Nhân Sự<br>
            Công ty TNHH ABC
        </div>
    </div>
</body>
</html>
