<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payroll Deduction Adjustment - ADJ-{{ str_pad($deductionRequest->id, 5, '0', STR_PAD_LEFT) }}</title>
    <style>
        @page {
            margin: 1.5cm;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 10pt;
            color: #1e293b;
            line-height: 1.6;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .header-logo {
            width: 80px;
            vertical-align: middle;
            text-align: left;
        }
        .header-logo img {
            max-height: 65px;
            width: auto;
        }
        .header-text {
            vertical-align: middle;
            text-align: left;
            padding-left: 15px;
        }
        .coop-title {
            font-size: 15pt;
            font-weight: bold;
            color: #065f46; /* Deep Emerald */
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 0;
            padding: 0;
            line-height: 1.2;
        }
        .coop-subtitle {
            font-size: 8.5pt;
            color: #475569;
            margin: 3px 0 0 0;
            padding: 0;
            font-weight: normal;
        }
        .coop-contact {
            font-size: 7.5pt;
            color: #64748b;
            margin: 2px 0 0 0;
            padding: 0;
        }
        .header-line {
            border: 0;
            border-top: 3px double #059669;
            margin-bottom: 25px;
        }
        .doc-title-container {
            text-align: center;
            margin-bottom: 25px;
        }
        .doc-title {
            display: inline-block;
            font-size: 12pt;
            font-weight: bold;
            color: #0f172a;
            border-bottom: 2px solid #10b981;
            padding-bottom: 4px;
            text-transform: uppercase;
            letter-spacing: 1.5px;
        }
        .section-title {
            font-size: 9.5pt;
            font-weight: bold;
            color: #065f46;
            background-color: #f0fdf4;
            border-left: 3px solid #059669;
            padding: 5px 10px;
            margin-top: 25px;
            margin-bottom: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .info-table td {
            padding: 6px 12px;
            vertical-align: top;
            border-bottom: 1px solid #f1f5f9;
        }
        .info-label {
            width: 28%;
            font-weight: bold;
            color: #475569;
            font-size: 9pt;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        .info-value {
            width: 72%;
            color: #0f172a;
            font-size: 9.5pt;
        }
        .badge {
            display: inline-block;
            padding: 2px 8px;
            font-size: 8pt;
            font-weight: bold;
            border-radius: 4px;
            text-transform: uppercase;
        }
        .badge-warning {
            background-color: #fef3c7;
            color: #92400e;
        }
        .badge-success {
            background-color: #dcfce7;
            color: #15803d;
        }
        .badge-danger {
            background-color: #fee2e2;
            color: #991b1b;
        }
        .signoff-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 50px;
        }
        .signoff-box {
            width: 48%;
            vertical-align: bottom;
        }
        .signoff-spacer {
            width: 4%;
        }
        .signoff-line {
            border-bottom: 1px solid #94a3b8;
            margin-top: 45px;
            margin-bottom: 6px;
            width: 100%;
        }
        .signoff-caption {
            font-size: 8pt;
            color: #64748b;
            text-align: center;
        }
        .footer {
            position: absolute;
            bottom: -10px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 7.5pt;
            color: #94a3b8;
            border-top: 1px solid #f1f5f9;
            padding-top: 8px;
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
                    <span style="font-size: 28pt; line-height: 1; color: #065f46; font-weight: bold;">🏢</span>
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
        <div class="doc-title">Payroll Deduction Adjustment Form</div>
        <div style="font-size: 9pt; color: #64748b; margin-top: 5px; font-weight: bold; font-family: monospace;">
            REFERENCE NO: ADJ-{{ str_pad($deductionRequest->id, 5, '0', STR_PAD_LEFT) }}
        </div>
    </div>

    <!-- Section 1: Member Credentials -->
    <div class="section-title">Cooperative Member Information</div>
    <table class="info-table">
        <tr>
            <td class="info-label">Member ID</td>
            <td class="info-value" style="font-family: monospace; font-weight: bold; font-size: 11.5pt; color: #065f46;">
                {{ $deductionRequest->user->company_id ?: 'N/A' }}
            </td>
        </tr>
        <tr>
            <td class="info-label">Full Name</td>
            <td class="info-value" style="font-weight: bold;">
                {{ $deductionRequest->user->name }}
            </td>
        </tr>
        <tr>
            <td class="info-label">Email Address</td>
            <td class="info-value">
                {{ $deductionRequest->user->email }}
            </td>
        </tr>
        @if($deductionRequest->user->contact_number)
        <tr>
            <td class="info-label">Contact Number</td>
            <td class="info-value">
                {{ $deductionRequest->user->contact_number }}
            </td>
        </tr>
        @endif
    </table>

    <!-- Section 2: Adjustment Specifications -->
    <div class="section-title">Contribution Adjustment Specifications</div>
    <table class="info-table">
        <tr>
            <td class="info-label">Savings Deposit Amount</td>
            <td class="info-value" style="font-family: monospace; font-weight: bold; color: #0f172a;">
                {{ number_format($deductionRequest->savings_amount, 2) }} / Payday
            </td>
        </tr>
        <tr>
            <td class="info-label">Fixed Deposit Amount</td>
            <td class="info-value" style="font-family: monospace; font-weight: bold; color: #0f172a;">
                {{ number_format($deductionRequest->fixed_amount, 2) }} / Payday
            </td>
        </tr>
        <tr>
            <td class="info-label">Effectivity Date</td>
            <td class="info-value" style="font-weight: bold;">
                {{ $deductionRequest->effectivity_date->format('F d, Y') }}
            </td>
        </tr>
        <tr>
            <td class="info-label">Remarks / Purpose</td>
            <td class="info-value" style="font-style: italic;">
                {{ $deductionRequest->remarks ?: 'No additional remarks submitted' }}
            </td>
        </tr>
    </table>

    <!-- Section 3: Evaluation Summary -->
    <div class="section-title">Administrative Evaluation & Status</div>
    <table class="info-table">
        <tr>
            <td class="info-label">Filing Date</td>
            <td class="info-value">
                {{ $deductionRequest->created_at->format('F d, Y \a\t h:i A') }}
            </td>
        </tr>
        <tr>
            <td class="info-label">Current Status</td>
            <td class="info-value">
                @if($deductionRequest->status === 'pending')
                    <span class="badge badge-warning">Awaiting Review</span>
                @elseif($deductionRequest->status === 'approved')
                    <span class="badge badge-success">Approved</span>
                @else
                    <span class="badge badge-danger">Rejected</span>
                @endif
            </td>
        </tr>
        @if($deductionRequest->status !== 'pending')
        <tr>
            <td class="info-label">Evaluation Date</td>
            <td class="info-value">
                {{ $deductionRequest->updated_at->format('F d, Y \a\t h:i A') }}
            </td>
        </tr>
        @endif
    </table>

    <!-- Section 4: Authentication and Signatories -->
    <table class="signoff-table">
        <tr>
            <td class="signoff-box">
                @if($deductionRequest->user->signature && file_exists(storage_path('app/public/' . $deductionRequest->user->signature)))
                    <div style="text-align: center; margin-bottom: -15px;">
                        <img src="{{ storage_path('app/public/' . $deductionRequest->user->signature) }}" alt="Member Signature" style="max-height: 50px; max-width: 150px; object-fit: contain;">
                    </div>
                @endif
                <div class="signoff-line" style="margin-top: {{ $deductionRequest->user->signature && file_exists(storage_path('app/public/' . $deductionRequest->user->signature)) ? '5px' : '35px' }};"></div>
                <div class="signoff-caption"><span style="font-weight: bold; color: #0f172a;">{{ $deductionRequest->user->name }}</span><br>Filing Member Signature &amp; Date</div>
            </td>
            <td class="signoff-spacer"></td>
            <td class="signoff-box">
                @if($deductionRequest->approver && $deductionRequest->approver->signature && file_exists(storage_path('app/public/' . $deductionRequest->approver->signature)))
                    <div style="text-align: center; margin-bottom: -15px;">
                        <img src="{{ storage_path('app/public/' . $deductionRequest->approver->signature) }}" alt="Approver Signature" style="max-height: 50px; max-width: 150px; object-fit: contain;">
                    </div>
                @endif
                <div class="signoff-line" style="margin-top: {{ $deductionRequest->approver && $deductionRequest->approver->signature && file_exists(storage_path('app/public/' . $deductionRequest->approver->signature)) ? '5px' : '35px' }};"></div>
                <div class="signoff-caption">
                    <span style="font-weight: bold; color: #0f172a;">
                        @if($deductionRequest->status === 'approved')
                            {{ $deductionRequest->approver ? $deductionRequest->approver->name : 'Admin Signed' }} (Approved)
                        @elseif($deductionRequest->status === 'rejected')
                            {{ $deductionRequest->approver ? $deductionRequest->approver->name : 'Admin Signed' }} (Rejected)
                        @else
                            Pending Action
                        @endif
                    </span><br>
                    Authorized Administrative Sign-off
                </div>
            </td>
        </tr>
    </table>

    <div class="footer">
        This is a computer-generated document from the M Lhuillier Sako Cooperative Portal.<br>
        Page 1 of 1 | Security Hash: {{ md5($deductionRequest->id . '-' . $deductionRequest->created_at) }}
    </div>

</body>
</html>
