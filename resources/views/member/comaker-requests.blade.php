@extends('layouts.user')

@section('title', 'Co-Maker Requests - ML Sako')

@section('navbar_title')
<span class="sm:hidden">Co-Maker Requests</span><span class="hidden sm:inline">Co-Maker Endorsements</span>
@endsection
@section('navbar_subtitle', 'Review, digitally authorize, and manage loan co-signing requests from fellow cooperative members.')

@section('content')
<div class="space-y-5 sm:space-y-6 animate-fade-in">

    <!-- Top Action Toolbar -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-3 border-b border-slate-200/60 dark:border-slate-800/60">
        <div class="flex flex-wrap items-center gap-2">
            @if($pendingRequests->count() > 0)
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-400 text-xs font-semibold border border-amber-200/60 dark:border-amber-800/40">
                    <span class="w-1.5 h-1.5 bg-amber-500 rounded-full animate-pulse"></span>
                    {{ $pendingRequests->count() }} Action{{ $pendingRequests->count() > 1 ? 's' : '' }} Required
                </span>
            @else
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 text-xs font-semibold border border-emerald-200/60 dark:border-emerald-800/40">
                    <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full"></span>
                    Inbox Up to Date
                </span>
            @endif
            <span class="text-xs text-slate-500 dark:text-slate-400 font-medium bg-slate-100 dark:bg-slate-800/80 px-2.5 py-1 rounded-lg border border-slate-200/80 dark:border-slate-700">
                ID: <strong class="text-slate-700 dark:text-slate-200">{{ Auth::user()->company_id ?: 'N/A' }}</strong>
            </span>
        </div>

        <div class="flex items-center gap-2">
            <span class="text-xs text-slate-500 dark:text-slate-400 font-medium">
                Total Sign-offs: <strong class="text-slate-800 dark:text-slate-200 font-mono">{{ $historicalRequests->count() }}</strong>
            </span>
        </div>
    </div>

    <!-- Metrics Summary Micro-Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
        <!-- Metric 1: Pending Signatures -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 p-4 shadow-xs flex items-center justify-between">
            <div class="space-y-0.5">
                <span class="text-[10px] font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wider block">Pending Signatures</span>
                <p class="text-xl sm:text-2xl font-extrabold text-slate-900 dark:text-white font-mono">
                    {{ $pendingRequests->count() }}
                </p>
                <span class="text-[10px] {{ $pendingRequests->count() > 0 ? 'text-amber-600 dark:text-amber-400 font-semibold' : 'text-slate-400 dark:text-slate-500' }} block">
                    {{ $pendingRequests->count() > 0 ? 'Awaiting your digital sign-off' : 'All requests completed' }}
                </span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400 border border-amber-200/60 dark:border-amber-800/40 flex items-center justify-center flex-shrink-0">
                <i class="fa-solid fa-pen-nib text-sm"></i>
            </div>
        </div>

        <!-- Metric 2: History Endorsed -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 p-4 shadow-xs flex items-center justify-between">
            <div class="space-y-0.5">
                <span class="text-[10px] font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wider block">History Endorsed</span>
                <p class="text-xl sm:text-2xl font-extrabold text-slate-900 dark:text-white font-mono">
                    {{ $historicalRequests->count() }}
                </p>
                <span class="text-[10px] text-slate-400 dark:text-slate-500 block">Total authorizations actioned</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-600 flex items-center justify-center flex-shrink-0">
                <i class="fa-solid fa-clipboard-check text-sm"></i>
            </div>
        </div>
    </div>

    <!-- Main Navigation / List Container -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 shadow-xs overflow-hidden flex flex-col">
        
        <!-- Segmented Tab Navigation -->
        <div class="flex items-center border-b border-slate-100 dark:border-slate-700/60 bg-slate-50/60 dark:bg-slate-900/40 p-2 gap-1.5 sm:gap-2">
            <button id="tab-inbox" type="button" class="tab-btn active inline-flex items-center gap-2 px-3 sm:px-4 py-2 rounded-xl text-xs font-bold transition-all duration-150 text-emerald-700 dark:text-emerald-400 bg-white dark:bg-slate-800 shadow-xs border border-slate-200/80 dark:border-slate-700">
                <i class="fa-solid fa-inbox text-xs"></i>
                <span>Pending Requests</span>
                <span class="px-1.5 py-0.2 rounded-full text-[10px] font-mono font-extrabold {{ $pendingRequests->count() > 0 ? 'bg-amber-100 dark:bg-amber-950 text-amber-800 dark:text-amber-300' : 'bg-slate-100 dark:bg-slate-700 text-slate-500' }}">
                    {{ $pendingRequests->count() }}
                </span>
            </button>
            <button id="tab-history" type="button" class="tab-btn inline-flex items-center gap-2 px-3 sm:px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200 hover:bg-white/60 dark:hover:bg-slate-800/60 transition-all duration-150">
                <i class="fa-solid fa-clock-rotate-left text-xs"></i>
                <span>Endorsement History</span>
                <span class="px-1.5 py-0.2 rounded-full text-[10px] font-mono font-bold bg-slate-100 dark:bg-slate-700 text-slate-500">
                    {{ $historicalRequests->count() }}
                </span>
            </button>
        </div>

        <!-- TAB 1: PENDING INBOX -->
        <div id="content-inbox" class="tab-panel p-4 sm:p-5">
            <!-- Desktop Table (Visible on md+ screens) -->
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="text-slate-500 dark:text-slate-400 bg-slate-50/80 dark:bg-slate-900/40 border-b border-slate-200/70 dark:border-slate-700 uppercase tracking-wider text-[10px] font-bold">
                            <th class="py-2.5 px-3.5 rounded-l-lg">Borrower Profile</th>
                            <th class="py-2.5 px-3.5">Loan Facility</th>
                            <th class="py-2.5 px-3.5">Requested Amount</th>
                            <th class="py-2.5 px-3.5">Date Requested</th>
                            <th class="py-2.5 px-3.5 text-right rounded-r-lg">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700/60 text-slate-700 dark:text-slate-300 font-medium">
                        @forelse($pendingRequests as $loan)
                            <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-900/30 transition-colors">
                                <!-- Borrower Profile -->
                                <td class="py-3 px-3.5">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-8 h-8 rounded-lg bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 border border-emerald-200/60 dark:border-emerald-800/40 flex items-center justify-center font-bold text-xs flex-shrink-0">
                                            {{ strtoupper(substr($loan->borrower->name, 0, 2)) }}
                                        </div>
                                        <div class="min-w-0">
                                            <h4 class="font-bold text-slate-900 dark:text-white text-xs truncate">{{ $loan->borrower->name }}</h4>
                                            <p class="text-[10px] text-slate-400 dark:text-slate-500 font-mono">ID: {{ $loan->borrower->company_id ?: 'N/A' }}</p>
                                        </div>
                                    </div>
                                </td>

                                <!-- Loan Facility -->
                                <td class="py-3 px-3.5">
                                    <span class="font-bold text-slate-900 dark:text-white block text-xs">
                                        {{ config("loans.{$loan->loan_category}.{$loan->loan_type}.name", ucwords(str_replace('_', ' ', $loan->loan_type))) }}
                                    </span>
                                    <span class="text-[10px] text-slate-400 dark:text-slate-500 uppercase tracking-wider font-semibold">
                                        {{ $loan->loan_category }} Loan • {{ $loan->form_data['term_months'] ?? 'N/A' }} Mos
                                    </span>
                                </td>

                                <!-- Requested Amount -->
                                <td class="py-3 px-3.5 font-mono font-bold text-slate-900 dark:text-white text-xs">
                                    ₱{{ number_format($loan->requested_amount, 2) }}
                                </td>

                                <!-- Date Requested -->
                                <td class="py-3 px-3.5 text-slate-500 dark:text-slate-400 text-xs">
                                    {{ $loan->created_at->format('M d, Y') }}
                                </td>

                                <!-- Action Button -->
                                <td class="py-3 px-3.5 text-right">
                                    <button type="button" class="btn-evaluate-comaker inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold shadow-xs shadow-emerald-600/10 transition-all cursor-pointer" 
                                            data-loan="{{ json_encode($loan) }}"
                                            data-borrower-name="{{ $loan->borrower->name }}"
                                            data-borrower-id="{{ $loan->borrower->company_id ?: 'N/A' }}"
                                            data-history="{{ json_encode($loan->approvals->map(function($appr) { return ['stage' => ucwords(str_replace('_', ' ', $appr->stage_role_slug)), 'actor' => $appr->actor->name, 'decision' => $appr->decision, 'remarks' => $appr->remarks, 'date' => $appr->created_at->format('M d, Y h:i A')]; })) }}">
                                        <i class="fa-solid fa-signature text-xs"></i>
                                        <span>Review &amp; Sign</span>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-10 text-center text-slate-400 dark:text-slate-500 font-medium italic text-xs">
                                    No pending co-maker endorsement requests found. You are all caught up!
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile Card Stack (Visible on <md screens) -->
            <div class="block md:hidden space-y-3">
                @forelse($pendingRequests as $loan)
                    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 p-4 space-y-3 shadow-xs">
                        <!-- Borrower Profile Header -->
                        <div class="flex items-start justify-between gap-2.5">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <div class="w-8 h-8 rounded-lg bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 border border-emerald-200/60 dark:border-emerald-800/40 flex items-center justify-center font-bold text-xs flex-shrink-0">
                                    {{ strtoupper(substr($loan->borrower->name, 0, 2)) }}
                                </div>
                                <div class="min-w-0">
                                    <h4 class="font-bold text-slate-900 dark:text-white text-xs truncate leading-tight">{{ $loan->borrower->name }}</h4>
                                    <p class="text-[10px] text-slate-400 dark:text-slate-500 font-mono mt-0.5">ID: {{ $loan->borrower->company_id ?: 'N/A' }}</p>
                                </div>
                            </div>
                            <span class="text-[10px] text-slate-400 dark:text-slate-500 font-semibold flex-shrink-0">
                                {{ $loan->created_at->format('M d, Y') }}
                            </span>
                        </div>

                        <!-- Loan Details Inset -->
                        <div class="grid grid-cols-2 gap-2 p-2.5 rounded-xl bg-slate-50 dark:bg-slate-900/50 border border-slate-200/60 dark:border-slate-700/60">
                            <div>
                                <span class="text-[9px] font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wider block">Facility</span>
                                <span class="text-xs font-bold text-slate-900 dark:text-white block truncate mt-0.5">
                                    {{ config("loans.{$loan->loan_category}.{$loan->loan_type}.name", ucwords(str_replace('_', ' ', $loan->loan_type))) }}
                                </span>
                                <span class="text-[9px] text-slate-400 uppercase font-semibold block">{{ $loan->loan_category }}</span>
                            </div>
                            <div class="border-l border-slate-200 dark:border-slate-700/80 pl-3">
                                <span class="text-[9px] font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wider block">Principal</span>
                                <span class="text-sm font-extrabold text-emerald-700 dark:text-emerald-400 font-mono block mt-0.5">
                                    ₱{{ number_format($loan->requested_amount, 2) }}
                                </span>
                                <span class="text-[9px] text-slate-400 block">{{ $loan->form_data['term_months'] ?? 'N/A' }} Months</span>
                            </div>
                        </div>

                        <!-- Action Button -->
                        <div class="pt-1">
                            <button type="button" class="btn-evaluate-comaker w-full inline-flex items-center justify-center gap-1.5 py-2.5 px-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs shadow-xs transition-all cursor-pointer" 
                                    data-loan="{{ json_encode($loan) }}"
                                    data-borrower-name="{{ $loan->borrower->name }}"
                                    data-borrower-id="{{ $loan->borrower->company_id ?: 'N/A' }}"
                                    data-history="{{ json_encode($loan->approvals->map(function($appr) { return ['stage' => ucwords(str_replace('_', ' ', $appr->stage_role_slug)), 'actor' => $appr->actor->name, 'decision' => $appr->decision, 'remarks' => $appr->remarks, 'date' => $appr->created_at->format('M d, Y h:i A')]; })) }}">
                                <i class="fa-solid fa-signature text-xs"></i>
                                <span>Review &amp; Sign Request</span>
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="py-8 text-center text-slate-400 dark:text-slate-500 font-medium italic text-xs">
                        No pending co-maker endorsement requests found. You are all caught up!
                    </div>
                @endforelse
            </div>
        </div>

        <!-- TAB 2: HISTORICAL ARCHIVE -->
        <div id="content-history" class="tab-panel hidden p-4 sm:p-5">
            <!-- Desktop Table (Visible on md+ screens) -->
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="text-slate-500 dark:text-slate-400 bg-slate-50/80 dark:bg-slate-900/40 border-b border-slate-200/70 dark:border-slate-700 uppercase tracking-wider text-[10px] font-bold">
                            <th class="py-2.5 px-3.5 rounded-l-lg">Borrower Profile</th>
                            <th class="py-2.5 px-3.5">Loan Facility</th>
                            <th class="py-2.5 px-3.5">Requested Amount</th>
                            <th class="py-2.5 px-3.5">Approval Status</th>
                            <th class="py-2.5 px-3.5 text-right rounded-r-lg">Audit Trail</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700/60 text-slate-700 dark:text-slate-300 font-medium">
                        @forelse($historicalRequests as $loan)
                            <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-900/30 transition-colors">
                                <!-- Borrower Profile -->
                                <td class="py-3 px-3.5">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-8 h-8 rounded-lg bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-600 flex items-center justify-center font-bold text-xs flex-shrink-0">
                                            {{ strtoupper(substr($loan->borrower->name, 0, 2)) }}
                                        </div>
                                        <div class="min-w-0">
                                            <h4 class="font-bold text-slate-900 dark:text-white text-xs truncate">{{ $loan->borrower->name }}</h4>
                                            <p class="text-[10px] text-slate-400 dark:text-slate-500 font-mono">ID: {{ $loan->borrower->company_id ?: 'N/A' }}</p>
                                        </div>
                                    </div>
                                </td>

                                <!-- Loan Facility -->
                                <td class="py-3 px-3.5">
                                    <span class="font-bold text-slate-900 dark:text-white block text-xs">
                                        {{ config("loans.{$loan->loan_category}.{$loan->loan_type}.name", ucwords(str_replace('_', ' ', $loan->loan_type))) }}
                                    </span>
                                    <span class="text-[10px] text-slate-400 dark:text-slate-500 uppercase tracking-wider font-semibold">
                                        {{ $loan->loan_category }} Loan
                                    </span>
                                </td>

                                <!-- Requested Amount -->
                                <td class="py-3 px-3.5 font-mono font-bold text-slate-900 dark:text-white text-xs">
                                    ₱{{ number_format($loan->requested_amount, 2) }}
                                </td>

                                <!-- Status Badge -->
                                <td class="py-3 px-3.5">
                                    @if($loan->status === 'approved')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 text-[10px] font-bold border border-emerald-200/60 dark:border-emerald-800/40">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            Released
                                        </span>
                                    @elseif($loan->status === 'rejected')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-400 text-[10px] font-bold border border-rose-200/60 dark:border-rose-800/40">
                                            Rejected
                                        </span>
                                    @elseif($loan->status === 'cancelled')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-slate-700 text-slate-500 dark:text-slate-400 text-[10px] font-bold">
                                            Cancelled
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-sky-50 dark:bg-sky-950/40 text-sky-700 dark:text-sky-400 text-[10px] font-bold border border-sky-200/60 dark:border-sky-800/40 animate-pulse">
                                            <span class="w-1.5 h-1.5 rounded-full bg-sky-500"></span>
                                            Processing ({{ ucwords(str_replace('_', ' ', $loan->current_stage)) }})
                                        </span>
                                    @endif
                                </td>

                                <!-- Timeline Trigger -->
                                <td class="py-3 px-3.5 text-right">
                                    <button type="button" class="btn-view-history-timeline inline-flex items-center gap-1 text-xs font-semibold text-emerald-600 dark:text-emerald-400 hover:text-emerald-700 dark:hover:text-emerald-300 transition-colors cursor-pointer" 
                                            data-history="{{ json_encode($loan->approvals->map(function($appr) { return ['stage' => ucwords(str_replace('_', ' ', $appr->stage_role_slug)), 'actor' => $appr->actor->name, 'decision' => $appr->decision, 'remarks' => $appr->remarks, 'date' => $appr->created_at->format('M d, Y h:i A')]; })) }}">
                                        <span>Signatories</span>
                                        <i class="fa-solid fa-arrow-right text-[10px]"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-10 text-center text-slate-400 dark:text-slate-500 font-medium italic text-xs">
                                    No historical endorsements recorded.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile Card Stack for History (Fixes missing mobile view bug) -->
            <div class="block md:hidden space-y-3">
                @forelse($historicalRequests as $loan)
                    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 p-4 space-y-3 shadow-xs">
                        <div class="flex items-start justify-between gap-2.5">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <div class="w-8 h-8 rounded-lg bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-600 flex items-center justify-center font-bold text-xs flex-shrink-0">
                                    {{ strtoupper(substr($loan->borrower->name, 0, 2)) }}
                                </div>
                                <div class="min-w-0">
                                    <h4 class="font-bold text-slate-900 dark:text-white text-xs truncate">{{ $loan->borrower->name }}</h4>
                                    <p class="text-[10px] text-slate-400 dark:text-slate-500 font-mono mt-0.5">ID: {{ $loan->borrower->company_id ?: 'N/A' }}</p>
                                </div>
                            </div>
                            <div>
                                @if($loan->status === 'approved')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 text-[10px] font-bold border border-emerald-200/60 dark:border-emerald-800/40">
                                        Released
                                    </span>
                                @elseif($loan->status === 'rejected')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-400 text-[10px] font-bold border border-rose-200/60 dark:border-rose-800/40">
                                        Rejected
                                    </span>
                                @elseif($loan->status === 'cancelled')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-700 text-slate-500 dark:text-slate-400 text-[10px] font-bold">
                                        Cancelled
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-sky-50 dark:bg-sky-950/40 text-sky-700 dark:text-sky-400 text-[10px] font-bold border border-sky-200/60 dark:border-sky-800/40">
                                        In Review
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Loan Details Inset -->
                        <div class="grid grid-cols-2 gap-2 p-2.5 rounded-xl bg-slate-50 dark:bg-slate-900/50 border border-slate-200/60 dark:border-slate-700/60">
                            <div>
                                <span class="text-[9px] font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wider block">Facility</span>
                                <span class="text-xs font-bold text-slate-900 dark:text-white block truncate mt-0.5">
                                    {{ config("loans.{$loan->loan_category}.{$loan->loan_type}.name", ucwords(str_replace('_', ' ', $loan->loan_type))) }}
                                </span>
                            </div>
                            <div class="border-l border-slate-200 dark:border-slate-700/80 pl-3">
                                <span class="text-[9px] font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wider block">Principal</span>
                                <span class="text-sm font-extrabold text-slate-900 dark:text-white font-mono block mt-0.5">
                                    ₱{{ number_format($loan->requested_amount, 2) }}
                                </span>
                            </div>
                        </div>

                        <!-- Audit Action Button -->
                        <div class="pt-1">
                            <button type="button" class="btn-view-history-timeline w-full inline-flex items-center justify-center gap-1.5 py-2 px-3 rounded-xl bg-slate-100 dark:bg-slate-700/70 hover:bg-slate-200/80 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 text-xs font-semibold transition-all cursor-pointer" 
                                    data-history="{{ json_encode($loan->approvals->map(function($appr) { return ['stage' => ucwords(str_replace('_', ' ', $appr->stage_role_slug)), 'actor' => $appr->actor->name, 'decision' => $appr->decision, 'remarks' => $appr->remarks, 'date' => $appr->created_at->format('M d, Y h:i A')]; })) }}">
                                <span>View Signatories Trail</span>
                                <i class="fa-solid fa-arrow-right text-[10px]"></i>
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="py-8 text-center text-slate-400 dark:text-slate-500 font-medium italic text-xs">
                        No historical endorsements recorded.
                    </div>
                @endforelse
            </div>
        </div>

    </div>

