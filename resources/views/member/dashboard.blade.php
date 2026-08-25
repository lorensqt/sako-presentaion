@extends('layouts.user')

@section('title', 'Member Dashboard - ML Sako')

@section('header')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight serif-font">My Dashboard</h1>
        <p class="text-xs text-slate-500 mt-1 font-semibold">Overview of your cooperative balances, active loans, and transactions.</p>
    </div>
    <div class="flex items-center gap-2.5">
        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-50 text-emerald-800 text-xs font-bold border border-emerald-100/50">
            <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse"></span>
            Account Status: Active
        </span>
        <span class="text-xs text-slate-400 font-bold bg-slate-100 px-3 py-1.5 rounded-xl border border-slate-200">
            ID: {{ Auth::user()->company_id ?: 'N/A' }}
        </span>
    </div>
</div>
@endsection

@section('content')
<div class="space-y-8">
    <!-- Success Alert -->
    @if(session('success'))
        <div class="p-4 bg-emerald-50 border-l-4 border-emerald-500 rounded-r-xl text-emerald-800 text-sm font-semibold flex items-center justify-between shadow-sm animate-fade-in">
            <span class="flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-600 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                {{ session('success') }}
            </span>
        </div>
    @endif

    <!-- Metrics Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Metric 1: Savings Balance -->
        <div class="bg-gradient-to-tr from-emerald-800 to-teal-950 text-white p-6 rounded-3xl shadow-xl relative overflow-hidden group">
            <div class="absolute -top-12 -right-12 w-32 h-32 bg-emerald-500/10 rounded-full blur-2xl"></div>
            <div class="space-y-3 relative z-10">
                <p class="text-[10px] font-bold text-emerald-300 uppercase tracking-widest">Cooperative Pool Savings</p>
                <h3 class="text-3xl font-black serif-font">₱142,490.00</h3>
                <div class="pt-2 border-t border-emerald-800/40 flex justify-between items-center text-[10px] text-emerald-200 font-semibold">
                    <span>Dividend Rate: 7.5% p.a.</span>
                    <span class="text-emerald-400">+₱2,400.00 this month</span>
                </div>
            </div>
        </div>

        <!-- Metric 2: Share Capital -->
        <div class="bg-white border border-slate-100 p-6 rounded-3xl shadow-sm relative overflow-hidden group">
            <div class="space-y-3">
                <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Contributed Share Capital</p>
                <h3 class="text-3xl font-bold text-slate-900 serif-font">₱50,000.00</h3>
                <div class="pt-2 border-t border-slate-100 flex justify-between items-center text-[10px] text-slate-500 font-semibold">
                    <span>Voting Shares: 500 Shares</span>
                    <span class="text-emerald-600">Locked Pool</span>
                </div>
            </div>
        </div>

        <!-- Metric 3: Active Loan Balance -->
        <div class="bg-white border border-slate-100 p-6 rounded-3xl shadow-sm relative overflow-hidden group">
            <div class="space-y-3">
                <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Outstanding Loans</p>
                <h3 class="text-3xl font-bold text-slate-900 serif-font">₱15,200.00</h3>
                <div class="pt-2 border-t border-slate-100 flex justify-between items-center text-[10px] text-slate-500 font-semibold">
                    <span>Active Loans: 1 (Personal)</span>
                    <span class="text-amber-600 font-bold">Due Aug 15</span>
                </div>
            </div>
        </div>

        <!-- Metric 4: Patronage Dividend Points -->
        <div class="bg-white border border-slate-100 p-6 rounded-3xl shadow-sm relative overflow-hidden group">
            <div class="space-y-3">
                <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Patronage Dividend Points</p>
                <h3 class="text-3xl font-bold text-slate-900 serif-font">1,824 Pts</h3>
                <div class="pt-2 border-t border-slate-100 flex justify-between items-center text-[10px] text-slate-500 font-semibold">
                    <span>Value: ₱1.00 per point</span>
                    <span class="text-emerald-600">Claimable Year-End</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Active Details Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Left: Recent Ledger Transactions -->
        <div class="lg:col-span-2 bg-white rounded-3xl border border-slate-100 shadow-sm p-6 sm:p-8 space-y-6">
            <div class="flex items-center justify-between border-b border-slate-50 pb-4">
                <div>
                    <h3 class="text-lg font-bold text-slate-900 serif-font">Recent Ledger Entries</h3>
                    <p class="text-[11px] text-slate-400 mt-0.5">Real-time deposit and amortization transactions.</p>
                </div>
                <button class="px-3.5 py-1.5 rounded-lg border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50 transition-all">
                    Full Ledger &rarr;
                </button>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="text-slate-400 font-bold border-b border-slate-100 uppercase tracking-wider">
                            <th class="pb-3">Reference ID</th>
                            <th class="pb-3">Type</th>
                            <th class="pb-3">Channel</th>
                            <th class="pb-3">Date</th>
                            <th class="pb-3 text-right">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50 font-medium text-slate-700">
                        <tr>
                            <td class="py-3.5 font-semibold text-slate-900">#DEP-10928</td>
                            <td class="py-3.5">
                                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-800 text-[10px] font-bold border border-emerald-100/30">
                                    Savings Pool Deposit
                                </span>
                            </td>
                            <td class="py-3.5 text-slate-500">M Lhuillier Branch</td>
                            <td class="py-3.5 text-slate-500">Jul 28, 2026</td>
                            <td class="py-3.5 text-right font-extrabold text-emerald-600">+₱2,500.00</td>
                        </tr>
                        <tr>
                            <td class="py-3.5 font-semibold text-slate-900">#LOA-88371</td>
                            <td class="py-3.5">
                                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full bg-slate-50 text-slate-800 text-[10px] font-bold border border-slate-200">
                                    Loan Amortization
                                </span>
                            </td>
                            <td class="py-3.5 text-slate-500">Payroll Deduction</td>
                            <td class="py-3.5 text-slate-500">Jul 15, 2026</td>
                            <td class="py-3.5 text-right font-extrabold text-slate-900">-₱1,200.00</td>
                        </tr>
                        <tr>
                            <td class="py-3.5 font-semibold text-slate-900">#DEP-10815</td>
                            <td class="py-3.5">
                                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-800 text-[10px] font-bold border border-emerald-100/30">
                                    Savings Pool Deposit
                                </span>
                            </td>
                            <td class="py-3.5 text-slate-500">M Lhuillier Branch</td>
                            <td class="py-3.5 text-slate-500">Jun 28, 2026</td>
                            <td class="py-3.5 text-right font-extrabold text-emerald-600">+₱2,500.00</td>
                        </tr>
                        <tr>
                            <td class="py-3.5 font-semibold text-slate-900">#PAT-20261</td>
                            <td class="py-3.5">
                                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full bg-teal-50 text-teal-800 text-[10px] font-bold border border-teal-100/30">
                                    Patronage Refund
                                </span>
                            </td>
                            <td class="py-3.5 text-slate-500">Year-End Distribution</td>
                            <td class="py-3.5 text-slate-500">Jan 02, 2026</td>
                            <td class="py-3.5 text-right font-extrabold text-emerald-600">+₱8,400.00</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Right: Active Loans Summary & Quick Actions -->
        <div class="space-y-8">
            <!-- Loan Card Summary -->
            <div class="bg-white border border-slate-100 rounded-3xl shadow-sm p-6 sm:p-8 space-y-6">
                <div>
                    <h3 class="text-lg font-bold text-slate-900 serif-font">Active Loans</h3>
                    <p class="text-[11px] text-slate-400 mt-0.5">Your currently outstanding loan amortization schedules.</p>
                </div>

                <div class="bg-slate-50 rounded-2xl p-4 border border-slate-100 space-y-4">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Salary Loan (Regular)</p>
                            <h4 class="text-base font-bold text-slate-900 mt-1">₱15,200.00 Outstanding</h4>
                        </div>
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-amber-50 text-amber-800 text-[10px] font-bold border border-amber-100">
                            12 / 24 Mon
                        </span>
                    </div>

                    <!-- Progress Bar -->
                    <div class="space-y-1.5">
                        <div class="flex justify-between text-[10px] font-semibold text-slate-400">
                            <span>Paid: ₱14,800.00</span>
                            <span>Total: ₱30,000.00</span>
                        </div>
                        <div class="w-full bg-slate-200 h-2 rounded-full overflow-hidden">
                            <div class="bg-emerald-500 h-full rounded-full" style="width: 49.33%"></div>
                        </div>
                    </div>

                    <div class="flex justify-between items-center text-[11px] pt-1">
                        <span class="text-slate-500 font-medium">Next Amortization:</span>
                        <span class="text-slate-900 font-extrabold">₱1,200.00 (Aug 15)</span>
                    </div>
                </div>

                <button class="w-full inline-flex items-center justify-center bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs py-3.5 rounded-xl transition-all duration-200 shadow-sm">
                    Apply for a New Loan
                </button>
            </div>

            <!-- Quick Assistance Contact Support Desk -->
            <div class="bg-gradient-to-tr from-slate-900 to-teal-950 text-white rounded-3xl p-6 sm:p-8 shadow-sm space-y-4 relative overflow-hidden group">
                <div class="absolute -bottom-12 -right-12 w-28 h-28 bg-emerald-500/10 rounded-full blur-xl"></div>
                <h4 class="text-base font-bold serif-font">Need Assistance?</h4>
                <p class="text-[11px] text-slate-300 leading-relaxed max-w-[200px]">Have queries about your dividend points or share capital? Reach our support officers directly.</p>
                <div class="pt-2">
                    <a href="mailto:support@mlsako.com" class="inline-flex items-center justify-center bg-emerald-500 hover:bg-emerald-600 text-white font-bold text-xs px-4 py-2.5 rounded-xl transition-all duration-200">
                        Email Support Desk
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
