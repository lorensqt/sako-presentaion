<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Loan Application Agreement - LN-{{ str_pad($application->id, 5, '0', STR_PAD_LEFT) }}</title>
    <style>
        @page {
            margin: 1.2cm;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 8.5pt;
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
            font-size: 13pt;
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
            margin: 2px 0 0 0;
            padding: 0;
        }
        .header-line {
            border: 0;
            border-top: 3px double #059669;
            margin-bottom: 15px;
        }
        .doc-title-container {
            text-align: center;
            margin-bottom: 15px;
        }
        .doc-title {
            display: inline-block;
            font-size: 10.5pt;
            font-weight: bold;
            color: #0f172a;
            border-bottom: 2px solid #10b981;
            padding-bottom: 3px;
            text-transform: uppercase;
            letter-spacing: 1.2px;
        }
        .section-title {
            font-size: 8.5pt;
            font-weight: bold;
            color: #065f46;
            background-color: #f0fdf4;
            border-left: 3px solid #059669;
            padding: 4px 8px;
            margin-top: 15px;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        .info-table td {
            padding: 5px 8px;
            vertical-align: top;
            border-bottom: 1px solid #f1f5f9;
        }
        .info-label {
            width: 25%;
            font-weight: bold;
            color: #475569;
            font-size: 8pt;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        .info-value {
            width: 75%;
            color: #0f172a;
            font-size: 8.5pt;
        }
        .badge {
            display: inline-block;
            padding: 1px 6px;
            font-size: 7.5pt;
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
        .badge-danger {
            background-color: #ffe4e6;
            color: #b91c1c;
        }
        .badge-primary {
            background-color: #e0f2fe;
            color: #0369a1;
        }
        /* Terms and conditions container styling */
        .terms-box {
            border: 1px solid #cbd5e1;
            background-color: #fafafa;
            border-radius: 6px;
            padding: 8px 12px;
            margin-top: 10px;
            margin-bottom: 15px;
        }
        .terms-header {
            font-size: 8pt;
            font-weight: bold;
            color: #0f172a;
            text-transform: uppercase;
            margin-bottom: 6px;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 3px;
            letter-spacing: 0.5px;
        }
        .terms-ol {
            margin: 0;
            padding-left: 15px;
        }
        .terms-li {
            font-size: 7.5pt;
            color: #475569;
            margin-bottom: 5px;
            text-align: justify;
        }
        .terms-bold {
            font-weight: bold;
            color: #1e293b;
        }
        /* Audit Trail timeline log styling */
        .audit-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8pt;
            margin-bottom: 10px;
        }
        .audit-table th {
            background-color: #f8fafc;
            border-bottom: 2px solid #cbd5e1;
            padding: 5px 8px;
            font-weight: bold;
            color: #475569;
            text-align: left;
            text-transform: uppercase;
            font-size: 7.5pt;
        }
        .audit-table td {
            padding: 5px 8px;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: top;
        }
        .signoff-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 25px;
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
            margin-top: 35px;
            margin-bottom: 4px;
            width: 100%;
        }
        .signoff-caption {
            font-size: 7.5pt;
            color: #64748b;
            text-align: center;
        }
        .footer {
            position: absolute;
            bottom: -15px;
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
        <div class="doc-title">Cooperative Loan Application & Agreement Contract</div>
    </div>

    <!-- Section 1: Borrower Information -->
    <div class="section-title">Borrower Details</div>
    <table class="info-table">
        <tr>
            <td class="info-label">Full Name</td>
            <td class="info-value" style="font-weight: bold; font-size: 9.5pt;">{{ $application->borrower->name }}</td>
        </tr>
        <tr>
            <td class="info-label">Company / Member ID</td>
            <td class="info-value" style="font-family: monospace; font-weight: bold; color: #065f46;">{{ $application->borrower->company_id }}</td>
        </tr>
        <tr>
            <td class="info-label">Contact & Email</td>
            <td class="info-value">{{ $application->borrower->contact_number ?: 'N/A' }} &bull; {{ $application->borrower->email }}</td>
        </tr>
        <tr>
            <td class="info-label">Residential Address</td>
            <td class="info-value">{{ $application->borrower->address ?: 'N/A' }}</td>
        </tr>
    </table>

    <!-- Section 2: Loan Parameters -->
    <div class="section-title">Loan Parameters</div>
    <table class="info-table">
        <tr>
            <td class="info-label">Reference Number</td>
            <td class="info-value" style="font-family: monospace; font-weight: bold; color: #b91c1c;">
                LN-{{ str_pad($application->id, 5, '0', STR_PAD_LEFT) }}
            </td>
        </tr>
        <tr>
            <td class="info-label">Loan Type</td>
            <td class="info-value" style="font-weight: bold;">
                {{ config("loans.{$application->loan_category}.{$application->loan_type}.name", ucwords(str_replace('_', ' ', $application->loan_type))) }}
                ({{ ucwords($application->loan_category) }} category)
            </td>
        </tr>
        <tr>
            <td class="info-label">Requested Principal</td>
            <td class="info-value" style="font-weight: bold; font-size: 10pt; color: #0f172a;">
                ₱{{ number_format($application->requested_amount, 2) }}
            </td>
        </tr>
        <tr>
            <td class="info-label">Repayment Term</td>
            <td class="info-value">{{ $application->form_data['term_months'] ?? 'N/A' }} Months</td>
        </tr>
        <tr>
            <td class="info-label">Submission Date</td>
            <td class="info-value">{{ $application->created_at ? $application->created_at->format('F d, Y \a\t h:i A') : 'N/A' }}</td>
        </tr>
        <tr>
            <td class="info-label">Current Status</td>
            <td class="info-value">
                @if($application->status === 'approved')
                    <span class="badge badge-success">Approved / Completed</span>
                @elseif($application->status === 'rejected')
                    <span class="badge badge-danger">Rejected</span>
                @else
                    <span class="badge badge-primary">Pending Stage: {{ ucwords(str_replace('_', ' ', $application->current_stage)) }}</span>
                @endif
            </td>
        </tr>
    </table>

    <!-- Section 3: Dynamic Form Data Details (Metadata details if available) -->
    @if(!empty($application->form_data) && count($application->form_data) > 1)
    <div class="section-title">Application Custom Details</div>
    <table class="info-table">
        @foreach($application->form_data as $key => $val)
            @if(!in_array($key, ['term_months', 'category', 'type']))
                <tr>
                    <td class="info-label" style="font-size: 7.5pt; width: 35%;">{{ ucwords(str_replace('_', ' ', $key)) }}</td>
                    <td class="info-value" style="width: 65%;">
                        @if(is_array($val))
                            {{ implode(', ', $val) }}
                        @else
                            {{ $val }}
                        @endif
                    </td>
                </tr>
            @endif
        @endforeach
    </table>
    @endif

    <!-- Section 4: Sample Terms & Conditions -->
    <div class="section-title">Loan Agreement Terms &amp; Conditions</div>
    <div class="terms-box">
        <div class="terms-header">Promissory Note &amp; Binding Legal Clauses</div>
        <ol class="terms-ol">
            <li class="terms-li">
                <span class="terms-bold">1. Repayment and Interest Obligations:</span> The Borrower agrees to pay M Lhuillier Sako Cooperative (the "Cooperative") the Principal Amount along with any applicable service fees and cooperative interest, computed and split equally across the specified amortization period of <span class="terms-bold">{{ $application->form_data['term_months'] ?? 'N/A' }} months</span>.
            </li>
            <li class="terms-li">
                <span class="terms-bold">2. Payroll Deduction Authorization:</span> By signing this agreement, the Borrower grants irrevocable authority to their Employer's payroll department to apply direct payroll deductions from their monthly/semi-monthly salary, bonuses, and allowances to satisfy the amortization schedules of this loan until completely liquidated.
            </li>
            <li class="terms-li">
                <span class="terms-bold">3. Accelerating Clause on Separation:</span> In the event of default on payments, or separation/termination of employment from the company for any reason, the entire outstanding principal balance of this loan shall become immediately due, payable, and demandable. The Cooperative is explicitly authorized to offset this balance against any share capital, savings account pools, separation benefits, or back-wages.
            </li>
            <li class="terms-li">
                <span class="terms-bold">4. Solidary Co-Maker Liability:</span> Any registered co-makers associated with this application bind themselves jointly and severally (solidarily liable) with the primary Borrower. In the event of borrower default, co-makers authorize payroll deductions on their respective accounts to cover outstanding sums.
            </li>
            <li class="terms-li">
                <span class="terms-bold">5. General Provisions &amp; Disputes:</span> This agreement is legally binding and governed by the Cooperative Code of the Philippines. Any litigation arising from default shall be filed exclusively in local courts of Cebu City.
            </li>
        </ol>
    </div>

    <!-- Section 5: Workflow Audit Trail Log -->
    @if($application->approvals->isNotEmpty())
    <div class="section-title">Official Workflow Review &amp; Audit Trail</div>
    <table class="audit-table">
        <thead>
            <tr>
                <th style="width: 25%;">Review Stage</th>
                <th style="width: 25%;">Authorized Signatory</th>
                <th style="width: 15%;">Decision</th>
                <th style="width: 20%;">Date Signed</th>
                <th style="width: 15%;">Remarks</th>
            </tr>
        </thead>
        <tbody>
            @foreach($application->approvals as $approval)
            <tr>
                <td style="font-weight: bold; color: #0f172a;">{{ ucwords(str_replace('_', ' ', $approval->stage_role_slug)) }}</td>
                <td>{{ $approval->actor->name }}</td>
                <td>
                    <span class="badge {{ $approval->decision === 'approved' ? 'badge-success' : 'badge-danger' }}">
                        {{ ucwords($approval->decision) }}
                    </span>
                </td>
                <td>{{ $approval->created_at->format('M d, Y h:i A') }}</td>
                <td style="font-style: italic; color: #475569;">{{ $approval->remarks ?: 'No written remarks.' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <!-- Section 6: Official Execution & Signature Blocks -->
    <table class="signoff-table">
        <tr>
            <!-- Left Signature box (Borrower) -->
            <td class="signoff-box">
                @if($application->borrower->signature && file_exists(storage_path('app/public/' . $application->borrower->signature)))
                    <div style="text-align: center; margin-bottom: -15px;">
                        <img src="{{ storage_path('app/public/' . $application->borrower->signature) }}" alt="Borrower Signature" style="max-height: 50px; max-width: 150px; object-fit: contain;">
                    </div>
                @endif
                <div class="signoff-line" style="margin-top: {{ $application->borrower->signature && file_exists(storage_path('app/public/' . $application->borrower->signature)) ? '5px' : '35px' }};"></div>
                <div class="signoff-caption"><span style="font-weight: bold; color: #0f172a;">{{ $application->borrower->name }}</span><br>Primary Borrower Signature</div>
            </td>
            
            <!-- Spacer -->
            <td class="signoff-spacer"></td>
            
            <!-- Right Signature box (Cooperative Representative / Admin) -->
            <td class="signoff-box">
                @if(auth()->user()->signature && file_exists(storage_path('app/public/' . auth()->user()->signature)))
                    <div style="text-align: center; margin-bottom: -15px;">
                        <img src="{{ storage_path('app/public/' . auth()->user()->signature) }}" alt="Officer Signature" style="max-height: 50px; max-width: 150px; object-fit: contain;">
                    </div>
                @endif
                <div class="signoff-line" style="margin-top: {{ auth()->user()->signature && file_exists(storage_path('app/public/' . auth()->user()->signature)) ? '5px' : '35px' }};"></div>
                <div class="signoff-caption"><span style="font-weight: bold; color: #0f172a;">{{ auth()->user()->name }}</span><br>Cooperative Auditor / Releasing Officer</div>
            </td>
        </tr>
    </table>

    <!-- Section 7: Co-Maker Endorsements & Signatures (if applicable) -->
    @php
        if (!isset($comakers)) {
            $comakerIds = $application->form_data['comakers'] ?? [];
            $comakers = \App\Models\User::whereIn('id', $comakerIds)->get();
        }
    @endphp

    @if($comakers->isNotEmpty())
    <div class="section-title" style="margin-top: 25px;">Co-Maker Solidary Endorsements &amp; Signatures</div>
    <table class="signoff-table" style="margin-top: 15px;">
        <tr>
            @foreach($comakers as $index => $comaker)
                @if($index > 0 && $index % 2 === 0)
                    </tr><tr>
                @endif
                <td class="signoff-box" style="width: 48%; vertical-align: bottom;">
                    @if($comaker->signature && file_exists(storage_path('app/public/' . $comaker->signature)))
                        <div style="text-align: center; margin-bottom: -15px;">
                            <img src="{{ storage_path('app/public/' . $comaker->signature) }}" alt="Co-Maker Signature" style="max-height: 50px; max-width: 150px; object-fit: contain;">
                        </div>
                    @endif
                    <div class="signoff-line" style="margin-top: {{ $comaker->signature && file_exists(storage_path('app/public/' . $comaker->signature)) ? '5px' : '35px' }};"></div>
                    <div class="signoff-caption">
                        <span style="font-weight: bold; color: #0f172a;">{{ $comaker->name }}</span><br>
                        Solidary Co-Maker (ID: {{ $comaker->company_id ?: 'N/A' }})
                    </div>
                </td>
                @if($index % 2 === 0 && !$loop->last)
                    <td class="signoff-spacer" style="width: 4%;"></td>
                @endif
            @endforeach
            
            @if($comakers->count() % 2 !== 0)
                <td class="signoff-spacer" style="width: 4%;"></td>
                <td class="signoff-box" style="width: 48%;"></td>
            @endif
        </tr>
    </table>
    @endif

    <!-- Professional Footer -->
    <div class="footer">
        ML Lhuillier Sako Cooperative • Confidential Financial Loan Contract • Page 1 of 1
    </div>

</body>
</html>
