<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your OTP</title>
    <style>
        body {
            font-family: Inter, Helvetica, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
        }

        .email-container {
            background-color: #ffffff;
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .otp-title {
            font-size: 24px;
            color: #333333;
            margin-bottom: 10px;
            font-weight: bolder;
        }

        .otp-code {
            font-size: 32px;
            font-weight: bold;
            color: #007BFF;
            letter-spacing: 4px;
            margin: 20px 0;
            display: inline-block;
            background-color: #f0f8ff;
            padding: 10px 20px;
            border-radius: 5px;
        }

        .message {
            font-size: 16px;
            color: #666666;
            margin-bottom: 20px;
        }

        .footer {
            font-size: 12px;
            color: #999999;
            margin-top: 30px;
        }
    </style>
</head>

<body>
    <div class="email-container">
        <h1 class="otp-title">Your OTP Code</h1>
        <p class="message">Please use the code below to verify your identity. The code is valid for a limited time.</p>
        <div class="otp-code">{{ $otp }}</div>
        <p class="message">If you didn't request this, please ignore this email.</p>
        <div class="footer">
            &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
        </div>
    </div>
</body>

</html>
