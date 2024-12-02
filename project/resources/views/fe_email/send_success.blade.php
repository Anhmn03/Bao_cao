<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thông Báo Chấp Nhận Giải Trình</title>
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
        .header img {
            max-width: 100px;
            margin-bottom: 10px;
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
        .info {
            background-color: #f1f1f1;
            padding: 15px;
            border-radius: 5px;
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="resources/img/banner.ipg" alt="Logo Công ty">
            <h2>Thông Báo Chấp Nhận Giải Trình</h2>
            <h4>Công Ty TNHH ABC</h4>
        </div>
        <div class="content">
            <p>Kính gửi: <strong>{{ $user->name }}</strong>,</p>
            <p>Trạng thái chấm công của bạn đã được thay đổi thành <strong>"Chấp nhận lý do giải trình"</strong>.</p>
            <p>Dưới đây là thông tin chi tiết:</p>
            <div class="info">
                <ul>
                    <li><strong>Lý do giải trình:</strong> {{ $userJustification }}</li>
                    <li><strong>Ngày giải trình:</strong> {{ $userDate }}</li>
                    <li><strong>Phòng ban liên quan:</strong> {{ $userDepartment }}</li>
                </ul>
            </div>
            <p>Cảm ơn bạn đã cung cấp lý do hợp lệ.</p>
            <p>Xin chân thành cảm ơn sự hợp tác của bạn.</p>
        </div>
        <div class="footer">
            Trân trọng,<br>
            Ban Nhân Sự<br>
            Công Ty TNHH ABC
        </div>
    </div>
</body>
</html>
