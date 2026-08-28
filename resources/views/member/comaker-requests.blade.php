@extends('layouts.user')

@section('title', 'Co-Maker Endorsements - ML Sako')

@section('header')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight serif-font">Co-Maker Endorsements</h1>
        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 font-semibold">Review, digitally authorize, and manage loan co-signing requests from fellow cooperative members.</p>
    </div>
</div>
@endsection

@section('content')
<div class="space-y-6 animate-fade-in">

    <!-- Metrics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="bg-white dark:bg-slate-800 p-5 rounded-3xl border border-slate-100 dark:border-slate-700 shadow-sm space-y-1.5 flex items-center justify-between">
            <div class="space-y-1">
                <span class="text-[10px] font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-widest block">Pending Signatures</span>
                <p class="text-3xl font-black text-slate-800 dark:text-white">{{ $pendingRequests->count() }}</p>
                <span class="text-2xs text-slate-400 dark:text-slate-500 font-bold uppercase tracking-wider">Awaiting My Verification</span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 dark:bg-emerald-950/30 text-emerald-600 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-800/40 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-800 p-5 rounded-3xl border border-slate-100 dark:border-slate-700 shadow-sm space-y-1.5 flex items-center justify-between">
            <div class="space-y-1">
                <span class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest block">History Endorsed</span>
                <p class="text-3xl font-black text-slate-800 dark:text-white">{{ $historicalRequests->count() }}</p>
                <span class="text-2xs text-slate-400 dark:text-slate-500 font-bold uppercase tracking-wider">Total Actions Taken</span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-slate-50 dark:bg-slate-700 text-slate-500 dark:text-slate-400 border border-slate-200/60 dark:border-slate-600 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>
    </div>

    <!-- Main Navigation/List Board -->
    <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-100 dark:border-slate-700 shadow-sm overflow-hidden flex flex-col">
        
        <!-- Tab Headers -->
        <div class="flex border-b border-slate-100 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-900/50 p-2 gap-2">
            <button id="tab-inbox" class="tab-btn active px-3 sm:px-4 py-2.5 rounded-xl font-bold text-xs transition-all duration-200 text-emerald-800 dark:text-emerald-400 bg-white dark:bg-slate-800 shadow-sm border border-slate-200/40 dark:border-slate-700">
                📥 <span class="sm:hidden">Pending</span><span class="hidden sm:inline">Pending Co-Signings</span> ({{ $pendingRequests->count() }})
            </button>
            <button id="tab-history" class="tab-btn px-3 sm:px-4 py-2.5 rounded-xl font-bold text-xs text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200 hover:bg-white/50 dark:hover:bg-slate-700/50 transition-all duration-200">
                🌐 <span class="sm:hidden">History</span><span class="hidden sm:inline">Historical Sign-offs</span> ({{ $historicalRequests->count() }})
            </button>
        </div>

        <!-- TAB 1: PENDING INBOX -->
        <div id="content-inbox" class="tab-panel">
            <!-- Desktop Table (Visible on larger screens) -->
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-slate-900/50 border-b border-slate-100 dark:border-slate-700 text-[10px] font-extrabold text-slate-400 dark:text-slate-500 uppercase tracking-widest">
                            <th class="px-6 py-4.5">Borrower Profile</th>
                            <th class="px-6 py-4.5">Loan Product</th>
                            <th class="px-6 py-4.5">Requested Amount</th>
                            <th class="px-6 py-4.5">Requested Date</th>
                            <th class="px-6 py-4.5 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50 dark:divide-slate-700 text-slate-700 dark:text-slate-300 font-medium">
                        @forelse($pendingRequests as $loan)
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-900/30 transition-colors">
                                <td class="px-6 py-4 flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full bg-slate-100 dark:bg-slate-700 flex items-center justify-center font-bold text-slate-600 dark:text-slate-300 text-xs border border-slate-200/40 dark:border-slate-600/40">
                                        {{ strtoupper(substr($loan->borrower->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-slate-950 dark:text-white">{{ $loan->borrower->name }}</h4>
                                        <p class="text-[10px] text-slate-400 dark:text-slate-500 font-semibold font-mono">ID: {{ $loan->borrower->company_id ?: 'N/A' }}</p>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="font-bold text-slate-950 dark:text-white block">
                                        {{ config("loans.{$loan->loan_category}.{$loan->loan_type}.name", ucwords(str_replace('_', ' ', $loan->loan_type))) }}
                                    </span>
                                    <span class="text-[10px] text-slate-400 dark:text-slate-500 font-bold uppercase tracking-wider">{{ $loan->loan_category }} Loan</span>
                                </td>
                                <td class="px-6 py-4 font-black font-mono text-slate-900 dark:text-white">
                                    ₱{{ number_format($loan->requested_amount, 2) }}
                                </td>
                                <td class="px-6 py-4 text-slate-400 dark:text-slate-500 font-bold">
                                    {{ $loan->created_at->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <button class="btn-evaluate-comaker bg-emerald-600 hover:bg-emerald-700 dark:bg-emerald-600 dark:hover:bg-emerald-500 text-white font-extrabold text-[10px] px-3.5 py-2 rounded-xl transition-all shadow-sm shadow-emerald-600/10 cursor-pointer" 
                                            data-loan="{{ json_encode($loan) }}"
                                            data-borrower-name="{{ $loan->borrower->name }}"
                                            data-borrower-id="{{ $loan->borrower->company_id ?: 'N/A' }}"
                                            data-history="{{ json_encode($loan->approvals->map(function($appr) { return ['stage' => ucwords(str_replace('_', ' ', $appr->stage_role_slug)), 'actor' => $appr->actor->name, 'decision' => $appr->decision, 'remarks' => $appr->remarks, 'date' => $appr->created_at->format('M d, Y h:i A')]; })) }}">
                                        ✍️ Review & Sign
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-slate-400 dark:text-slate-500 font-semibold italic">
                                    No pending co-maker endorsement requests found. You are all caught up!
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile Card Stack (Visible on mobile viewports) -->
            <div class="block md:hidden divide-y divide-slate-100 dark:divide-slate-700">
                @forelse($pendingRequests as $loan)
                    <div class="p-5 space-y-4">
                        <!-- Borrower Profile & Request Date -->
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full bg-slate-100 dark:bg-slate-700 flex items-center justify-center font-bold text-slate-600 dark:text-slate-300 text-xs border border-slate-200/40 dark:border-slate-600/40 flex-shrink-0">
                                    {{ strtoupper(substr($loan->borrower->name, 0, 1)) }}
                                </div>
                                <div>
                                    <h4 class="font-bold text-slate-950 dark:text-white text-sm leading-none">{{ $loan->borrower->name }}</h4>
                                    <p class="text-[10px] text-slate-400 dark:text-slate-500 font-semibold font-mono mt-1 leading-none">ID: {{ $loan->borrower->company_id ?: 'N/A' }}</p>
                                </div>
                            </div>
                            <span class="text-[10px] text-slate-400 dark:text-slate-500 font-bold mt-1 block">
                                {{ $loan->created_at->format('M d, Y') }}
                            </span>
                        </div>

                        <!-- Loan Details Grid -->
                        <div class="grid grid-cols-2 gap-4 p-3 bg-slate-50/50 dark:bg-slate-900/60 rounded-xl border border-slate-100 dark:border-slate-800/80">
                            <div>
                                <span class="text-[9px] font-extrabold text-slate-400 dark:text-slate-500 uppercase tracking-widest block leading-none">Loan Product</span>
                                <span class="text-xs font-bold text-slate-900 dark:text-white mt-1 block leading-tight">
                                    {{ config("loans.{$loan->loan_category}.{$loan->loan_type}.name", ucwords(str_replace('_', ' ', $loan->loan_type))) }}
                                </span>
                                <span class="text-[9px] text-slate-450 dark:text-slate-500 font-semibold uppercase tracking-wider block mt-0.5">{{ $loan->loan_category }} Loan</span>
                            </div>
                            <div class="border-l border-slate-150 dark:border-slate-800 pl-4">
                                <span class="text-[9px] font-extrabold text-slate-400 dark:text-slate-500 uppercase tracking-widest block leading-none">Requested Amount</span>
                                <span class="text-sm font-extrabold text-emerald-700 dark:text-emerald-400 font-mono mt-1 block leading-none">₱{{ number_format($loan->requested_amount, 2) }}</span>
                            </div>
                        </div>

                        <!-- Action Button -->
                        <div class="pt-1 text-right">
                            <button class="btn-evaluate-comaker w-full inline-flex items-center justify-center bg-emerald-600 hover:bg-emerald-700 dark:bg-emerald-600 dark:hover:bg-emerald-500 text-white font-extrabold text-xs px-4 py-3 rounded-xl transition-all shadow-sm cursor-pointer" 
                                    data-loan="{{ json_encode($loan) }}"
                                    data-borrower-name="{{ $loan->borrower->name }}"
                                    data-borrower-id="{{ $loan->borrower->company_id ?: 'N/A' }}"
                                    data-history="{{ json_encode($loan->approvals->map(function($appr) { return ['stage' => ucwords(str_replace('_', ' ', $appr->stage_role_slug)), 'actor' => $appr->actor->name, 'decision' => $appr->decision, 'remarks' => $appr->remarks, 'date' => $appr->created_at->format('M d, Y h:i A')]; })) }}">
                                ✍️ Review & Sign Request
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-slate-400 dark:text-slate-500 font-semibold italic">
                        No pending co-maker endorsement requests found. You are all caught up!
                    </div>
                @endforelse
            </div>
        </div>

        <!-- TAB 2: HISTORICAL ARCHIVE -->
        <div id="content-history" class="tab-panel hidden">
            <!-- Desktop Table (Visible on larger screens) -->
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-slate-900/50 border-b border-slate-100 dark:border-slate-700 text-[10px] font-extrabold text-slate-400 dark:text-slate-500 uppercase tracking-widest">
                            <th class="px-6 py-4.5">Borrower Profile</th>
                            <th class="px-6 py-4.5">Loan Product</th>
                            <th class="px-6 py-4.5">Requested Amount</th>
                            <th class="px-6 py-4.5">Approval Status</th>
                            <th class="px-6 py-4.5 text-right">Timeline</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50 dark:divide-slate-700 text-slate-700 dark:text-slate-300 font-medium">
                        @forelse($historicalRequests as $loan)
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-900/30 transition-colors">
                                <td class="px-6 py-4 flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full bg-slate-100 dark:bg-slate-700 flex items-center justify-center font-bold text-slate-600 dark:text-slate-300 text-xs border border-slate-200/40 dark:border-slate-600/40">
                                        {{ strtoupper(substr($loan->borrower->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-slate-950 dark:text-white">{{ $loan->borrower->name }}</h4>
                                        <p class="text-[10px] text-slate-400 dark:text-slate-500 font-semibold font-mono">ID: {{ $loan->borrower->company_id ?: 'N/A' }}</p>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="font-bold text-slate-950 dark:text-white block">
                                        {{ config("loans.{$loan->loan_category}.{$loan->loan_type}.name", ucwords(str_replace('_', ' ', $loan->loan_type))) }}
                                    </span>
                                    <span class="text-[10px] text-slate-400 dark:text-slate-500 font-bold uppercase tracking-wider">{{ $loan->loan_category }} Loan</span>
                                </td>
                                <td class="px-6 py-4 font-black font-mono text-slate-900 dark:text-white">
                                    ₱{{ number_format($loan->requested_amount, 2) }}
                                </td>
                                <td class="px-6 py-4">
                                    @if($loan->status === 'approved')
                                        <span class="px-2.5 py-1 rounded-full text-[9px] font-extrabold bg-emerald-50 dark:bg-emerald-950/30 text-emerald-700 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-800/40 uppercase tracking-wider">Approved / Released</span>
                                    @elseif($loan->status === 'rejected')
                                        <span class="px-2.5 py-1 rounded-full text-[9px] font-extrabold bg-rose-50 dark:bg-rose-950/30 text-rose-700 dark:text-rose-400 border border-rose-100 dark:border-rose-800/40 uppercase tracking-wider">Rejected</span>
                                    @elseif($loan->status === 'cancelled')
                                        <span class="px-2.5 py-1 rounded-full text-[9px] font-extrabold bg-slate-100 dark:bg-slate-700 text-slate-500 dark:text-slate-400 border border-slate-200 dark:border-slate-600 uppercase tracking-wider">Cancelled</span>
                                    @else
                                        <span class="px-2.5 py-1 rounded-full text-[9px] font-extrabold bg-blue-50 dark:bg-blue-950/30 text-blue-700 dark:text-blue-400 border border-blue-100/60 dark:border-blue-800/40 uppercase tracking-wider animate-pulse">Processing (Current: {{ ucwords(str_replace('_', ' ', $loan->current_stage)) }})</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <button class="btn-view-history-timeline text-2xs font-extrabold text-emerald-600 dark:text-emerald-400 hover:text-emerald-700 dark:hover:text-emerald-300 hover:underline" 
                                            data-history="{{ json_encode($loan->approvals->map(function($appr) { return ['stage' => ucwords(str_replace('_', ' ', $appr->stage_role_slug)), 'actor' => $appr->actor->name, 'decision' => $appr->decision, 'remarks' => $appr->remarks, 'date' => $appr->created_at->format('M d, Y h:i A')]; })) }}">
                                        View Signatories
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-slate-400 dark:text-slate-500 font-semibold italic">
                                    No historical endorsements recorded.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- DRAWER: REVIEW & SIGN -->
<div id="drawer-comaker-evaluate" class="fixed inset-0 z-50 hidden">
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-slate-950/40 dark:bg-slate-950/60 backdrop-blur-md opacity-0 transition-opacity duration-500 ease-[cubic-bezier(0.16,1,0.3,1)] pointer-events-none modal-overlay"></div>
    
    <!-- Floating Premium Panel -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-2xl shadow-slate-950/15 dark:shadow-slate-950/50 overflow-hidden h-[calc(100vh-2rem)] w-[calc(100vw-2rem)] sm:w-full sm:max-w-xl fixed right-4 top-4 bottom-4 z-50 transform translate-x-[calc(100%+2rem)] transition-transform duration-500 ease-[cubic-bezier(0.16,1,0.3,1)] modal-container p-6 sm:p-8 flex flex-col">
        <!-- Header -->
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-4">
            <div>
                <h3 class="text-lg font-bold text-slate-900 dark:text-slate-100 tracking-tight serif-font">Evaluate Endorsement</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 font-medium mt-1">ML Sako Digital Signature Portal</p>
            </div>
            <button class="modal-close p-1.5 rounded-xl text-slate-400 hover:text-slate-700 dark:text-slate-500 dark:hover:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800/80 transition-all duration-200">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <!-- Scrollable Content Container -->
        <div class="flex-1 overflow-y-auto pr-1 -mr-1 space-y-6 mt-6">
            <!-- Co-Maker Liability Notice -->
            <div class="bg-amber-500/5 dark:bg-amber-500/10 border border-amber-500/20 dark:border-amber-500/30 p-4 rounded-xl flex items-start gap-3">
                <svg class="w-5 h-5 text-amber-600 dark:text-amber-400 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <div class="space-y-1">
                    <h5 class="text-xs font-semibold text-amber-800 dark:text-amber-400">Joint Liability Certification</h5>
                    <p class="text-xs text-amber-700 dark:text-amber-500/90 leading-relaxed">
                        Co-signing is a joint financial commitment. By authorizing this request, you agree to act as a co-guarantor, assuming shared responsibility for the outstanding balance should the primary borrower default.
                    </p>
                </div>
            </div>

            <!-- Borrower Profile Section -->
            <div class="bg-slate-50 dark:bg-slate-950/40 border border-slate-100 dark:border-slate-800 p-4 rounded-xl flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-emerald-500 text-white flex items-center justify-center font-bold text-sm shadow-md shadow-emerald-500/10 border border-white/10">
                    <span id="display-borrower-initials"></span>
                </div>
                <div class="space-y-0.5">
                    <span class="text-xs font-semibold text-emerald-600 dark:text-emerald-400 block mb-0.5">Primary Borrower</span>
                    <h4 class="text-base font-bold text-slate-900 dark:text-slate-100 leading-tight" id="display-borrower-name"></h4>
                    <p class="text-xs text-slate-500 dark:text-slate-400 font-mono">Member ID: <span id="display-borrower-id" class="font-semibold text-slate-700 dark:text-slate-300"></span></p>
                </div>
            </div>

            <!-- Loan Specifications -->
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <h4 class="text-xs font-semibold text-slate-500 dark:text-slate-400">Loan Facility Details</h4>
                    <span class="text-xs font-medium text-slate-500 dark:text-slate-400 bg-slate-100 dark:bg-slate-800/60 px-2.5 py-0.5 rounded-full">Vetted Facility</span>
                </div>
                <div class="bg-slate-50/50 dark:bg-slate-950/30 border border-slate-100 dark:border-slate-800 p-4 rounded-xl space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <span class="text-xs text-slate-500 dark:text-slate-400 font-medium">Product Category</span>
                            <span class="font-semibold text-slate-900 dark:text-slate-150 block text-sm mt-0.5" id="display-loan-name"></span>
                        </div>
                        <div class="space-y-1">
                            <span class="text-xs text-slate-500 dark:text-slate-400 font-medium">Repayment Horizon</span>
                            <span class="font-semibold text-slate-900 dark:text-slate-150 block text-sm mt-0.5" id="display-loan-term"></span>
                        </div>
                    </div>
                    
                    <div class="border-t border-slate-100 dark:border-slate-800 pt-3">
                        <span class="text-xs text-slate-500 dark:text-slate-400 font-medium">Principal Amount</span>
                        <span class="font-bold text-slate-950 dark:text-white block text-xl tracking-tight font-mono mt-1" id="display-loan-amount"></span>
                    </div>

                    <div class="border-t border-slate-100 dark:border-slate-800 pt-3 space-y-1">
                        <span class="text-xs text-slate-500 dark:text-slate-400 font-medium block">Statement of Purpose / Remarks</span>
                        <blockquote class="text-xs text-slate-600 dark:text-slate-400 italic pl-3 border-l-2 border-emerald-500/50 leading-relaxed bg-slate-100/40 dark:bg-slate-900/40 p-3 rounded-r-lg mt-1" id="display-member-remarks">
                        </blockquote>
                    </div>
                </div>
            </div>

            <!-- Signatory Trial Timeline -->
            <div class="space-y-3">
                <h4 class="text-xs font-semibold text-slate-500 dark:text-slate-400 font-medium">Digital Validation Trail</h4>
                <div class="space-y-3" id="comaker-history-timeline">
                    <!-- Populated via JS -->
                </div>
            </div>

            <!-- Action Station -->
            <div class="space-y-4 pt-4 border-t border-slate-100 dark:border-slate-800">
                <div class="space-y-1">
                    <h4 class="text-sm font-bold text-rose-600 dark:text-rose-400">Co-Signer Decision Station</h4>
                    <p class="text-xs text-slate-500 dark:text-slate-400 leading-normal">Please review carefully. Your decision will be logged permanently in the cooperative's ledger.</p>
                </div>
                
                <form id="form-comaker-decision" method="POST" class="space-y-4">
                    @csrf
                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-slate-700 dark:text-slate-300 flex items-center justify-between">
                            <span>Digital Certification Notes</span>
                            <span class="text-xs text-slate-400 dark:text-slate-500 font-medium">(Required)</span>
                        </label>
                        <textarea name="remarks" id="comaker-remarks" required rows="3" placeholder="State your verification notes or justification for endorsing/declining this request..." class="w-full px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700/80 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 rounded-xl outline-none focus:border-emerald-500 dark:focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 dark:focus:ring-emerald-500/10 placeholder-slate-400 dark:placeholder-slate-600 transition-all duration-200 resize-none"></textarea>
                        <p class="text-xs text-slate-500 dark:text-slate-400 leading-normal mt-1">
                            ⚠️ Vetting confirmation: By signing, you certify that you have vetted the borrower's capacity to pay.
                        </p>
                    </div>

                    <div class="grid grid-cols-2 gap-3 pt-2">
                        <button type="submit" id="btn-comaker-reject" class="bg-rose-50 hover:bg-rose-100 dark:bg-rose-950/20 dark:hover:bg-rose-900/30 text-rose-700 dark:text-rose-400 border border-rose-200 dark:border-rose-900/40 font-semibold text-sm px-4 py-3 rounded-xl transition-all">
                            Decline Request
                        </button>
                        <button type="submit" id="btn-comaker-approve" class="bg-emerald-600 hover:bg-emerald-700 dark:bg-emerald-600 dark:hover:bg-emerald-500 text-white font-semibold text-sm px-4 py-3 rounded-xl shadow-md shadow-emerald-600/15 dark:shadow-emerald-900/25 hover:-translate-y-0.5 transition-all duration-200">
                            Authorize & Sign
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- MODAL: VIEW SIGNATORIES -->
<div id="modal-signatories" class="fixed inset-0 z-50 hidden overflow-y-auto flex items-center justify-center p-4">
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-slate-950/40 dark:bg-slate-950/60 backdrop-blur-md opacity-0 transition-opacity duration-500 ease-[cubic-bezier(0.16,1,0.3,1)] pointer-events-none modal-overlay"></div>
    
    <!-- Modal Container -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-2xl shadow-slate-950/15 dark:shadow-slate-950/50 overflow-hidden w-full max-w-md relative z-10 p-6 sm:p-8 space-y-6 transform scale-95 opacity-0 transition-all duration-500 ease-[cubic-bezier(0.16,1,0.3,1)] modal-container">
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-4">
            <div>
                <h3 class="text-xl font-extrabold text-slate-900 dark:text-slate-100 tracking-tight serif-font">Verification History</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 font-medium mt-1">Cooperative Audit Logs</p>
            </div>
            <button class="modal-close p-1.5 rounded-xl text-slate-400 hover:text-slate-700 dark:text-slate-500 dark:hover:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800/80 transition-all duration-200">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <!-- Timeline Steps Container -->
        <div class="space-y-4" id="signatories-timeline-container">
            <!-- Dynamically populated via JS -->
        </div>

        <div class="pt-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-end">
            <button type="button" class="modal-close px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-xs font-bold text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-800 dark:hover:text-slate-100 transition-colors">Close</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function () {
        
        // --- Tab Swappings ---
        const tabInbox = document.getElementById("tab-inbox");
        const tabHistory = document.getElementById("tab-history");
        const contentInbox = document.getElementById("content-inbox");
        const contentHistory = document.getElementById("content-history");

        if (tabInbox && tabHistory) {
            tabInbox.addEventListener("click", () => {
                tabInbox.className = "tab-btn active px-4 py-2.5 rounded-xl font-bold text-xs transition-all duration-200 text-emerald-800 dark:text-emerald-400 bg-white dark:bg-slate-800 shadow-sm border border-slate-200/40 dark:border-slate-700";
                tabHistory.className = "tab-btn px-4 py-2.5 rounded-xl font-bold text-xs text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200 hover:bg-white/50 dark:hover:bg-slate-700/50 transition-all duration-200";
                contentInbox.classList.remove("hidden");
                contentHistory.classList.add("hidden");
            });

            tabHistory.addEventListener("click", () => {
                tabHistory.className = "tab-btn active px-4 py-2.5 rounded-xl font-bold text-xs transition-all duration-200 text-emerald-800 dark:text-emerald-400 bg-white dark:bg-slate-800 shadow-sm border border-slate-200/40 dark:border-slate-700";
                tabInbox.className = "tab-btn px-4 py-2.5 rounded-xl font-bold text-xs text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200 hover:bg-white/50 dark:hover:bg-slate-700/50 transition-all duration-200";
                contentHistory.classList.remove("hidden");
                contentInbox.classList.add("hidden");
            });
        }

        // --- Drawer & Modal Controls ---
        function openDrawer(id) {
            const el = document.getElementById(id);
            if (!el) return;
            el.classList.remove("hidden");
            
            const overlay = el.querySelector(".modal-overlay");
            const container = el.querySelector(".modal-container");
            
            setTimeout(() => {
                if (overlay) {
                    overlay.classList.remove("opacity-0", "pointer-events-none");
                    overlay.classList.add("opacity-100", "pointer-events-auto");
                }
                if (container) {
                    if (id.startsWith("drawer-")) {
                        container.classList.remove("translate-x-[calc(100%+2rem)]");
                        container.classList.add("translate-x-0");
                    } else {
                        container.classList.remove("scale-95", "opacity-0");
                        container.classList.add("scale-100", "opacity-100");
                    }
                }
            }, 50);
        }

        function closeDrawer(id) {
            const el = document.getElementById(id);
            if (!el) return;
            
            const overlay = el.querySelector(".modal-overlay");
            const container = el.querySelector(".modal-container");
            
            if (overlay) {
                overlay.classList.add("opacity-0", "pointer-events-none");
                overlay.classList.remove("opacity-100", "pointer-events-auto");
            }
            if (container) {
                if (id.startsWith("drawer-")) {
                    container.classList.add("translate-x-[calc(100%+2rem)]");
                    container.classList.remove("translate-x-0");
                } else {
                    container.classList.add("scale-95", "opacity-0");
                    container.classList.remove("scale-100", "opacity-100");
                }
            }
            
            setTimeout(() => {
                el.classList.add("hidden");
            }, 500);
        }

        // Bind escape keys & global overlays to close
        document.querySelectorAll(".modal-close, .modal-overlay").forEach(btn => {
            btn.addEventListener("click", function () {
                const modal = this.closest('[id^="drawer-"], [id^="modal-"]');
                if (modal) closeDrawer(modal.id);
            });
        });

        // --- Render Timelines helper ---
        function renderTimeline(timelineArray, targetContainerId) {
            const container = document.getElementById(targetContainerId);
            container.innerHTML = "";

            if (timelineArray.length === 0) {
                container.innerHTML = '<p class="text-center py-4 text-slate-500 dark:text-slate-400 italic text-xs bg-slate-50 dark:bg-slate-950/40 border border-slate-100 dark:border-slate-800 rounded-xl">This request has no prior validations.</p>';
            } else {
                timelineArray.forEach((step, idx) => {
                    const card = document.createElement("div");
                    card.className = "flex gap-4 relative";
                    
                    const lineHtml = idx < timelineArray.length - 1 
                        ? '<div class="absolute left-4.5 top-8 w-0.5 h-12 bg-emerald-100 dark:bg-emerald-950/30"></div>' 
                        : '';

                    card.innerHTML = `
                        ${lineHtml}
                        <div class="w-8.5 h-8.5 rounded-full ${step.decision === 'approved' ? 'bg-emerald-50 dark:bg-emerald-950/30 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800/40' : 'bg-rose-50 dark:bg-rose-950/30 text-rose-600 dark:text-rose-400 border border-rose-200 dark:border-rose-800/40'} flex items-center justify-center flex-shrink-0 text-xs font-bold relative z-10">
                            ${idx + 1}
                        </div>
                        <div class="flex-grow bg-slate-50 dark:bg-slate-950/40 border border-slate-100 dark:border-slate-800 p-4 rounded-xl text-xs leading-relaxed">
                            <div class="flex justify-between items-start gap-4">
                                <span class="font-semibold text-slate-900 dark:text-white">${step.stage}</span>
                                <span class="text-[10px] font-semibold capitalize px-2.5 py-0.5 rounded-full ${step.decision === 'approved' ? 'bg-emerald-50 dark:bg-emerald-950/30 text-emerald-700 dark:text-emerald-400 border border-emerald-150 dark:border-emerald-800/40' : 'bg-rose-50 dark:bg-rose-950/30 text-rose-700 dark:text-rose-400 border border-rose-150 dark:border-rose-800/40'}">${step.decision}</span>
                            </div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">By: <span class="font-semibold text-slate-700 dark:text-slate-300">${step.actor}</span></p>
                            ${step.remarks ? `<p class="text-slate-600 dark:text-slate-400 italic mt-2 p-2.5 bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-xl leading-relaxed">"${step.remarks}"</p>` : ''}
                            <p class="text-[10px] text-slate-400 dark:text-slate-500 mt-2 text-right font-medium">${step.date}</p>
                        </div>
                    `;
                    container.appendChild(card);
                });
            }
        }

        // --- View History Modal Triggers ---
        document.querySelectorAll(".btn-view-history-timeline").forEach(btn => {
            btn.addEventListener("click", function () {
                const history = JSON.parse(this.getAttribute("data-history") || "[]");
                renderTimeline(history, "signatories-timeline-container");
                openDrawer("modal-signatories");
            });
        });

        // --- Evaluate Request Drawer Trigger ---
        document.querySelectorAll(".btn-evaluate-comaker").forEach(btn => {
            btn.addEventListener("click", function () {
                const loan = JSON.parse(this.getAttribute("data-loan"));
                const borrowerName = this.getAttribute("data-borrower-name");
                const borrowerId = this.getAttribute("data-borrower-id");
                const history = JSON.parse(this.getAttribute("data-history") || "[]");

                // Populate drawer texts
                document.getElementById("display-borrower-initials").textContent = borrowerName.substring(0, 2).toUpperCase();
                document.getElementById("display-borrower-name").textContent = borrowerName;
                document.getElementById("display-borrower-id").textContent = borrowerId;

                // Configure dynamic loan texts
                const category = loan.loan_category;
                const type = loan.loan_type;
                const requestedAmount = parseFloat(loan.requested_amount);
                
                // Set loan details
                document.getElementById("display-loan-name").textContent = `${loan.loan_type.replace('_', ' ').toUpperCase()} (${category.toUpperCase()})`;
                document.getElementById("display-loan-amount").textContent = "₱" + requestedAmount.toLocaleString('en-US', {minimumFractionDigits: 2});
                document.getElementById("display-loan-term").textContent = (loan.form_data.term_months || 'N/A') + " Months";
                document.getElementById("display-member-remarks").textContent = loan.form_data.member_remarks ? `"${loan.form_data.member_remarks}"` : "None specified.";

                // Render history step list
                renderTimeline(history, "comaker-history-timeline");

                // Set up decision buttons & actions
                const formDecision = document.getElementById("form-comaker-decision");
                const txtRemarks = document.getElementById("comaker-remarks");
                const btnApprove = document.getElementById("btn-comaker-approve");
                const btnReject = document.getElementById("btn-comaker-reject");

                txtRemarks.value = "";

                btnApprove.onclick = function (e) {
                    e.preventDefault();
                    if (!txtRemarks.value.trim()) {
                        if (window.MLSAKOAlert) {
                            MLSAKOAlert.fire({
                                icon: 'warning',
                                title: 'Remarks Required',
                                text: "Please enter brief digital verification remarks before endorsing.",
                                confirmButtonText: 'Understood'
                            });
                        } else {
                            alert("Please enter brief digital verification remarks before endorsing.");
                        }
                        return;
                    }
                    formDecision.action = `/loans/${loan.id}/approve`;
                    formDecision.submit();
                };

                btnReject.onclick = function (e) {
                    e.preventDefault();
                    if (!txtRemarks.value.trim()) {
                        if (window.MLSAKOAlert) {
                            MLSAKOAlert.fire({
                                icon: 'warning',
                                title: 'Remarks Required',
                                text: "Please specify rejection remarks to log the case decision.",
                                confirmButtonText: 'Understood'
                            });
                        } else {
                            alert("Please specify rejection remarks to log the case decision.");
                        }
                        return;
                    }
                    formDecision.action = `/loans/${loan.id}/reject`;
                    formDecision.submit();
                };

                openDrawer("drawer-comaker-evaluate");
            });
        });

    });
</script>
@endpush
