<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thư Giải Trình</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f9f9f9;
            padding: 20px;
        }
        .email-container {
            max-width: 800px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border: 1px solid #ddd;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #4CAF50;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }
        .header img {
            width: 150px;
            margin-bottom: 10px;
        }
        .content {
            padding: 20px 0;
        }
        .content p {
            margin: 10px 0;
        }
        .content h3 {
            margin-top: 20px;
            color: #4CAF50;
        }
        .signature {
            margin-top: 30px;
            text-align: right;
            font-style: italic;
        }
        .signature img {
            width: 100px;
            margin-top: 10px;
        }
        .footer {
            margin-top: 30px;
            font-size: 0.9em;
            color: #777;
            text-align: center;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <img src="resources/img/banner.ipg" alt="Logo Công ty">
            <h2>Thư Giải Trình</h2>
        </div>
        <div class="content">
            <p>Kính gửi: <strong>Ban Nhân sự Công ty TNHH ABC</strong></p>
            <p>Nhân viên: <strong>{{ $user->name }}</strong></p>
            <p>Phòng/Ban: <strong>{{ $user->department->name }}</strong></p>
            <p>Ngày gửi: <strong>{{ date('d/m/Y') }}</strong></p>

            <h3>Lý do giải trình</h3>
            <p>{{ $justificationReason }}</p>

            <p>Kính mong Ban Nhân sự xem xét và thông cảm cho lý do nêu trên. Tôi xin cam kết thực hiện tốt nhiệm vụ và không để sự việc tương tự xảy ra trong tương lai.</p>
        </div>
        <div class="signature">
            <p>Trân trọng,</p>
            <p><strong>{{ $user->name }}</strong></p>
            <p>Nhân viên, Phòng/Ban: {{ $user->department->name }}</p>
            <img src="signature.png" alt="Chữ ký nhân viên">
        </div>
        <div class="footer">
            <p>&copy; 2024 Công ty TNHH ABC. Tất cả các quyền được bảo lưu.</p>
        </div>
    </div>
</body>
</html>
