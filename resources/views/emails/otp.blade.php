<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Login OTP</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 600px;
            margin: 30px auto;
            background: #fff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #490D59 0%, #6b2180 100%);
            color: #fff;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 700;
        }
        .content {
            padding: 40px 30px;
            text-align: center;
        }
        .otp-box {
            background: #f8f5ff;
            border: 2px dashed #490D59;
            border-radius: 10px;
            padding: 20px;
            margin: 30px 0;
            display: inline-block;
        }
        .otp-code {
            font-size: 32px;
            font-weight: 800;
            color: #490D59;
            letter-spacing: 5px;
        }
        .footer {
            background: #f9f9f9;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #888;
            border-top: 1px solid #eee;
        }
        .note {
            font-size: 14px;
            color: #666;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>The Skool Store</h1>
        </div>
        <div class="content">
            <h2>Login Verification</h2>
            <p>Hello,</p>
            <p>Use the following One Time Password (OTP) to log in to your account. This OTP is valid for 10 minutes.</p>
            
            <div class="otp-box">
                <div class="otp-code">{{ $otp }}</div>
            </div>
            
            <p class="note">If you didn't request this code, please ignore this email.</p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} The Skool Store. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
