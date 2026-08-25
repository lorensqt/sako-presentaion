<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Co-Maker Endorsement Request</title>
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
            background-color: #065f46; /* Deep Emerald */
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
            color: #a7f3d0;
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
            background-color: #f0fdf4; /* Light Mint */
            border: 1px solid #bbf7d0;
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
            color: #047857;
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
            background-color: #059669; /* Emerald */
            color: #ffffff !important;
            text-decoration: none;
            font-weight: bold;
            font-size: 13px;
            padding: 12px 30px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(5, 150, 105, 0.15);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .btn:hover {
            background-color: #047857;
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
                <h1>Cooperative Endorsement</h1>
                <p>M Lhuillier Sako Cooperative</p>
            </div>
            
            <div class="content">
                <h2>Hello {{ $coMakerName }},</h2>
                <p>
                    You have been nominated as a **Solidary Co-Maker** for a new cooperative loan application by **{{ $borrowerName }}**. Please review the parameters of the request below:
                </p>
                
                <div class="summary-card">
                    <div class="summary-row">
                        <span class="summary-label">Borrower Name</span>
                        <span class="summary-value">{{ $borrowerName }}</span>
                    </div>
                    <div class="summary-row">
                        <span class="summary-label">Loan Package</span>
                        <span class="summary-value">{{ $loanTypeName }}</span>
                    </div>
                    <div class="summary-row" style="margin-top: 12px;">
                        <span class="summary-label">Requested Principal</span>
                        <span class="summary-value" style="color: #065f46; font-size: 18px; font-weight: bold;">
                            ₱{{ number_format($requestedAmount, 2) }}
                        </span>
                    </div>
                </div>
                
                <p>
                    To endorse or reject this request, please click the button below to focus directly into your co-maker requests panel:
                </p>
                
                <div class="btn-container">
                    <a href="{{ route('member.comaker_requests') }}" class="btn">View Request Panel</a>
                </div>
                
                <div class="warning-box">
                    <div class="warning-title">⚠️ Solidary Liability Notice</div>
                    By approving this co-makership endorsement, you agree to bind yourself jointly and severally (solidarily liable) with the primary borrower. In the event of default on payment, the cooperative holds legal authority to recover the balance from your share capital, savings pool, or through direct payroll deductions.
                </div>
            </div>
            
            <div class="footer">
                <p>
                    ML Lhuillier Sako Cooperative • Confidential MBA & Savings Program<br>
                    Cebu City, Philippines • This is an automated email, please do not reply.
                </p>
            </div>
        </div>
    </div>
</body>
</html>