</div>

<!-- RESPONSIVE DIALOG: REVIEW & SIGN (MODERN CREDIT GUARANTEE SHEET) -->
<div id="drawer-comaker-evaluate" class="fixed inset-0 z-50 hidden overflow-y-auto flex items-center justify-center p-3 sm:p-4">
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-slate-950/40 dark:bg-slate-950/70 backdrop-blur-sm opacity-0 transition-opacity duration-300 pointer-events-none modal-overlay"></div>
    
    <!-- Modal Container with Fixed Header, Scrollable Body, and Fixed Footer -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl sm:rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-2xl w-full max-w-xl relative z-10 flex flex-col max-h-[90vh] transform scale-95 opacity-0 transition-all duration-300 modal-container overflow-hidden">
        
        <!-- FIXED HEADER -->
        <div class="flex items-start justify-between gap-3 p-4 sm:p-5 border-b border-slate-100 dark:border-slate-800 bg-white dark:bg-slate-900 flex-shrink-0">
            <div class="space-y-1 min-w-0 flex-1">
                <div class="flex flex-wrap items-center gap-1.5 sm:gap-2">
                    <h3 class="text-sm sm:text-base md:text-lg font-bold text-slate-900 dark:text-slate-100 leading-snug">
                        Co-Maker Guarantee Endorsement
                    </h3>
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 text-[9px] sm:text-[10px] font-bold border border-emerald-200/60 dark:border-emerald-800/40 flex-shrink-0">
                        <i class="fa-solid fa-shield-halved text-[9px]"></i>
                        <span>Bylaws Compliant</span>
                    </span>
                </div>
                <div class="flex flex-wrap items-center gap-x-2 gap-y-1 text-[11px] sm:text-xs text-slate-500 dark:text-slate-400 font-medium">
                    <span class="flex items-center gap-1">
                        Ref: <span id="display-app-ref" class="font-mono font-bold text-slate-800 dark:text-slate-200 bg-slate-100 dark:bg-slate-800 px-1.5 py-0.5 rounded border border-slate-200/70 dark:border-slate-700">LN-00000</span>
                    </span>
                    <span class="text-slate-300 dark:text-slate-600 hidden xs:inline">•</span>
                    <span class="truncate">Legally Binding Guaranty</span>
                </div>
            </div>
            <button type="button" class="modal-close p-1.5 rounded-xl text-slate-400 hover:text-slate-700 dark:text-slate-500 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors flex-shrink-0 cursor-pointer" aria-label="Close modal">
                <i class="fa-solid fa-xmark text-base"></i>
            </button>
        </div>

        <!-- FORM WRAPPER (ENCLOSES SCROLLABLE BODY & FIXED FOOTER) -->
        <form id="form-comaker-decision" method="POST" class="flex flex-col flex-1 overflow-hidden m-0">
            @csrf
            <!-- Hidden PIN input passed to controller -->
            <input type="hidden" name="pin" id="comaker-pin-input">

            <!-- SCROLLABLE BODY CONTENT -->
            <div class="flex-1 overflow-y-auto p-4 sm:p-6 space-y-4">
                
                <!-- Guaranteed Financial Exposure Banner -->
                <div class="p-4 sm:p-5 rounded-2xl bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 text-white shadow-md relative overflow-hidden">
                    <!-- Subtle background watermark icon -->
                    <i class="fa-solid fa-file-contract absolute -right-3 -bottom-3 text-7xl text-white/5 pointer-events-none"></i>
                    
                    <div class="relative z-10 space-y-1.5">
                        <div class="flex flex-wrap items-center justify-between gap-1.5">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-400 flex items-center gap-1.5">
                                <i class="fa-solid fa-hand-holding-dollar text-xs"></i>
                                Guaranteed Principal Exposure
                            </span>
                            <span class="text-[10px] text-slate-300 font-medium bg-white/10 px-2 py-0.5 rounded-md backdrop-blur-xs">
                                Joint &amp; Several Liability
                            </span>
                        </div>
                        
                        <div class="flex items-baseline justify-between pt-1">
                            <h2 id="display-loan-amount" class="text-2xl sm:text-3xl font-extrabold font-mono text-white tracking-tight">₱0.00</h2>
                            <div class="text-right">
                                <span class="text-[10px] text-slate-400 uppercase tracking-wider block">Horizon</span>
                                <span id="display-loan-term" class="text-xs font-bold font-mono text-emerald-300">0 Months</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Unified Borrower Profile & Loan Specifications Brief -->
                <div class="bg-slate-50 dark:bg-slate-950/40 border border-slate-200/70 dark:border-slate-800/70 p-4 rounded-2xl space-y-3">
                    <!-- Borrower Header row -->
                    <div class="flex items-center justify-between gap-3">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <div class="w-9 h-9 rounded-xl bg-emerald-600 text-white flex items-center justify-center font-bold text-xs flex-shrink-0 shadow-xs">
                                <span id="display-borrower-initials">--</span>
                            </div>
                            <div class="min-w-0">
                                <span class="text-[10px] font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wider block">Primary Borrower</span>
                                <h4 class="text-xs sm:text-sm font-bold text-slate-900 dark:text-white truncate" id="display-borrower-name">--</h4>
                            </div>
                        </div>
                        <div class="text-right flex-shrink-0">
                            <span class="text-[10px] font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wider block">Company ID</span>
                            <span id="display-borrower-id" class="text-xs font-mono font-bold text-slate-700 dark:text-slate-300">--</span>
                        </div>
                    </div>

                    <!-- Facility Details row -->
                    <div class="pt-2 border-t border-slate-200/60 dark:border-slate-800/60 flex items-center justify-between text-xs">
                        <span class="text-[11px] text-slate-500 dark:text-slate-400 font-medium">Facility Type:</span>
                        <span id="display-loan-name" class="font-bold text-slate-800 dark:text-slate-200 text-xs">--</span>
                    </div>

                    <!-- Stated Purpose row -->
                    <div class="pt-2 border-t border-slate-200/60 dark:border-slate-800/60 space-y-1">
                        <span class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider block">Borrower Purpose / Note:</span>
                        <p id="display-member-remarks" class="text-xs text-slate-600 dark:text-slate-300 italic pl-2.5 border-l-2 border-emerald-500 bg-white dark:bg-slate-900/60 p-2.5 rounded-r-xl leading-relaxed">
                            --
                        </p>
                    </div>
                </div>

                <!-- Signatory Audit Trail -->
                <div class="space-y-2">
                    <h4 class="text-[11px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider flex items-center gap-1.5">
                        <i class="fa-solid fa-list-check text-blue-500"></i>
                        Signatory Audit Trail
                    </h4>
                    <div class="space-y-2 max-h-[140px] overflow-y-auto pr-1" id="comaker-history-timeline">
                        <!-- Populated via JS -->
                    </div>
                </div>

                <!-- Verification Remarks Input -->
                <div class="space-y-1.5 pt-2 border-t border-slate-100 dark:border-slate-800">
                    <label class="text-xs font-semibold text-slate-700 dark:text-slate-300 flex items-center justify-between">
                        <span>Digital Verification Remarks</span>
                        <span class="text-[10px] text-slate-400 dark:text-slate-500 font-medium">(Required)</span>
                    </label>
                    <textarea name="remarks" id="comaker-remarks" required rows="2" placeholder="State your verification notes (e.g. capacity confirmed, repayment plan reviewed)..." class="w-full px-3.5 py-2.5 text-xs border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 rounded-xl outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500/20 placeholder-slate-400 dark:placeholder-slate-500 transition-all resize-none"></textarea>
                </div>

                <!-- Legal Attestation Checkbox -->
                <label class="flex items-start gap-2.5 p-3 rounded-xl bg-slate-50 dark:bg-slate-950/40 border border-slate-200/60 dark:border-slate-800/60 cursor-pointer select-none group">
                    <input type="checkbox" id="comaker-consent-checkbox" class="mt-0.5 rounded border-slate-300 dark:border-slate-700 text-emerald-600 focus:ring-emerald-500 h-4 w-4 transition-colors flex-shrink-0 cursor-pointer">
                    <span class="text-[11px] text-slate-600 dark:text-slate-400 leading-normal group-hover:text-slate-900 dark:group-hover:text-slate-200 transition-colors">
                        I certify that I have verified the borrower's capacity to pay and voluntarily authorize joint and several liability as co-guarantor under ML Sako bylaws.
                    </span>
                </label>
            </div>

            <!-- FIXED STICKY FOOTER -->
            <div class="p-4 sm:p-5 border-t border-slate-100 dark:border-slate-800 bg-slate-50/80 dark:bg-slate-900/90 backdrop-blur-xs flex items-center justify-between gap-3 flex-shrink-0">
                <button type="button" id="btn-comaker-reject" class="inline-flex items-center justify-center gap-1.5 bg-rose-50 hover:bg-rose-100 dark:bg-rose-950/20 dark:hover:bg-rose-900/30 text-rose-700 dark:text-rose-400 border border-rose-200 dark:border-rose-900/40 font-bold text-xs py-2.5 px-4 rounded-xl transition-all cursor-pointer">
                    <i class="fa-solid fa-ban text-xs"></i>
                    <span>Decline Request</span>
                </button>
                <button type="button" id="btn-comaker-approve" class="inline-flex items-center justify-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs py-2.5 px-5 rounded-xl shadow-md shadow-emerald-600/20 hover:shadow-emerald-600/30 hover:-translate-y-0.5 active:translate-y-0 transition-all cursor-pointer">
                    <i class="fa-solid fa-file-signature text-xs"></i>
                    <span>Authorize &amp; Sign</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL: VIEW SIGNATORIES -->
