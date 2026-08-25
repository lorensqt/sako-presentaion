<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Security Alert: PIN Lockout</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            background-color: #f8fafc;
            margin: 0;
            padding: 0;
            -webkit-font-smoothing: antialiased;
        }
        .wrapper {
            width: 100%;
            background-color: #f8fafc;
            padding: 40px 0;
        }
        .container {
            max-width: 580px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.03);
            border: 1px solid #e2e8f0;
        }
        .header {
            background-color: #be123c; /* Rose-700 Red for Security Alert */
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            color: #ffffff;
            font-size: 20px;
            font-weight: 800;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .header p {
            color: #fecdd3;
            font-size: 11px;
            margin: 5px 0 0 0;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .content {
            padding: 35px 30px;
        }
        .greeting {
            font-size: 15px;
            font-weight: 700;
            color: #0f172a;
            margin: 0 0 15px 0;
        }
        .message-body {
            font-size: 14px;
            color: #334155;
            line-height: 1.6;
            margin: 0 0 25px 0;
        }
        .alert-card {
            background-color: #fff1f2; /* Rose-50 */
            border: 1px solid #fecdd3; /* Rose-200 */
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 25px;
        }
        .alert-item {
            display: flex;
            margin-bottom: 10px;
            font-size: 13px;
        }
        .alert-item:last-child {
            margin-bottom: 0;
        }
        .alert-label {
            font-weight: 700;
            color: #9f1239; /* Rose-800 */
            width: 120px;
            flex-shrink: 0;
        }
        .alert-value {
            color: #334155;
        }
        .divider {
            height: 1px;
            background-color: #e2e8f0;
            margin: 25px 0;
        }
        .footer {
            background-color: #f1f5f9;
            padding: 25px 30px;
            text-align: center;
            border-top: 1px solid #e2e8f0;
        }
        .footer p {
            font-size: 11px;
            color: #64748b;
            margin: 0 0 5px 0;
            line-height: 1.5;
        }
        .footer p:last-child {
            margin-bottom: 0;
        }
        .badge {
            display: inline-block;
            background-color: #e11d48;
            color: #ffffff;
            font-size: 10px;
            font-weight: 800;
            padding: 4px 10px;
            border-radius: 9999px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 12px;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container">
            <div class="header">
                <h1>Security Alert</h1>
                <p>ML SAKO Cooperative Protection</p>
            </div>
            <div class="content">
                <div class="badge">Lockout Triggered</div>
                <h2 class="greeting">Dear {{ $name }},</h2>
                <p class="message-body">
                    We detected consecutive failed security PIN attempts on your account. As an active protective countermeasure to secure your funds and personal information, your current login session has been terminated and the account was logged out immediately.
                </p>
                
                <div class="alert-card">
                    <div class="alert-item">
                        <span class="alert-label">Security Event:</span>
                        <span class="alert-value">Multiple Failed PIN Attempts (3/3)</span>
                    </div>
                    <div class="alert-item">
                        <span class="alert-label">Protective Action:</span>
                        <span class="alert-value">Session Terminated & Automatic Logout</span>
                    </div>
                    <div class="alert-item">
                        <span class="alert-label">Timestamp:</span>
                        <span class="alert-value">{{ now()->format('Y-m-d H:i:s T') }}</span>
                    </div>
                </div>

                <p class="message-body" style="font-weight: 600;">
                    What should you do next?
                </p>
                <p class="message-body" style="font-size: 13px;">
                    • <strong>If this was you:</strong> You can log back into your account. Please make sure to enter your 6-digit PIN carefully. If you have forgotten your PIN, please contact your Sako Administrator immediately.<br><br>
                    • <strong>If this was NOT you:</strong> Someone else may be attempting to access your account. Please log in immediately, change your primary password, and contact administration to report unauthorized activity.
                </p>
                
                <div class="divider"></div>
                <p class="message-body" style="font-size: 12px; color: #64748b; font-style: italic; text-align: center;">
                    This is an automated system notification. Please do not reply directly to this email.
                </p>
            </div>
            <div class="footer">
                <p><strong>ML Sako Cooperative Association</strong></p>
                <p>Empowering Your Financial Future, Together.</p>
                <p>&copy; {{ date('Y') }} ML Sako. All rights reserved.</p>
            </div>
        </div>
    </div>
</body>
</html>
