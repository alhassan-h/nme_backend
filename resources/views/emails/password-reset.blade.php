<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Your Password</title>
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
            background-color: #2563eb !important;
            color: #ffffff !important;
            text-decoration: none !important;
            padding: 12px 30px !important;
            border-radius: 6px !important;
            font-weight: 600 !important;
            text-align: center !important;
            margin: 20px 0 !important;
            border: 1px solid #2563eb !important;
        }
        .button:hover {
            background-color: #1d4ed8 !important;
        }
        .warning {
            background-color: #fef3c7;
            border: 1px solid #f59e0b;
            border-radius: 6px;
            padding: 15px;
            margin: 20px 0;
        }
        .warning-title {
            font-weight: bold;
            color: #92400e;
            margin-bottom: 5px;
        }
        .warning-text {
            color: #78350f;
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
        .security-note {
            background-color: #f3f4f6;
            border-radius: 6px;
            padding: 15px;
            margin: 20px 0;
            font-size: 14px;
            color: #374151;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">NME Platform</div>
            <h1 class="title">Reset Your Password</h1>
            <p class="subtitle">We received a request to reset your password</p>
        </div>

        <div class="content">
            <p>Hello {{ $user->first_name }},</p>

            <p>We received a request to reset the password for your NME Platform account associated with this email address. If you made this request, please click the button below to reset your password:</p>

            <div style="text-align: center;">
                <a href="{{ $resetUrl }}" class="button" style="display: inline-block; background-color: #2563eb; color: #ffffff; text-decoration: none; padding: 12px 30px; border-radius: 6px; font-weight: 600; text-align: center; margin: 20px 0; border: 1px solid #2563eb;">Reset Password</a>
            </div>

            <p>If the button above doesn't work, you can copy and paste this link into your browser:</p>
            <p class="link">{{ $resetUrl }}</p>

            <div class="warning">
                <div class="warning-title">Important Security Information</div>
                <div class="warning-text">
                    This password reset link will expire in 60 minutes for your security.
                    If you didn't request this password reset, please ignore this email.
                    Your password will remain unchanged.
                </div>
            </div>

            <div class="security-note">
                <strong>Security Tips:</strong>
                <ul style="margin: 10px 0; padding-left: 20px;">
                    <li>Choose a strong password with at least 8 characters</li>
                    <li>Include a mix of uppercase and lowercase letters</li>
                    <li>Add numbers and special characters</li>
                    <li>Don't reuse passwords from other accounts</li>
                </ul>
            </div>
        </div>

        <div class="footer">
            <p>This email was sent to {{ $user->email }} because a password reset was requested for your NME Platform account.</p>
            <p>If you have any questions, please contact our support team.</p>
            <p>&copy; {{ date('Y') }} NME Platform. All rights reserved.</p>
        </div>
    </div>
</body>
</html>