<div id="modal-signatories" class="fixed inset-0 z-50 hidden overflow-y-auto flex items-center justify-center p-3 sm:p-4">
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-slate-950/40 dark:bg-slate-950/70 backdrop-blur-sm opacity-0 transition-opacity duration-300 pointer-events-none modal-overlay"></div>
    
    <!-- Modal Container -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-2xl w-full max-w-md relative z-10 p-5 sm:p-6 space-y-4 transform scale-95 opacity-0 transition-all duration-300 modal-container">
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
            <div>
                <h3 class="text-base font-bold text-slate-900 dark:text-slate-100">Signatory Audit Log</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Recorded digital endorsements</p>
            </div>
            <button type="button" class="modal-close p-1.5 rounded-lg text-slate-400 hover:text-slate-700 dark:text-slate-500 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                <i class="fa-solid fa-xmark text-base"></i>
            </button>
        </div>

        <!-- Timeline Steps Container -->
        <div class="space-y-3 max-h-[300px] overflow-y-auto pr-1" id="signatories-timeline-container">
            <!-- Dynamically populated via JS -->
        </div>

        <div class="pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-end">
            <button type="button" class="modal-close px-4 py-2 rounded-xl border border-slate-200 dark:border-slate-700 text-xs font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">Close</button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function () {
        
        // --- Segmented Tab Switcher ---
        const tabInbox = document.getElementById("tab-inbox");
        const tabHistory = document.getElementById("tab-history");
        const contentInbox = document.getElementById("content-inbox");
        const contentHistory = document.getElementById("content-history");

        const activeTabClass = "tab-btn active inline-flex items-center gap-2 px-3 sm:px-4 py-2 rounded-xl text-xs font-bold transition-all duration-150 text-emerald-700 dark:text-emerald-400 bg-white dark:bg-slate-800 shadow-xs border border-slate-200/80 dark:border-slate-700";
        const inactiveTabClass = "tab-btn inline-flex items-center gap-2 px-3 sm:px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200 hover:bg-white/60 dark:hover:bg-slate-800/60 transition-all duration-150";

        if (tabInbox && tabHistory) {
            tabInbox.addEventListener("click", () => {
                tabInbox.className = activeTabClass;
                tabHistory.className = inactiveTabClass;
                contentInbox.classList.remove("hidden");
                contentHistory.classList.add("hidden");
            });

            tabHistory.addEventListener("click", () => {
                tabHistory.className = activeTabClass;
                tabInbox.className = inactiveTabClass;
                contentHistory.classList.remove("hidden");
                contentInbox.classList.add("hidden");
            });
        }

        // --- Drawer & Modal Controls ---
        function openDrawer(id) {
            const el = document.getElementById(id);
            if (!el) return;
            el.classList.remove("hidden");
            el.classList.add("flex");
            
            const overlay = el.querySelector(".modal-overlay");
            const container = el.querySelector(".modal-container");
            
            setTimeout(() => {
                if (overlay) {
                    overlay.classList.remove("opacity-0", "pointer-events-none");
                    overlay.classList.add("opacity-100", "pointer-events-auto");
                }
                if (container) {
                    container.classList.remove("scale-95", "opacity-0");
                    container.classList.add("scale-100", "opacity-100");
                }
            }, 30);
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
                container.classList.add("scale-95", "opacity-0");
                container.classList.remove("scale-100", "opacity-100");
            }
            
            setTimeout(() => {
                el.classList.add("hidden");
                el.classList.remove("flex");
            }, 250);
        }

        // Close on overlay or close button clicks
        document.querySelectorAll(".modal-close, .modal-overlay").forEach(btn => {
            btn.addEventListener("click", function () {
                const modal = this.closest('[id^="drawer-"], [id^="modal-"]');
                if (modal) closeDrawer(modal.id);
            });
        });

        // Close on Escape key
        document.addEventListener("keydown", function(e) {
            if (e.key === "Escape") {
                const openModal = document.querySelector('[id^="drawer-"]:not(.hidden), [id^="modal-"]:not(.hidden)');
                if (openModal) closeDrawer(openModal.id);
            }
        });

        // --- Render Timelines Helper ---
        function renderTimeline(timelineArray, targetContainerId) {
            const container = document.getElementById(targetContainerId);
            container.innerHTML = "";

            if (timelineArray.length === 0) {
                container.innerHTML = '<p class="text-center py-3 text-slate-500 dark:text-slate-400 italic text-xs bg-slate-50 dark:bg-slate-950/40 border border-slate-200/60 dark:border-slate-800/60 rounded-xl">This request has no prior validations recorded.</p>';
            } else {
                timelineArray.forEach((step, idx) => {
                    const card = document.createElement("div");
                    card.className = "flex gap-2.5 relative";
                    
                    const isApproved = step.decision === 'approved';
                    const circleBadge = isApproved 
                        ? 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 border border-emerald-200/60 dark:border-emerald-800/40' 
                        : 'bg-rose-50 dark:bg-rose-950/40 text-rose-600 dark:text-rose-400 border border-rose-200/60 dark:border-rose-800/40';

                    const statusPill = isApproved
                        ? 'bg-emerald-100 dark:bg-emerald-950 text-emerald-800 dark:text-emerald-300'
                        : 'bg-rose-100 dark:bg-rose-950 text-rose-800 dark:text-rose-300';

                    card.innerHTML = `
                        <div class="w-6 h-6 rounded-full ${circleBadge} flex items-center justify-center flex-shrink-0 text-[10px] font-bold mt-1">
                            ${idx + 1}
                        </div>
                        <div class="flex-1 bg-slate-50 dark:bg-slate-950/40 border border-slate-200/60 dark:border-slate-800/60 p-2.5 rounded-xl text-xs leading-normal">
                            <div class="flex justify-between items-center gap-2">
                                <span class="font-bold text-slate-900 dark:text-white text-xs">${step.stage}</span>
                                <span class="text-[9px] font-bold uppercase px-2 py-0.2 rounded-full ${statusPill}">${step.decision}</span>
                            </div>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">By: <strong class="text-slate-700 dark:text-slate-300">${step.actor}</strong></p>
                            ${step.remarks ? `<p class="text-slate-600 dark:text-slate-300 italic mt-1.5 p-2 bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800/60 rounded-lg text-[11px] leading-relaxed">"${step.remarks}"</p>` : ''}
                            <p class="text-[9px] text-slate-400 font-mono mt-1 text-right">${step.date}</p>
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
                const requestedAmount = parseFloat(loan.requested_amount);
                
                // Set loan details & reference
                const appRefEl = document.getElementById("display-app-ref");
                if (appRefEl) {
                    appRefEl.textContent = "LN-" + String(loan.id).padStart(5, '0');
                }
                document.getElementById("display-loan-name").textContent = `${loan.loan_type.replace('_', ' ').toUpperCase()} (${category.toUpperCase()})`;
                document.getElementById("display-loan-amount").textContent = "₱" + requestedAmount.toLocaleString('en-US', {minimumFractionDigits: 2});
                document.getElementById("display-loan-term").textContent = (loan.form_data.term_months || 'N/A') + " Months";
                document.getElementById("display-member-remarks").textContent = loan.form_data.member_remarks ? `"${loan.form_data.member_remarks}"` : "None specified.";

                // Render history step list
                renderTimeline(history, "comaker-history-timeline");

                // Set up decision buttons & actions
                const formDecision = document.getElementById("form-comaker-decision");
                const txtRemarks = document.getElementById("comaker-remarks");
                const chkConsent = document.getElementById("comaker-consent-checkbox");
                const btnApprove = document.getElementById("btn-comaker-approve");
                const btnReject = document.getElementById("btn-comaker-reject");

                // Reset decision inputs & loading states
                txtRemarks.value = "";
                txtRemarks.readOnly = false;
                if (chkConsent) {
                    chkConsent.checked = false;
                    chkConsent.disabled = false;
                }

                btnApprove.disabled = false;
                btnReject.disabled = false;
                btnApprove.classList.remove("opacity-80", "opacity-40", "cursor-wait", "pointer-events-none");
                btnReject.classList.remove("opacity-80", "opacity-40", "cursor-wait", "pointer-events-none");
                btnApprove.innerHTML = '<i class="fa-solid fa-file-signature text-xs"></i> <span>Authorize &amp; Sign</span>';
                btnReject.innerHTML = '<i class="fa-solid fa-ban text-xs"></i> <span>Decline Request</span>';

                btnApprove.onclick = function (e) {
                    e.preventDefault();
                    if (!txtRemarks.value.trim()) {
                        const alertInstance = window.MLSAKOAlert || Swal;
                        if (alertInstance) {
                            alertInstance.fire({
                                icon: 'warning',
                                title: 'Remarks Required',
                                text: "Please enter digital verification remarks before endorsing.",
                                confirmButtonText: 'Understood'
                            });
                        } else {
                            alert("Please enter digital verification remarks before endorsing.");
                        }
                        return;
                    }
                    if (chkConsent && !chkConsent.checked) {
                        const alertInstance = window.MLSAKOAlert || Swal;
                        if (alertInstance) {
                            alertInstance.fire({
                                icon: 'warning',
                                title: 'Legal Attestation Required',
                                text: "Please acknowledge the co-guarantor joint liability certification before authorizing.",
                                confirmButtonText: 'Understood'
                            });
                        } else {
                            alert("Please acknowledge the co-guarantor joint liability certification before authorizing.");
                        }
                        return;
                    }

                    const alertInstance = window.MLSAKOAlert || Swal;
                    if (alertInstance) {
                        alertInstance.fire({
                            icon: 'question',
                            title: 'Authorize Co-Maker Guarantee',
                            html: `
                                <div class="space-y-4 text-center">
                                    <div class="space-y-1">
                                        <p class="text-xs font-semibold text-slate-600 dark:text-slate-400">
                                            You are endorsing <strong class="text-slate-900 dark:text-white font-bold">${borrowerName}</strong> for <strong class="text-emerald-600 dark:text-emerald-400 font-mono font-bold">${document.getElementById("display-loan-amount").textContent}</strong>.
                                        </p>
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 dark:bg-amber-950/50 text-amber-700 dark:text-amber-400 border border-amber-200/60 dark:border-amber-800/40">
                                            Joint &amp; Several Solidary Obligation
                                        </span>
                                    </div>
                                    <div class="space-y-1.5 pt-2 border-t border-slate-100 dark:border-slate-800">
                                        <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">
                                            Enter 6-Digit Security PIN to Authorize
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
                            confirmButtonText: 'Authorize & Sign',
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
                                document.getElementById('comaker-pin-input').value = result.value;

                                // Activate loading state
                                btnApprove.disabled = true;
                                btnReject.disabled = true;
                                txtRemarks.readOnly = true;
                                if (chkConsent) chkConsent.disabled = true;

                                btnApprove.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin text-xs"></i> <span>Authorizing...</span>';
                                btnApprove.classList.add("opacity-80", "cursor-wait");
                                btnReject.classList.add("opacity-40", "pointer-events-none");

                                formDecision.action = `/loans/${loan.id}/approve`;
                                formDecision.submit();
                            }
                        });
                    } else {
                        const pinPrompt = prompt('Enter your 6-digit Security PIN to authorize:');
                        if (pinPrompt && pinPrompt.length === 6 && !isNaN(pinPrompt)) {
                            document.getElementById('comaker-pin-input').value = pinPrompt;
                            btnApprove.disabled = true;
                            btnReject.disabled = true;
                            txtRemarks.readOnly = true;
                            if (chkConsent) chkConsent.disabled = true;

                            btnApprove.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin text-xs"></i> <span>Authorizing...</span>';
                            btnApprove.classList.add("opacity-80", "cursor-wait");
                            btnReject.classList.add("opacity-40", "pointer-events-none");

                            formDecision.action = `/loans/${loan.id}/approve`;
                            formDecision.submit();
                        }
                    }
                };

                btnReject.onclick = function (e) {
                    e.preventDefault();
                    if (!txtRemarks.value.trim()) {
                        const alertInstance = window.MLSAKOAlert || Swal;
                        if (alertInstance) {
                            alertInstance.fire({
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

                    const alertInstance = window.MLSAKOAlert || Swal;
                    if (alertInstance) {
                        alertInstance.fire({
                            icon: 'warning',
                            title: 'Decline Endorsement?',
                            text: "Are you sure you want to decline to co-sign this loan application? The primary borrower will be notified.",
                            showCancelButton: true,
                            confirmButtonText: 'Yes, Decline',
                            cancelButtonText: 'Cancel',
                            iconColor: '#f43f5e'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                btnApprove.disabled = true;
                                btnReject.disabled = true;
                                txtRemarks.readOnly = true;
                                if (chkConsent) chkConsent.disabled = true;

                                btnReject.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin text-xs"></i> <span>Declining...</span>';
                                btnReject.classList.add("opacity-80", "cursor-wait");
                                btnApprove.classList.add("opacity-40", "pointer-events-none");

                                formDecision.action = `/loans/${loan.id}/reject`;
                                formDecision.submit();
                            }
                        });
                    } else {
                        if (confirm("Are you sure you want to decline to co-sign this loan application?")) {
                            btnApprove.disabled = true;
                            btnReject.disabled = true;
                            txtRemarks.readOnly = true;
                            if (chkConsent) chkConsent.disabled = true;

                            btnReject.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin text-xs"></i> <span>Declining...</span>';
                            btnReject.classList.add("opacity-80", "cursor-wait");
                            btnApprove.classList.add("opacity-40", "pointer-events-none");

                            formDecision.action = `/loans/${loan.id}/reject`;
                            formDecision.submit();
                        }
                    }
                };

                openDrawer("drawer-comaker-evaluate");
            });
        });

    });
</script>
@endpush
