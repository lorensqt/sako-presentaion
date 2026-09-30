@extends('layouts.user')

@section('title', 'My Savings - ML Sako')

@section('navbar_title', 'My Savings')
@section('navbar_subtitle', 'Manage and track your cooperative capital and savings account balances.')

@section('content')
<div class="space-y-5 sm:space-y-6 animate-fade-in">

    <!-- Top Action Toolbar -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-3 border-b border-slate-200/60 dark:border-slate-800/60">
        <div class="flex flex-wrap items-center gap-2">
            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 text-xs font-semibold border border-emerald-200/60 dark:border-emerald-800/40">
                <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse"></span>
                Active Member
            </span>
            <span class="text-xs text-slate-500 dark:text-slate-400 font-medium bg-slate-100 dark:bg-slate-800/80 px-2.5 py-1 rounded-lg border border-slate-200/80 dark:border-slate-700">
                ID: <strong class="text-slate-700 dark:text-slate-200">{{ Auth::user()->company_id ?: 'N/A' }}</strong>
            </span>
        </div>

        <div class="flex items-center gap-2">
            <!-- Balance Privacy Toggle Button -->
            <button id="toggle-balances-btn" type="button" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700/60 text-xs font-semibold shadow-xs transition-all duration-150 cursor-pointer">
                <!-- Eye Off Icon (Default: Balances are hidden) -->
                <i id="eye-off-icon" class="fa-solid fa-eye-slash text-slate-500 dark:text-slate-400 text-xs"></i>
                <!-- Eye Icon (When balances are visible) -->
                <i id="eye-icon" class="fa-solid fa-eye text-slate-500 dark:text-slate-400 text-xs hidden"></i>
                <span id="toggle-btn-text">Show Balances</span>
            </button>
        </div>
    </div>

    <!-- Savings & Capital Cards Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-5">
        
        <!-- Card 1: Share Capital (Locked Equity Pool) -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 p-5 sm:p-6 shadow-xs flex flex-col justify-between space-y-5 transition-all duration-200 hover:border-slate-300 dark:hover:border-slate-600">
            <div class="space-y-4">
                <!-- Header Badge & Status -->
                <div class="flex items-center justify-between gap-2">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-slate-700/70 text-slate-700 dark:text-slate-300 text-[11px] font-bold border border-slate-200 dark:border-slate-600/70">
                        <i class="fa-solid fa-vault text-slate-500 dark:text-slate-400 text-xs"></i>
                        Coop Equity Pool
                    </span>
                    <span class="text-[11px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Share Capital</span>
                </div>
                
                <!-- Balance & Label -->
                <div class="space-y-0.5">
                    <p class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Contributed Share Capital</p>
                    <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight font-mono">
                        <span class="balance-masked">₱ ••••••</span>
                        <span class="balance-unmasked hidden">₱{{ number_format($sharedCapital, 2) }}</span>
                    </h2>
                </div>

                <!-- Metrics Strip: Dividends & Shares -->
                <div class="grid grid-cols-2 gap-2.5 p-3 rounded-xl bg-slate-50 dark:bg-slate-900/50 border border-slate-200/60 dark:border-slate-700/60">
                    <div class="space-y-0.5">
                        <span class="text-[10px] font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wider block">Estimated Dividend</span>
                        <p class="text-xs sm:text-sm font-bold text-emerald-700 dark:text-emerald-400">7.50% p.a.</p>
                        <span class="text-[10px] text-slate-400 dark:text-slate-500 block">Surplus distribution</span>
                    </div>
                    <div class="space-y-0.5 border-l border-slate-200 dark:border-slate-700/80 pl-3">
                        <span class="text-[10px] font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wider block">Voting Rights</span>
                        <p class="text-xs sm:text-sm font-bold text-slate-800 dark:text-slate-200">500 Shares</p>
                        <span class="text-[10px] text-emerald-600 dark:text-emerald-400 font-medium block">Active voting status</span>
                    </div>
                </div>

                <!-- Compact Policy Notice -->
                <div class="p-3 rounded-xl bg-amber-50/70 dark:bg-amber-950/20 border border-amber-200/70 dark:border-amber-900/40 flex items-start gap-2.5 text-xs text-amber-900 dark:text-amber-200">
                    <i class="fa-solid fa-circle-info text-amber-600 dark:text-amber-400 flex-shrink-0 mt-0.5 text-xs"></i>
                    <div class="text-[11px] leading-relaxed">
                        <strong class="font-bold text-amber-950 dark:text-amber-300">Non-Withdrawable Equity:</strong> Permanent cooperative equity under bylaws. Non-withdrawable during active membership; qualifies you for voting and annual dividend yields.
                    </div>
                </div>
            </div>

            <!-- Footer Action Link -->
            <div class="pt-3 border-t border-slate-100 dark:border-slate-700/70 flex items-center justify-between text-xs">
                <span class="text-slate-400 dark:text-slate-500 text-[11px]">Updated via Monthly Payroll</span>
                <a href="{{ route('member.deductions') }}" class="inline-flex items-center gap-1.5 font-semibold text-emerald-600 dark:text-emerald-400 hover:text-emerald-700 dark:hover:text-emerald-300 transition-colors">
                    <span>Adjust Contribution</span>
                    <i class="fa-solid fa-arrow-right text-[10px]"></i>
                </a>
            </div>
        </div>

        <!-- Card 2: Savings Deposit (Liquid Account) -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 p-5 sm:p-6 shadow-xs flex flex-col justify-between space-y-5 transition-all duration-200 hover:border-slate-300 dark:hover:border-slate-600">
            <div class="space-y-4">
                <!-- Header Badge & Status -->
                <div class="flex items-center justify-between gap-2">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 text-[11px] font-bold border border-emerald-200/60 dark:border-emerald-800/40">
                        <i class="fa-solid fa-wallet text-emerald-600 dark:text-emerald-400 text-xs"></i>
                        Liquid Savings Pool
                    </span>
                    <span class="text-[11px] font-bold text-emerald-700 dark:text-emerald-400 uppercase tracking-wider">Savings Deposit</span>
                </div>

                <!-- Balance & Label -->
                <div class="space-y-0.5">
                    <p class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total Savings Balance</p>
                    <h2 class="text-2xl sm:text-3xl font-extrabold text-emerald-700 dark:text-emerald-400 tracking-tight font-mono">
                        <span class="balance-masked">₱ ••••••</span>
                        <span class="balance-unmasked hidden">₱{{ number_format($savingsDeposit, 2) }}</span>
                    </h2>
                </div>

                <!-- Split Metrics Box: Reserve vs. Available -->
                <div class="grid grid-cols-2 gap-2.5 p-3 rounded-xl bg-slate-50 dark:bg-slate-900/50 border border-slate-200/60 dark:border-slate-700/60">
                    <div class="space-y-0.5">
                        <span class="text-[10px] font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wider block">Maintaining Reserve</span>
                        <p class="text-xs sm:text-sm font-bold text-slate-700 dark:text-slate-300 font-mono">₱{{ number_format($minBalance, 2) }}</p>
                        <span class="text-[10px] text-slate-400 dark:text-slate-500 block">Required minimum</span>
                    </div>
                    <div class="space-y-0.5 border-l border-slate-200 dark:border-slate-700/80 pl-3">
                        <span class="text-[10px] font-bold text-emerald-700 dark:text-emerald-400 uppercase tracking-wider block">Withdrawable</span>
                        <p class="text-xs sm:text-sm font-extrabold text-emerald-700 dark:text-emerald-400 font-mono">
                            <span class="balance-masked">₱ ••••••</span>
                            <span class="balance-unmasked hidden">₱{{ number_format($withdrawableAmount, 2) }}</span>
                        </p>
                        <span class="text-[10px] text-emerald-600/90 dark:text-emerald-400/90 font-medium block">Ready for payout</span>
                    </div>
                </div>

                <!-- Compact Maintaining Notice -->
                <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-900/40 border border-slate-200/60 dark:border-slate-700/60 flex items-start gap-2.5 text-xs text-slate-600 dark:text-slate-300">
                    <i class="fa-solid fa-shield-halved text-emerald-600 dark:text-emerald-400 flex-shrink-0 mt-0.5 text-xs"></i>
                    <div class="text-[11px] leading-relaxed">
                        <strong class="font-bold text-slate-800 dark:text-slate-200">Maintaining Rule:</strong> A minimum reserve of <strong>₱500.00</strong> remains in your account to preserve active status. Payouts process via M Lhuillier branches or MCash.
                    </div>
                </div>
            </div>

            <!-- Footer Action Buttons -->
            <div class="pt-3 border-t border-slate-100 dark:border-slate-700/70 flex items-center gap-2">
                <a href="{{ route('member.withdrawals') }}" class="flex-1 inline-flex items-center justify-center gap-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs py-2.5 px-4 rounded-xl transition-all duration-150 shadow-xs shadow-emerald-600/10">
                    <span>Withdraw Funds</span>
                    <i class="fa-solid fa-arrow-up-from-bracket text-xs"></i>
                </a>
                <a href="{{ route('member.deductions') }}" class="inline-flex items-center justify-center bg-slate-100 hover:bg-slate-200/80 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 font-semibold text-xs py-2.5 px-3 rounded-xl transition-all duration-150" title="Adjust payroll deductions">
                    <i class="fa-solid fa-sliders text-xs"></i>
                </a>
            </div>
        </div>

    </div>

    <!-- Savings Ledger Transactions Container -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 p-5 sm:p-6 shadow-xs space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 pb-1 border-b border-slate-100 dark:border-slate-700/60">
            <div>
                <h3 class="text-sm sm:text-base font-bold text-slate-900 dark:text-white">Savings Account Ledger</h3>
                <p class="text-[11px] text-slate-500 dark:text-slate-400">Verified record of payroll deductions, interest bonuses, and pool allocations.</p>
            </div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 text-[10px] font-bold">
                    {{ count($ledgerEntries) }} Records
                </span>
            </div>
        </div>

        <!-- Desktop Table (Visible on md+ screens) -->
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="text-slate-500 dark:text-slate-400 bg-slate-50/80 dark:bg-slate-900/40 border-b border-slate-200/70 dark:border-slate-700 uppercase tracking-wider text-[10px] font-bold">
                        <th class="py-2.5 px-3 rounded-l-lg">Reference ID</th>
                        <th class="py-2.5 px-3">Transaction Type</th>
                        <th class="py-2.5 px-3">Disbursement Channel</th>
                        <th class="py-2.5 px-3">Post Date</th>
                        <th class="py-2.5 px-3 text-right rounded-r-lg">Credited Amount</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700/60 text-slate-700 dark:text-slate-300 font-medium">
                    @foreach($ledgerEntries as $entry)
                        <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-900/30 transition-colors">
                            <td class="py-3 px-3 font-mono font-bold text-slate-900 dark:text-white">{{ $entry['reference'] }}</td>
                            <td class="py-3 px-3">
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 text-[11px] font-semibold border border-emerald-200/50 dark:border-emerald-800/40">
                                    <span class="w-1 h-1 rounded-full bg-emerald-500"></span>
                                    {{ $entry['type'] }}
                                </span>
                            </td>
                            <td class="py-3 px-3 text-slate-600 dark:text-slate-400">{{ $entry['channel'] }}</td>
                            <td class="py-3 px-3 text-slate-500 dark:text-slate-400">{{ $entry['date'] }}</td>
                            <td class="py-3 px-3 text-right font-mono font-bold text-emerald-600 dark:text-emerald-400">
                                <span class="balance-masked">+₱ ••••••</span>
                                <span class="balance-unmasked hidden">+₱{{ number_format($entry['amount'], 2) }}</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Mobile Transactions List (Visible on <md screens) -->
        <div class="block md:hidden space-y-2.5">
            @foreach($ledgerEntries as $entry)
                <div class="bg-slate-50/50 dark:bg-slate-900/40 border border-slate-200/60 dark:border-slate-700/60 p-3 rounded-xl flex items-center justify-between gap-3 transition-colors hover:bg-slate-50 dark:hover:bg-slate-900/70">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <div class="w-8 h-8 rounded-lg bg-emerald-50 dark:bg-emerald-950/40 flex items-center justify-center text-emerald-600 dark:text-emerald-400 border border-emerald-200/60 dark:border-emerald-800/30 flex-shrink-0">
                            <!-- Inflow arrow icon -->
                            <i class="fa-solid fa-arrow-down text-xs"></i>
                        </div>
                        <div class="min-w-0 space-y-0.5">
                            <p class="text-xs font-semibold text-slate-900 dark:text-slate-100 truncate">{{ $entry['type'] }}</p>
                            <p class="text-[10px] text-slate-500 dark:text-slate-400 truncate">{{ $entry['date'] }} • {{ $entry['channel'] }}</p>
                        </div>
                    </div>
                    <div class="text-right space-y-0.5 flex-shrink-0">
                        <p class="text-xs font-bold text-emerald-600 dark:text-emerald-400 font-mono">
                            <span class="balance-masked">+₱ ••••••</span>
                            <span class="balance-unmasked hidden">+₱{{ number_format($entry['amount'], 2) }}</span>
                        </p>
                        <p class="text-[9px] font-mono text-slate-400 dark:text-slate-500">{{ $entry['reference'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

</div>

<!-- Privacy Balance Masking Script -->
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const toggleBtn = document.getElementById("toggle-balances-btn");
        const eyeIcon = document.getElementById("eye-icon");
        const eyeOffIcon = document.getElementById("eye-off-icon");
        const btnText = document.getElementById("toggle-btn-text");

        const maskedElements = document.querySelectorAll(".balance-masked");
        const unmaskedElements = document.querySelectorAll(".balance-unmasked");

        let balancesVisible = false; // Default: Masked/Hidden

        if (toggleBtn) {
            toggleBtn.addEventListener("click", function () {
                balancesVisible = !balancesVisible;

                if (balancesVisible) {
                    // Show actual figures
                    maskedElements.forEach(el => el.classList.add("hidden"));
                    unmaskedElements.forEach(el => el.classList.remove("hidden"));
                    if (eyeIcon) eyeIcon.classList.remove("hidden");
                    if (eyeOffIcon) eyeOffIcon.classList.add("hidden");
                    if (btnText) btnText.textContent = "Hide Balances";
                } else {
                    // Hide/mask figures
                    maskedElements.forEach(el => el.classList.remove("hidden"));
                    unmaskedElements.forEach(el => el.classList.add("hidden"));
                    if (eyeIcon) eyeIcon.classList.add("hidden");
                    if (eyeOffIcon) eyeOffIcon.classList.remove("hidden");
                    if (btnText) btnText.textContent = "Show Balances";
                }
            });
        }
    });
</script>
@endsection
