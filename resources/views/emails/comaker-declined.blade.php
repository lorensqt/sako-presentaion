<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Co-Maker Request Declined</title>
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
            background-color: #9f1239; /* Deep Rose/Crimson */
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
            background-color: #fff5f5; /* Light Red/Rose */
            border: 1px solid #fee2e2;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 25px;
        }
        .summary-row {
            margin-bottom: 8px;
            font-size: 13px;
        }
        .summary-row:last-child {
            margin-bottom: 0;
        }
        .summary-label {
            font-weight: bold;
            color: #9f1239;
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
        .btn-container {
            text-align: center;
            margin: 30px 0;
        }
        .btn {
            display: inline-block;
            background-color: #e11d48; /* Red/Rose */
            color: #ffffff !important;
            text-decoration: none;
            font-weight: bold;
            font-size: 13px;
            padding: 12px 30px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(225, 29, 72, 0.15);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .btn:hover {
            background-color: #be123c;
        }
        .remarks-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-left: 4px solid #cbd5e1;
            border-radius: 8px;
            padding: 15px;
            font-size: 13px;
            color: #334155;
            line-height: 1.5;
            margin-bottom: 20px;
            font-style: italic;
        }
        .warning-box {
            background-color: #fffbeb; /* Light amber */
            border: 1px solid #fef3c7;
            border-radius: 10px;
            padding: 15px;
            font-size: 12px;
            color: #b45309;
            line-height: 1.5;
            margin-top: 20px;
        }
        .warning-title {
            font-weight: bold;
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
                <h1>Request Declined</h1>
                <p>M Lhuillier Sako Cooperative</p>
            </div>
            <div class="content">
                <h2>Hello {{ $borrowerName }},</h2>
                <p>We are writing to inform you that <strong>{{ $coMakerName }}</strong> has declined your co-maker endorsement request for your active loan application.</p>
                
                @if(!empty($remarks))
                    <p><strong>Decline Reason / Remarks:</strong></p>
                    <div class="remarks-box">
                        "{{ $remarks }}"
                    </div>
                @endif

                <div class="summary-card">
                    <div class="summary-row">
                        <span class="summary-label">Loan Product</span>
                        <span class="summary-value">{{ $loanTypeName }}</span>
                    </div>
                    <div class="summary-row" style="margin-top: 12px;">
                        <span class="summary-label">Requested Amount</span>
                        <span class="summary-value">₱{{ number_format($requestedAmount, 2) }}</span>
                    </div>
                </div>

                <p>Don't worry, you can designate a new co-maker for your application. Simply log into the member portal and submit a replacement co-maker from your active loan applications list.</p>

                <div class="btn-container">
                    <a href="{{ route('member.loans') }}" class="btn">Choose New Co-Maker</a>
                </div>

                <div class="warning-box">
                    <div class="warning-title">⚠️ Action Required</div>
                    Your loan application will remain paused in the queue until a replacement co-maker is designated and endorses the request.
                </div>
            </div>
            <div class="footer">
                <p>This is an automated notification from the M Lhuillier Sako Cooperative portal.<br>Please do not reply directly to this email.</p>
            </div>
        </div>
    </div>
</body>
</html>
