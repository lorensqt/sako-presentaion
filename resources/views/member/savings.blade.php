@extends('layouts.user')

@section('title', 'My Savings - ML Sako')

@section('header')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight serif-font">My Savings</h1>
        <p class="text-sm text-slate-600 dark:text-slate-400 mt-1 font-semibold">Manage and track your cooperative capital and savings account balances.</p>
    </div>
    <div class="flex items-center gap-3">
        <!-- Balance Toggle Button -->
        <button id="toggle-balances-btn" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 dark:bg-slate-800 dark:hover:bg-slate-700 text-white text-xs font-bold shadow-sm transition-all duration-200">
            <!-- Eye Off Icon (Default: Hidden) -->
            <svg id="eye-off-icon" class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.993 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
            </svg>
            <!-- Eye Icon (Shown) -->
            <svg id="eye-icon" class="w-4 h-4 flex-shrink-0" hidden="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c3.478 0 6.561 1.767 8.543 4.542m-17.086 0A11.026 11.026 0 001.054 12c1.378 4.057 5.168 7 9.636 7 3.478 0 6.561-1.767 8.543-4.542"/>
            </svg>
            <span id="toggle-btn-text">Show Balances</span>
        </button>

        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-50 dark:bg-emerald-950/30 text-emerald-800 dark:text-emerald-400 text-xs font-bold border border-emerald-100 dark:border-emerald-800/40">
            <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse"></span>
            Savings Account: Active
        </span>
    </div>
</div>
@endsection

