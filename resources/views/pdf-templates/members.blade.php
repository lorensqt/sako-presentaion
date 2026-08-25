<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Member Profile - {{ $user->name }}</title>
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
            background-color: #e2e8f0;
            color: #334155;
            border-radius: 4px;
            text-transform: uppercase;
        }
        .badge-success {
            background-color: #dcfce7;
            color: #15803d;
        }
        .badge-primary {
            background-color: #e0f2fe;
            color: #0369a1;
        }
        .signoff-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 45px;
        }
        .signoff-box {
            width: 48%;
            vertical-align: bottom;
        }
        .signoff-spacer {
            width: 4%;
        }
        .signoff-label {
            font-size: 8pt;
            color: #64748b;
            margin-bottom: 4px;
        }
        .signoff-value {
            font-size: 9pt;
            color: #1e293b;
            font-weight: bold;
        }
        .signoff-line {
            border-bottom: 1px solid #94a3b8;
            margin-top: 40px;
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
        <div class="doc-title">Member Information Sheet</div>
    </div>

    <!-- Section 1: Membership Credentials -->
    <div class="section-title">Membership Profile</div>
    <table class="info-table">
        <tr>
            <td class="info-label">Member ID</td>
            <td class="info-value" style="font-family: monospace; font-weight: bold; font-size: 11pt; color: #065f46;">
                {{ $user->company_id }}
            </td>
        </tr>
        <tr>
            <td class="info-label">Current Status</td>
            <td class="info-value">
                <span class="badge badge-success">Active Member</span>
            </td>
        </tr>
        <tr>
            <td class="info-label">System Account Level</td>
            <td class="info-value">
                <span class="badge badge-primary">{{ ucwords(str_replace('_', ' ', $user->role)) }}</span>
            </td>
        </tr>
        <tr>
            <td class="info-label">Designated Group Roles</td>
            <td class="info-value" style="font-weight: 500;">
                @if($user->roles->isNotEmpty())
                    {{ $user->roles->pluck('name')->implode(', ') }}
                @else
                    <span style="color: #64748b; font-style: italic;">Standard Member</span>
                @endif
            </td>
        </tr>
        <tr>
            <td class="info-label">Registration Date</td>
            <td class="info-value">
                {{ $user->created_at ? $user->created_at->format('F d, Y') : 'N/A' }}
            </td>
        </tr>
    </table>

    <!-- Section 2: Personal Details -->
    <div class="section-title">Personal Information</div>
    <table class="info-table">
        <tr>
            <td class="info-label">Full Name</td>
            <td class="info-value" style="font-weight: bold; font-size: 10.5pt;">
                {{ $user->name }}
            </td>
        </tr>
        <tr>
            <td class="info-label">Email Address</td>
            <td class="info-value">
                {{ $user->email }}
            </td>
        </tr>
        <tr>
            <td class="info-label">Contact Number</td>
            <td class="info-value">
                {{ $user->contact_number ?: 'N/A' }}
            </td>
        </tr>
        <tr>
            <td class="info-label">Residential Address</td>
            <td class="info-value">
                {{ $user->address ?: 'N/A' }}
            </td>
        </tr>
    </table>

    <!-- Section 3: Validation and Verification -->
    <table class="signoff-table">
        <tr>
            <!-- Left Signature box (Office) -->
            <td class="signoff-box">
                <div class="signoff-label">Certified True and Correct:</div>
                <div class="signoff-value" style="margin-top: 15px;">{{ auth()->user()->name }}</div>
                <div style="font-size: 8pt; color: #475569; font-weight: normal; margin-top: 2px;">
                    {{ ucwords(str_replace('_', ' ', auth()->user()->role)) }} (System Administrator)
                </div>
                <div style="font-size: 8.5pt; color: #64748b; margin-top: 4px;">
                    Generated on: {{ now()->format('F d, Y h:i A') }}
                </div>
            </td>
            
            <!-- Spacer -->
            <td class="signoff-spacer"></td>
            
            <!-- Right Signature box (Member) -->
            <td class="signoff-box">
                @if($user->signature && file_exists(storage_path('app/public/' . $user->signature)))
                    <div style="text-align: center; margin-bottom: -15px;">
                        <img src="{{ storage_path('app/public/' . $user->signature) }}" alt="Member Signature" style="max-height: 55px; max-width: 160px; object-fit: contain;">
                    </div>
                @endif
                <div class="signoff-line" style="margin-top: {{ $user->signature && file_exists(storage_path('app/public/' . $user->signature)) ? '5px' : '40px' }};"></div>
                <div class="signoff-caption">
                    <span style="font-weight: bold; color: #0f172a; text-transform: uppercase; font-size: 9.5pt;">{{ $user->name }}</span><br>
                    Registered Member Signature
                </div>
            </td>
        </tr>
    </table>

    <!-- Professional Footer -->
    <div class="footer">
        ML Lhuillier Sako Cooperative • Confidential Official Record • Page 1 of 1
    </div>

</body>
</html>
