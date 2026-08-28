@inject('workflowService', 'App\Services\LoanWorkflowService')
@extends('layouts.user')

@section('title', 'My Loans - ML Sako')

@section('header')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight serif-font">My Loans</h1>
        <p class="text-sm text-slate-600 dark:text-slate-400 mt-1 font-semibold">Track your active loan applications, amortization schedules, and payment histories.</p>
    </div>
    <div class="flex items-center gap-2.5">
        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-amber-50 dark:bg-amber-950/20 text-amber-800 dark:text-amber-400 text-xs font-bold border border-amber-100 dark:border-amber-800/40">
            Active Loans: 1 Outstanding
        </span>
    </div>
</div>
@endsection

@section('content')
<div class="space-y-8 animate-fade-in">

    <!-- Flash Alert Feedbacks -->
    @if(session('success'))
        <div class="p-4 bg-emerald-50 dark:bg-emerald-950/20 border-l-4 border-emerald-500 rounded-r-xl text-emerald-800 dark:text-emerald-300 text-sm font-semibold flex items-center justify-between shadow-sm">
            <span class="flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                {{ session('success') }}
            </span>
        </div>
    @endif

    @if(session('error'))
        <div class="p-4 bg-rose-50 dark:bg-rose-950/20 border-l-4 border-rose-500 rounded-r-xl text-rose-800 dark:text-rose-300 text-sm font-semibold flex items-center justify-between shadow-sm">
            <span class="flex items-center gap-2">
                <svg class="w-5 h-5 text-rose-600 dark:text-rose-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                {{ session('error') }}
            </span>
        </div>
    @endif

    @if($errors->any())
        <div class="p-4 bg-rose-50 dark:bg-rose-950/20 border-l-4 border-rose-500 rounded-r-xl text-rose-800 dark:text-rose-300 text-xs font-semibold space-y-1 shadow-sm">
            <p class="font-bold text-sm">Please correct the following validation errors:</p>
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Active Loans Summary -->
    <div class="bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700 rounded-3xl shadow-sm p-6 sm:p-8 space-y-6">
        <div class="flex items-center justify-between border-b border-slate-50 dark:border-slate-700 pb-4">
            <div>
                <h3 class="text-lg font-bold text-slate-900 dark:text-white serif-font">Outstanding Amortization Balance</h3>
                <p class="text-xs text-slate-600 dark:text-slate-400 mt-1 font-semibold">Your currently running salary loan repayment schedule.</p>
            </div>
            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-emerald-50 dark:bg-emerald-950/30 text-emerald-800 dark:text-emerald-400 text-[10px] font-bold border border-emerald-100 dark:border-emerald-800/40">
                Current Rate: 5% p.a.
            </span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-slate-50 dark:bg-slate-900/40 p-5 rounded-2xl border border-slate-200 dark:border-slate-700 space-y-1">
                <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider block">Original Loan Amount</span>
                <p class="text-2xl font-black text-slate-800 dark:text-slate-200">₱30,000.00</p>
                <p class="text-xs text-slate-600 dark:text-slate-500 font-bold block mt-1">Approved on Mar 15, 2026</p>
            </div>
            <div class="bg-slate-50 dark:bg-slate-900/40 p-5 rounded-2xl border border-slate-200 dark:border-slate-700 space-y-1">
                <span class="text-xs font-bold text-emerald-700 dark:text-emerald-400 uppercase tracking-wider block">Total Repaid Amount</span>
                <p class="text-2xl font-black text-emerald-700 dark:text-emerald-300">₱14,800.00</p>
                <p class="text-xs text-emerald-600 dark:text-emerald-500 font-bold block mt-1">12 of 24 months paid</p>
            </div>
            <div class="bg-slate-50 dark:bg-slate-900/40 p-5 rounded-2xl border border-slate-200 dark:border-slate-700 space-y-1">
                <span class="text-xs font-bold text-amber-700 dark:text-amber-400 uppercase tracking-wider block">Remaining Balance</span>
                <p class="text-2xl font-black text-amber-700 dark:text-amber-300">₱15,200.00</p>
                <p class="text-xs text-amber-600 dark:text-amber-500 font-bold block mt-1">Next: ₱1,200.00 due Aug 15</p>
            </div>
        </div>

        <!-- Progress bar -->
        <div class="space-y-2">
            <div class="flex justify-between text-xs font-bold text-slate-500 dark:text-slate-400">
                <span>Repayment Progress (49.33%)</span>
                <span>₱14,800.00 Paid of ₱30,000.00</span>
            </div>
            <div class="w-full bg-slate-100 dark:bg-slate-900 h-3 rounded-full overflow-hidden border border-slate-200/40 dark:border-slate-700">
                <div class="bg-emerald-500 h-full rounded-full transition-all duration-500" style="width: 49.33%"></div>
            </div>
        </div>
    </div>

    <!-- My Loan Applications Queue -->
    @if($applications->isNotEmpty())
        <div class="bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700 rounded-3xl shadow-sm p-6 sm:p-8 space-y-6 animate-fade-in">
            <div class="flex items-center justify-between border-b border-slate-50 dark:border-slate-700 pb-4">
                <div>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white serif-font">My Loan Applications</h3>
                    <p class="text-xs text-slate-600 dark:text-slate-400 mt-1 font-semibold">Track the live progress of your submitted cooperative loan facilities.</p>
                </div>
            </div>
            
            <!-- Desktop Table (Visible on larger screens) -->
            <div class="hidden lg:block overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-slate-900/50 border-b border-slate-150 dark:border-slate-700 text-[11px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                            <th class="px-6 py-4.5">Loan Type</th>
                            <th class="px-6 py-4.5">Requested Amount</th>
                            <th class="px-6 py-4.5">Active Stage</th>
                            <th class="px-6 py-4.5">Status</th>
                            <th class="px-6 py-4.5">Submitted Date</th>
                            <th class="px-6 py-4.5 text-right">Details</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50 dark:divide-slate-700 font-medium text-slate-700 dark:text-slate-300">
                        @foreach($applications as $app)
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-900/30 transition-colors">
                                <td class="px-6 py-4">
                                    <span class="font-bold text-slate-900 dark:text-white block text-sm">
                                        {{ config("loans.{$app->loan_category}.{$app->loan_type}.name", ucwords(str_replace('_', ' ', $app->loan_type))) }}
                                    </span>
                                    <span class="text-[11px] text-slate-500 dark:text-slate-400 font-extrabold uppercase tracking-wider mt-0.5 block">{{ $app->loan_category }} Loan</span>
                                </td>
                                <td class="px-6 py-4 font-extrabold font-mono text-slate-900 dark:text-white">
                                    ₱{{ number_format($app->requested_amount, 2) }}
                                </td>
                                <td class="px-6 py-4 font-semibold text-slate-600 dark:text-slate-400">
                                    @if($app->status === 'approved')
                                        <span class="text-emerald-600 dark:text-emerald-400 font-extrabold flex items-center gap-1.5">
                                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                            Released / Completed
                                        </span>
                                    @elseif($app->status === 'returned')
                                        <span class="text-amber-600 dark:text-amber-400 font-extrabold flex items-center gap-1.5">
                                            <span class="w-2 h-2 rounded-full bg-amber-550 animate-pulse"></span>
                                            Returned (Correction Required)
                                        </span>
                                    @elseif($app->status === 'rejected')
                                        <span class="text-rose-600 dark:text-rose-400 font-bold">Rejected</span>
                                    @elseif($app->status === 'cancelled')
                                        <span class="text-slate-400 dark:text-slate-500 font-bold">Cancelled</span>
                                    @else
                                        @if($app->current_stage === 'sako_staff')
                                            <span class="px-2.5 py-1 rounded-lg bg-sky-50 dark:bg-sky-950/30 text-sky-700 dark:text-sky-400 border border-sky-100/50 dark:border-sky-800/40 font-extrabold uppercase text-[10px]">Sako Staff Review</span>
                                        @elseif($app->current_stage === 'hrmd_staff')
                                            <span class="px-2.5 py-1 rounded-lg bg-rose-50 dark:bg-rose-950/30 text-rose-700 dark:text-rose-400 border border-rose-100/50 dark:border-rose-800/40 font-extrabold uppercase text-[10px]">HRMD Verification</span>
                                        @elseif($app->current_stage === 'credit_committee')
                                            <span class="px-2.5 py-1 rounded-lg bg-indigo-50 dark:bg-indigo-950/30 text-indigo-700 dark:text-indigo-400 border border-indigo-100/50 dark:border-indigo-800/40 font-extrabold uppercase text-[10px]">Credit Comm Review</span>
                                        @elseif($app->current_stage === 'accounting')
                                            <span class="px-2.5 py-1 rounded-lg bg-amber-50 dark:bg-amber-950/30 text-amber-700 dark:text-amber-400 border border-amber-100/50 dark:border-amber-800/40 font-extrabold uppercase text-[10px]">Accounting Computations</span>
                                        @elseif($app->current_stage === 'releasing_officer')
                                            <span class="px-2.5 py-1 rounded-lg bg-teal-50 dark:bg-teal-950/30 text-teal-700 dark:text-teal-400 border border-teal-100/50 dark:border-teal-800/40 font-extrabold uppercase text-[10px]">Awaiting Disbursement</span>
                                        @else
                                            <span class="px-2.5 py-1 rounded-lg bg-slate-50 dark:bg-slate-700/50 text-slate-600 dark:text-slate-400 border border-slate-100/50 dark:border-slate-600/50 font-extrabold uppercase text-[10px]">{{ ucwords(str_replace('_', ' ', $app->current_stage)) }}</span>
                                        @endif
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @if($app->status === 'approved')
                                        <span class="px-2.5 py-1 rounded-full text-[9px] font-extrabold bg-emerald-50 dark:bg-emerald-950/30 text-emerald-700 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-800/40 uppercase tracking-wider">Approved</span>
                                    @elseif($app->status === 'rejected')
                                        <span class="px-2.5 py-1 rounded-full text-[9px] font-extrabold bg-rose-50 dark:bg-rose-950/30 text-rose-700 dark:text-rose-400 border border-rose-100 dark:border-rose-800/40 uppercase tracking-wider">Rejected</span>
                                    @elseif($app->status === 'cancelled')
                                        <span class="px-2.5 py-1 rounded-full text-[9px] font-extrabold bg-slate-100 dark:bg-slate-700 text-slate-500 dark:text-slate-400 border border-slate-200 dark:border-slate-600 uppercase tracking-wider">Cancelled</span>
                                    @else
                                        <span class="px-2.5 py-1 rounded-full text-[9px] font-extrabold bg-blue-50 dark:bg-blue-950/30 text-blue-700 dark:text-blue-400 border border-blue-100/60 dark:border-blue-800/40 uppercase tracking-wider animate-pulse">Pending</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-slate-600 dark:text-slate-400 font-bold">
                                    {{ $app->created_at->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex flex-col items-end gap-1.5">
                                        <button class="btn-view-ledger text-xs font-bold text-emerald-600 dark:text-emerald-400 hover:text-emerald-700 dark:hover:text-emerald-300 hover:underline cursor-pointer" 
                                            data-ledger="{{ json_encode($workflowService->getWorkflowDetails($app)) }}"
                                            data-activities="{{ json_encode($app->activities) }}">
                                            View Timeline
                                        </button>

                                        @if($app->status === 'returned')
                                            <a href="{{ route('member.forms') }}?resubmit_id={{ $app->id }}" class="mt-1 px-3 py-1.5 text-[10px] font-extrabold bg-amber-500 hover:bg-amber-600 text-white rounded-lg shadow-sm hover:shadow transition-all flex items-center gap-1 cursor-pointer">
                                                ↩️ Modify & Resubmit
                                            </a>
                                        @endif
                                        
                                        @php
                                            $activeComakers = $app->form_data['comakers'] ?? [];
                                            $rejectedComakers = $app->comakers()
                                                ->where('status', 'rejected')
                                                ->whereIn('user_id', $activeComakers)
                                                ->with('user')
                                                ->get();
                                            $hasRejectedComaker = $app->current_stage === 'comakers' && $app->status === 'pending' && $rejectedComakers->isNotEmpty();
                                        @endphp
                                        
                                        @if($hasRejectedComaker)
                                            @foreach($rejectedComakers as $rc)
                                                <button class="btn-replace-comaker mt-1 px-2.5 py-1 text-[10px] font-extrabold bg-amber-500 hover:bg-amber-600 text-white rounded-lg shadow-sm transition-all cursor-pointer" 
                                                    data-app-id="{{ $app->id }}"
                                                    data-old-id="{{ $rc->user_id }}"
                                                    data-old-name="{{ $rc->user->name }}"
                                                    data-active-comakers="{{ json_encode($activeComakers) }}">
                                                    Replace Rejected Co-maker ({{ $rc->user->name }})
                                                </button>
                                            @endforeach
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Mobile Applications Card Stack (Visible on mobile viewports) -->
            <div class="block lg:hidden space-y-4">
                @foreach($applications as $app)
                    <div class="bg-slate-50/40 dark:bg-slate-900/40 border-2 border-slate-100 dark:border-slate-700/60 p-5 rounded-2xl space-y-4 transition-all duration-200">
                        
                        <!-- Top header row: Loan Name & Status -->
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <span class="font-bold text-slate-900 dark:text-white block text-sm leading-tight">
                                    {{ config("loans.{$app->loan_category}.{$app->loan_type}.name", ucwords(str_replace('_', ' ', $app->loan_type))) }}
                                </span>
                                <span class="text-[10px] text-slate-500 dark:text-slate-400 font-extrabold uppercase tracking-wider mt-1 block">
                                    {{ $app->loan_category }} Loan
                                </span>
                            </div>
                            
                            <!-- Status Badge -->
                            <div>
                                @if($app->status === 'approved')
                                    <span class="px-2 py-0.5 rounded-full text-[9px] font-extrabold bg-emerald-50 dark:bg-emerald-950/30 text-emerald-700 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-800/40 uppercase tracking-wider">Approved</span>
                                @elseif($app->status === 'rejected')
                                    <span class="px-2 py-0.5 rounded-full text-[9px] font-extrabold bg-rose-50 dark:bg-rose-950/30 text-rose-700 dark:text-rose-400 border border-rose-100 dark:border-rose-800/40 uppercase tracking-wider">Rejected</span>
                                @elseif($app->status === 'cancelled')
                                    <span class="px-2 py-0.5 rounded-full text-[9px] font-extrabold bg-slate-100 dark:bg-slate-700 text-slate-500 dark:text-slate-400 border border-slate-200 dark:border-slate-600 uppercase tracking-wider">Cancelled</span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-[9px] font-extrabold bg-blue-50 dark:bg-blue-950/30 text-blue-700 dark:text-blue-400 border border-blue-100/60 dark:border-blue-800/40 uppercase tracking-wider animate-pulse">Pending</span>
                                @endif
                            </div>
                        </div>

                        <!-- Inner Metadata Card grid (Requested Amount & Submitted Date) -->
                        <div class="grid grid-cols-2 gap-4 p-3 bg-white dark:bg-slate-900/60 rounded-xl border border-slate-100 dark:border-slate-800/80">
                            <div>
                                <span class="text-[9px] font-extrabold text-slate-400 dark:text-slate-500 uppercase tracking-widest block leading-none">Requested Amount</span>
                                <span class="text-sm font-extrabold text-slate-900 dark:text-white font-mono mt-1 block">₱{{ number_format($app->requested_amount, 2) }}</span>
                            </div>
                            <div class="border-l border-slate-150 dark:border-slate-800 pl-4">
                                <span class="text-[9px] font-extrabold text-slate-400 dark:text-slate-500 uppercase tracking-widest block leading-none">Submitted Date</span>
                                <span class="text-xs font-semibold text-slate-700 dark:text-slate-300 mt-1 block">{{ $app->created_at->format('M d, Y') }}</span>
                            </div>
                        </div>

                        <!-- Active Stage bar -->
                        <div class="flex items-center justify-between gap-3 text-xs">
                            <span class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Active Stage:</span>
                            <div>
                                @if($app->status === 'approved')
                                    <span class="text-emerald-600 dark:text-emerald-400 font-extrabold flex items-center gap-1">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                        Released / Completed
                                    </span>
                                @elseif($app->status === 'returned')
                                    <span class="text-amber-600 dark:text-amber-400 font-extrabold flex items-center gap-1">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-550 animate-pulse"></span>
                                        Returned (Correction)
                                    </span>
                                @elseif($app->status === 'rejected')
                                    <span class="text-rose-600 dark:text-rose-400 font-bold">Rejected</span>
                                @elseif($app->status === 'cancelled')
                                    <span class="text-slate-400 dark:text-slate-500 font-bold">Cancelled</span>
                                @else
                                    @if($app->current_stage === 'sako_staff')
                                        <span class="px-2 py-0.5 rounded bg-sky-50 dark:bg-sky-950/20 text-sky-700 dark:text-sky-400 border border-sky-100/50 dark:border-sky-800/20 font-bold uppercase text-[9px]">Sako Staff Review</span>
                                    @elseif($app->current_stage === 'hrmd_staff')
                                        <span class="px-2 py-0.5 rounded bg-rose-50 dark:bg-rose-950/20 text-rose-700 dark:text-rose-400 border border-rose-100/50 dark:border-rose-800/20 font-bold uppercase text-[9px]">HRMD Verification</span>
                                    @elseif($app->current_stage === 'credit_committee')
                                        <span class="px-2 py-0.5 rounded bg-indigo-50 dark:bg-indigo-950/20 text-indigo-700 dark:text-indigo-400 border border-indigo-100/50 dark:border-indigo-800/20 font-bold uppercase text-[9px]">Credit Comm Review</span>
                                    @elseif($app->current_stage === 'accounting')
                                        <span class="px-2 py-0.5 rounded bg-amber-50 dark:bg-amber-950/20 text-amber-700 dark:text-amber-400 border border-amber-100/50 dark:border-amber-800/20 font-bold uppercase text-[9px]">Accounting Computations</span>
                                    @elseif($app->current_stage === 'releasing_officer')
                                        <span class="px-2 py-0.5 rounded bg-teal-50 dark:bg-teal-950/20 text-teal-700 dark:text-teal-400 border border-teal-100/50 dark:border-teal-800/20 font-bold uppercase text-[9px]">Awaiting Disbursement</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded bg-slate-50 dark:bg-slate-700/50 text-slate-600 dark:text-slate-400 border border-slate-100/50 dark:border-slate-600/50 font-bold uppercase text-[9px]">{{ ucwords(str_replace('_', ' ', $app->current_stage)) }}</span>
                                    @endif
                                @endif
                            </div>
                        </div>

                        <!-- Action buttons row -->
                        <div class="pt-3 border-t border-slate-100 dark:border-slate-700 flex flex-wrap gap-2 items-center justify-between">
                            <button class="btn-view-ledger text-xs font-bold text-emerald-600 dark:text-emerald-400 hover:text-emerald-700 dark:hover:text-emerald-300 hover:underline cursor-pointer" 
                                data-ledger="{{ json_encode($workflowService->getWorkflowDetails($app)) }}"
                                data-activities="{{ json_encode($app->activities) }}">
                                View Timeline ➔
                            </button>

                            @if($app->status === 'returned')
                                <a href="{{ route('member.forms') }}?resubmit_id={{ $app->id }}" class="px-3 py-1.5 text-[10px] font-extrabold bg-amber-500 hover:bg-amber-600 text-white rounded-lg shadow-sm hover:shadow transition-all flex items-center gap-1 cursor-pointer">
                                    ↩️ Modify & Resubmit
                                </a>
                            @endif
                            
                            @php
                                $activeComakers = $app->form_data['comakers'] ?? [];
                                $rejectedComakers = $app->comakers()
                                    ->where('status', 'rejected')
                                    ->whereIn('user_id', $activeComakers)
                                    ->with('user')
                                    ->get();
                                $hasRejectedComaker = $app->current_stage === 'comakers' && $app->status === 'pending' && $rejectedComakers->isNotEmpty();
                            @endphp
                            
                            @if($hasRejectedComaker)
                                @foreach($rejectedComakers as $rc)
                                    <button class="btn-replace-comaker px-2.5 py-1 text-[10px] font-extrabold bg-amber-500 hover:bg-amber-600 text-white rounded-lg shadow-sm transition-all cursor-pointer" 
                                        data-app-id="{{ $app->id }}"
                                        data-old-id="{{ $rc->user_id }}"
                                        data-old-name="{{ $rc->user->name }}"
                                        data-active-comakers="{{ json_encode($activeComakers) }}">
                                        Replace Rejected Co-maker ({{ $rc->user->name }})
                                    </button>
                                @endforeach
                            @endif
                        </div>

                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>

<!-- MODAL: VIEW APPROVAL TIMELINE -->
<div id="modal-ledger" class="fixed inset-0 z-50 hidden overflow-y-auto flex items-center justify-center p-4">
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-slate-950/40 dark:bg-slate-950/60 backdrop-blur-md opacity-0 transition-opacity duration-500 ease-[cubic-bezier(0.16,1,0.3,1)] pointer-events-none modal-overlay"></div>
    
    <!-- Modal Container -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-2xl shadow-slate-950/15 dark:shadow-slate-950/50 w-full max-w-md sm:max-w-5xl relative z-10 p-4 sm:p-6 lg:p-8 space-y-6 transform scale-95 opacity-0 transition-all duration-500 ease-[cubic-bezier(0.16,1,0.3,1)] modal-container max-h-[90vh] overflow-y-auto scrollbar-thin scrollbar-thumb-slate-200 dark:scrollbar-thumb-slate-800">
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-4">
            <div>
                <h3 class="text-lg sm:text-xl font-extrabold text-slate-900 dark:text-slate-100 tracking-tight serif-font">Loan Approval Timeline & History</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 font-medium mt-1">Live Workflow Stages and Activity Audit Log</p>
            </div>
            <button class="modal-close p-1.5 rounded-xl text-slate-400 hover:text-slate-700 dark:text-slate-500 dark:hover:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800/80 transition-all duration-200">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <div class="space-y-6">
            <!-- PART 1: Workflow Steps -->
            <div class="space-y-3">
                <h4 class="text-xs font-extrabold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Workflow Stages</h4>
                <div class="relative min-h-[140px] flex items-center justify-center bg-slate-50/40 dark:bg-slate-900/40 border border-slate-100/50 dark:border-slate-800/50 rounded-2xl p-4">
                    <!-- Background connecting line (visible on large screens) -->
                    <div class="hidden sm:block absolute top-[1rem] left-[4.5rem] right-[4.5rem] h-0.5 bg-slate-200 dark:bg-slate-800 z-0"></div>
                    
                    <!-- Vertical connecting line (visible on mobile only) -->
                    <div class="block sm:hidden absolute top-6 bottom-6 left-[2rem] w-0.5 bg-slate-200 dark:bg-slate-800 z-0"></div>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-6 gap-4 sm:gap-2 w-full relative z-10" id="ledger-timeline-container">
                        <!-- Dynamically populated via JS -->
                    </div>
                </div>
            </div>

            <!-- PART 2: Activity Log Timeline -->
            <div class="space-y-3">
                <h4 class="text-xs font-extrabold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Activity Log Timeline</h4>
                <div class="bg-slate-50/40 dark:bg-slate-900/40 border border-slate-100/50 dark:border-slate-800/50 rounded-2xl pl-6 pr-4 py-6 max-h-[300px] overflow-y-auto scrollbar-thin scrollbar-thumb-slate-200 dark:scrollbar-thumb-slate-800">
                    <div id="activity-timeline-log" class="relative pl-6 border-l-2 border-slate-200 dark:border-slate-800 space-y-6">
                        <!-- Dynamically populated via JS -->
                    </div>
                </div>
            </div>
        </div>

        <div class="pt-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-end">
            <button type="button" class="modal-close px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-xs font-bold text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-800 dark:hover:text-slate-100 transition-colors">Close</button>
        </div>
    </div>
</div>

<!-- MODAL: REPLACE REJECTED CO-MAKER -->
<div id="modal-replace-comaker" class="fixed inset-0 z-50 hidden overflow-y-auto flex items-center justify-center p-4">
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-slate-950/40 dark:bg-slate-950/60 backdrop-blur-md opacity-0 transition-opacity duration-500 ease-[cubic-bezier(0.16,1,0.3,1)] pointer-events-none modal-overlay"></div>
    
    <!-- Modal Container -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-2xl overflow-visible w-full max-w-md relative z-10 p-6 space-y-6 transform scale-95 opacity-0 transition-all duration-500 ease-[cubic-bezier(0.16,1,0.3,1)] modal-container">
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-4">
            <div>
                <h3 class="text-lg font-extrabold text-slate-900 dark:text-slate-100 tracking-tight serif-font">Replace Co-maker</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 font-medium mt-1">Designate a new co-maker for your loan application</p>
            </div>
            <button class="modal-close p-1.5 rounded-xl text-slate-400 hover:text-slate-700 dark:text-slate-500 dark:hover:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800/80 transition-all duration-200">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form id="replace-comaker-form" method="POST" action="">
            @csrf
            @method('PATCH')
            <input type="hidden" name="old_comaker_id" id="replace-old-id">
            
            <div class="space-y-4">
                <div class="bg-amber-50 dark:bg-amber-950/20 border border-amber-200/50 dark:border-amber-900/40 rounded-xl p-3 text-xs text-amber-800 dark:text-amber-400 leading-normal">
                    <span class="font-extrabold block mb-0.5">⚠️ Replaced Co-maker:</span>
                    <span id="replace-old-name" class="font-semibold"></span> has declined your request. Please select a replacement below.
                </div>

                <div class="space-y-1.5 relative" id="searchable-comaker-wrapper">
                    <label class="text-xs font-semibold text-slate-700 dark:text-slate-300">Select New Co-maker</label>
                    
                    <!-- Selected Value Trigger Button -->
                    <button type="button" id="comaker-select-trigger" class="w-full px-3.5 py-2.5 text-xs font-medium border border-slate-200 dark:border-slate-700 rounded-xl bg-white dark:bg-slate-800 outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500/20 transition-all text-slate-700 dark:text-slate-300 flex items-center justify-between">
                        <span id="comaker-select-label" class="text-slate-400 dark:text-slate-500">Select a member...</span>
                        <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    <!-- Hidden Input for Form Submission -->
                    <input type="hidden" name="new_comaker_id" id="comaker-hidden-input" required>

                    <!-- Searchable Dropdown List Panel -->
                    <div id="comaker-select-dropdown" class="hidden absolute left-0 right-0 z-20 mt-1 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl shadow-xl overflow-hidden flex flex-col max-h-60">
                        <!-- Search Box -->
                        <div class="p-2 border-b border-slate-100 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-900/50">
                            <input type="text" id="comaker-search-input" placeholder="Search by name or member ID..." class="w-full px-3 py-2 text-xs border border-slate-200 dark:border-slate-700 rounded-lg bg-white dark:bg-slate-950 outline-none focus:border-emerald-500 transition-all text-slate-900 dark:text-slate-100">
                        </div>
                        
                        <!-- Options Scroll Area -->
                        <ul id="comaker-select-options" class="flex-1 overflow-y-auto divide-y divide-slate-50 dark:divide-slate-700/50">
                            @foreach($members as $m)
                                <li data-value="{{ $m->id }}" data-search="{{ strtolower($m->name . ' ' . $m->company_id) }}" class="comaker-option-item px-4 py-2.5 text-xs text-slate-700 dark:text-slate-300 hover:bg-emerald-50 hover:text-emerald-800 dark:hover:bg-slate-700/80 dark:hover:text-white cursor-pointer transition-colors flex items-center justify-between">
                                    <span class="font-semibold">{{ $m->name }}</span>
                                    <span class="text-[10px] font-mono opacity-60">ID: {{ $m->company_id ?: 'N/A' }}</span>
                                </li>
                            @endforeach
                            <!-- No Results message -->
                            <li id="comaker-no-results" class="hidden px-4 py-3 text-xs text-center text-slate-400 italic">No matching members found</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="pt-6 border-t border-slate-100 dark:border-slate-800 flex items-center justify-end gap-3 mt-6">
                <button type="button" class="modal-close px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-xs font-bold text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-800 dark:hover:text-slate-100 transition-colors">Cancel</button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-md shadow-emerald-500/10 hover:shadow-emerald-600/20 transition-all">Submit Replacement</button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Parse the exact php loan configurations dynamically
        const loanConfig = @json($loanConfig);

        // Drawer toggles
        function openDrawer(drawerId) {
            const drawer = document.getElementById(drawerId);
            const overlay = drawer.querySelector(".modal-overlay");
            const container = drawer.querySelector(".modal-container");
            
            drawer.classList.remove("hidden");
            setTimeout(() => {
                if (overlay) {
                    overlay.classList.remove("opacity-0", "pointer-events-none");
                    overlay.classList.add("opacity-100", "pointer-events-auto");
                }
                if (container) {
                    if (drawerId === "drawer-apply") {
                        container.classList.remove("translate-x-full");
                        container.classList.add("translate-x-0");
                    } else {
                        container.classList.remove("scale-95", "opacity-0");
                        container.classList.add("scale-100", "opacity-100");
                    }
                }
            }, 50);
        }

        function closeDrawer(drawerId) {
            const drawer = document.getElementById(drawerId);
            const overlay = drawer.querySelector(".modal-overlay");
            const container = drawer.querySelector(".modal-container");
            
            if (overlay) {
                overlay.classList.add("opacity-0", "pointer-events-none");
                overlay.classList.remove("opacity-100", "pointer-events-auto");
            }
            if (container) {
                if (drawerId === "drawer-apply") {
                    container.classList.add("translate-x-full");
                    container.classList.remove("translate-x-0");
                } else {
                    container.classList.add("scale-95", "opacity-0");
                    container.classList.remove("scale-100", "opacity-100");
                }
            }
            setTimeout(() => {
                drawer.classList.add("hidden");
            }, 300);
        }

        // Close click events
        document.querySelectorAll(".modal-close, .modal-overlay").forEach(btn => {
            btn.addEventListener("click", function() {
                const drawer = this.closest('[id^="drawer-"], [id^="modal-"]');
                if (drawer) {
                    closeDrawer(drawer.id);
                }
            });
        });

        // Trigger action buttons
        const btnOpenApply = document.getElementById("btn-open-application");
        const btnOpenCalc = document.getElementById("btn-open-calculator");
        
        if (btnOpenApply) {
            btnOpenApply.addEventListener("click", () => openDrawer("drawer-apply"));
        }
        if (btnOpenCalc) {
            btnOpenCalc.addEventListener("click", () => openDrawer("drawer-apply"));
        }

        // State variables for package limit checks
        let currentMaxLimit = 0;
        let currentMaxTerm = 24;
        let requiredComakersCount = 0;

        // Step 1 Event Listeners: Dynamic drop-downs populated from config/loans.php
        const selectCategory = document.getElementById("loan-category");
        const selectType = document.getElementById("loan-type");

        if (selectCategory) {
            selectCategory.addEventListener("change", function() {
                const category = this.value;
                if (selectType) {
                    selectType.innerHTML = '<option value="" disabled selected>Select package...</option>';
                    
                    if (loanConfig[category]) {
                        selectType.removeAttribute("disabled");
                        Object.keys(loanConfig[category]).forEach(key => {
                            const option = document.createElement("option");
                            option.value = key;
                            option.textContent = loanConfig[category][key].name;
                            selectType.appendChild(option);
                        });
                    } else {
                        selectType.setAttribute("disabled", "true");
                    }
                }

                // Reset inputs
                resetInteractiveWizard();
            });
        }

        if (selectType) {
            selectType.addEventListener("change", function() {
                const category = selectCategory ? selectCategory.value : '';
                const type = this.value;
                const config = loanConfig[category] ? loanConfig[category][type] : null;

                if (config) {
                    // Parse package limits
                    currentMaxLimit = typeof config.loanable_amount === 'number' ? config.loanable_amount : 100000; // fallback for complex formulas
                    currentMaxTerm = config.max_term_months || 24;
                    
                    // Show dynamic card details
                    const nameEl = document.getElementById("info-package-name");
                    if (nameEl) nameEl.textContent = config.name;
                    const limitEl = document.getElementById("info-limit");
                    if (limitEl) limitEl.textContent = typeof config.loanable_amount === 'number' ? "₱" + currentMaxLimit.toLocaleString() : config.loanable_amount;
                    const termEl = document.getElementById("info-max-term");
                    if (termEl) termEl.textContent = currentMaxTerm + " Months";
                    const depositEl = document.getElementById("info-deposit");
                    if (depositEl) depositEl.textContent = config.fixed_deposit ? "₱" + config.fixed_deposit.toLocaleString() : "None";

                    // Handle conditional comakers count
                    requiredComakersCount = typeof config.comakers === 'number' ? config.comakers : 0;
                    const comakersEl = document.getElementById("info-comakers");
                    if (comakersEl) comakersEl.textContent = requiredComakersCount || "None";

                    // Populate and reveal dynamic form components
                    const infoCard = document.getElementById("package-info-card");
                    if (infoCard) infoCard.classList.remove("hidden");

                    // Enable parameters
                    const inputAmount = document.getElementById("loan-amount");
                    const inputTerm = document.getElementById("loan-term");
                    
                    if (inputAmount) {
                        inputAmount.removeAttribute("disabled");
                        inputAmount.value = "";
                        inputAmount.max = currentMaxLimit;
                    }
                    if (inputTerm) {
                        inputTerm.removeAttribute("disabled");
                        inputTerm.value = "";
                        inputTerm.max = currentMaxTerm;
                    }

                    // Handle Acquisition Partners (optical, jewelry, appliances)
                    const partnerSection = document.getElementById("partner-product-section");
                    const selectPartner = document.getElementById("loan-partner");
                    if (selectPartner) {
                        selectPartner.innerHTML = '<option value="" selected>Select partner...</option>';

                        if (config.partner) {
                            if (partnerSection) partnerSection.classList.remove("hidden");
                            if (Array.isArray(config.partner)) {
                                config.partner.forEach(p => {
                                    const opt = document.createElement("option");
                                    opt.value = p;
                                    opt.textContent = p;
                                    selectPartner.appendChild(opt);
                                });
                            } else {
                                const opt = document.createElement("option");
                                opt.value = config.partner;
                                opt.textContent = config.partner;
                                selectPartner.appendChild(opt);
                            }
                        } else {
                            if (partnerSection) partnerSection.classList.add("hidden");
                        }
                    }

                    // Handle dynamic products lists (ADTEL)
                    const inputProduct = document.getElementById("loan-product");
                    if (config.products && Array.isArray(config.products)) {
                        if (partnerSection) partnerSection.classList.remove("hidden");
                        const wrapper = document.getElementById("product-input-wrapper");
                        if (wrapper) {
                            wrapper.innerHTML = '<label class="text-2xs font-extrabold uppercase text-slate-500 tracking-wider">Product Option</label>' +
                                '<select name="product" id="loan-product" required class="w-full px-3 py-2.5 text-xs border border-slate-200 rounded-lg bg-white outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500/20 transition-all"></select>';
                        }
                        const productSelect = document.getElementById("loan-product");
                        if (productSelect) {
                            config.products.forEach(prod => {
                                const opt = document.createElement("option");
                                opt.value = prod;
                                opt.textContent = prod;
                                productSelect.appendChild(opt);
                            });
                        }
                    }

                    // Display comakers checklist section if required
                    const comakersSection = document.getElementById("comakers-selection-section");
                    if (requiredComakersCount > 0) {
                        if (comakersSection) comakersSection.classList.remove("hidden");
                        const reqComakersCountEl = document.getElementById("required-comaker-count");
                        if (reqComakersCountEl) reqComakersCountEl.textContent = requiredComakersCount;
                        const comakerWarn = document.getElementById("comaker-warning");
                        if (comakerWarn) comakerWarn.classList.remove("hidden");
                    } else {
                        if (comakersSection) comakersSection.classList.add("hidden");
                        const comakerWarn = document.getElementById("comaker-warning");
                        if (comakerWarn) comakerWarn.classList.add("hidden");
                    }
                }
            });
        }

        // Live calculator triggers
        const inputAmount = document.getElementById("loan-amount");
        const inputTerm = document.getElementById("loan-term");

        if (inputAmount) {
            inputAmount.addEventListener("input", performAmortizationCalculation);
        }
        if (inputTerm) {
            inputTerm.addEventListener("input", performAmortizationCalculation);
        }

        function performAmortizationCalculation() {
            if (!inputAmount || !inputTerm) return;
            const amount = parseFloat(inputAmount.value) || 0;
            const term = parseInt(inputTerm.value) || 0;
            const calcPreview = document.getElementById("calculator-preview");

            // Evaluate comaker limits dynamically for Instant and Petty Cash loans
            const category = selectCategory ? selectCategory.value : '';
            const type = selectType ? selectType.value : '';
            const config = loanConfig[category]?.[type];

            if (config && typeof config.comakers === 'object' && !Array.isArray(config.comakers)) {
                // Dynamic limits, e.g. Petty Cash limits <=10000, >10000
                requiredComakersCount = 0;
                Object.keys(config.comakers).forEach(rangeKey => {
                    if (rangeKey.startsWith("≤") || rangeKey.startsWith("<=")) {
                        const threshold = parseFloat(rangeKey.replace("≤", "").replace("<=", ""));
                        if (amount <= threshold) requiredComakersCount = config.comakers[rangeKey];
                    } else if (rangeKey.startsWith(">")) {
                        const threshold = parseFloat(rangeKey.replace(">", ""));
                        if (amount > threshold) requiredComakersCount = config.comakers[rangeKey];
                    }
                });
                
                // Refresh comakers UI warning dynamically based on request amount!
                const comakersSection = document.getElementById("comakers-selection-section");
                if (requiredComakersCount > 0) {
                    if (comakersSection) comakersSection.classList.remove("hidden");
                    const countEl = document.getElementById("required-comaker-count");
                    if (countEl) countEl.textContent = requiredComakersCount;
                    const comakerWarn = document.getElementById("comaker-warning");
                    if (comakerWarn) comakerWarn.classList.remove("hidden");
                } else {
                    if (comakersSection) comakersSection.classList.add("hidden");
                    const comakerWarn = document.getElementById("comaker-warning");
                    if (comakerWarn) comakerWarn.classList.add("hidden");
                }
            }

            if (amount > 0 && term > 0) {
                if (calcPreview) calcPreview.classList.remove("hidden");
                
                // Amortization formulas
                const principalMonthly = amount / term;
                // Interest: 5% per annum = 0.05 / 12 monthly interest factor
                const interestMonthly = (amount * 0.05) / 12;
                const totalMonthly = principalMonthly + interestMonthly;

                const princEl = document.getElementById("calc-monthly-principal");
                if (princEl) princEl.textContent = "₱" + principalMonthly.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                const intEl = document.getElementById("calc-monthly-interest");
                if (intEl) intEl.textContent = "₱" + interestMonthly.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                const totEl = document.getElementById("calc-monthly-total");
                if (totEl) totEl.textContent = "₱" + totalMonthly.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
            } else {
                if (calcPreview) calcPreview.classList.add("hidden");
            }
        }

        function resetInteractiveWizard() {
            const pkgInfoCard = document.getElementById("package-info-card");
            if (pkgInfoCard) pkgInfoCard.classList.add("hidden");
            const calcPreview = document.getElementById("calculator-preview");
            if (calcPreview) calcPreview.classList.add("hidden");
            const partnerSect = document.getElementById("partner-product-section");
            if (partnerSect) partnerSect.classList.add("hidden");
            const comakersSect = document.getElementById("comakers-selection-section");
            if (comakersSect) comakersSect.classList.add("hidden");
            if (inputAmount) inputAmount.setAttribute("disabled", "true");
            if (inputTerm) inputTerm.setAttribute("disabled", "true");
        }

        // Form submission logic validating dynamic constraints
        const loanForm = document.getElementById("loan-wizard-form");
        if (loanForm) {
            loanForm.addEventListener("submit", function(e) {
                if (!inputAmount || !inputTerm) return;
                const amount = parseFloat(inputAmount.value) || 0;
                const term = parseInt(inputTerm.value) || 0;

                // Validate Limit Max Bounds
                if (currentMaxLimit > 0 && amount > currentMaxLimit) {
                    e.preventDefault();
                    alert("The requested amount exceeds the maximum limit of ₱" + currentMaxLimit.toLocaleString() + " for this package.");
                    return;
                }

                // Validate Term Max Bounds
                if (term > currentMaxTerm) {
                    e.preventDefault();
                    alert("The selected repayment term exceeds the maximum term of " + currentMaxTerm + " months allowed.");
                    return;
                }

                // Validate Comakers selection count
                if (requiredComakersCount > 0) {
                    const checkedComakers = document.querySelectorAll(".comaker-checkbox:checked").length;
                    if (checkedComakers !== requiredComakersCount) {
                        e.preventDefault();
                        alert("This loan package requires exactly " + requiredComakersCount + " comakers. You currently have selected " + checkedComakers + ".");
                        return;
                    }
                }
            });
        }

        // Trigger tracking ledger modals
        document.querySelectorAll(".btn-view-ledger").forEach(btn => {
            btn.addEventListener("click", function() {
                const ledger = JSON.parse(this.getAttribute("data-ledger") || "[]");
                const container = document.getElementById("ledger-timeline-container");
                container.innerHTML = "";

                if (ledger.length === 0) {
                    container.innerHTML = '<div class="col-span-6 text-center py-6 text-slate-500 dark:text-slate-400 font-bold italic text-xs bg-slate-50 dark:bg-slate-950/40 border border-slate-100 dark:border-slate-800 rounded-xl">This application has no active workflow stages.</div>';
                } else {
                    ledger.forEach((log, index) => {
                        const stepCard = document.createElement("div");
                        stepCard.className = "flex flex-row sm:flex-col items-center gap-2 sm:gap-2.5 relative z-10";

                        let circleClass = '';
                        let containerClass = '';
                        let badgeClass = '';
                        let statusText = '';
                        let detailText = '';
                        let cardOpacity = 'opacity-100';

                        switch (log.status) {
                            case 'approved':
                            case 'completed':
                                circleClass = 'bg-emerald-50 dark:bg-emerald-950/30 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800/40 font-extrabold';
                                containerClass = 'bg-emerald-500/5 dark:bg-emerald-500/10 border border-emerald-100 dark:border-emerald-900/30';
                                badgeClass = 'bg-emerald-50 dark:bg-emerald-950/30 text-emerald-700 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-800/40';
                                statusText = log.status === 'completed' ? 'Released' : 'Approved';
                                detailText = log.actor ? `Approved by: ${log.actor}` : 'Stage completed';
                                break;
                            case 'rejected':
                                circleClass = 'bg-rose-50 dark:bg-rose-950/30 text-rose-600 dark:text-rose-400 border border-rose-200 dark:border-rose-800/40 font-extrabold';
                                containerClass = 'bg-rose-500/5 dark:bg-rose-500/10 border border-rose-100 dark:border-rose-900/30';
                                badgeClass = 'bg-rose-50 dark:bg-rose-950/30 text-rose-700 dark:text-rose-400 border border-rose-100 dark:border-rose-800/40';
                                statusText = 'Rejected';
                                detailText = log.actor ? `Declined by: ${log.actor}` : 'Stage declined';
                                break;
                            case 'skipped':
                                circleClass = 'bg-slate-100 dark:bg-slate-800 text-slate-400 dark:text-slate-500 border border-slate-200/60 dark:border-slate-700/60 font-semibold';
                                containerClass = 'bg-slate-50/50 dark:bg-slate-950/20 border border-slate-100 dark:border-slate-800';
                                badgeClass = 'bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 border border-slate-200 dark:border-slate-700';
                                statusText = 'Skipped';
                                detailText = 'Not required';
                                cardOpacity = 'opacity-65';
                                break;
                            case 'current':
                                circleClass = 'bg-sky-50 dark:bg-sky-950/30 text-sky-600 dark:text-sky-400 border border-sky-300 dark:border-sky-700/60 animate-pulse font-extrabold';
                                containerClass = 'bg-sky-500/5 dark:bg-sky-500/10 border border-sky-200 dark:border-sky-900/30 ring-1 ring-sky-500/10';
                                badgeClass = 'bg-sky-50 dark:bg-sky-950/30 text-sky-700 dark:text-sky-400 border border-sky-100/50 dark:border-sky-800/40 animate-pulse';
                                statusText = 'Under Review';
                                detailText = 'Awaiting decision';
                                break;
                            case 'cancelled':
                                circleClass = 'bg-slate-100 dark:bg-slate-800 text-slate-300 dark:text-slate-600 border border-slate-200/60 dark:border-slate-700/60';
                                containerClass = 'bg-slate-50/20 dark:bg-slate-950/10 border border-slate-100/50 dark:border-slate-800/50';
                                badgeClass = 'bg-slate-100 dark:bg-slate-800 text-slate-400 dark:text-slate-500 border border-slate-200 dark:border-slate-700';
                                statusText = 'Cancelled';
                                detailText = 'Workflow halted';
                                cardOpacity = 'opacity-40';
                                break;
                            case 'pending':
                            default:
                                circleClass = 'bg-slate-50 dark:bg-slate-900 text-slate-300 dark:text-slate-600 border border-slate-100 dark:border-slate-800';
                                containerClass = 'bg-slate-50/20 dark:bg-slate-950/10 border border-slate-100/50 dark:border-slate-850/50';
                                badgeClass = 'bg-slate-50 dark:bg-slate-900 text-slate-400 dark:text-slate-500 border border-slate-100 dark:border-slate-800';
                                statusText = 'Pending';
                                detailText = 'Future stage';
                                cardOpacity = 'opacity-45';
                                break;
                        }

                        // Parse short date to keep horizontal space clean
                        const dateStr = log.date ? log.date.split(' ')[0] + ' ' + log.date.split(' ')[1] : '';

                        stepCard.innerHTML = `
                            <!-- Circle Node -->
                            <div class="w-8 h-8 rounded-full ${circleClass} flex items-center justify-center flex-shrink-0 relative z-10 shadow-sm transition-all duration-300">
                                <span class="text-xs font-black">${index + 1}</span>
                            </div>
                            
                            <!-- Info Card -->
                            <div class="flex-1 sm:flex-grow-0 sm:w-full ${containerClass} p-2 rounded-xl transition-all duration-300 text-left sm:text-center flex flex-col justify-between sm:min-h-[110px] text-2xs ${cardOpacity}">
                                <div class="space-y-0.5">
                                    <h4 class="font-extrabold text-slate-900 dark:text-slate-100 text-[9px] sm:text-[10px] uppercase tracking-wider line-clamp-1 leading-tight">${log.label}</h4>
                                    <div>
                                        <span class="text-[7.5px] px-1.5 py-0.5 rounded-full ${badgeClass} font-extrabold uppercase tracking-wide inline-block leading-none">${statusText}</span>
                                    </div>
                                </div>
                                
                                <div class="mt-1.5 border-t border-slate-100 dark:border-slate-800/80 pt-1.5 space-y-0.5 flex-1 flex flex-col justify-end">
                                    <p class="text-[8.5px] text-slate-500 dark:text-slate-400 font-bold line-clamp-2 leading-tight">${detailText}</p>
                                    ${dateStr ? `<p class="text-[8px] text-slate-400 dark:text-slate-500 font-bold leading-none">${dateStr}</p>` : ''}
                                </div>
                                
                                ${log.remarks ? `
                                    <div class="mt-1.5 pt-1 border-t border-slate-150 dark:border-slate-800/80 group relative">
                                        <span class="text-[8.5px] font-extrabold text-emerald-600 dark:text-emerald-400 hover:underline cursor-pointer block sm:text-center">💬 Remarks</span>
                                        <!-- Hover Tooltip -->
                                        <div class="absolute left-0 sm:left-1/2 sm:-translate-x-1/2 bottom-full mb-2 hidden group-hover:block w-48 bg-slate-950 text-white dark:bg-slate-900 dark:border dark:border-slate-800 p-2.5 rounded-xl text-[9px] leading-relaxed shadow-xl z-50">
                                            "${log.remarks}"
                                        </div>
                                    </div>
                                ` : ''}
                            </div>
                        `;
                        container.appendChild(stepCard);
                    });
                }

                // Render Activity Log Narrative
                const activities = JSON.parse(this.getAttribute("data-activities") || "[]");
                const activityContainer = document.getElementById("activity-timeline-log");
                activityContainer.innerHTML = "";

                if (activities.length === 0) {
                    activityContainer.innerHTML = '<div class="text-slate-500 dark:text-slate-400 font-bold italic text-xs pl-2">No timeline activity logged yet for this application.</div>';
                } else {
                    activities.forEach((act) => {
                        const actEl = document.createElement("div");
                        actEl.className = "relative group";
                        
                        // Circle node on left border line
                        const nodeDot = document.createElement("span");
                        nodeDot.className = "absolute -left-[31px] top-1.5 w-4.5 h-4.5 rounded-full border-4 border-white dark:border-slate-900 bg-emerald-500 group-hover:bg-emerald-600 transition-colors shadow-sm";
                        
                        const dateText = new Date(act.created_at).toLocaleString('en-US', {
                            month: 'short', day: 'numeric', year: 'numeric', hour: '2-digit', minute: '2-digit', hour12: true
                        });

                        actEl.innerHTML = `
                            ${nodeDot.outerHTML}
                            <div class="text-xs">
                                <span class="font-extrabold text-slate-900 dark:text-slate-100 text-[13px]">${act.description}</span>
                                <div class="flex items-center gap-1.5 text-[11px] text-slate-500 dark:text-slate-400 font-semibold mt-1">
                                    <span>${dateText}</span>
                                    ${act.actor ? `<span>•</span> <span>By: ${act.actor.name}</span>` : ''}
                                </div>
                            </div>
                        `;
                        activityContainer.appendChild(actEl);
                    });
                }

                openDrawer("modal-ledger");
            });
        });

        // Trigger co-maker replacement modal
        document.querySelectorAll(".btn-replace-comaker").forEach(btn => {
            btn.addEventListener("click", function() {
                const appId = this.getAttribute("data-app-id");
                const oldId = this.getAttribute("data-old-id");
                const oldName = this.getAttribute("data-old-name");
                const activeComakers = JSON.parse(this.getAttribute("data-active-comakers") || "[]");

                const form = document.getElementById("replace-comaker-form");
                form.action = `/loans/${appId}/replace-comaker`;

                const inputOldId = document.getElementById("replace-old-id");
                inputOldId.value = oldId;

                const txtOldName = document.getElementById("replace-old-name");
                txtOldName.textContent = oldName;

                if (typeof window.resetComakerSearchableSelect === 'function') {
                    window.resetComakerSearchableSelect();
                }

                // Brand any active co-maker so they can't be selected in the custom select
                const options = document.querySelectorAll(".comaker-option-item");
                options.forEach(opt => {
                    const val = opt.getAttribute("data-value");
                    const isAlreadyActive = activeComakers.includes(Number(val)) || activeComakers.includes(String(val));
                    
                    if (isAlreadyActive) {
                        opt.classList.add("hidden-old-comaker", "hidden");
                    } else {
                        opt.classList.remove("hidden-old-comaker");
                    }
                });

                openDrawer("modal-replace-comaker");
            });
        });

        // --- Custom Searchable Co-maker Select logic ---
        const wrapper = document.getElementById("searchable-comaker-wrapper");
        if (wrapper) {
            const trigger = document.getElementById("comaker-select-trigger");
            const dropdown = document.getElementById("comaker-select-dropdown");
            const searchInput = document.getElementById("comaker-search-input");
            const hiddenInput = document.getElementById("comaker-hidden-input");
            const label = document.getElementById("comaker-select-label");
            const options = document.querySelectorAll(".comaker-option-item");
            const noResults = document.getElementById("comaker-no-results");

            // Toggle dropdown
            trigger.addEventListener("click", function (e) {
                e.stopPropagation();
                dropdown.classList.toggle("hidden");
                if (!dropdown.classList.contains("hidden")) {
                    searchInput.value = "";
                    searchInput.focus();
                    // Show all options (except the hidden old co-maker) and hide noResults
                    options.forEach(opt => {
                        if (opt.classList.contains("hidden-old-comaker")) {
                            opt.classList.add("hidden");
                        } else {
                            opt.classList.remove("hidden");
                        }
                    });
                    noResults.classList.add("hidden");
                }
            });

            // Filter on search text change
            searchInput.addEventListener("input", function () {
                const query = this.value.trim().toLowerCase();
                let matches = 0;

                options.forEach(opt => {
                    if (opt.classList.contains("hidden-old-comaker")) {
                        opt.classList.add("hidden");
                        return;
                    }
                    const searchData = opt.getAttribute("data-search") || "";
                    if (searchData.includes(query)) {
                        opt.classList.remove("hidden");
                        matches++;
                    } else {
                        opt.classList.add("hidden");
                    }
                });

                if (matches === 0) {
                    noResults.classList.remove("hidden");
                } else {
                    noResults.classList.add("hidden");
                }
            });

            // Handle option selection
            options.forEach(opt => {
                opt.addEventListener("click", function () {
                    const value = this.getAttribute("data-value");
                    const name = this.querySelector("span:first-child").textContent.trim();
                    const companyId = this.querySelector("span:last-child").textContent.trim();

                    hiddenInput.value = value;
                    label.textContent = `${name} (${companyId})`;
                    label.classList.remove("text-slate-400", "dark:text-slate-500");
                    label.classList.add("text-slate-700", "dark:text-slate-300");

                    dropdown.classList.add("hidden");
                });
            });

            // Close dropdown when clicking outside
            document.addEventListener("click", function (e) {
                if (!wrapper.contains(e.target)) {
                    dropdown.classList.add("hidden");
                }
            });

            // Reset selection state helper
            window.resetComakerSearchableSelect = function () {
                hiddenInput.value = "";
                label.textContent = "Select a member...";
                label.classList.add("text-slate-400", "dark:text-slate-500");
                label.classList.remove("text-slate-700", "dark:text-slate-300");
                dropdown.classList.add("hidden");
                options.forEach(opt => {
                    opt.classList.remove("hidden-old-comaker", "hidden");
                });
            };
        }

    });
</script>
@endpush
