<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $type === 'email_verification' ? 'Email Verification' : 'Password Reset' }} OTP - AC Chill</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 40px auto;
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #1E88E5 0%, #1565C0 100%);
            color: #ffffff;
            text-align: center;
            padding: 30px 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 600;
        }
        .header p {
            margin: 10px 0 0;
            font-size: 14px;
            opacity: 0.9;
        }
        .content {
            padding: 40px 30px;
            text-align: center;
        }
        .greeting {
            font-size: 18px;
            color: #333333;
            margin-bottom: 20px;
        }
        .message {
            font-size: 16px;
            color: #666666;
            line-height: 1.6;
            margin-bottom: 30px;
        }
        .otp-box {
            background: linear-gradient(135deg, #E3F2FD 0%, #BBDEFB 100%);
            border: 2px dashed #1E88E5;
            border-radius: 10px;
            padding: 25px;
            margin: 30px 0;
        }
        .otp-label {
            font-size: 14px;
            color: #1565C0;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 10px;
        }
        .otp-code {
            font-size: 36px;
            font-weight: 700;
            color: #1E88E5;
            letter-spacing: 8px;
            font-family: 'Courier New', monospace;
        }
        .validity {
            font-size: 13px;
            color: #999999;
            margin-top: 25px;
        }
        .warning {
            background-color: #FFF3E0;
            border-left: 4px solid #FF9800;
            padding: 15px;
            margin: 25px 0;
            text-align: left;
            font-size: 14px;
            color: #E65100;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 25px;
            text-align: center;
            border-top: 1px solid #eeeeee;
        }
        .footer p {
            margin: 5px 0;
            font-size: 13px;
            color: #999999;
        }
        .footer a {
            color: #1E88E5;
            text-decoration: none;
        }
        .brand {
            color: #1E88E5;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>AC Chill</h1>
            <p>Cool Comfort at Your Doorstep</p>
        </div>

        <div class="content">
            <p class="greeting">Hello, <strong>{{ $userName }}</strong>!</p>

            @if($type === 'email_verification')
                <p class="message">
                    Thank you for registering with <span class="brand">AC Chill</span>!
                    To complete your registration, please verify your email address using the OTP below.
                </p>
            @else
                <p class="message">
                    We received a request to reset your password for your <span class="brand">AC Chill</span> account.
                    Use the OTP below to proceed with the password reset.
                </p>
            @endif

            <div class="otp-box">
                <div class="otp-label">Your Verification Code</div>
                <div class="otp-code">{{ $otp }}</div>
            </div>

            <p class="validity">This OTP is valid for <strong>10 minutes</strong>.</p>

            <div class="warning">
                <strong>Security Notice:</strong> Never share this OTP with anyone.
                AC Chill team will never ask for your OTP via phone or email.
            </div>
        </div>

        <div class="footer">
            <p>If you didn't request this, please ignore this email.</p>
            <p>Need help? Contact us at <a href="mailto:support@acchill.com">support@acchill.com</a></p>
            <p>&copy; {{ date('Y') }} AC Chill. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
