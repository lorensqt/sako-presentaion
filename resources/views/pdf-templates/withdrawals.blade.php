<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Savings Account Withdrawal Form</title>
    <style>
        @page {
            margin: 1.5cm;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 9.5pt;
            color: #1e293b;
            line-height: 1.6;
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
        .header-line {
            border: 0;
            border-top: 2px double #059669;
            margin-bottom: 22px;
        }
        .doc-title-container {
            text-align: center;
            margin-bottom: 25px;
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

        /* Form Table Layout */
        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            margin-bottom: 35px;
        }
        .details-table td {
            padding: 6px 0;
            vertical-align: top;
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 9.5pt;
            color: #1e293b;
        }
        .field-label {
            width: 170px;
            font-weight: normal;
        }
        .field-value {
            font-weight: normal;
        }
        .sub-field-label {
            width: 170px;
            padding-left: 18px !important;
            font-weight: normal;
        }
        .section-header-row td {
            padding-top: 14px;
            padding-bottom: 4px;
        }
        .section-header-title {
            font-size: 9.5pt;
            font-weight: bold;
            color: #1e293b;
        }

        /* Signature block */
        .signoff-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 40px;
            margin-bottom: 35px;
        }
        .signature-name {
            font-weight: bold;
            color: #0f172a;
            text-transform: uppercase;
            font-size: 10pt;
            padding-bottom: 3px;
        }
        .signature-line {
            border-top: 1px solid #0f172a;
            width: 100%;
            margin-bottom: 5px;
        }
        .signature-title {
            font-size: 9pt;
            font-weight: 600;
            color: #334155;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Legal Notice */
        .legal-notice {
            text-align: center;
            font-size: 8pt;
            color: #64748b;
            line-height: 1.5;
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #e2e8f0;
        }
    </style>
</head>
<body>

@php
    if (!function_exists('amountToWordsPesos')) {
        function amountToWordsPesos($amount) {
            $amount = round((float)$amount, 2);
            $whole = floor($amount);
            $fraction = round(($amount - $whole) * 100);

            $ones = [
                0 => '', 1 => 'One', 2 => 'Two', 3 => 'Three', 4 => 'Four',
                5 => 'Five', 6 => 'Six', 7 => 'Seven', 8 => 'Eight', 9 => 'Nine',
                10 => 'Ten', 11 => 'Eleven', 12 => 'Twelve', 13 => 'Thirteen', 14 => 'Fourteen',
                15 => 'Fifteen', 16 => 'Sixteen', 17 => 'Seventeen', 18 => 'Eighteen', 19 => 'Nineteen'
            ];
            $tens = [
                2 => 'Twenty', 3 => 'Thirty', 4 => 'Forty', 5 => 'Fifty',
                6 => 'Sixty', 7 => 'Seventy', 8 => 'Eighty', 9 => 'Ninety'
            ];

            $convertChunk = function($n) use ($ones, $tens) {
                $chunkStr = '';
                if ($n >= 100) {
                    $hundreds = floor($n / 100);
                    $chunkStr .= $ones[$hundreds] . ' Hundred ';
                    $n %= 100;
                }
                if ($n >= 20) {
                    $t = floor($n / 10);
                    $chunkStr .= $tens[$t] . ' ';
                    $n %= 10;
                }
                if ($n > 0) {
                    $chunkStr .= $ones[$n] . ' ';
                }
                return trim($chunkStr);
            };

            if ($whole == 0) {
                $words = 'Zero';
            } else {
                $words = '';
                $billions = floor($whole / 1000000000);
                $whole %= 1000000000;
                $millions = floor($whole / 1000000);
                $whole %= 1000000;
                $thousands = floor($whole / 1000);
                $whole %= 1000;

                if ($billions > 0) {
                    $words .= $convertChunk($billions) . ' Billion ';
                }
                if ($millions > 0) {
                    $words .= $convertChunk($millions) . ' Million ';
                }
                if ($thousands > 0) {
                    $words .= $convertChunk($thousands) . ' Thousand ';
                }
                if ($whole > 0) {
                    $words .= $convertChunk($whole) . ' ';
                }
                $words = trim($words);
            }

            $currency = 'Pesos';
            if ($fraction > 0) {
                return $words . ' ' . $currency . ' and ' . str_pad($fraction, 2, '0', STR_PAD_RIGHT) . '/100 Only';
            }

            return $words . ' ' . $currency . ' Only';
        }
    }

    $items = isset($withdrawals) ? $withdrawals : (isset($withdrawal) ? collect([$withdrawal]) : collect());
@endphp

@foreach($items as $w)
    <div class="page-container" @if(!$loop->last) style="page-break-after: always;" @endif>
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
                    <h1 class="coop-title">SAMAHANG KOOPERATIBA NG M.LHUILLIER</h1>
                    <h2 class="coop-subtitle">Room 201, ML Borromeo Bldg. Borromeo St. Pahina Central Cebu City, 6000</h2>
                </td>
            </tr>
        </table>

        <div class="header-line"></div>

        <!-- Document Title -->
        <div class="doc-title-container">
            <div class="doc-title">SAVINGS ACCOUNT WITHDRAWAL FORM</div>
        </div>

        <!-- Left-Aligned Withdrawal Details -->
        <table class="details-table">
            <tr>
                <td class="field-label">Date Applied :</td>
                <td class="field-value">{{ $w->created_at ? $w->created_at->format('F d, Y') : now()->format('F d, Y') }}</td>
            </tr>
            <tr>
                <td class="field-label">Member Name :</td>
                <td class="field-value">{{ $w->user->name ?? '—' }}</td>
            </tr>
            <tr>
                <td class="field-label">Chapter / Region :</td>
                <td class="field-value">{{ $w->user->chapter ?? ($w->user->region ?? 'Not Found') }}</td>
            </tr>
            <tr>
                <td class="field-label">Payment Method :</td>
                <td class="field-value">{{ $w->channel ?? 'Cash' }}</td>
            </tr>
            <tr class="section-header-row">
                <td colspan="2">
                    <span class="section-header-title">Amount to be withdrawn :</span>
                </td>
            </tr>
            <tr>
                <td class="sub-field-label">In Word :</td>
                <td class="field-value">{{ amountToWordsPesos($w->amount) }}</td>
            </tr>
            <tr>
                <td class="sub-field-label">In Figure :</td>
                <td class="field-value">PHP {{ number_format($w->amount, 2) }}</td>
            </tr>
            <tr class="section-header-row">
                <td colspan="2">
                    <span class="section-header-title">Remarks :</span>
                </td>
            </tr>
            <tr>
                <td colspan="2" style="padding-left: 18px; padding-top: 2px;">
                    {{ $w->remarks ?: ($w->reason ?: '—') }}
                </td>
            </tr>
        </table>

        <!-- Bottom Right Maker / Depositor Block -->
        <table class="signoff-table">
            <tr>
                <td style="width: 55%;"></td>
                <td style="width: 45%; text-align: center;">
                    <div class="signature-name">{{ $w->user->name ?? '—' }}</div>
                    <div class="signature-line"></div>
                    <div class="signature-title">Maker / Depositor</div>
                </td>
            </tr>
        </table>

        <!-- Centered Bottom Legal Notice -->
        <div class="legal-notice">
            "This is a computer-generated document and does not require a physical signature. Pursuant to Republic Act No. 8792 (Electronic Commerce Act of 2000) and its implementing Rules and Regulations, electronic documens are legally recognized and enforceable under Philippine law."
        </div>
    </div>
@endforeach

</body>
</html>
