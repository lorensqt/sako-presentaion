<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Withdrawal Request Declined Notification</title>
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
            background-color: #e11d48; /* Rose */
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
            color: #ffe4e6;
            font-size: 11px;
            margin: 5px 0 0 0;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .content {
            padding: 35px 30px;
        }
        .content h2 {
            color: #0f172a;
            font-size: 16px;
            font-weight: 700;
            margin-top: 0;
            margin-bottom: 15px;
        }
        .content p {
            color: #475569;
            font-size: 14px;
            line-height: 1.6;
            margin-top: 0;
            margin-bottom: 20px;
        }
        .summary-card {
            background-color: #fff1f2;
            border: 1px solid #fecdd3;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 25px;
        }
        .summary-row {
            margin-bottom: 10px;
            font-size: 13px;
        }
        .summary-row:last-child {
            margin-bottom: 0;
        }
        .summary-label {
            font-weight: bold;
            color: #be123c;
            text-transform: uppercase;
            font-size: 10px;
            letter-spacing: 0.5px;
            display: block;
            margin-bottom: 2px;
        }
        .summary-value {
            color: #0f172a;
            font-weight: 600;
            font-size: 14px;
        }
        .remarks-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-left: 4px solid #e11d48;
            border-radius: 8px;
            padding: 16px 20px;
            margin-bottom: 25px;
        }
        .remarks-label {
            font-size: 10px;
            font-weight: 800;
            color: #be123c;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: block;
            margin-bottom: 4px;
        }
        .remarks-text {
            color: #334155;
            font-size: 13.5px;
            font-style: italic;
            margin: 0;
            line-height: 1.6;
        }
        .btn-container {
            text-align: center;
            margin: 30px 0;
        }
        .btn {
            display: inline-block;
            background-color: #0f172a;
            color: #ffffff !important;
            text-decoration: none;
            font-weight: bold;
            font-size: 13px;
            padding: 12px 30px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(15, 23, 42, 0.15);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .info-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 15px;
            font-size: 12px;
            color: #475569;
            line-height: 1.5;
            margin-top: 20px;
        }
        .info-title {
            font-weight: bold;
            color: #0f172a;
            margin-bottom: 3px;
            text-transform: uppercase;
            font-size: 9.5px;
            letter-spacing: 0.3px;
        }
        .footer {
            background-color: #f1f5f9;
            padding: 20px 30px;
            text-align: center;
            border-top: 1px solid #e2e8f0;
        }
        .footer p {
            color: #94a3b8;
            font-size: 11px;
            margin: 0;
            line-height: 1.5;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container">
            <div class="header">
                <h1>Withdrawal Request Declined</h1>
                <p>M Lhuillier Sako Cooperative</p>
            </div>
            
            <div class="content">
                <h2>Hello {{ $memberName }},</h2>
                <p>
                    Please be informed that your savings payout request has been reviewed by the cooperative administration and could not be approved at this time.
                </p>
                
                <div class="summary-card">
                    <div class="summary-row">
                        <span class="summary-label">Reference Number</span>
                        <span class="summary-value" style="font-family: monospace; font-weight: bold;">{{ $referenceNo }}</span>
                    </div>
                    <div class="summary-row">
                        <span class="summary-label">Disbursement Channel</span>
                        <span class="summary-value">{{ $channel }}</span>
                    </div>
                    <div class="summary-row" style="margin-top: 12px;">
                        <span class="summary-label">Requested Amount</span>
                        <span class="summary-value" style="color: #be123c; font-size: 20px; font-weight: bold; font-family: monospace;">
                            ₱{{ number_format($amount, 2) }}
                        </span>
                    </div>
                    <div class="summary-row" style="margin-top: 8px;">
                        <span class="summary-label">Date Reviewed</span>
                        <span class="summary-value" style="font-size: 12px; color: #64748b;">{{ $rejectedAt }}</span>
                    </div>
                </div>
                
                <div class="remarks-box">
                    <span class="remarks-label">Administrative Rejection Reason / Remarks</span>
                    <p class="remarks-text">
                        &ldquo;{{ $remarks }}&rdquo;
                    </p>
                    @if(!empty($adminName))
                        <span style="display: block; font-size: 11px; color: #64748b; font-weight: 700; margin-top: 8px; text-transform: uppercase; letter-spacing: 0.5px;">
                            — Reviewed by {{ $adminName }} (Cooperative Administrator)
                        </span>
                    @endif
                </div>
                
                <p>
                    No funds have been deducted from your savings pool balance. You may submit a new request if your circumstances change or after addressing the remarks above.
                </p>
                
                <div class="btn-container">
                    <a href="{{ route('member.withdrawals') }}" class="btn">Check Member Portal</a>
                </div>
                
                <div class="info-box">
                    <div class="info-title">ℹ️ Need Assistance?</div>
                    If you believe this decline was made in error or need further clarification, feel free to contact the cooperative finance desk.
                </div>
            </div>
            
            <div class="footer">
                <p>
                    M Lhuillier Sako Cooperative • Confidential MBA &amp; Savings Program<br>
                    Cebu City, Philippines • This is an automated email, please do not reply.
                </p>
            </div>
        </div>
    </div>
</body>
</html>