@section('content')
<div class="space-y-8 animate-fade-in">

    <!-- Savings Cards Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        
        <!-- Card 1: Shared Capital (Locked Equity) -->
        <div class="bg-white dark:bg-slate-800 border-2 border-slate-100 dark:border-slate-700 rounded-3xl shadow-sm p-6 sm:p-8 flex flex-col justify-between space-y-6 hover:shadow-md transition-all duration-300">
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-100 dark:bg-slate-700 text-slate-800 dark:text-slate-200 text-xs font-extrabold border border-slate-200 dark:border-slate-600">
                        <svg class="w-3.5 h-3.5 text-slate-600 dark:text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        Coop Equity Pool
                    </span>
                    <span class="text-xs text-slate-500 dark:text-slate-400 font-extrabold tracking-widest uppercase">Share Capital</span>
                </div>
                
                <div class="space-y-1">
                    <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest">Contributed Share Capital</p>
                    <h2 class="text-4xl font-black text-slate-900 dark:text-white serif-font">
                        <span class="balance-masked">₱ ••••••</span>
                        <span class="balance-unmasked hidden">₱{{ number_format($sharedCapital, 2) }}</span>
                    </h2>
                </div>

                <!-- Custom warning alert notice for non-withdrawable capital with high-contrast text and border -->
                <div class="p-4 rounded-2xl bg-amber-50 dark:bg-amber-950/20 border-l-4 border-amber-500 text-amber-950 dark:text-amber-200 text-xs font-medium flex items-start gap-3">
                    <svg class="w-5 h-5 text-amber-700 dark:text-amber-500 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <div class="space-y-1">
                        <p class="font-extrabold text-amber-950 dark:text-amber-300 uppercase tracking-wider text-[11px]">Non-Withdrawable Equity Notice</p>
                        <p class="leading-relaxed text-amber-900 dark:text-amber-400 text-xs font-semibold">This amount represents your permanent share capital (equity) in the cooperative. Under cooperative bylaws, this capital is <strong>strictly non-withdrawable</strong> during active membership. It grants you active voting rights and earns annual dividend payouts based on cooperative net surplus.</p>
                    </div>
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100 dark:border-slate-700 flex items-center justify-between text-xs text-slate-600 dark:text-slate-400 font-bold">
                <span>Earned Dividends: 7.5% p.a.</span>
                <span class="text-slate-500 dark:text-slate-500">ID: {{ Auth::user()->company_id ?: 'N/A' }}</span>
            </div>
        </div>

        <!-- Card 2: Savings Deposit (Withdrawable Account) -->
        <div class="bg-gradient-to-br from-white to-emerald-50/20 dark:from-slate-800 dark:to-emerald-950/10 border-2 border-slate-100 dark:border-slate-700 rounded-3xl shadow-sm p-6 sm:p-8 flex flex-col justify-between space-y-6 hover:shadow-md transition-all duration-300">
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-50 dark:bg-emerald-950/30 text-emerald-900 dark:text-emerald-400 text-xs font-extrabold border border-emerald-100 dark:border-emerald-800/40">
                        <svg class="w-3.5 h-3.5 text-emerald-700 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4"/></svg>
                        Liquid Savings Pool
                    </span>
                    <span class="text-xs text-emerald-800 dark:text-emerald-400 font-extrabold tracking-widest uppercase">Savings Deposit</span>
                </div>

                <div class="space-y-1">
                    <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest">Cooperative Savings Balance</p>
                    <h2 class="text-4xl font-black text-emerald-950 dark:text-emerald-400 serif-font">
                        <span class="balance-masked">₱ ••••••</span>
                        <span class="balance-unmasked hidden">₱{{ number_format($savingsDeposit, 2) }}</span>
                    </h2>
                </div>

                <!-- High legibility maintain balance & withdrawable metrics -->
                <div class="grid grid-cols-2 gap-4 p-4 rounded-2xl bg-white dark:bg-slate-900/60 border border-slate-200/60 dark:border-slate-700 shadow-xs">
                    <div class="space-y-1">
                        <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider block">Maintaining Balance</span>
                        <p class="text-base font-extrabold text-slate-800 dark:text-slate-200">₱{{ number_format($minBalance, 2) }}</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400 font-semibold leading-none">Required minimum</p>
                    </div>
                    <div class="space-y-1 border-l border-slate-200/80 dark:border-slate-700 pl-4">
                        <span class="text-xs font-extrabold text-emerald-800 dark:text-emerald-400 uppercase tracking-wider block">Withdrawable Amount</span>
                        <p class="text-xl font-black text-emerald-700 dark:text-emerald-300">
                            <span class="balance-masked">₱ ••••••</span>
                            <span class="balance-unmasked hidden">₱{{ number_format($withdrawableAmount, 2) }}</span>
                        </p>
                        <p class="text-xs text-emerald-600 dark:text-emerald-400 font-bold leading-none">Available for payout</p>
                    </div>
                </div>

                <div class="text-xs text-slate-700 dark:text-slate-300 font-semibold leading-relaxed bg-slate-100/50 dark:bg-slate-900/40 p-4 rounded-2xl border border-slate-200/40 dark:border-slate-700">
                    💡 <strong>Maintaining Rule:</strong> To keep your savings pool active, a minimum remaining balance of <strong>₱500.00</strong> must be retained. Payout requests exceeding the withdrawable limit will be queued for administrator review.
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100 dark:border-slate-700 flex gap-3.5">
                <a href="{{ route('member.withdrawals') }}" class="flex-1 inline-flex items-center justify-center bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm py-3.5 rounded-xl transition-all duration-200 shadow-md shadow-emerald-600/10">
                    Withdraw Funds &rarr;
                </a>
            </div>
        </div>

    </div>

    <!-- Savings Ledger Transactions with High-Contrast Header and Text -->
    <div class="bg-white dark:bg-slate-800 rounded-3xl border-2 border-slate-100 dark:border-slate-700 shadow-sm p-6 sm:p-8 space-y-6">
        <div>
            <h3 class="text-lg font-bold text-slate-900 dark:text-white serif-font">Savings Account Ledger</h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 font-semibold">Historical ledger of deposits, interest bonuses, and dividend postings.</p>
        </div>

        <!-- Table with improved readability -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="text-slate-700 dark:text-slate-300 bg-slate-50 dark:bg-slate-900/50 border-b border-slate-200 dark:border-slate-700 uppercase tracking-wider text-[11px] font-extrabold">
                        <th class="p-3.5 rounded-l-xl">Reference ID</th>
                        <th class="p-3.5">Type</th>
                        <th class="p-3.5">Channel</th>
                        <th class="p-3.5">Date</th>
                        <th class="p-3.5 text-right rounded-r-xl">Amount</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700 font-semibold text-slate-700 dark:text-slate-300">
                    @foreach($ledgerEntries as $entry)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-900/30 transition-colors">
                            <td class="p-3.5 font-bold text-slate-900 dark:text-white">{{ $entry['reference'] }}</td>
                            <td class="p-3.5">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-emerald-50 dark:bg-emerald-950/30 text-emerald-900 dark:text-emerald-400 text-xs font-bold border border-emerald-200/50 dark:border-emerald-800/40">
                                    {{ $entry['type'] }}
                                </span>
                            </td>
                            <td class="p-3.5 text-slate-600 dark:text-slate-400">{{ $entry['channel'] }}</td>
                            <td class="p-3.5 text-slate-600 dark:text-slate-400">{{ $entry['date'] }}</td>
                            <td class="p-3.5 text-right font-black text-emerald-700 dark:text-emerald-400 text-sm">
                                <span class="balance-masked">+₱ ••••••</span>
                                <span class="balance-unmasked hidden">+₱{{ number_format($entry['amount'], 2) }}</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Self-Contained Vanilla JS Toggle script for Privacy balance masking -->
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const toggleBtn = document.getElementById("toggle-balances-btn");
        const eyeIcon = document.getElementById("eye-icon");
        const eyeOffIcon = document.getElementById("eye-off-icon");
        const btnText = document.getElementById("toggle-btn-text");

        const maskedElements = document.querySelectorAll(".balance-masked");
        const unmaskedElements = document.querySelectorAll(".balance-unmasked");

        let balancesVisible = false; // Default: Hidden

        if (toggleBtn) {
            toggleBtn.addEventListener("click", function () {
                balancesVisible = !balancesVisible;

                if (balancesVisible) {
                    // Show actual amounts
                    maskedElements.forEach(el => el.classList.add("hidden"));
                    unmaskedElements.forEach(el => el.classList.remove("hidden"));
                    if (eyeIcon) eyeIcon.classList.remove("hidden");
                    if (eyeOffIcon) eyeOffIcon.classList.add("hidden");
                    if (btnText) btnText.textContent = "Hide Balances";
                } else {
                    // Mask/hide amounts
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
