<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $newsletter->subject }}</title>
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
            border-bottom: 2px solid #2563eb;
            padding-bottom: 20px;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #2563eb;
            margin-bottom: 10px;
        }
        .subject {
            font-size: 24px;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 10px;
        }
        .content {
            margin-bottom: 30px;
            font-size: 16px;
            line-height: 1.7;
        }
        .content h1, .content h2, .content h3 {
            color: #1f2937;
            margin-top: 30px;
            margin-bottom: 15px;
        }
        .content p {
            margin-bottom: 15px;
        }
        .content ul, .content ol {
            margin-bottom: 15px;
            padding-left: 30px;
        }
        .content li {
            margin-bottom: 5px;
        }
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            font-size: 14px;
            color: #6b7280;
            text-align: center;
        }
        .unsubscribe-link {
            color: #2563eb;
            text-decoration: none;
        }
        .unsubscribe-link:hover {
            text-decoration: underline;
        }
        .info-box {
            background-color: #eff6ff;
            border: 1px solid #3b82f6;
            border-radius: 6px;
            padding: 15px;
            margin: 20px 0;
        }
        .info-text {
            color: #1e40af;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">NME Platform</div>
            <h1 class="subject">{{ $newsletter->subject }}</h1>
        </div>

        <div class="content">
            @if($newsletter->html_content)
                {!! $newsletter->html_content !!}
            @else
                {!! nl2br(e($newsletter->content)) !!}
            @endif
        </div>

        <div class="info-box">
            <div class="info-text">
                <strong>You're receiving this newsletter because you're subscribed to NME Platform updates.</strong>
                <br>
                <a href="{{ $unsubscribeUrl }}" class="unsubscribe-link">Unsubscribe from future newsletters</a>
            </div>
        </div>

        <div class="footer">
            <p>This newsletter was sent to {{ $subscriber->email }} from NME Platform.</p>
            <p>If you have any questions, please contact our support team.</p>
            <p>&copy; {{ date('Y') }} NME Platform. All rights reserved.</p>
        </div>
    </div>
</body>
</html>