@extends('layouts.user')

@section('title', 'My Withdrawals - ML Sako')

@section('navbar_title', 'My Withdrawals')
@section('navbar_subtitle', 'Initiate savings payout requests or track your pending withdrawal disbursements.')

@push('styles')
<style>
    .btn-preset-amount, .cancel-btn {
        cursor: pointer !important;
    }
</style>
@endpush

@section('content')
<div class="space-y-5 sm:space-y-6 animate-fade-in">

    <!-- Top Action Toolbar -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-3 border-b border-slate-200/60 dark:border-slate-800/60">
        <div class="flex flex-wrap items-center gap-2">
            @php
                $pendingWithdrawalsCount = $withdrawals->where('status', 'pending')->count();
            @endphp
            @if($pendingWithdrawalsCount > 0)
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-400 text-xs font-semibold border border-amber-200/60 dark:border-amber-800/40">
                    <span class="w-1.5 h-1.5 bg-amber-500 rounded-full animate-pulse"></span>
                    {{ $pendingWithdrawalsCount }} Pending Payout{{ $pendingWithdrawalsCount > 1 ? 's' : '' }}
                </span>
            @endif
            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 text-xs font-semibold border border-emerald-200/60 dark:border-emerald-800/40 font-mono">
                <i class="fa-solid fa-wallet text-[11px]"></i>
                Withdrawable: ₱{{ number_format($withdrawableAmount, 2) }}
            </span>
            <span class="text-xs text-slate-500 dark:text-slate-400 font-medium bg-slate-100 dark:bg-slate-800/80 px-2.5 py-1 rounded-lg border border-slate-200/80 dark:border-slate-700">
                ID: <strong class="text-slate-700 dark:text-slate-200">{{ Auth::user()->company_id ?: 'N/A' }}</strong>
            </span>
        </div>

        <div class="flex items-center gap-2">
            <span class="text-xs text-slate-500 dark:text-slate-400 font-medium">
                Total Requests: <strong class="text-slate-800 dark:text-slate-200 font-mono">{{ $withdrawals->count() }}</strong>
            </span>
        </div>
    </div>

    <!-- Mobile Segmented View Switcher (Visible on <lg screens) -->
    <div class="block lg:hidden">
        <div class="flex items-center bg-slate-100 dark:bg-slate-800/80 p-1 rounded-xl border border-slate-200/70 dark:border-slate-700/70">
            <button type="button" id="mobile-tab-form" class="flex-1 py-2 rounded-lg text-xs font-bold transition-all text-emerald-700 dark:text-emerald-400 bg-white dark:bg-slate-700 shadow-xs flex items-center justify-center gap-1.5 cursor-pointer">
                <i class="fa-solid fa-paper-plane text-[10px]"></i>
                <span>File Payout</span>
            </button>
            <button type="button" id="mobile-tab-history" class="flex-1 py-2 rounded-lg text-xs font-semibold transition-all text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200 flex items-center justify-center gap-1.5 cursor-pointer">
                <i class="fa-solid fa-clock-rotate-left text-[10px]"></i>
                <span>History Log</span>
                <span class="px-1.5 py-0.2 rounded-full text-[9px] font-mono font-bold bg-slate-200 dark:bg-slate-600 text-slate-700 dark:text-slate-200">
                    {{ $withdrawals->count() }}
                </span>
            </button>
        </div>
    </div>

    <!-- Main Dual-Pane Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-5">
        
        <!-- Left Pane: File Payout Station -->
        <div id="pane-withdrawal-form" class="lg:col-span-1 bg-white dark:bg-slate-800 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 shadow-xs p-5 sm:p-6 space-y-4 h-fit">
            <div class="border-b border-slate-100 dark:border-slate-700/60 pb-3">
                <h3 class="text-sm sm:text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <i class="fa-solid fa-arrow-up-from-bracket text-emerald-600 dark:text-emerald-400 text-sm"></i>
                    File Payout Request
                </h3>
                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">Submit a secure cash-out from your liquid savings pool.</p>
            </div>

            <form id="withdrawal-request-form" action="{{ route('member.withdrawals.store') }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="pin" id="withdrawal-pin-input">

                <!-- Amount Field with Quick Presets -->
                <div class="space-y-1.5">
                    <div class="flex items-center justify-between text-xs">
                        <label class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider block">Amount to Withdraw</label>
                        <span class="text-[10px] text-emerald-700 dark:text-emerald-400 font-mono font-bold">Max: ₱{{ number_format($withdrawableAmount, 2) }}</span>
                    </div>

                    <div class="relative">
                        <span class="absolute left-3.5 top-2.5 text-xs font-extrabold text-slate-400">₱</span>
                        <input type="number" id="withdrawal-amount-input" name="amount" min="100" max="{{ $withdrawableAmount }}" value="500" required class="w-full pl-7 pr-3.5 py-2 text-xs font-mono font-bold rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500/20 transition-all">
                    </div>

                    <!-- Quick Preset Amount Chips -->
                    <div class="flex flex-wrap items-center gap-1.5 pt-1">
                        <button type="button" class="btn-preset-amount px-2.5 py-1 rounded-lg text-[10px] font-bold font-mono bg-slate-100 hover:bg-emerald-50 hover:text-emerald-700 dark:bg-slate-700/60 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 border border-slate-200/80 dark:border-slate-600 transition-colors cursor-pointer" data-amount="1000">+₱1,000</button>
                        <button type="button" class="btn-preset-amount px-2.5 py-1 rounded-lg text-[10px] font-bold font-mono bg-slate-100 hover:bg-emerald-50 hover:text-emerald-700 dark:bg-slate-700/60 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 border border-slate-200/80 dark:border-slate-600 transition-colors cursor-pointer" data-amount="5000">+₱5,000</button>
                        <button type="button" class="btn-preset-amount px-2.5 py-1 rounded-lg text-[10px] font-bold font-mono bg-slate-100 hover:bg-emerald-50 hover:text-emerald-700 dark:bg-slate-700/60 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 border border-slate-200/80 dark:border-slate-600 transition-colors cursor-pointer" data-amount="10000">+₱10,000</button>
                        <button type="button" class="btn-preset-amount px-2.5 py-1 rounded-lg text-[10px] font-bold font-mono bg-emerald-50 hover:bg-emerald-100 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-400 border border-emerald-200/60 dark:border-emerald-800/40 transition-colors cursor-pointer" data-amount="{{ $withdrawableAmount }}">Max</button>
                    </div>
                </div>

                <!-- Custom Disbursement Channel Dropdown -->
                <div class="space-y-1.5 relative" id="custom-channel-wrapper">
                    <label class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider block">
                        Disbursement Channel
                    </label>

                    <!-- Hidden Input for Form Submission -->
                    <input type="hidden" name="channel" id="custom-channel-input" value="MCash" required>

                    <!-- Dropdown Trigger Button -->
                    <button type="button" id="custom-channel-trigger"
                        class="w-full px-3.5 py-2 text-xs font-semibold rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500/20 transition-all flex items-center justify-between cursor-pointer">
                        <div class="flex items-center gap-2">
                            <i id="custom-channel-icon" class="fa-solid fa-mobile-screen text-blue-500 text-xs"></i>
                            <span id="custom-channel-label" class="font-bold text-slate-900 dark:text-slate-100">MCash</span>
                        </div>
                        <i id="custom-channel-chevron" class="fa-solid fa-chevron-down text-[10px] text-slate-400 transition-transform duration-200"></i>
                    </button>

                    <!-- Dropdown Options Panel -->
                    <div id="custom-channel-dropdown"
                        class="hidden absolute left-0 right-0 z-30 mt-1 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl shadow-xl overflow-hidden py-1 space-y-0.5">
                        
                        <!-- Option 1: MCash -->
                        <div class="channel-option-item px-3.5 py-2.5 hover:bg-emerald-50 dark:hover:bg-slate-700/60 cursor-pointer transition-colors flex items-center justify-between group"
                            data-value="MCash" data-icon="fa-solid fa-mobile-screen text-blue-500">
                            <div class="flex items-center gap-2.5">
                                <div class="w-7 h-7 rounded-lg bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400 flex items-center justify-center text-xs flex-shrink-0">
                                    <i class="fa-solid fa-mobile-screen"></i>
                                </div>
                                <div>
                                    <span class="font-bold text-xs text-slate-900 dark:text-white block leading-tight">MCash</span>
                                    <span class="text-[10px] text-slate-400 block leading-tight">Instant direct transfer to your MCash mobile wallet</span>
                                </div>
                            </div>
                            <i class="channel-check-icon fa-solid fa-check text-emerald-600 dark:text-emerald-400 text-xs"></i>
                        </div>

                        <!-- Option 2: Payment Solution -->
                        <div class="channel-option-item px-3.5 py-2.5 hover:bg-emerald-50 dark:hover:bg-slate-700/60 cursor-pointer transition-colors flex items-center justify-between group"
                            data-value="Payment Solution" data-icon="fa-solid fa-money-check-dollar text-emerald-600 dark:text-emerald-400">
                            <div class="flex items-center gap-2.5">
                                <div class="w-7 h-7 rounded-lg bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-xs flex-shrink-0">
                                    <i class="fa-solid fa-money-check-dollar"></i>
                                </div>
                                <div>
                                    <span class="font-bold text-xs text-slate-900 dark:text-white block leading-tight">Payment Solution</span>
                                    <span class="text-[10px] text-slate-400 block leading-tight">Over-the-counter settlement via partner financial network</span>
                                </div>
                            </div>
                            <i class="channel-check-icon fa-solid fa-check text-emerald-600 dark:text-emerald-400 text-xs hidden"></i>
                        </div>

                    </div>
                </div>

                <!-- Conditional Field: MCash Account Number -->
                <div id="mcash-account-container" class="space-y-1.5 transition-all">
                    <label class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider block">
                        MCash Account Number: <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-2 text-xs text-slate-400">
                            <i class="fa-solid fa-hashtag text-[10px]"></i>
                        </span>
                        <input type="text" id="mcash-account-input" name="mcash_account" required
                            placeholder="e.g. 09171234567"
                            maxlength="25"
                            class="w-full pl-8 pr-3.5 py-2 text-xs font-mono font-bold rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500/20 transition-all placeholder-slate-400">
                    </div>
                    <p class="text-[10px] text-slate-400 leading-tight">
                        Disbursement will be transferred directly to this mobile wallet account number.
                    </p>
                </div>

                <!-- Reason for Payout -->
                <div class="space-y-1.5">
                    <label class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider block">
                        Reason for Payout <span class="text-rose-500">*</span>
                    </label>
                    <textarea name="reason" rows="2" placeholder="State purpose (e.g. Tuition, medical, family emergency)" required class="w-full px-3.5 py-2 text-xs font-medium rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500/20 placeholder-slate-400 dark:placeholder-slate-500 transition-all resize-none"></textarea>
                </div>

                <!-- Maintaining Reserve Notice -->
                <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-900/50 border border-slate-200/60 dark:border-slate-700/60 flex items-start gap-2.5 text-xs text-slate-600 dark:text-slate-300">
                    <i class="fa-solid fa-shield-halved text-emerald-600 dark:text-emerald-400 text-xs flex-shrink-0 mt-0.5"></i>
                    <div class="text-[11px] leading-relaxed">
                        <strong class="font-bold text-slate-800 dark:text-slate-200">Maintaining Reserve:</strong> A minimum of <strong>₱500.00</strong> remains in your savings account to preserve active membership.
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" id="btn-submit-withdrawal" class="w-full inline-flex items-center justify-center gap-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs py-2.5 px-4 rounded-xl transition-all shadow-xs shadow-emerald-600/10 cursor-pointer">
                    <i class="fa-solid fa-paper-plane text-xs"></i>
                    <span>Submit Request</span>
                </button>
            </form>
        </div>

        <!-- Right Pane: Withdrawal History Log -->
        <div id="pane-withdrawal-history" class="hidden lg:block lg:col-span-2 bg-white dark:bg-slate-800 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 shadow-xs p-5 sm:p-6 space-y-4 flex flex-col">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 pb-1 border-b border-slate-100 dark:border-slate-700/60">
                <div>
                    <h3 class="text-sm sm:text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <i class="fa-solid fa-receipt text-slate-400 dark:text-slate-500 text-sm"></i>
                        Withdrawal History Log
                    </h3>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400">Complete record of your savings payout requests and live settlement status.</p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 text-[10px] font-bold">
                        {{ $withdrawals->count() }} Records
                    </span>
                </div>
            </div>

            <!-- Desktop Table (Visible on md+ screens) -->
            <div class="hidden md:block overflow-x-auto flex-grow">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="text-slate-500 dark:text-slate-400 bg-slate-50/80 dark:bg-slate-900/40 border-b border-slate-200/70 dark:border-slate-700 uppercase tracking-wider text-[10px] font-bold">
                            <th class="py-2.5 px-3.5 rounded-l-lg">Reference No.</th>
                            <th class="py-2.5 px-3.5">Date Filed</th>
                            <th class="py-2.5 px-3.5">Disbursement Channel</th>
                            <th class="py-2.5 px-3.5">Amount Requested</th>
                            <th class="py-2.5 px-3.5 text-right rounded-r-lg">Status &amp; Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700/60 text-slate-700 dark:text-slate-300 font-medium">
                        @forelse($withdrawals as $w)
                            <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-900/30 transition-colors">
                                <!-- Reference No -->
                                <td class="py-3 px-3.5 font-mono font-bold text-slate-900 dark:text-white text-xs">
                                    #WD-{{ str_pad($w->id, 5, '0', STR_PAD_LEFT) }}
                                </td>

                                <!-- Date Filed -->
                                <td class="py-3 px-3.5 text-slate-500 dark:text-slate-400 text-xs">
                                    {{ $w->created_at->format('M d, Y h:i A') }}
                                </td>

                                <!-- Disbursement Channel -->
                                <td class="py-3 px-3.5">
                                    <div class="flex items-center gap-1.5">
                                        @if(str_contains(strtolower($w->channel), 'mcash'))
                                            <i class="fa-solid fa-mobile-screen text-blue-500 text-xs"></i>
                                        @else
                                            <i class="fa-solid fa-building-columns text-emerald-600 dark:text-emerald-400 text-xs"></i>
                                        @endif
                                        <span class="text-xs font-semibold text-slate-800 dark:text-slate-200">{{ $w->channel }}</span>
                                    </div>
                                    @if($w->reason)
                                        <span class="text-[10px] text-slate-400 dark:text-slate-500 italic block truncate max-w-[200px] mt-0.5" title="{{ $w->reason }}">
                                            "{{ $w->reason }}"
                                        </span>
                                    @endif
                                </td>

                                <!-- Amount Requested -->
                                <td class="py-3 px-3.5 font-mono font-extrabold text-slate-900 dark:text-white text-xs">
                                    ₱{{ number_format($w->amount, 2) }}
                                </td>

                                <!-- Status & Action -->
                                <td class="py-3 px-3.5 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        @if($w->status === 'pending')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-400 border border-amber-200/60 dark:border-amber-800/40 animate-pulse">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                                Pending
                                            </span>
                                            <form action="{{ route('member.withdrawals.cancel', $w->id) }}" method="POST" class="inline cancel-withdrawal-form m-0">
                                                @csrf
                                                <button type="submit" class="cancel-btn px-2 py-0.5 rounded-md text-[10px] font-bold text-rose-600 hover:text-rose-700 hover:bg-rose-50 dark:hover:bg-rose-950/30 transition-colors cursor-pointer">
                                                    Cancel
                                                </button>
                                            </form>
                                        @elseif($w->status === 'processing')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-sky-50 dark:bg-sky-950/40 text-sky-700 dark:text-sky-400 border border-sky-200/60 dark:border-sky-800/40">
                                                <span class="w-1.5 h-1.5 rounded-full bg-sky-500 animate-pulse"></span>
                                                Processing
                                            </span>
                                        @elseif($w->status === 'released')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 border border-emerald-200/60 dark:border-emerald-800/40">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                Released
                                            </span>
                                        @elseif($w->status === 'cancelled')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-slate-100 dark:bg-slate-700 text-slate-500 dark:text-slate-400 border border-slate-200 dark:border-slate-600">
                                                Cancelled
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-400 border border-rose-200/60 dark:border-rose-800/40">
                                                {{ strtoupper($w->status) }}
                                            </span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-10 text-center text-slate-400 dark:text-slate-500 font-medium italic text-xs">
                                    <div class="w-10 h-10 rounded-xl bg-slate-50 dark:bg-slate-900/60 border border-slate-200/60 dark:border-slate-700/60 flex items-center justify-center mx-auto text-slate-400 mb-2">
                                        <i class="fa-solid fa-receipt text-sm"></i>
                                    </div>
                                    No past or pending withdrawal requests recorded.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile Transaction Cards (Visible on <md screens) -->
            <div class="block md:hidden space-y-3">
                @forelse($withdrawals as $w)
                    <div class="bg-slate-50/50 dark:bg-slate-900/40 border border-slate-200/70 dark:border-slate-700/70 p-4 rounded-xl space-y-3 transition-colors">
                        
                        <!-- Reference & Status Header -->
                        <div class="flex items-start justify-between gap-2.5">
                            <div>
                                <span class="font-mono font-bold text-slate-900 dark:text-white text-xs block">
                                    #WD-{{ str_pad($w->id, 5, '0', STR_PAD_LEFT) }}
                                </span>
                                <span class="text-[10px] text-slate-400 dark:text-slate-500 block mt-0.5">
                                    {{ $w->created_at->format('M d, Y h:i A') }}
                                </span>
                            </div>

                            <div class="flex items-center gap-1.5">
                                @if($w->status === 'pending')
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[9px] font-bold bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-400 border border-amber-200/60 dark:border-amber-800/40 animate-pulse">
                                        Pending
                                    </span>
                                    <form action="{{ route('member.withdrawals.cancel', $w->id) }}" method="POST" class="inline cancel-withdrawal-form m-0">
                                        @csrf
                                        <button type="submit" class="cancel-btn text-[10px] font-bold text-rose-600 hover:text-rose-700 transition-colors cursor-pointer">
                                            Cancel
                                        </button>
                                    </form>
                                @elseif($w->status === 'processing')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-bold bg-sky-50 dark:bg-sky-950/40 text-sky-700 dark:text-sky-400 border border-sky-200/60 dark:border-sky-800/40">
                                        Processing
                                    </span>
                                @elseif($w->status === 'released')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-bold bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 border border-emerald-200/60 dark:border-emerald-800/40">
                                        Released
                                    </span>
                                @elseif($w->status === 'cancelled')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-semibold bg-slate-100 dark:bg-slate-700 text-slate-500 dark:text-slate-400">
                                        Cancelled
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-bold bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-400 border border-rose-200/60 dark:border-rose-800/40">
                                        {{ strtoupper($w->status) }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Amount & Channel Inset Box -->
                        <div class="grid grid-cols-2 gap-2 p-2.5 rounded-xl bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800">
                            <div>
                                <span class="text-[9px] font-semibold text-slate-400 uppercase tracking-wider block">Requested</span>
                                <span class="text-sm font-extrabold text-slate-900 dark:text-white font-mono mt-0.5 block">₱{{ number_format($w->amount, 2) }}</span>
                            </div>
                            <div class="border-l border-slate-150 dark:border-slate-800 pl-3">
                                <span class="text-[9px] font-semibold text-slate-400 uppercase tracking-wider block">Channel</span>
                                <span class="text-xs font-semibold text-slate-700 dark:text-slate-300 truncate mt-0.5 block">{{ $w->channel }}</span>
                            </div>
                        </div>

                        <!-- Purpose Note -->
                        @if($w->reason)
                            <div class="pt-1 text-[11px] text-slate-500 dark:text-slate-400 italic">
                                "{{ $w->reason }}"
                            </div>
                        @endif

                    </div>
                @empty
                    <div class="py-8 text-center text-slate-400 dark:text-slate-500 font-medium italic text-xs">
                        No past or pending withdrawal requests recorded.
                    </div>
                @endforelse
            </div>
        </div>

    </div>

</div>
@endsection

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        
        // --- Mobile Segmented Tab Switcher ---
        const tabForm = document.getElementById("mobile-tab-form");
        const tabHistory = document.getElementById("mobile-tab-history");
        const paneForm = document.getElementById("pane-withdrawal-form");
        const paneHistory = document.getElementById("pane-withdrawal-history");

        const activeMobileClass = "flex-1 py-2 rounded-lg text-xs font-bold transition-all text-emerald-700 dark:text-emerald-400 bg-white dark:bg-slate-700 shadow-xs flex items-center justify-center gap-1.5 cursor-pointer";
        const inactiveMobileClass = "flex-1 py-2 rounded-lg text-xs font-semibold transition-all text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200 flex items-center justify-center gap-1.5 cursor-pointer";

        if (tabForm && tabHistory && paneForm && paneHistory) {
            tabForm.addEventListener("click", function() {
                tabForm.className = activeMobileClass;
                tabHistory.className = inactiveMobileClass;
                paneForm.classList.remove("hidden");
                paneHistory.classList.add("hidden");
            });

            tabHistory.addEventListener("click", function() {
                tabHistory.className = activeMobileClass;
                tabForm.className = inactiveMobileClass;
                paneForm.classList.add("hidden");
                paneHistory.classList.remove("hidden");
            });
        }

        // --- Quick Preset Amount Chips ---
        const amountInput = document.getElementById("withdrawal-amount-input");
        document.querySelectorAll(".btn-preset-amount").forEach(btn => {
            btn.addEventListener("click", function() {
                const targetAmt = parseFloat(this.getAttribute("data-amount")) || 0;
                if (amountInput) {
                    amountInput.value = targetAmt;
                    amountInput.focus();
                }
            });
        });

        // --- Custom Disbursement Channel Dropdown ---
        const channelWrapper = document.getElementById("custom-channel-wrapper");
        const channelTrigger = document.getElementById("custom-channel-trigger");
        const channelDropdown = document.getElementById("custom-channel-dropdown");
        const channelInput = document.getElementById("custom-channel-input");
        const channelLabel = document.getElementById("custom-channel-label");
        const channelIcon = document.getElementById("custom-channel-icon");
        const channelChevron = document.getElementById("custom-channel-chevron");
        const channelOptions = document.querySelectorAll(".channel-option-item");
        const mcashContainer = document.getElementById("mcash-account-container");
        const mcashInput = document.getElementById("mcash-account-input");

        function selectChannel(value, iconClass) {
            channelInput.value = value;
            channelLabel.textContent = value;
            channelIcon.className = iconClass + " text-xs";
            channelDropdown.classList.add("hidden");
            channelChevron.classList.remove("rotate-180");

            // Update active checkmarks
            channelOptions.forEach(opt => {
                const checkIcon = opt.querySelector(".channel-check-icon");
                if (opt.getAttribute("data-value") === value) {
                    checkIcon.classList.remove("hidden");
                } else {
                    checkIcon.classList.add("hidden");
                }
            });

            // Toggle MCash field
            if (value === "MCash") {
                mcashContainer.classList.remove("hidden");
                mcashInput.required = true;
                mcashInput.focus();
            } else {
                mcashContainer.classList.add("hidden");
                mcashInput.required = false;
                mcashInput.value = "";
            }
        }

        if (channelTrigger && channelDropdown) {
            channelTrigger.addEventListener("click", function(e) {
                e.stopPropagation();
                const isHidden = channelDropdown.classList.toggle("hidden");
                if (isHidden) {
                    channelChevron.classList.remove("rotate-180");
                } else {
                    channelChevron.classList.add("rotate-180");
                }
            });

            channelOptions.forEach(opt => {
                opt.addEventListener("click", function() {
                    selectChannel(this.getAttribute("data-value"), this.getAttribute("data-icon"));
                });
            });

            document.addEventListener("click", function(e) {
                if (channelWrapper && !channelWrapper.contains(e.target)) {
                    channelDropdown.classList.add("hidden");
                    channelChevron.classList.remove("rotate-180");
                }
            });
        }

        // --- Form Submission & Security PIN Modal ---
        const form = document.getElementById("withdrawal-request-form");
        const btnSubmit = document.getElementById("btn-submit-withdrawal");

        if (form) {
            form.addEventListener("submit", function(e) {
                if (form.dataset.confirmed === "true") {
                    return;
                }
                e.preventDefault();

                const amount = parseFloat(form.amount.value) || 0;
                const reason = form.reason.value;
                const channel = channelInput ? channelInput.value : "MCash";
                const mcashAcc = mcashInput ? mcashInput.value.trim() : "";

                if (channel === "MCash" && !mcashAcc) {
                    if (window.MLSAKOAlert) {
                        MLSAKOAlert.fire({
                            icon: 'warning',
                            title: 'MCash Account Required',
                            text: 'Please enter your MCash Account Number to receive funds.',
                            confirmButtonText: 'Acknowledge'
                        });
                    } else {
                        alert('Please enter your MCash Account Number.');
                    }
                    if (mcashInput) mcashInput.focus();
                    return;
                }

                if (!reason.trim()) {
                    if (window.MLSAKOAlert) {
                        MLSAKOAlert.fire({
                            icon: 'warning',
                            title: 'Validation Failed',
                            text: 'Please provide a reason for the payout request.',
                            confirmButtonText: 'Acknowledge'
                        });
                    } else {
                        alert('Please provide a reason for the payout request.');
                    }
                    return;
                }

                const channelDisplay = channel === "MCash" ? `MCash (${mcashAcc})` : "Payment Solution";

                if (window.MLSAKOAlert) {
                    MLSAKOAlert.fire({
                        icon: 'question',
                        title: 'Confirm Withdrawal',
                        html: `
                            <div class="space-y-4 text-center">
                                <div class="space-y-1">
                                    <p class="text-xs font-semibold text-slate-600 dark:text-slate-400">
                                        Confirm payout request of <strong class="text-slate-900 dark:text-white font-mono font-bold">₱${amount.toLocaleString('en-US', {minimumFractionDigits: 2})}</strong>?
                                    </p>
                                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300">
                                        Channel: ${channelDisplay}
                                    </span>
                                </div>
                                <div class="space-y-1.5 pt-2 border-t border-slate-100 dark:border-slate-800">
                                    <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">
                                        Enter 6-Digit Security PIN
                                    </p>
                                    <div class="flex justify-center gap-1.5" id="swal-pin-inputs-container">
                                        <input type="password" maxlength="1" pattern="[0-9]" inputmode="numeric" class="swal-pin-digit-input w-9 h-11 text-center text-lg font-bold bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-lg focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500 transition-all duration-150" required>
                                        <input type="password" maxlength="1" pattern="[0-9]" inputmode="numeric" class="swal-pin-digit-input w-9 h-11 text-center text-lg font-bold bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-lg focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500 transition-all duration-150" required>
                                        <input type="password" maxlength="1" pattern="[0-9]" inputmode="numeric" class="swal-pin-digit-input w-9 h-11 text-center text-lg font-bold bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-lg focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500 transition-all duration-150" required>
                                        <input type="password" maxlength="1" pattern="[0-9]" inputmode="numeric" class="swal-pin-digit-input w-9 h-11 text-center text-lg font-bold bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-lg focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500 transition-all duration-150" required>
                                        <input type="password" maxlength="1" pattern="[0-9]" inputmode="numeric" class="swal-pin-digit-input w-9 h-11 text-center text-lg font-bold bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-lg focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500 transition-all duration-150" required>
                                        <input type="password" maxlength="1" pattern="[0-9]" inputmode="numeric" class="swal-pin-digit-input w-9 h-11 text-center text-lg font-bold bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-lg focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500 transition-all duration-150" required>
                                    </div>
                                    <input type="hidden" id="swal-hidden-pin">
                                </div>
                            </div>
                        `,
                        showCancelButton: true,
                        confirmButtonText: 'Authorize Request',
                        cancelButtonText: 'Cancel',
                        iconColor: '#10b981',
                        didOpen: () => {
                            const container = document.getElementById('swal-pin-inputs-container');
                            if (container) {
                                const inputs = container.querySelectorAll('input');
                                const hidden = document.getElementById('swal-hidden-pin');
                                
                                inputs.forEach((input, index) => {
                                    input.addEventListener('input', () => {
                                        input.value = input.value.replace(/[^0-9]/g, '');
                                        if (input.value.length === 1 && index < inputs.length - 1) {
                                            inputs[index + 1].focus();
                                        }
                                        updateVal();
                                    });

                                    input.addEventListener('keydown', (e) => {
                                        if (e.key === 'Backspace') {
                                            if (input.value.length === 0 && index > 0) {
                                                inputs[index - 1].value = '';
                                                inputs[index - 1].focus();
                                                e.preventDefault();
                                            } else {
                                                input.value = '';
                                            }
                                            updateVal();
                                        }
                                    });

                                    input.addEventListener('paste', (e) => {
                                        e.preventDefault();
                                        const pasteData = (e.clipboardData || window.clipboardData).getData('text').replace(/[^0-9]/g, '').slice(0, inputs.length);
                                        if (pasteData) {
                                            for (let i = 0; i < pasteData.length; i++) {
                                                if (inputs[i]) {
                                                    inputs[i].value = pasteData[i];
                                                }
                                            }
                                            const nextFocus = Math.min(pasteData.length, inputs.length - 1);
                                            inputs[nextFocus].focus();
                                            updateVal();
                                        }
                                    });
                                });

                                const updateVal = () => {
                                    let fullVal = '';
                                    inputs.forEach(inp => fullVal += inp.value);
                                    hidden.value = fullVal;
                                };

                                setTimeout(() => {
                                    if (inputs[0]) inputs[0].focus();
                                }, 150);
                            }
                        },
                        preConfirm: () => {
                            const pinVal = document.getElementById('swal-hidden-pin').value;
                            if (pinVal.length !== 6) {
                                Swal.showValidationMessage('Please enter your 6-digit security PIN.');
                                return false;
                            }
                            return pinVal;
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            document.getElementById('withdrawal-pin-input').value = result.value;
                            form.dataset.confirmed = "true";

                            // Activate loading state
                            if (btnSubmit) {
                                btnSubmit.disabled = true;
                                btnSubmit.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin text-xs"></i> <span>Processing...</span>';
                                btnSubmit.classList.add("opacity-80", "cursor-wait");
                            }

                            form.submit();
                        }
                    });
                } else {
                    const pinPrompt = prompt('Enter your 6-digit Security PIN to confirm:');
                    if (pinPrompt && pinPrompt.length === 6 && !isNaN(pinPrompt)) {
                        document.getElementById('withdrawal-pin-input').value = pinPrompt;
                        form.dataset.confirmed = "true";
                        if (btnSubmit) {
                            btnSubmit.disabled = true;
                            btnSubmit.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin text-xs"></i> <span>Processing...</span>';
                        }
                        form.submit();
                    }
                }
            });
        }

        // --- Cancellation Confirmation ---
        document.querySelectorAll(".cancel-withdrawal-form").forEach(cancelForm => {
            cancelForm.addEventListener("submit", function(e) {
                if (cancelForm.dataset.confirmed === "true") {
                    return;
                }
                e.preventDefault();

                if (window.MLSAKOAlert) {
                    MLSAKOAlert.fire({
                        icon: 'warning',
                        title: 'Cancel Request',
                        text: 'Are you sure you want to cancel this pending withdrawal request?',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, Cancel',
                        cancelButtonText: 'No, Keep It',
                        confirmButtonColor: '#e11d48',
                        iconColor: '#e11d48'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            cancelForm.dataset.confirmed = "true";
                            cancelForm.submit();
                        }
                    });
                } else {
                    if (confirm('Cancel this pending withdrawal request?')) {
                        cancelForm.dataset.confirmed = "true";
                        cancelForm.submit();
                    }
                }
            });
        });
    });
</script>
@endpush
