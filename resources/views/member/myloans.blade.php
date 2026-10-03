@inject('workflowService', 'App\Services\LoanWorkflowService')
@extends('layouts.user')

@section('title', 'My Loans - ML Sako')

@section('navbar_title', 'My Loans')
@section('navbar_subtitle', 'Track your active loan applications, amortization schedules, and payment histories.')

@push('styles')
<style>
    .btn-preview-pdf, .btn-view-ledger, .btn-replace-comaker {
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
                $approvedCount = $applications->where('status', 'approved')->count();
                $pendingCount = $applications->where('status', 'pending')->count();
            @endphp
            @if($approvedCount > 0)
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 text-xs font-semibold border border-emerald-200/60 dark:border-emerald-800/40">
                    <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse"></span>
                    Active Loans: {{ $approvedCount }} Released
                </span>
            @endif
            @if($pendingCount > 0)
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-400 text-xs font-semibold border border-amber-200/60 dark:border-amber-800/40">
                    <span class="w-1.5 h-1.5 bg-amber-500 rounded-full animate-pulse"></span>
                    {{ $pendingCount }} In Review
                </span>
            @endif
            @if($approvedCount === 0 && $pendingCount === 0)
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 text-xs font-semibold border border-slate-200/80 dark:border-slate-700">
                    No Active Loans
                </span>
            @endif
            <span class="text-xs text-slate-500 dark:text-slate-400 font-medium bg-slate-100 dark:bg-slate-800/80 px-2.5 py-1 rounded-lg border border-slate-200/80 dark:border-slate-700">
                ID: <strong class="text-slate-700 dark:text-slate-200">{{ Auth::user()->company_id ?: 'N/A' }}</strong>
            </span>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('member.forms') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-1.5 px-3.5 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold shadow-xs shadow-emerald-600/10 transition-all duration-150">
                <i class="fa-solid fa-plus text-xs"></i>
                <span>Apply for a Loan</span>
            </a>
        </div>
    </div>

    <!-- Flash Alert Feedbacks -->
    @if(session('success'))
        <div class="p-3.5 bg-emerald-50 dark:bg-emerald-950/20 border-l-4 border-emerald-500 rounded-r-xl text-emerald-800 dark:text-emerald-300 text-xs font-medium flex items-center justify-between shadow-xs">
            <span class="flex items-center gap-2">
                <i class="fa-solid fa-circle-check text-emerald-600 dark:text-emerald-400 text-sm flex-shrink-0"></i>
                {{ session('success') }}
            </span>
        </div>
    @endif

    @if(session('error'))
        <div class="p-3.5 bg-rose-50 dark:bg-rose-950/20 border-l-4 border-rose-500 rounded-r-xl text-rose-800 dark:text-rose-300 text-xs font-medium flex items-center justify-between shadow-xs">
            <span class="flex items-center gap-2">
                <i class="fa-solid fa-triangle-exclamation text-rose-600 dark:text-rose-400 text-sm flex-shrink-0"></i>
                {{ session('error') }}
            </span>
        </div>
    @endif

    @if($errors->any())
        <div class="p-3.5 bg-rose-50 dark:bg-rose-950/20 border-l-4 border-rose-500 rounded-r-xl text-rose-800 dark:text-rose-300 text-xs font-medium space-y-1 shadow-xs">
            <p class="font-bold text-xs">Please review the following:</p>
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- KPI Summary Micro-Grid (Visible when applications exist) -->
    @if($applications->isNotEmpty())
        @php
            $releasedTotal = $applications->where('status', 'approved')->sum('requested_amount');
            $pendingTotal = $applications->where('status', 'pending')->sum('requested_amount');
            $actionRequiredCount = $applications->filter(function($app) {
                $activeComakers = $app->form_data['comakers'] ?? [];
                $hasRejectedComaker = $app->current_stage === 'comakers' && $app->status === 'pending' && $app->comakers()->where('status', 'rejected')->whereIn('user_id', $activeComakers)->exists();
                return $app->status === 'returned' || $hasRejectedComaker;
            })->count();
        @endphp
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4">
            <!-- Metric 1: Released Principal -->
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 p-4 shadow-xs">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Released Principal</span>
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                </div>
                <p class="text-xl sm:text-2xl font-extrabold text-slate-900 dark:text-white font-mono mt-1">
                    ₱{{ number_format($releasedTotal, 2) }}
                </p>
                <span class="text-[10px] text-slate-500 dark:text-slate-400 block mt-0.5">{{ $approvedCount }} Approved Facility{{ $approvedCount === 1 ? '' : 's' }}</span>
            </div>

            <!-- Metric 2: Applications In Review -->
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 p-4 shadow-xs">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wider">In Verification</span>
                    <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                </div>
                <p class="text-xl sm:text-2xl font-extrabold text-slate-900 dark:text-white font-mono mt-1">
                    ₱{{ number_format($pendingTotal, 2) }}
                </p>
                <span class="text-[10px] text-slate-500 dark:text-slate-400 block mt-0.5">{{ $pendingCount }} Pending Approval{{ $pendingCount === 1 ? '' : 's' }}</span>
            </div>

            <!-- Metric 3: Action Needed -->
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 p-4 shadow-xs">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Action Items</span>
                    <span class="w-2 h-2 rounded-full {{ $actionRequiredCount > 0 ? 'bg-rose-500 animate-pulse' : 'bg-slate-300 dark:bg-slate-600' }}"></span>
                </div>
                <p class="text-xl sm:text-2xl font-extrabold font-mono mt-1 {{ $actionRequiredCount > 0 ? 'text-amber-600 dark:text-amber-400' : 'text-slate-900 dark:text-white' }}">
                    {{ $actionRequiredCount }}
                </p>
                <span class="text-[10px] {{ $actionRequiredCount > 0 ? 'text-amber-600 dark:text-amber-400 font-semibold' : 'text-slate-500 dark:text-slate-400' }} block mt-0.5">
                    {{ $actionRequiredCount > 0 ? 'Requires your attention' : 'All workflows on track' }}
                </span>
            </div>
        </div>
    @endif

    <!-- My Loan Applications Container -->
    @if($applications->isNotEmpty())
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 p-5 sm:p-6 shadow-xs space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 pb-1 border-b border-slate-100 dark:border-slate-700/60">
                <div>
                    <h3 class="text-sm sm:text-base font-bold text-slate-900 dark:text-white">Loan Applications Queue</h3>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400">Real-time status tracking, verification stages, and audit history.</p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 text-[10px] font-bold">
                        {{ $applications->count() }} Application{{ $applications->count() === 1 ? '' : 's' }}
                    </span>
                </div>
            </div>
            
            <!-- Desktop Table (Visible on lg+ screens) -->
            <div class="hidden lg:block overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="text-slate-500 dark:text-slate-400 bg-slate-50/80 dark:bg-slate-900/40 border-b border-slate-200/70 dark:border-slate-700 uppercase tracking-wider text-[10px] font-bold">
                            <th class="py-2.5 px-3.5 rounded-l-lg">Facility &amp; Type</th>
                            <th class="py-2.5 px-3.5">Requested</th>
                            <th class="py-2.5 px-3.5">Current Stage &amp; Status</th>
                            <th class="py-2.5 px-3.5">Documents</th>
                            <th class="py-2.5 px-3.5">Submitted</th>
                            <th class="py-2.5 px-3.5 text-right rounded-r-lg">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700/60 text-slate-700 dark:text-slate-300 font-medium">
                        @foreach($applications as $app)
                            @php
                                $activeComakers = $app->form_data['comakers'] ?? [];
                                $rejectedComakers = $app->comakers()
                                    ->where('status', 'rejected')
                                    ->whereIn('user_id', $activeComakers)
                                    ->with('user')
                                    ->get();
                                $hasRejectedComaker = $app->current_stage === 'comakers' && $app->status === 'pending' && $rejectedComakers->isNotEmpty();
                            @endphp
                            <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-900/30 transition-colors">
                                <!-- Facility & Type -->
                                <td class="py-3 px-3.5">
                                    <span class="font-bold text-slate-900 dark:text-white block text-xs">
                                        {{ config("loans.{$app->loan_category}.{$app->loan_type}.name", ucwords(str_replace('_', ' ', $app->loan_type))) }}
                                    </span>
                                    <span class="text-[10px] text-slate-400 dark:text-slate-500 uppercase tracking-wider font-semibold">
                                        {{ $app->loan_category }} Loan • LN-{{ str_pad($app->id, 5, '0', STR_PAD_LEFT) }}
                                    </span>
                                </td>

                                <!-- Requested Amount -->
                                <td class="py-3 px-3.5 font-mono font-bold text-slate-900 dark:text-white text-xs">
                                    ₱{{ number_format($app->requested_amount, 2) }}
                                </td>

                                <!-- Current Stage & Status -->
                                <td class="py-3 px-3.5">
                                    @if($app->status === 'approved')
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 text-[11px] font-semibold border border-emerald-200/60 dark:border-emerald-800/40">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                            Released / Completed
                                        </span>
                                    @elseif($app->status === 'returned')
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-400 text-[11px] font-semibold border border-amber-200/60 dark:border-amber-800/40">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                            Returned for Revision
                                        </span>
                                    @elseif($app->status === 'rejected')
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-400 text-[11px] font-semibold border border-rose-200/60 dark:border-rose-800/40">
                                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                            Rejected
                                        </span>
                                    @elseif($app->status === 'cancelled')
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 text-[11px] font-semibold border border-slate-200 dark:border-slate-700">
                                            Cancelled
                                        </span>
                                    @else
                                        @if($hasRejectedComaker)
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-400 text-[11px] font-semibold border border-amber-200/60 dark:border-amber-800/40 animate-pulse">
                                                <i class="fa-solid fa-triangle-exclamation text-[10px]"></i>
                                                Co-maker Declined
                                            </span>
                                        @elseif($app->current_stage === 'sako_staff')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-sky-50 dark:bg-sky-950/40 text-sky-700 dark:text-sky-400 border border-sky-200/60 dark:border-sky-800/40 text-[11px] font-semibold">
                                                <span class="w-1.5 h-1.5 rounded-full bg-sky-500 animate-pulse"></span>
                                                Sako Staff Review
                                            </span>
                                        @elseif($app->current_stage === 'hrmd_staff')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-400 border border-rose-200/60 dark:border-rose-800/40 text-[11px] font-semibold">
                                                <span class="w-1.5 h-1.5 rounded-full bg-rose-500 animate-pulse"></span>
                                                HRMD Verification
                                            </span>
                                        @elseif($app->current_stage === 'credit_committee')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-indigo-50 dark:bg-indigo-950/40 text-indigo-700 dark:text-indigo-400 border border-indigo-200/60 dark:border-indigo-800/40 text-[11px] font-semibold">
                                                <span class="w-1.5 h-1.5 rounded-full bg-indigo-500 animate-pulse"></span>
                                                Credit Comm Review
                                            </span>
                                        @elseif($app->current_stage === 'accounting')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-400 border border-amber-200/60 dark:border-amber-800/40 text-[11px] font-semibold">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                                Accounting Computations
                                            </span>
                                        @elseif($app->current_stage === 'releasing_officer')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-teal-50 dark:bg-teal-950/40 text-teal-700 dark:text-teal-400 border border-teal-200/60 dark:border-teal-800/40 text-[11px] font-semibold">
                                                <span class="w-1.5 h-1.5 rounded-full bg-teal-500 animate-pulse"></span>
                                                Awaiting Disbursement
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-slate-700/60 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-600 text-[11px] font-semibold">
                                                {{ ucwords(str_replace('_', ' ', $app->current_stage)) }}
                                            </span>
                                        @endif
                                    @endif
                                </td>

                                <!-- Documents -->
                                <td class="py-3 px-3.5">
                                    <div class="flex flex-wrap items-center gap-1.5 max-w-[200px]">
                                        @if($app->documents->isNotEmpty())
                                            @foreach($app->documents->take(2) as $doc)
                                                <button type="button" class="btn-preview-pdf inline-flex items-center gap-1 px-2 py-0.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700/60 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 rounded text-[10px] font-semibold transition-all truncate max-w-[90px] cursor-pointer"
                                                    data-url="{{ $doc->file_url }}"
                                                    data-name="{{ $doc->original_name }}"
                                                    data-size="{{ $doc->formatted_file_size }}"
                                                    title="{{ $doc->original_name }}">
                                                    <i class="fa-solid fa-file-pdf text-rose-500 text-[9px]"></i>
                                                    <span class="truncate">{{ $doc->original_name }}</span>
                                                </button>
                                            @endforeach
                                            @if($app->documents->count() > 2)
                                                <span class="text-[10px] text-slate-400 font-semibold">+{{ $app->documents->count() - 2 }}</span>
                                            @endif
                                        @endif

                                        @if($app->ledger_path)
                                            <button type="button" class="btn-preview-pdf inline-flex items-center gap-1 px-2 py-0.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-400 rounded text-[10px] font-bold border border-emerald-200/50 dark:border-emerald-800/40 cursor-pointer"
                                                data-url="{{ $app->ledger_url }}"
                                                data-name="Loan_Ledger_LN-{{ str_pad($app->id, 5, '0', STR_PAD_LEFT) }}.pdf"
                                                data-size="PDF"
                                                title="View Ledger">
                                                <i class="fa-solid fa-book text-[9px]"></i>
                                                <span>Ledger</span>
                                            </button>
                                        @endif

                                        @if($app->schedule_path)
                                            <button type="button" class="btn-preview-pdf inline-flex items-center gap-1 px-2 py-0.5 bg-blue-50 hover:bg-blue-100 text-blue-700 dark:bg-blue-950/40 dark:text-blue-400 rounded text-[10px] font-bold border border-blue-200/50 dark:border-blue-800/40 cursor-pointer"
                                                data-url="{{ $app->schedule_url }}"
                                                data-name="Amortization_Schedule_LN-{{ str_pad($app->id, 5, '0', STR_PAD_LEFT) }}.pdf"
                                                data-size="PDF"
                                                title="View Schedule">
                                                <i class="fa-solid fa-calendar-days text-[9px]"></i>
                                                <span>Schedule</span>
                                            </button>
                                        @endif

                                        @if($app->documents->isEmpty() && !$app->ledger_path && !$app->schedule_path)
                                            <span class="text-[10px] text-slate-400 italic">—</span>
                                        @endif
                                    </div>
                                </td>

                                <!-- Submitted Date -->
                                <td class="py-3 px-3.5 text-slate-500 dark:text-slate-400 text-xs">
                                    {{ $app->created_at->format('M d, Y') }}
                                </td>

                                <!-- Actions -->
                                <td class="py-3 px-3.5 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <button type="button" class="btn-view-ledger inline-flex items-center gap-1 text-xs font-semibold text-emerald-600 dark:text-emerald-400 hover:text-emerald-700 dark:hover:text-emerald-300 transition-colors cursor-pointer"
                                            data-ledger="{{ json_encode($workflowService->getWorkflowDetails($app)) }}"
                                            data-activities="{{ json_encode($app->activities) }}"
                                            data-documents="{{ json_encode($app->documents->map(fn($d) => ['name' => $d->original_name, 'size' => $d->formatted_file_size, 'url' => $d->file_url])) }}"
                                            data-ledger-url="{{ $app->ledger_url }}"
                                            data-schedule-url="{{ $app->schedule_url }}"
                                            data-app-id="{{ $app->id }}">
                                            <span>Timeline</span>
                                            <i class="fa-solid fa-arrow-right text-[10px]"></i>
                                        </button>

                                        @if($app->status === 'returned')
                                            <a href="{{ route('member.forms') }}?resubmit_id={{ $app->id }}" class="px-2.5 py-1 text-[10px] font-bold bg-amber-500 hover:bg-amber-600 text-white rounded-md shadow-xs transition-all flex items-center gap-1 cursor-pointer" title="Modify and resubmit this loan application">
                                                <i class="fa-solid fa-rotate-left"></i>
                                                <span>Revise</span>
                                            </a>
                                        @endif

                                        @if($hasRejectedComaker)
                                            @foreach($rejectedComakers as $rc)
                                                <button type="button" class="btn-replace-comaker px-2 py-1 text-[10px] font-bold bg-amber-500 hover:bg-amber-600 text-white rounded-md shadow-xs transition-all flex items-center gap-1 cursor-pointer"
                                                    data-app-id="{{ $app->id }}"
                                                    data-old-id="{{ $rc->user_id }}"
                                                    data-old-name="{{ $rc->user->name }}"
                                                    data-active-comakers="{{ json_encode($activeComakers) }}"
                                                    title="Replace declined co-maker {{ $rc->user->name }}">
                                                    <i class="fa-solid fa-user-pen"></i>
                                                    <span>Replace ({{ Str::limit($rc->user->name, 10) }})</span>
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

            <!-- Mobile Applications Card Stack (Visible on <lg screens) -->
            <div class="block lg:hidden space-y-3">
                @foreach($applications as $app)
                    @php
                        $activeComakers = $app->form_data['comakers'] ?? [];
                        $rejectedComakers = $app->comakers()
                            ->where('status', 'rejected')
                            ->whereIn('user_id', $activeComakers)
                            ->with('user')
                            ->get();
                        $hasRejectedComaker = $app->current_stage === 'comakers' && $app->status === 'pending' && $rejectedComakers->isNotEmpty();
                    @endphp
                    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 p-4 space-y-3 shadow-xs">
                        
                        <!-- Header Row: Loan Name + Status -->
                        <div class="flex items-start justify-between gap-2.5">
                            <div class="min-w-0 flex-1">
                                <span class="font-bold text-slate-900 dark:text-white text-xs sm:text-sm block leading-snug truncate">
                                    {{ config("loans.{$app->loan_category}.{$app->loan_type}.name", ucwords(str_replace('_', ' ', $app->loan_type))) }}
                                </span>
                                <span class="text-[10px] text-slate-400 dark:text-slate-500 uppercase tracking-wider font-semibold block mt-0.5">
                                    {{ $app->loan_category }} Loan • LN-{{ str_pad($app->id, 5, '0', STR_PAD_LEFT) }}
                                </span>
                            </div>
                            
                            <!-- Unified Status Badge -->
                            <div class="flex-shrink-0">
                                @if($app->status === 'approved')
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 text-[10px] font-bold border border-emerald-200/60 dark:border-emerald-800/40">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                        Released
                                    </span>
                                @elseif($app->status === 'returned')
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-400 text-[10px] font-bold border border-amber-200/60 dark:border-amber-800/40">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                        Returned
                                    </span>
                                @elseif($app->status === 'rejected')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-400 text-[10px] font-bold border border-rose-200/60 dark:border-rose-800/40">
                                        Rejected
                                    </span>
                                @elseif($app->status === 'cancelled')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-700 text-slate-500 dark:text-slate-400 text-[10px] font-bold">
                                        Cancelled
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-sky-50 dark:bg-sky-950/40 text-sky-700 dark:text-sky-400 text-[10px] font-bold border border-sky-200/60 dark:border-sky-800/40 animate-pulse">
                                        <span class="w-1.5 h-1.5 rounded-full bg-sky-500"></span>
                                        Pending
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Amount & Date Inset Box -->
                        <div class="grid grid-cols-2 gap-2 p-2.5 rounded-xl bg-slate-50 dark:bg-slate-900/50 border border-slate-200/60 dark:border-slate-700/60">
                            <div>
                                <span class="text-[9px] font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wider block">Requested</span>
                                <span class="text-sm font-extrabold text-slate-900 dark:text-white font-mono mt-0.5 block">
                                    ₱{{ number_format($app->requested_amount, 2) }}
                                </span>
                            </div>
                            <div class="border-l border-slate-200 dark:border-slate-700/80 pl-3">
                                <span class="text-[9px] font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wider block">Submitted</span>
                                <span class="text-xs font-semibold text-slate-700 dark:text-slate-300 mt-0.5 block">
                                    {{ $app->created_at->format('M d, Y') }}
                                </span>
                            </div>
                        </div>

                        <!-- Active Workflow Stage Pill (Mobile) -->
                        <div class="flex items-center justify-between text-xs gap-2">
                            <span class="text-[10px] font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Stage:</span>
                            <div class="text-right">
                                @if($hasRejectedComaker)
                                    <span class="text-[10px] font-bold text-amber-600 dark:text-amber-400 flex items-center gap-1">
                                        <i class="fa-solid fa-triangle-exclamation"></i>
                                        Co-maker Declined
                                    </span>
                                @elseif($app->status === 'approved')
                                    <span class="text-[10px] font-bold text-emerald-600 dark:text-emerald-400">Funds Disbursed</span>
                                @elseif($app->status === 'returned')
                                    <span class="text-[10px] font-bold text-amber-600 dark:text-amber-400">Needs Modification</span>
                                @else
                                    <span class="text-[10px] font-bold text-slate-700 dark:text-slate-300">
                                        {{ ucwords(str_replace('_', ' ', $app->current_stage)) }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Attached Files Quick Chips (if available) -->
                        @if($app->documents->isNotEmpty() || $app->ledger_path || $app->schedule_path)
                            <div class="flex flex-wrap gap-1.5 pt-1 border-t border-slate-100 dark:border-slate-700/60">
                                @foreach($app->documents->take(3) as $doc)
                                    <button type="button" class="btn-preview-pdf inline-flex items-center gap-1 px-2 py-0.5 bg-slate-100 dark:bg-slate-700/60 text-slate-700 dark:text-slate-300 rounded-md text-[10px] font-semibold truncate max-w-[140px] cursor-pointer"
                                        data-url="{{ $doc->file_url }}"
                                        data-name="{{ $doc->original_name }}"
                                        data-size="{{ $doc->formatted_file_size }}">
                                        <i class="fa-solid fa-file-pdf text-rose-500 text-[9px]"></i>
                                        <span class="truncate">{{ $doc->original_name }}</span>
                                    </button>
                                @endforeach
                                @if($app->documents->count() > 3)
                                    <span class="inline-flex items-center px-1.5 py-0.5 text-[9px] font-semibold text-slate-400">
                                        +{{ $app->documents->count() - 3 }} more
                                    </span>
                                @endif
                                @if($app->ledger_path)
                                    <button type="button" class="btn-preview-pdf inline-flex items-center gap-1 px-2 py-0.5 bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 rounded-md text-[10px] font-bold border border-emerald-200/50 dark:border-emerald-800/40 cursor-pointer"
                                        data-url="{{ $app->ledger_url }}"
                                        data-name="Loan_Ledger_LN-{{ str_pad($app->id, 5, '0', STR_PAD_LEFT) }}.pdf"
                                        data-size="PDF">
                                        <i class="fa-solid fa-book text-[9px]"></i>
                                        <span>Ledger</span>
                                    </button>
                                @endif
                                @if($app->schedule_path)
                                    <button type="button" class="btn-preview-pdf inline-flex items-center gap-1 px-2 py-0.5 bg-blue-50 dark:bg-blue-950/40 text-blue-700 dark:text-blue-400 rounded-md text-[10px] font-bold border border-blue-200/50 dark:border-blue-800/40 cursor-pointer"
                                        data-url="{{ $app->schedule_url }}"
                                        data-name="Amortization_Schedule_LN-{{ str_pad($app->id, 5, '0', STR_PAD_LEFT) }}.pdf"
                                        data-size="PDF">
                                        <i class="fa-solid fa-calendar-days text-[9px]"></i>
                                        <span>Schedule</span>
                                    </button>
                                @endif
                            </div>
                        @endif

                        <!-- Action Buttons Row (Mobile) -->
                        <div class="pt-2 border-t border-slate-100 dark:border-slate-700/60 flex items-center justify-between gap-2">
                            <button type="button" class="btn-view-ledger flex-1 inline-flex items-center justify-center gap-1.5 py-2 px-3 rounded-xl bg-slate-100 dark:bg-slate-700/70 hover:bg-slate-200/80 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 text-xs font-semibold transition-all cursor-pointer"
                                data-ledger="{{ json_encode($workflowService->getWorkflowDetails($app)) }}"
                                data-activities="{{ json_encode($app->activities) }}"
                                data-documents="{{ json_encode($app->documents->map(fn($d) => ['name' => $d->original_name, 'size' => $d->formatted_file_size, 'url' => $d->file_url])) }}"
                                data-ledger-url="{{ $app->ledger_url }}"
                                data-schedule-url="{{ $app->schedule_url }}"
                                data-app-id="{{ $app->id }}">
                                <span>View Timeline</span>
                                <i class="fa-solid fa-arrow-right text-[10px]"></i>
                            </button>

                            @if($app->status === 'returned')
                                <a href="{{ route('member.forms') }}?resubmit_id={{ $app->id }}" class="inline-flex items-center gap-1 py-2 px-3 rounded-xl bg-amber-500 hover:bg-amber-600 text-white text-xs font-semibold shadow-xs transition-all cursor-pointer">
                                    <i class="fa-solid fa-rotate-left text-[10px]"></i>
                                    <span>Revise</span>
                                </a>
                            @endif

                            @if($hasRejectedComaker)
                                @foreach($rejectedComakers as $rc)
                                    <button type="button" class="btn-replace-comaker inline-flex items-center gap-1 py-2 px-3 rounded-xl bg-amber-500 hover:bg-amber-600 text-white text-xs font-semibold shadow-xs transition-all cursor-pointer"
                                        data-app-id="{{ $app->id }}"
                                        data-old-id="{{ $rc->user_id }}"
                                        data-old-name="{{ $rc->user->name }}"
                                        data-active-comakers="{{ json_encode($activeComakers) }}">
                                        <i class="fa-solid fa-user-pen text-[10px]"></i>
                                        <span>Replace</span>
                                    </button>
                                @endforeach
                            @endif
                        </div>

                    </div>
                @endforeach
            </div>
        </div>
    @else
        <!-- Empty State Container -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 p-8 sm:p-12 text-center shadow-xs space-y-4">
            <div class="w-14 h-14 mx-auto rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 border border-emerald-200/60 dark:border-emerald-800/40 flex items-center justify-center text-xl">
                <i class="fa-solid fa-file-signature"></i>
            </div>
            <div class="space-y-1">
                <h3 class="text-base font-bold text-slate-900 dark:text-white">No Loan Applications Found</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 max-w-sm mx-auto leading-relaxed">
                    You haven't submitted any cooperative loan requests yet. Explore flexible salary, emergency, or appliance loan facilities.
                </p>
            </div>
            <div class="pt-2">
                <a href="{{ route('member.forms') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold shadow-xs shadow-emerald-600/10 transition-all">
                    <i class="fa-solid fa-plus text-xs"></i>
                    <span>Apply for a Loan</span>
                </a>
            </div>
        </div>
    @endif

</div>

<!-- MODAL: VIEW APPROVAL TIMELINE -->
<div id="modal-ledger" class="fixed inset-0 z-50 hidden overflow-y-auto flex items-center justify-center p-3 sm:p-4">
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-slate-950/40 dark:bg-slate-950/70 backdrop-blur-sm opacity-0 transition-opacity duration-300 pointer-events-none modal-overlay"></div>
    
    <!-- Modal Container -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-2xl w-full max-w-4xl relative z-10 p-5 sm:p-6 lg:p-7 space-y-5 transform scale-95 opacity-0 transition-all duration-300 modal-container max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
            <div>
                <h3 class="text-base sm:text-lg font-bold text-slate-900 dark:text-slate-100">Loan Approval Timeline &amp; History</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Live workflow progression, activity audit trail, and compliance files.</p>
            </div>
            <button type="button" class="modal-close p-1.5 rounded-lg text-slate-400 hover:text-slate-700 dark:text-slate-500 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                <i class="fa-solid fa-xmark text-base"></i>
            </button>
        </div>

        <div class="space-y-5">
            <!-- PART 1: Workflow Steps -->
            <div class="space-y-2.5">
                <h4 class="text-[11px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider flex items-center gap-1.5">
                    <i class="fa-solid fa-list-check text-emerald-500"></i>
                    Workflow Stages
                </h4>
                <div class="relative min-h-[120px] flex items-center justify-center bg-slate-50/60 dark:bg-slate-950/40 border border-slate-200/60 dark:border-slate-800/60 rounded-xl p-3 sm:p-4">
                    <!-- Background connecting line (visible on large screens) -->
                    <div class="hidden sm:block absolute top-[1.25rem] left-[4.5rem] right-[4.5rem] h-0.5 bg-slate-200 dark:bg-slate-800 z-0"></div>
                    
                    <!-- Vertical connecting line (visible on mobile only) -->
                    <div class="block sm:hidden absolute top-6 bottom-6 left-[1.75rem] w-0.5 bg-slate-200 dark:bg-slate-800 z-0"></div>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-6 gap-3 sm:gap-2 w-full relative z-10" id="ledger-timeline-container">
                        <!-- Dynamically populated via JS -->
                    </div>
                </div>
            </div>

            <!-- PART 2: Activity Log Timeline -->
            <div class="space-y-2.5">
                <h4 class="text-[11px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider flex items-center gap-1.5">
                    <i class="fa-solid fa-clock-rotate-left text-blue-500"></i>
                    Activity Audit Log
                </h4>
                <div class="bg-slate-50/60 dark:bg-slate-950/40 border border-slate-200/60 dark:border-slate-800/60 rounded-xl pl-5 pr-4 py-4 max-h-[220px] overflow-y-auto">
                    <div id="activity-timeline-log" class="relative pl-5 border-l-2 border-slate-200 dark:border-slate-800 space-y-4">
                        <!-- Dynamically populated via JS -->
                    </div>
                </div>
            </div>

            <!-- PART 3: Attached Loan Documents -->
            <div class="space-y-2.5 pt-1 border-t border-slate-100 dark:border-slate-800">
                <div class="flex items-center justify-between">
                    <h4 class="text-[11px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider flex items-center gap-1.5">
                        <i class="fa-solid fa-folder-open text-emerald-500"></i>
                        Attached Loan Documents
                    </h4>
                    <span id="modal-docs-count-badge" class="text-[10px] font-mono text-slate-400 font-bold"></span>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <!-- Column A: Compliance Documents -->
                    <div class="bg-slate-50/60 dark:bg-slate-950/40 border border-slate-200/60 dark:border-slate-800/60 rounded-xl p-3.5 space-y-2">
                        <span class="text-[10px] font-bold uppercase text-slate-500 dark:text-slate-400 tracking-wider flex items-center gap-1.5">
                            <i class="fa-solid fa-id-card text-emerald-500"></i>
                            Compliance Files (ID &amp; Payslips)
                        </span>
                        <div id="modal-compliance-docs-list" class="space-y-1.5">
                            <!-- Populated dynamically -->
                        </div>
                    </div>

                    <!-- Column B: Accounting Documents -->
                    <div class="bg-slate-50/60 dark:bg-slate-950/40 border border-slate-200/60 dark:border-slate-800/60 rounded-xl p-3.5 space-y-2">
                        <span class="text-[10px] font-bold uppercase text-slate-500 dark:text-slate-400 tracking-wider flex items-center gap-1.5">
                            <i class="fa-solid fa-file-invoice text-blue-500"></i>
                            Accounting Official Files
                        </span>
                        <div id="modal-accounting-docs-list" class="space-y-1.5">
                            <!-- Populated dynamically -->
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-end">
            <button type="button" class="modal-close px-4 py-2 rounded-xl border border-slate-200 dark:border-slate-700 text-xs font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">Close</button>
        </div>
    </div>
</div>

<!-- MODAL: REPLACE REJECTED CO-MAKER -->
<div id="modal-replace-comaker" class="fixed inset-0 z-50 hidden overflow-y-auto flex items-center justify-center p-3 sm:p-4">
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-slate-950/40 dark:bg-slate-950/70 backdrop-blur-sm opacity-0 transition-opacity duration-300 pointer-events-none modal-overlay"></div>
    
    <!-- Modal Container -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-2xl overflow-visible w-full max-w-md relative z-10 p-5 sm:p-6 space-y-5 transform scale-95 opacity-0 transition-all duration-300 modal-container">
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
            <div>
                <h3 class="text-base font-bold text-slate-900 dark:text-slate-100">Replace Co-maker</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Designate an eligible member as replacement.</p>
            </div>
            <button type="button" class="modal-close p-1.5 rounded-lg text-slate-400 hover:text-slate-700 dark:text-slate-500 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                <i class="fa-solid fa-xmark text-base"></i>
            </button>
        </div>

        <form id="replace-comaker-form" method="POST" action="">
            @csrf
            @method('PATCH')
            <input type="hidden" name="old_comaker_id" id="replace-old-id">
            
            <div class="space-y-4">
                <div class="bg-amber-50/80 dark:bg-amber-950/20 border border-amber-200/70 dark:border-amber-900/40 rounded-xl p-3 text-xs text-amber-900 dark:text-amber-300 leading-normal">
                    <span class="font-bold block mb-0.5"><i class="fa-solid fa-triangle-exclamation text-amber-500 mr-1"></i> Co-maker Declined:</span>
                    <span id="replace-old-name" class="font-bold"></span> has declined your request. Please select another co-maker below to keep your application moving.
                </div>

                <div class="space-y-1.5 relative" id="searchable-comaker-wrapper">
                    <label class="text-xs font-semibold text-slate-700 dark:text-slate-300">Select Replacement Member</label>
                    
                    <!-- Selected Value Trigger Button -->
                    <button type="button" id="comaker-select-trigger" class="w-full px-3.5 py-2.5 text-xs font-medium border border-slate-200 dark:border-slate-700 rounded-xl bg-white dark:bg-slate-800 outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500/20 transition-all text-slate-700 dark:text-slate-300 flex items-center justify-between">
                        <span id="comaker-select-label" class="text-slate-400 dark:text-slate-500">Select a member...</span>
                        <i class="fa-solid fa-chevron-down text-slate-400 text-xs"></i>
                    </button>

                    <!-- Hidden Input for Form Submission -->
                    <input type="hidden" name="new_comaker_id" id="comaker-hidden-input" required>

                    <!-- Searchable Dropdown List Panel -->
                    <div id="comaker-select-dropdown" class="hidden absolute left-0 right-0 z-20 mt-1 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl shadow-xl overflow-hidden flex flex-col max-h-60">
                        <!-- Search Box -->
                        <div class="p-2 border-b border-slate-100 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-900/50 flex items-center gap-2">
                            <i class="fa-solid fa-magnifying-glass text-slate-400 text-xs pl-2"></i>
                            <input type="text" id="comaker-search-input" placeholder="Search by name or company ID..." class="w-full px-2 py-1 text-xs border-0 bg-transparent outline-none text-slate-900 dark:text-slate-100">
                        </div>
                        
                        <!-- Options Scroll Area -->
                        <ul id="comaker-select-options" class="flex-1 overflow-y-auto divide-y divide-slate-50 dark:divide-slate-700/50">
                            @foreach($members as $m)
                                <li data-value="{{ $m->id }}" data-search="{{ strtolower($m->name . ' ' . $m->company_id) }}" class="comaker-option-item px-3.5 py-2.5 text-xs text-slate-700 dark:text-slate-300 hover:bg-emerald-50 hover:text-emerald-800 dark:hover:bg-slate-700/80 dark:hover:text-white cursor-pointer transition-colors flex items-center justify-between">
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

            <div class="pt-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-end gap-2">
                <button type="button" class="modal-close px-4 py-2 rounded-xl border border-slate-200 dark:border-slate-700 text-xs font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">Cancel</button>
                <button type="submit" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold shadow-xs shadow-emerald-600/10 transition-all">Submit Replacement</button>
            </div>
        </form>
    </div>
</div>

@include('admin.partials.pdf-viewer-modal')

@endsection

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function () {

        // Modal Drawer toggles
        function openDrawer(drawerId) {
            const drawer = document.getElementById(drawerId);
            if (!drawer) return;
            const overlay = drawer.querySelector(".modal-overlay");
            const container = drawer.querySelector(".modal-container");
            
            drawer.classList.remove("hidden");
            drawer.classList.add("flex");
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

        function closeDrawer(drawerId) {
            const drawer = document.getElementById(drawerId);
            if (!drawer) return;
            const overlay = drawer.querySelector(".modal-overlay");
            const container = drawer.querySelector(".modal-container");
            
            if (overlay) {
                overlay.classList.add("opacity-0", "pointer-events-none");
                overlay.classList.remove("opacity-100", "pointer-events-auto");
            }
            if (container) {
                container.classList.add("scale-95", "opacity-0");
                container.classList.remove("scale-100", "opacity-100");
            }
            setTimeout(() => {
                drawer.classList.add("hidden");
                drawer.classList.remove("flex");
            }, 250);
        }

        // Close click events on all modals
        document.querySelectorAll(".modal-close, .modal-overlay").forEach(btn => {
            btn.addEventListener("click", function() {
                const drawer = this.closest('[id^="drawer-"], [id^="modal-"]');
                if (drawer) {
                    closeDrawer(drawer.id);
                }
            });
        });

        // Trigger tracking ledger & timeline modal
        document.querySelectorAll(".btn-view-ledger").forEach(btn => {
            btn.addEventListener("click", function() {
                const ledger = JSON.parse(this.getAttribute("data-ledger") || "[]");
                const container = document.getElementById("ledger-timeline-container");
                container.innerHTML = "";

                if (ledger.length === 0) {
                    container.innerHTML = '<div class="col-span-6 text-center py-6 text-slate-500 dark:text-slate-400 font-semibold italic text-xs bg-slate-50 dark:bg-slate-950/40 border border-slate-100 dark:border-slate-800 rounded-xl">This application has no active workflow stages.</div>';
                } else {
                    ledger.forEach((log, index) => {
                        const stepCard = document.createElement("div");
                        stepCard.className = "flex flex-row sm:flex-col items-center gap-2 sm:gap-2 relative z-10";

                        let circleClass = '';
                        let containerClass = '';
                        let badgeClass = '';
                        let statusText = '';
                        let detailText = '';
                        let cardOpacity = 'opacity-100';

                        switch (log.status) {
                            case 'approved':
                            case 'completed':
                                circleClass = 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 border border-emerald-300 dark:border-emerald-800 font-extrabold';
                                containerClass = 'bg-emerald-50/40 dark:bg-emerald-950/20 border border-emerald-200/60 dark:border-emerald-900/40';
                                badgeClass = 'bg-emerald-100 dark:bg-emerald-950 text-emerald-800 dark:text-emerald-300';
                                statusText = log.status === 'completed' ? 'Released' : 'Approved';
                                detailText = log.actor ? `Approved by: ${log.actor}` : 'Stage completed';
                                break;
                            case 'rejected':
                                circleClass = 'bg-rose-50 dark:bg-rose-950/40 text-rose-600 dark:text-rose-400 border border-rose-300 dark:border-rose-800 font-extrabold';
                                containerClass = 'bg-rose-50/40 dark:bg-rose-950/20 border border-rose-200/60 dark:border-rose-900/40';
                                badgeClass = 'bg-rose-100 dark:bg-rose-950 text-rose-800 dark:text-rose-300';
                                statusText = 'Rejected';
                                detailText = log.actor ? `Declined by: ${log.actor}` : 'Stage declined';
                                break;
                            case 'skipped':
                                circleClass = 'bg-slate-100 dark:bg-slate-800 text-slate-400 dark:text-slate-500 border border-slate-200 dark:border-slate-700 font-semibold';
                                containerClass = 'bg-slate-50 dark:bg-slate-950/20 border border-slate-200/60 dark:border-slate-800';
                                badgeClass = 'bg-slate-100 dark:bg-slate-800 text-slate-500';
                                statusText = 'Skipped';
                                detailText = 'Not required';
                                cardOpacity = 'opacity-60';
                                break;
                            case 'current':
                                circleClass = 'bg-sky-50 dark:bg-sky-950/40 text-sky-600 dark:text-sky-400 border-2 border-sky-400 dark:border-sky-600 animate-pulse font-extrabold';
                                containerClass = 'bg-sky-50/40 dark:bg-sky-950/20 border border-sky-200 dark:border-sky-800/60 ring-1 ring-sky-500/20';
                                badgeClass = 'bg-sky-100 dark:bg-sky-950 text-sky-800 dark:text-sky-300 animate-pulse';
                                statusText = 'Under Review';
                                detailText = 'Awaiting decision';
                                break;
                            case 'cancelled':
                                circleClass = 'bg-slate-100 dark:bg-slate-800 text-slate-400 dark:text-slate-600 border border-slate-200 dark:border-slate-700';
                                containerClass = 'bg-slate-50/40 dark:bg-slate-950/20 border border-slate-200/40 dark:border-slate-800/40';
                                badgeClass = 'bg-slate-100 dark:bg-slate-800 text-slate-400';
                                statusText = 'Cancelled';
                                detailText = 'Workflow halted';
                                cardOpacity = 'opacity-40';
                                break;
                            case 'pending':
                            default:
                                circleClass = 'bg-slate-50 dark:bg-slate-900 text-slate-300 dark:text-slate-600 border border-slate-200 dark:border-slate-800';
                                containerClass = 'bg-slate-50/30 dark:bg-slate-950/10 border border-slate-200/40 dark:border-slate-800/40';
                                badgeClass = 'bg-slate-100 dark:bg-slate-800 text-slate-400';
                                statusText = 'Pending';
                                detailText = 'Future stage';
                                cardOpacity = 'opacity-50';
                                break;
                        }

                        // Parse short date to keep horizontal space clean
                        const dateStr = log.date ? log.date.split(' ')[0] + ' ' + log.date.split(' ')[1] : '';

                        stepCard.innerHTML = `
                            <!-- Circle Node -->
                            <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-full ${circleClass} flex items-center justify-center flex-shrink-0 relative z-10 shadow-xs transition-all">
                                <span class="text-xs font-bold">${index + 1}</span>
                            </div>
                            
                            <!-- Info Card -->
                            <div class="flex-1 sm:flex-grow-0 sm:w-full ${containerClass} p-2 rounded-xl transition-all text-left sm:text-center flex flex-col justify-between sm:min-h-[100px] ${cardOpacity}">
                                <div class="space-y-0.5">
                                    <h4 class="font-bold text-slate-900 dark:text-slate-100 text-[10px] uppercase tracking-wider line-clamp-1 leading-tight">${log.label}</h4>
                                    <div>
                                        <span class="text-[8px] px-1.5 py-0.5 rounded-full ${badgeClass} font-bold uppercase tracking-wide inline-block leading-none">${statusText}</span>
                                    </div>
                                </div>
                                
                                <div class="mt-1 border-t border-slate-200/60 dark:border-slate-800/80 pt-1 space-y-0.5 flex-1 flex flex-col justify-end">
                                    <p class="text-[9px] text-slate-500 dark:text-slate-400 font-medium line-clamp-2 leading-tight">${detailText}</p>
                                    ${dateStr ? `<p class="text-[8px] text-slate-400 dark:text-slate-500 font-mono leading-none">${dateStr}</p>` : ''}
                                </div>
                                
                                ${log.remarks ? `
                                    <div class="mt-1 pt-1 border-t border-slate-200/60 dark:border-slate-800/80 group relative">
                                        <span class="text-[9px] font-semibold text-emerald-600 dark:text-emerald-400 hover:underline cursor-pointer flex items-center justify-center gap-1">
                                            <i class="fa-solid fa-comment-dots text-[10px]"></i>
                                            <span>Remarks</span>
                                        </span>
                                        <div class="absolute left-0 sm:left-1/2 sm:-translate-x-1/2 bottom-full mb-1.5 hidden group-hover:block w-48 bg-slate-900 text-white dark:bg-slate-950 dark:border dark:border-slate-800 p-2 rounded-lg text-[10px] leading-relaxed shadow-xl z-50">
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
                    activityContainer.innerHTML = '<div class="text-slate-500 dark:text-slate-400 font-medium italic text-xs pl-2">No timeline activity logged yet for this application.</div>';
                } else {
                    activities.forEach((act) => {
                        const actEl = document.createElement("div");
                        actEl.className = "relative group";
                        
                        const dateText = new Date(act.created_at).toLocaleString('en-US', {
                            month: 'short', day: 'numeric', year: 'numeric', hour: '2-digit', minute: '2-digit', hour12: true
                        });

                        actEl.innerHTML = `
                            <span class="absolute -left-[27px] top-1.5 w-3.5 h-3.5 rounded-full border-2 border-white dark:border-slate-900 bg-emerald-500 group-hover:bg-emerald-600 transition-colors shadow-xs"></span>
                            <div class="text-xs">
                                <span class="font-bold text-slate-900 dark:text-slate-100 text-xs">${act.description}</span>
                                <div class="flex items-center gap-1.5 text-[10px] text-slate-500 dark:text-slate-400 font-medium mt-0.5">
                                    <span>${dateText}</span>
                                    ${act.actor ? `<span>•</span> <span>By: ${act.actor.name}</span>` : ''}
                                </div>
                            </div>
                        `;
                        activityContainer.appendChild(actEl);
                    });
                }

                // Render Attached Documents (Compliance & Accounting)
                const documents = JSON.parse(this.getAttribute("data-documents") || "[]");
                const ledgerUrl = this.getAttribute("data-ledger-url");
                const scheduleUrl = this.getAttribute("data-schedule-url");
                const appId = this.getAttribute("data-app-id");

                const complianceContainer = document.getElementById("modal-compliance-docs-list");
                const accountingContainer = document.getElementById("modal-accounting-docs-list");
                const docsCountBadge = document.getElementById("modal-docs-count-badge");

                let totalCount = documents.length + (ledgerUrl ? 1 : 0) + (scheduleUrl ? 1 : 0);
                if (docsCountBadge) {
                    docsCountBadge.textContent = `${totalCount} File${totalCount === 1 ? '' : 's'}`;
                }

                if (complianceContainer) {
                    complianceContainer.innerHTML = "";
                    if (documents.length > 0) {
                        documents.forEach(doc => {
                            const docEl = document.createElement("div");
                            docEl.className = "flex items-center justify-between p-2 bg-white dark:bg-slate-900 border border-slate-200/70 dark:border-slate-800 rounded-lg text-xs";
                            docEl.innerHTML = `
                                <div class="flex items-center gap-2 truncate pr-2">
                                    <i class="fa-solid fa-file-pdf text-rose-500 text-xs"></i>
                                    <span class="font-semibold text-slate-800 dark:text-slate-200 truncate" title="${doc.name}">${doc.name}</span>
                                    <span class="text-[9px] font-mono text-slate-400">(${doc.size})</span>
                                </div>
                                <button type="button" class="btn-preview-pdf px-2 py-0.5 rounded-md bg-emerald-50 hover:bg-emerald-100 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-400 text-[10px] font-bold border border-emerald-200/60 dark:border-emerald-800/40 transition-colors flex items-center gap-1 flex-shrink-0"
                                    data-url="${doc.url}" data-name="${doc.name}" data-size="${doc.size}">
                                    <i class="fa-solid fa-eye text-[9px]"></i>
                                    <span>Preview</span>
                                </button>
                            `;
                            complianceContainer.appendChild(docEl);
                        });
                    } else {
                        complianceContainer.innerHTML = '<p class="text-xs text-slate-400 italic">No compliance documents attached</p>';
                    }
                }

                if (accountingContainer) {
                    accountingContainer.innerHTML = "";
                    let hasAccounting = false;

                    if (ledgerUrl) {
                        hasAccounting = true;
                        const ledgerEl = document.createElement("div");
                        ledgerEl.className = "flex items-center justify-between p-2 bg-white dark:bg-slate-900 border border-slate-200/70 dark:border-slate-800 rounded-lg text-xs";
                        ledgerEl.innerHTML = `
                            <div class="flex items-center gap-2 truncate pr-2">
                                <i class="fa-solid fa-book text-emerald-600 text-xs"></i>
                                <span class="font-semibold text-slate-800 dark:text-slate-200 truncate">General Ledger</span>
                            </div>
                            <button type="button" class="btn-preview-pdf px-2 py-0.5 rounded-md bg-emerald-50 hover:bg-emerald-100 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-400 text-[10px] font-bold border border-emerald-200/60 dark:border-emerald-800/40 transition-colors flex items-center gap-1 flex-shrink-0"
                                data-url="${ledgerUrl}" data-name="Loan_Ledger_LN-${appId}.pdf" data-size="PDF">
                                <i class="fa-solid fa-eye text-[9px]"></i>
                                <span>Preview</span>
                            </button>
                        `;
                        accountingContainer.appendChild(ledgerEl);
                    }

                    if (scheduleUrl) {
                        hasAccounting = true;
                        const schedEl = document.createElement("div");
                        schedEl.className = "flex items-center justify-between p-2 bg-white dark:bg-slate-900 border border-slate-200/70 dark:border-slate-800 rounded-lg text-xs";
                        schedEl.innerHTML = `
                            <div class="flex items-center gap-2 truncate pr-2">
                                <i class="fa-solid fa-calendar-days text-blue-600 text-xs"></i>
                                <span class="font-semibold text-slate-800 dark:text-slate-200 truncate">Payment Schedule</span>
                            </div>
                            <button type="button" class="btn-preview-pdf px-2 py-0.5 rounded-md bg-blue-50 hover:bg-blue-100 text-blue-700 dark:bg-blue-950/40 dark:text-blue-400 text-[10px] font-bold border border-blue-200/60 dark:border-blue-800/40 transition-colors flex items-center gap-1 flex-shrink-0"
                                data-url="${scheduleUrl}" data-name="Amortization_Schedule_LN-${appId}.pdf" data-size="PDF">
                                <i class="fa-solid fa-eye text-[9px]"></i>
                                <span>Preview</span>
                            </button>
                        `;
                        accountingContainer.appendChild(schedEl);
                    }

                    if (!hasAccounting) {
                        accountingContainer.innerHTML = '<p class="text-xs text-slate-400 italic">Awaiting Accounting stage upload</p>';
                    }
                }

                // Re-bind preview triggers within newly injected dynamic modal elements
                document.querySelectorAll(".btn-preview-pdf").forEach(pBtn => {
                    pBtn.onclick = function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        openPdfPreview(this.dataset.url, this.dataset.name, this.dataset.size);
                    };
                });

                openDrawer("modal-ledger");
            });
        });

        // --- PDF PREVIEW MODAL LOGIC ---
        const modalPdf = document.getElementById("modal-pdf-viewer");
        const pdfIframe = document.getElementById("pdf-viewer-frame");
        const pdfLoader = document.getElementById("pdf-viewer-loader");
        const pdfTitle = document.getElementById("pdf-viewer-title");
        const pdfMeta = document.getElementById("pdf-viewer-meta");
        const pdfExternalLink = document.getElementById("pdf-viewer-external-link");
        const btnClosePdf = document.getElementById("btn-close-pdf-viewer");
        const backdropPdf = document.getElementById("pdf-viewer-backdrop");

        function openPdfPreview(url, filename, filesize) {
            if (!modalPdf) return;
            if (pdfTitle) pdfTitle.textContent = filename || 'Compliance Document';
            if (pdfMeta) pdfMeta.textContent = (filesize ? filesize + ' • ' : '') + 'Verified PDF Stream';

            // Normalize URL scheme to match page protocol to prevent mixed content
            let targetUrl = url;
            if (window.location.protocol === 'https:' && targetUrl.startsWith('http://')) {
                targetUrl = targetUrl.replace('http://', 'https://');
            }

            if (pdfExternalLink) pdfExternalLink.href = targetUrl;

            if (pdfLoader) pdfLoader.classList.remove("opacity-0", "pointer-events-none");
            if (pdfIframe) {
                pdfIframe.src = targetUrl + '#toolbar=1&navpanes=0';
                pdfIframe.onload = function() {
                    setTimeout(() => {
                        if (pdfLoader) pdfLoader.classList.add("opacity-0", "pointer-events-none");
                    }, 250);
                };
            }
            openDrawer("modal-pdf-viewer");
        }

        function closePdfPreview() {
            closeDrawer("modal-pdf-viewer");
            setTimeout(() => {
                if (pdfIframe) pdfIframe.src = "about:blank";
            }, 300);
        }

        if (btnClosePdf) btnClosePdf.addEventListener("click", closePdfPreview);
        if (backdropPdf) backdropPdf.addEventListener("click", closePdfPreview);

        document.addEventListener("keydown", function(e) {
            if (e.key === "Escape") {
                if (modalPdf && !modalPdf.classList.contains("hidden")) {
                    closePdfPreview();
                    e.stopPropagation();
                    return;
                }
                const ledgerModal = document.getElementById("modal-ledger");
                if (ledgerModal && !ledgerModal.classList.contains("hidden")) {
                    closeDrawer("modal-ledger");
                }
                const comakerModal = document.getElementById("modal-replace-comaker");
                if (comakerModal && !comakerModal.classList.contains("hidden")) {
                    closeDrawer("modal-replace-comaker");
                }
            }
        });

        // Global preview listeners
        document.querySelectorAll(".btn-preview-pdf").forEach(btn => {
            btn.addEventListener("click", function(e) {
                e.preventDefault();
                e.stopPropagation();
                openPdfPreview(this.dataset.url, this.dataset.name, this.dataset.size);
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
