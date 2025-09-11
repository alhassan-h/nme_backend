<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Your Email Address</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            background-color: #f8f9fa;
            padding: 20px;
        }
        .container {
            background-color: #ffffff;
            border-radius: 8px;
            padding: 40px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #2563eb;
            margin-bottom: 10px;
        }
        .title {
            font-size: 28px;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 10px;
        }
        .subtitle {
            font-size: 16px;
            color: #6b7280;
            margin-bottom: 30px;
        }
        .content {
            margin-bottom: 30px;
        }
        .button {
            display: inline-block;
            background-color: #2563eb;
            color: #ffffff;
            text-decoration: none;
            padding: 12px 30px;
            border-radius: 6px;
            font-weight: 600;
            text-align: center;
            margin: 20px 0;
        }
        .button:hover {
            background-color: #1d4ed8;
        }
        .info-box {
            background-color: #eff6ff;
            border: 1px solid #3b82f6;
            border-radius: 6px;
            padding: 15px;
            margin: 20px 0;
        }
        .info-title {
            font-weight: bold;
            color: #1e40af;
            margin-bottom: 5px;
        }
        .info-text {
            color: #1e40af;
            font-size: 14px;
        }
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            font-size: 14px;
            color: #6b7280;
            text-align: center;
        }
        .link {
            color: #2563eb;
            word-break: break-all;
        }
        .benefits {
            background-color: #f3f4f6;
            border-radius: 6px;
            padding: 20px;
            margin: 20px 0;
        }
        .benefits h3 {
            color: #374151;
            margin-bottom: 10px;
            font-size: 16px;
        }
        .benefits ul {
            margin: 0;
            padding-left: 20px;
            color: #6b7280;
        }
        .benefits li {
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">NME Platform</div>
            <h1 class="title">Verify Your Email Address</h1>
            <p class="subtitle">Complete your account setup to get started</p>
        </div>

        <div class="content">
            <p>Hello {{ $user->first_name }},</p>

            <p>Thank you for registering with NME Platform! To complete your account setup and ensure the security of your account, we need to verify your email address.</p>

            <div style="text-align: center;">
                <a href="{{ $verificationUrl }}" class="button">Verify Email Address</a>
            </div>

            <p>If the button above doesn't work, you can copy and paste this link into your browser:</p>
            <p class="link">{{ $verificationUrl }}</p>

            <div class="info-box">
                <div class="info-title">Why verify your email?</div>
                <div class="info-text">
                    Email verification helps us ensure that your account is secure and that we can communicate important updates with you.
                </div>
            </div>

            <div class="benefits">
                <h3>Once verified, you'll be able to:</h3>
                <ul>
                    <li>Access all platform features</li>
                    <li>Receive important notifications</li>
                    <li>Reset your password if needed</li>
                    <li>Participate in marketplace activities</li>
                    <li>Join community discussions</li>
                </ul>
            </div>

            <p>If you didn't create an account with NME Platform, please ignore this email. Your email address will not be added to our system.</p>
        </div>

        <div class="footer">
            <p>This email was sent to {{ $user->email }} because an account was created with this email address on NME Platform.</p>
            <p>If you have any questions, please contact our support team.</p>
            <p>&copy; {{ date('Y') }} NME Platform. All rights reserved.</p>
        </div>
    </div>
</body>
</html>