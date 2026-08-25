<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Withdrawal Requests Manifest</title>
    <style>
        @page {
            margin: 1.5cm;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 9pt;
            color: #1e293b;
            line-height: 1.5;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        .header-logo {
            width: 70px;
            vertical-align: middle;
            text-align: left;
        }
        .header-logo img {
            max-height: 55px;
            width: auto;
        }
        .header-text {
            vertical-align: middle;
            text-align: left;
            padding-left: 12px;
        }
        .coop-title {
            font-size: 14pt;
            font-weight: bold;
            color: #065f46; /* Deep Emerald */
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 0;
            padding: 0;
            line-height: 1.1;
        }
        .coop-subtitle {
            font-size: 8pt;
            color: #475569;
            margin: 2px 0 0 0;
            padding: 0;
            font-weight: normal;
        }
        .coop-contact {
            font-size: 7pt;
            color: #64748b;
            margin: 1px 0 0 0;
            padding: 0;
        }
        .header-line {
            border: 0;
            border-top: 2px double #059669;
            margin-bottom: 20px;
        }
        .doc-title-container {
            text-align: center;
            margin-bottom: 20px;
        }
        .doc-title {
            display: inline-block;
            font-size: 11pt;
            font-weight: bold;
            color: #0f172a;
            border-bottom: 2px solid #10b981;
            padding-bottom: 4px;
            text-transform: uppercase;
            letter-spacing: 1.2px;
        }
        
        /* Summary Cards */
        .summary-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .summary-card {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 10px 15px;
            width: 48%;
            vertical-align: top;
        }
        .summary-label {
            font-size: 7.5pt;
            font-weight: bold;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }
        .summary-value {
            font-size: 13pt;
            font-weight: 900;
            color: #0f172a;
        }
        .meta-text {
            font-size: 8pt;
            color: #475569;
            line-height: 1.4;
        }
        .meta-bold {
            font-weight: bold;
            color: #0f172a;
        }

        /* Requests Table */
        .requests-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .requests-table th {
            background-color: #065f46;
            color: #ffffff;
            font-size: 7.5pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 8px 10px;
            text-align: left;
            border: 1px solid #059669;
        }
        .requests-table td {
            padding: 8px 10px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 8.5pt;
            vertical-align: middle;
        }
        .requests-table tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .ref-code {
            font-family: monospace;
            font-weight: bold;
            color: #0f172a;
        }
        .amount-val {
            font-weight: bold;
            color: #0f172a;
            font-family: monospace;
        }
        
        .badge {
            display: inline-block;
            padding: 2px 6px;
            font-size: 7pt;
            font-weight: bold;
            border-radius: 4px;
            text-transform: uppercase;
            text-align: center;
        }
        .badge-pending {
            background-color: #fef3c7;
            color: #92400e;
        }
        .badge-processing {
            background-color: #dbeafe;
            color: #1e40af;
        }
        .badge-released {
            background-color: #dcfce7;
            color: #166534;
        }
        .badge-fallback {
            background-color: #f1f5f9;
            color: #334155;
        }

        /* Sign-off signatures */
        .signoff-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 40px;
            page-break-inside: avoid;
        }
        .signoff-box {
            width: 30%;
            vertical-align: bottom;
        }
        .signoff-spacer {
            width: 5%;
        }
        .signoff-line {
            border-bottom: 1px solid #94a3b8;
            margin-top: 35px;
            margin-bottom: 5px;
            width: 100%;
        }
        .signoff-caption {
            font-size: 7.5pt;
            color: #64748b;
            text-align: center;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        
        .footer {
            position: absolute;
            bottom: -10px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 7pt;
            color: #94a3b8;
            border-top: 1px solid #f1f5f9;
            padding-top: 6px;
        }
    </style>
</head>
<body>

    <!-- Logo and Cooperative Header -->
    <table class="header-table">
        <tr>
            <td class="header-logo">
                @if(extension_loaded('gd') && file_exists(public_path('img/sako-logo-nobg.png')))
                    <img src="{{ public_path('img/sako-logo-nobg.png') }}" alt="Coop Logo">
                @else
                    <span style="font-size: 24pt; line-height: 1; color: #065f46; font-weight: bold;">🏢</span>
                @endif
            </td>
            <td class="header-text">
                <h1 class="coop-title">M Lhuillier Sako Cooperative</h1>
                <h2 class="coop-subtitle">Mutual Benefit Association & Savings Program</h2>
                <p class="coop-contact">Cebu City, Philippines | Email: support@mlsako.com | Tel: (032) 123-4567</p>
            </td>
        </tr>
    </table>

    <div class="header-line"></div>

    <!-- Document Title -->
    <div class="doc-title-container">
        <div class="doc-title">Withdrawal Requests Manifest</div>
    </div>

    <!-- Summary of Exported Data -->
    <table class="summary-table">
        <tr>
            <td class="summary-card" style="margin-right: 4%;">
                <div class="summary-label">Manifest Summary</div>
                <div style="margin-top: 5px;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <tr>
                            <td class="meta-text" style="padding: 2px 0; border: none;">Selected Requests:</td>
                            <td class="meta-text meta-bold" style="padding: 2px 0; border: none; text-align: right;">{{ $withdrawals->count() }}</td>
                        </tr>
                        <tr>
                            <td class="meta-text" style="padding: 2px 0; border: none;">Aggregated Total Amount:</td>
                            <td class="meta-text meta-bold" style="padding: 2px 0; border: none; text-align: right; font-family: monospace; color: #065f46;">{{ number_format($withdrawals->sum('amount'), 2) }}</td>
                        </tr>
                    </table>
                </div>
            </td>
            <td class="summary-spacer" style="width: 4%;"></td>
            <td class="summary-card">
                <div class="summary-label">Export Metadata</div>
                <div style="margin-top: 5px;" class="meta-text">
                    Generated By: <span class="meta-bold">{{ auth()->user()->name }}</span><br>
                    Date Generated: <span class="meta-bold">{{ now()->format('M d, Y h:i A') }}</span><br>
                    Scope: <span class="meta-bold">Selected Queue Items</span>
                </div>
            </td>
        </tr>
    </table>

    <!-- Requests Table -->
    <table class="requests-table">
        <thead>
            <tr>
                <th style="width: 12%;">Ref ID</th>
                <th style="width: 25%;">Member Details</th>
                <th style="width: 15%; text-align: right;">Amount</th>
                <th style="width: 15%;">Channel</th>
                <th style="width: 15%;">Status</th>
                <th style="width: 18%;">Transaction ID</th>
            </tr>
        </thead>
        <tbody>
            @foreach($withdrawals as $w)
                <tr>
                    <td class="ref-code">#WD-{{ str_pad($w->id, 5, '0', STR_PAD_LEFT) }}</td>
                    <td>
                        <div style="font-weight: bold; color: #0f172a;">{{ $w->user->name }}</div>
                        <div style="font-size: 7.5pt; color: #64748b;">ID: {{ $w->user->company_id ?: 'N/A' }}</div>
                    </td>
                    <td class="amount-val" style="text-align: right;">{{ number_format($w->amount, 2) }}</td>
                    <td>{{ $w->channel }}</td>
                    <td>
                        @if($w->status === 'pending')
                            <span class="badge badge-pending">Pending</span>
                        @elseif($w->status === 'processing')
                            <span class="badge badge-processing">Processing</span>
                        @elseif($w->status === 'released')
                            <span class="badge badge-released">Released</span>
                        @else
                            <span class="badge badge-fallback">{{ strtoupper($w->status) }}</span>
                        @endif
                    </td>
                    <td style="font-family: monospace; font-size: 8.5pt; color: #0f172a; font-weight: bold;">
                        {{ $w->transaction_id ?: '—' }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Verification / Sign-off Block -->
    <table class="signoff-table">
        <tr>
            <td class="signoff-box">
                <div class="signoff-label">Prepared By:</div>
                <div class="signoff-line"></div>
                <div class="signoff-caption">{{ auth()->user()->name }}</div>
            </td>
            <td class="signoff-spacer"></td>
            <td class="signoff-box">
                <div class="signoff-label">Checked By:</div>
                <div class="signoff-line"></div>
                <div class="signoff-caption">Finance Department</div>
            </td>
            <td class="signoff-spacer"></td>
            <td class="signoff-box">
                <div class="signoff-label">Approved By:</div>
                <div class="signoff-line"></div>
                <div class="signoff-caption">Cooperative Board</div>
            </td>
        </tr>
    </table>

    <!-- Bottom Footer -->
    <div class="footer">
        M Lhuillier Sako Cooperative • Confidental Administrative Output • Page 1 of 1
    </div>

</body>
</html>
