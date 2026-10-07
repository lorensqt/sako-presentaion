@extends('layouts.admin')

@section('title', 'Withdrawals Decision Board - Sako Cooperative')
@section('page_title', 'Withdrawals Decision Board')
@section('page_subtitle', 'Review savings payout requests, acknowledge processing tasks, release completed disbursements, or reject with audit remarks.')

@section('content')
<div class="space-y-6 animate-fade-in">

    <!-- Withdrawal KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Pending Requests -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-4 sm:p-5 shadow-xs relative overflow-hidden group hover:border-amber-300 dark:hover:border-amber-700/60 transition-all duration-200">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Pending Requests</span>
                <span class="w-8 h-8 rounded-xl bg-amber-50 dark:bg-amber-950/50 text-amber-600 dark:text-amber-400 flex items-center justify-center text-xs">
                    <i class="fa-solid fa-hourglass-half"></i>
                </span>
            </div>
            <div class="mt-3 flex items-baseline gap-2">
                <span class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white font-mono" id="kpi-pending-count">{{ $metrics['pending'] }}</span>
                <span class="text-xs font-semibold text-amber-600 dark:text-amber-400">Queue</span>
            </div>
            <div class="mt-2 flex items-center gap-1.5 text-[11px] text-amber-600 dark:text-amber-400 font-semibold">
                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                <span>Awaiting Verification</span>
            </div>
        </div>

        <!-- In Processing -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-4 sm:p-5 shadow-xs relative overflow-hidden group hover:border-blue-300 dark:hover:border-blue-700/60 transition-all duration-200">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">In Processing</span>
                <span class="w-8 h-8 rounded-xl bg-blue-50 dark:bg-blue-950/50 text-blue-600 dark:text-blue-400 flex items-center justify-center text-xs">
                    <i class="fa-solid fa-arrows-rotate"></i>
                </span>
            </div>
            <div class="mt-3 flex items-baseline gap-2">
                <span class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white font-mono" id="kpi-processing-count">{{ $metrics['processing'] }}</span>
                <span class="text-xs font-semibold text-blue-600 dark:text-blue-400">Ongoing</span>
            </div>
            <div class="mt-2 flex items-center gap-1.5 text-[11px] text-blue-600 dark:text-blue-400 font-semibold">
                <span class="w-1.5 h-1.5 rounded-full bg-blue-500 animate-pulse"></span>
                <span>Disbursements Ongoing</span>
            </div>
        </div>

        <!-- Completed Payouts -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-4 sm:p-5 shadow-xs relative overflow-hidden group hover:border-emerald-300 dark:hover:border-emerald-700/60 transition-all duration-200">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Completed Payouts</span>
                <span class="w-8 h-8 rounded-xl bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-xs">
                    <i class="fa-solid fa-circle-check"></i>
                </span>
            </div>
            <div class="mt-3 flex items-baseline gap-2">
                <span class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white font-mono" id="kpi-released-count">{{ $metrics['released'] }}</span>
                <span class="text-xs font-semibold text-emerald-600 dark:text-emerald-400">Settled</span>
            </div>
            <div class="mt-2 flex items-center gap-1.5 text-[11px] text-emerald-600 dark:text-emerald-400 font-semibold">
                <i class="fa-solid fa-shield-halved text-[10px]"></i>
                <span>Funds Safely Released</span>
            </div>
        </div>

        <!-- Rejected Requests -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-4 sm:p-5 shadow-xs relative overflow-hidden group hover:border-rose-300 dark:hover:border-rose-700/60 transition-all duration-200">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Rejected Requests</span>
                <span class="w-8 h-8 rounded-xl bg-rose-50 dark:bg-rose-950/50 text-rose-600 dark:text-rose-400 flex items-center justify-center text-xs">
                    <i class="fa-solid fa-circle-xmark"></i>
                </span>
            </div>
            <div class="mt-3 flex items-baseline gap-2">
                <span class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white font-mono" id="kpi-rejected-count">{{ $metrics['rejected'] ?? 0 }}</span>
                <span class="text-xs font-semibold text-rose-600 dark:text-rose-400">Declined</span>
            </div>
            <div class="mt-2 flex items-center gap-1.5 text-[11px] text-rose-600 dark:text-rose-400 font-semibold">
                <i class="fa-solid fa-ban text-[10px]"></i>
                <span>Audit Reasons Logged</span>
            </div>
        </div>
    </div>

    <!-- Interactive Filter & Command Bar (Revamped with Custom Filtering) -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 p-4 sm:p-5 rounded-2xl sm:rounded-3xl shadow-xs space-y-4">
        <!-- Top Row: Interactive Search, Customized Smooth Dropdowns, and Bulk Export -->
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3">
            
            <!-- Smooth Search Bar with Live Spinner and Instant Clear -->
            <div class="relative flex-1 min-w-[260px] max-w-md">
                <i id="search-icon" class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 dark:text-slate-500 text-xs transition-colors duration-200"></i>
                <input type="text" id="ajax-search" value="{{ $search ?? '' }}" placeholder="Search by member, email, ref #WD-..., channel, or remarks..." class="w-full pl-9 pr-14 py-2.5 text-xs font-semibold border border-slate-200 dark:border-slate-700/80 bg-slate-50/60 dark:bg-slate-950 text-slate-900 dark:text-slate-100 rounded-xl focus:bg-white dark:focus:bg-slate-950 focus:border-emerald-500 dark:focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 dark:focus:ring-emerald-500/10 placeholder-slate-400 dark:placeholder-slate-500 transition-all outline-none">
                
                <div class="absolute right-3 top-1/2 -translate-y-1/2 flex items-center gap-1.5">
                    <i id="search-spinner" class="fa-solid fa-circle-notch fa-spin text-emerald-500 text-xs hidden"></i>
                    <button type="button" id="btn-clear-search" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 text-xs cursor-pointer p-0.5 {{ !empty($search) ? '' : 'hidden' }}" title="Clear search">
                        <i class="fa-solid fa-circle-xmark"></i>
                    </button>
                </div>
            </div>

            <!-- Customized Smooth Dropdowns -->
            <div class="flex flex-wrap items-center gap-2.5">
                
                <!-- Status Custom Dropdown -->
                <div class="relative custom-dropdown" id="dropdown-wrapper-status">
                    <input type="hidden" id="filter-status" value="{{ $status ?: 'all' }}">
                    <button type="button" id="dropdown-btn-status" class="inline-flex items-center gap-2 px-3.5 py-2.5 text-xs font-semibold border border-slate-200 dark:border-slate-700/80 bg-slate-50/70 dark:bg-slate-950 text-slate-800 dark:text-slate-200 rounded-xl hover:border-emerald-500/50 hover:bg-white dark:hover:bg-slate-900 transition-all cursor-pointer select-none">
                        <i class="fa-solid fa-filter text-slate-400 text-xs"></i>
                        <span id="label-status" class="truncate max-w-[110px]">
                            @if($status === 'pending') Pending Only
                            @elseif($status === 'processing') In Processing
                            @elseif($status === 'released') Released
                            @elseif($status === 'rejected') Rejected
                            @else All Status
                            @endif
                        </span>
                        <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 transition-transform duration-200 dropdown-arrow"></i>
                    </button>
                    <!-- Floating Menu -->
                    <div id="dropdown-menu-status" class="absolute top-full left-0 mt-1.5 w-48 z-40 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-xl shadow-slate-900/10 dark:shadow-slate-950/40 p-1.5 space-y-0.5 transform opacity-0 scale-95 pointer-events-none transition-all duration-200 ease-out origin-top-left">
                        <div class="dropdown-item px-3 py-2 text-xs font-semibold rounded-xl text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800/70 cursor-pointer flex items-center justify-between transition-colors" data-value="all">
                            <span>All Status</span>
                            <i class="fa-solid fa-check text-emerald-600 dark:text-emerald-400 text-[11px] check-icon {{ (!$status || $status === 'all') ? '' : 'hidden' }}"></i>
                        </div>
                        <div class="dropdown-item px-3 py-2 text-xs font-semibold rounded-xl text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800/70 cursor-pointer flex items-center justify-between transition-colors" data-value="pending">
                            <span>Pending Only</span>
                            <i class="fa-solid fa-check text-emerald-600 dark:text-emerald-400 text-[11px] check-icon {{ $status === 'pending' ? '' : 'hidden' }}"></i>
                        </div>
                        <div class="dropdown-item px-3 py-2 text-xs font-semibold rounded-xl text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800/70 cursor-pointer flex items-center justify-between transition-colors" data-value="processing">
                            <span>In Processing</span>
                            <i class="fa-solid fa-check text-emerald-600 dark:text-emerald-400 text-[11px] check-icon {{ $status === 'processing' ? '' : 'hidden' }}"></i>
                        </div>
                        <div class="dropdown-item px-3 py-2 text-xs font-semibold rounded-xl text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800/70 cursor-pointer flex items-center justify-between transition-colors" data-value="released">
                            <span>Released</span>
                            <i class="fa-solid fa-check text-emerald-600 dark:text-emerald-400 text-[11px] check-icon {{ $status === 'released' ? '' : 'hidden' }}"></i>
                        </div>
                        <div class="dropdown-item px-3 py-2 text-xs font-semibold rounded-xl text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800/70 cursor-pointer flex items-center justify-between transition-colors" data-value="rejected">
                            <span>Rejected</span>
                            <i class="fa-solid fa-check text-emerald-600 dark:text-emerald-400 text-[11px] check-icon {{ $status === 'rejected' ? '' : 'hidden' }}"></i>
                        </div>
                    </div>
                </div>

                <!-- Channel Custom Dropdown -->
                <div class="relative custom-dropdown" id="dropdown-wrapper-channel">
                    <input type="hidden" id="filter-channel" value="{{ $channel ?: 'all' }}">
                    <button type="button" id="dropdown-btn-channel" class="inline-flex items-center gap-2 px-3.5 py-2.5 text-xs font-semibold border border-slate-200 dark:border-slate-700/80 bg-slate-50/70 dark:bg-slate-950 text-slate-800 dark:text-slate-200 rounded-xl hover:border-emerald-500/50 hover:bg-white dark:hover:bg-slate-900 transition-all cursor-pointer select-none">
                        <i class="fa-solid fa-wallet text-slate-400 text-xs"></i>
                        <span id="label-channel" class="truncate max-w-[110px]">
                            @if($channel && $channel !== 'all') {{ $channel }}
                            @else All Channels
                            @endif
                        </span>
                        <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 transition-transform duration-200 dropdown-arrow"></i>
                    </button>
                    <!-- Floating Menu -->
                    <div id="dropdown-menu-channel" class="absolute top-full left-0 mt-1.5 w-48 z-40 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-xl shadow-slate-900/10 dark:shadow-slate-950/40 p-1.5 space-y-0.5 transform opacity-0 scale-95 pointer-events-none transition-all duration-200 ease-out origin-top-left">
                        <div class="dropdown-item px-3 py-2 text-xs font-semibold rounded-xl text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800/70 cursor-pointer flex items-center justify-between transition-colors" data-value="all">
                            <span>All Channels</span>
                            <i class="fa-solid fa-check text-emerald-600 dark:text-emerald-400 text-[11px] check-icon {{ (!$channel || $channel === 'all') ? '' : 'hidden' }}"></i>
                        </div>
                        <div class="dropdown-item px-3 py-2 text-xs font-semibold rounded-xl text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800/70 cursor-pointer flex items-center justify-between transition-colors" data-value="MCash">
                            <span>MCash</span>
                            <i class="fa-solid fa-check text-emerald-600 dark:text-emerald-400 text-[11px] check-icon {{ $channel === 'MCash' ? '' : 'hidden' }}"></i>
                        </div>
                        <div class="dropdown-item px-3 py-2 text-xs font-semibold rounded-xl text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800/70 cursor-pointer flex items-center justify-between transition-colors" data-value="Payment Solution">
                            <span>Payment Solution</span>
                            <i class="fa-solid fa-check text-emerald-600 dark:text-emerald-400 text-[11px] check-icon {{ $channel === 'Payment Solution' ? '' : 'hidden' }}"></i>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Fast Quick Actions -->
            <div class="flex items-center gap-2 flex-shrink-0">
                <button id="btn-trigger-manifest" type="button" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-slate-900 hover:bg-slate-800 dark:bg-slate-800 dark:hover:bg-slate-700 text-white font-bold text-xs px-4 py-2.5 rounded-xl border border-slate-700/60 shadow-xs transition-all duration-200 cursor-pointer">
                    <i class="fa-solid fa-file-pdf text-xs text-rose-400"></i>
                    <span>Export Manifest</span>
                </button>
            </div>
        </div>

        <!-- Bottom Row: Quick Status Filter Pills & Counter / Reset -->
        <div class="flex flex-wrap items-center justify-between gap-2 pt-3 border-t border-slate-100 dark:border-slate-800">
            <div class="flex flex-wrap items-center gap-1.5" id="quick-status-pills">
                <span class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mr-1">Quick Filter:</span>
                <button type="button" data-status="all" class="status-chip px-2.5 py-1 rounded-lg text-[10px] font-bold border transition-all cursor-pointer {{ (!$status || $status === 'all') ? 'bg-slate-900 text-white border-slate-900 dark:bg-white dark:text-slate-900 dark:border-white shadow-2xs' : 'border-slate-200 dark:border-slate-700/80 text-slate-600 dark:text-slate-400 hover:border-slate-400' }}">All</button>
                <button type="button" data-status="pending" class="status-chip px-2.5 py-1 rounded-lg text-[10px] font-bold border transition-all cursor-pointer {{ $status === 'pending' ? 'bg-amber-500 text-white border-amber-500 shadow-2xs' : 'border-slate-200 dark:border-slate-700/80 text-slate-600 dark:text-slate-400 hover:border-amber-500 hover:text-amber-600 dark:hover:text-amber-400' }}">Pending</button>
                <button type="button" data-status="processing" class="status-chip px-2.5 py-1 rounded-lg text-[10px] font-bold border transition-all cursor-pointer {{ $status === 'processing' ? 'bg-blue-600 text-white border-blue-600 shadow-2xs' : 'border-slate-200 dark:border-slate-700/80 text-slate-600 dark:text-slate-400 hover:border-blue-500 hover:text-blue-600 dark:hover:text-blue-400' }}">In Processing</button>
                <button type="button" data-status="released" class="status-chip px-2.5 py-1 rounded-lg text-[10px] font-bold border transition-all cursor-pointer {{ $status === 'released' ? 'bg-emerald-600 text-white border-emerald-600 shadow-2xs' : 'border-slate-200 dark:border-slate-700/80 text-slate-600 dark:text-slate-400 hover:border-emerald-500 hover:text-emerald-600 dark:hover:text-emerald-400' }}">Released</button>
                <button type="button" data-status="rejected" class="status-chip px-2.5 py-1 rounded-lg text-[10px] font-bold border transition-all cursor-pointer {{ $status === 'rejected' ? 'bg-rose-600 text-white border-rose-600 shadow-2xs' : 'border-slate-200 dark:border-slate-700/80 text-slate-600 dark:text-slate-400 hover:border-rose-500 hover:text-rose-600 dark:hover:text-rose-400' }}">Rejected</button>
            </div>
            
            <div class="flex items-center gap-3">
                <span id="withdrawal-count-badge" class="text-[11px] font-bold px-2.5 py-1 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700">
                    <span id="count-number">{{ $withdrawals->total() }}</span> Filings
                </span>
                <button type="button" id="btn-reset-filters" class="text-[11px] font-bold text-slate-500 hover:text-rose-600 dark:text-slate-400 dark:hover:text-rose-400 transition-colors flex items-center gap-1.5 cursor-pointer">
                    <i class="fa-solid fa-arrows-rotate text-[10px]"></i>
                    <span>Reset Filters</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Main List Container -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm overflow-hidden flex flex-col">
        <div class="p-5 border-b border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/50 flex items-center justify-between">
            <h3 class="text-xs font-extrabold text-slate-800 dark:text-white uppercase tracking-widest flex items-center gap-2">
                <i class="fa-solid fa-list-check text-emerald-600 dark:text-emerald-400"></i>
                <span>Withdrawal Pipeline Queue</span>
            </h3>
            <span class="text-2xs font-extrabold text-slate-400 dark:text-slate-500 uppercase tracking-wider flex items-center gap-1.5">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                <span>Live Queue Stream</span>
            </span>
        </div>

        <div class="overflow-x-auto relative">
            <!-- Table Loading Overlay -->
            <div id="table-loading" class="absolute inset-0 bg-white/70 dark:bg-slate-950/70 backdrop-blur-xs flex items-center justify-center z-20 hidden transition-opacity duration-200">
                <div class="inline-flex items-center gap-2 px-4 py-2 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-lg text-xs font-bold text-slate-700 dark:text-slate-300">
                    <i class="fa-solid fa-circle-notch fa-spin text-emerald-500"></i>
                    <span>Filtering Withdrawals...</span>
                </div>
            </div>

            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-900/70 border-b border-slate-100 dark:border-slate-800 text-[10px] font-extrabold text-slate-400 dark:text-slate-500 uppercase tracking-widest">
                        <th class="px-6 py-4.5" style="width: 45px;">
                            <input type="checkbox" id="select-all-withdrawals" class="rounded border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-emerald-600 focus:ring-emerald-500 dark:focus:ring-emerald-600 w-4 h-4 cursor-pointer">
                        </th>
                        <th class="px-6 py-4.5">Member Profile</th>
                        <th class="px-6 py-4.5">Requested Amount</th>
                        <th class="px-6 py-4.5">Disbursement Channel</th>
                        <th class="px-6 py-4.5">Admin Remarks</th>
                        <th class="px-6 py-4.5">Date Filed</th>
                        <th class="px-6 py-4.5">Status</th>
                        <th class="px-6 py-4.5 text-right">Lifecycle Actions</th>
                    </tr>
                </thead>
                <tbody id="withdrawals-table-body" class="divide-y divide-slate-100 dark:divide-slate-800 text-slate-700 dark:text-slate-300 font-medium">
                    @include('admin.partials.withdrawals-table-rows')
                </tbody>
            </table>
        </div>

        <div id="pagination-container" class="px-6 py-4 border-t border-slate-100 dark:border-slate-800 {{ $withdrawals->hasPages() ? '' : 'hidden' }}">
            {{ $withdrawals->links() }}
        </div>
    </div>
</div>

<!-- FLOATING ACTION BAR FOR BULK PDF PREVIEW -->
<div id="bulk-pdf-bar" class="fixed bottom-6 left-1/2 -translate-x-1/2 bg-slate-900/95 dark:bg-slate-950/95 backdrop-blur-md px-6 py-4 rounded-3xl shadow-2xl border border-slate-800 z-50 flex items-center gap-6 transition-all duration-300 transform translate-y-32 opacity-0">
    <div class="flex items-center gap-2">
        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
        <p class="text-xs text-slate-300 font-bold"><span id="selected-count" class="text-white font-black font-mono">0</span> items selected for export</p>
    </div>
    <div class="h-4 w-px bg-slate-700"></div>
    <div class="flex items-center gap-3">
        <button id="btn-export-pdf" type="button" class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-[10px] uppercase tracking-wider px-4 py-2 rounded-xl shadow-xs transition-all cursor-pointer">
            <i class="fa-solid fa-file-pdf"></i>
            <span>Preview Manifest PDF</span>
        </button>
        <button id="btn-clear-selection" type="button" class="text-slate-400 hover:text-white font-bold text-[10px] uppercase tracking-wider transition-colors cursor-pointer">
            Clear
        </button>
    </div>
</div>

<!-- HIDDEN PDF EXPORT FORM -->
<form id="pdf-export-form" action="{{ route('admin.withdrawals.pdf') }}" method="GET" target="_blank" class="hidden">
</form>

<!-- MODAL: RELEASE WITHDRAWAL (WITH REMARKS) -->
<div id="modal-release-withdrawal" class="fixed inset-0 z-50 hidden overflow-y-auto flex items-center justify-center p-4">
    <!-- Backdrop -->
    <div id="modal-release-backdrop" class="fixed inset-0 bg-slate-950/40 dark:bg-slate-950/60 backdrop-blur-md opacity-0 transition-opacity duration-300 ease-out cursor-pointer"></div>

    <!-- Modal Box -->
    <div id="modal-release-box" class="bg-white dark:bg-slate-900 rounded-[2rem] border border-slate-200 dark:border-slate-800 shadow-2xl overflow-hidden w-full max-w-md relative z-10 p-6 sm:p-8 space-y-6 transform scale-95 opacity-0 transition-all duration-300 ease-out">
        <div class="flex items-center justify-between">
            <h3 class="text-base font-extrabold text-slate-950 dark:text-white serif-font tracking-tight flex items-center gap-2">
                <i class="fa-solid fa-hand-holding-dollar text-emerald-600"></i>
                <span>Release Savings Payout</span>
            </h3>
            <button type="button" class="btn-close-release p-1.5 rounded-xl text-slate-400 hover:text-slate-700 dark:text-slate-500 dark:hover:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800/80 transition-all duration-200 outline-none">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>

        <form id="release-withdrawal-form" action="" method="POST" class="space-y-4 m-0">
            @csrf
            <input type="hidden" name="action" value="release">

            <!-- Context Info -->
            <div class="bg-slate-50 dark:bg-slate-950/60 border border-slate-200/70 dark:border-slate-800/80 rounded-2xl p-4 space-y-2 text-xs">
                <div class="flex justify-between">
                    <span class="text-slate-400 font-semibold uppercase tracking-wider text-[10px]">Reference</span>
                    <span id="release-modal-ref" class="font-bold text-slate-800 dark:text-white font-mono">WD-00000</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-400 font-semibold uppercase tracking-wider text-[10px]">Member</span>
                    <span id="release-modal-name" class="font-bold text-slate-800 dark:text-white">Jane Doe</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-400 font-semibold uppercase tracking-wider text-[10px]">Amount</span>
                    <span id="release-modal-amount" class="font-bold text-emerald-600 font-mono">₱0.00</span>
                </div>
            </div>

            <!-- Remarks Input Field -->
            <div class="space-y-1.5">
                <label for="release_remarks_input" class="text-[10px] font-extrabold text-slate-400 dark:text-slate-500 uppercase tracking-widest block">Disbursement Remarks / Notes</label>
                <textarea id="release_remarks_input" name="remarks" required rows="3" placeholder="Enter disbursement notes, reference number, or remarks..." class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-2xl px-4 py-3 text-xs text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all font-medium resize-none"></textarea>
                <p class="text-[10px] text-slate-400 dark:text-slate-500 font-medium">Please enter disbursement notes or reference details. An automated notification email will be dispatched to the member.</p>
            </div>

            <!-- Actions -->
            <div class="flex items-center gap-3 pt-2">
                <button type="button" class="btn-close-release flex-1 px-4 py-3 rounded-2xl border border-slate-200 dark:border-slate-700 text-xs font-bold text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-800 dark:hover:text-slate-100 transition-colors cursor-pointer">
                    Cancel
                </button>
                <button type="submit" class="flex-1 inline-flex items-center justify-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs uppercase tracking-wider py-3 rounded-2xl shadow-xs hover:shadow-md transition-all cursor-pointer">
                    <i class="fa-solid fa-paper-plane text-[11px]"></i>
                    <span>Mark as Released</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL: REJECT WITHDRAWAL (WITH REMARKS) -->
<div id="modal-reject-withdrawal" class="fixed inset-0 z-50 hidden overflow-y-auto flex items-center justify-center p-4">
    <!-- Backdrop -->
    <div id="modal-reject-backdrop" class="fixed inset-0 bg-slate-950/40 dark:bg-slate-950/60 backdrop-blur-md opacity-0 transition-opacity duration-300 ease-out cursor-pointer"></div>

    <!-- Modal Box -->
    <div id="modal-reject-box" class="bg-white dark:bg-slate-900 rounded-[2rem] border border-slate-200 dark:border-slate-800 shadow-2xl overflow-hidden w-full max-w-md relative z-10 p-6 sm:p-8 space-y-6 transform scale-95 opacity-0 transition-all duration-300 ease-out">
        <div class="flex items-center justify-between">
            <h3 class="text-base font-extrabold text-slate-950 dark:text-white serif-font tracking-tight flex items-center gap-2">
                <i class="fa-solid fa-ban text-rose-600"></i>
                <span>Reject Withdrawal Request</span>
            </h3>
            <button type="button" class="btn-close-reject p-1.5 rounded-xl text-slate-400 hover:text-slate-700 dark:text-slate-500 dark:hover:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800/80 transition-all duration-200 outline-none">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>

        <form id="reject-withdrawal-form" action="" method="POST" class="space-y-4 m-0">
            @csrf
            <input type="hidden" name="action" value="reject">

            <!-- Context Info -->
            <div class="bg-slate-50 dark:bg-slate-950/60 border border-slate-200/70 dark:border-slate-800/80 rounded-2xl p-4 space-y-2 text-xs">
                <div class="flex justify-between">
                    <span class="text-slate-400 font-semibold uppercase tracking-wider text-[10px]">Reference</span>
                    <span id="reject-modal-ref" class="font-bold text-slate-800 dark:text-white font-mono">WD-00000</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-400 font-semibold uppercase tracking-wider text-[10px]">Member</span>
                    <span id="reject-modal-name" class="font-bold text-slate-800 dark:text-white">Jane Doe</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-400 font-semibold uppercase tracking-wider text-[10px]">Amount</span>
                    <span id="reject-modal-amount" class="font-bold text-rose-600 font-mono">₱0.00</span>
                </div>
            </div>

            <!-- Remarks Input Field -->
            <div class="space-y-1.5">
                <label for="reject_remarks_input" class="text-[10px] font-extrabold text-slate-400 dark:text-slate-500 uppercase tracking-widest block">Rejection Remarks / Reason</label>
                <textarea id="reject_remarks_input" name="remarks" required rows="3" placeholder="Enter reason for declining this withdrawal request..." class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-2xl px-4 py-3 text-xs text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition-all font-medium resize-none"></textarea>
                <p class="text-[10px] text-slate-400 dark:text-slate-500 font-medium">Please provide a clear reason for rejecting this request. A decline notice email will be dispatched to the member.</p>
            </div>

            <!-- Actions -->
            <div class="flex items-center gap-3 pt-2">
                <button type="button" class="btn-close-reject flex-1 px-4 py-3 rounded-2xl border border-slate-200 dark:border-slate-700 text-xs font-bold text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-800 dark:hover:text-slate-100 transition-colors cursor-pointer">
                    Cancel
                </button>
                <button type="submit" class="flex-1 inline-flex items-center justify-center gap-2 bg-rose-600 hover:bg-rose-700 text-white font-extrabold text-xs uppercase tracking-wider py-3 rounded-2xl shadow-xs hover:shadow-md transition-all cursor-pointer">
                    <i class="fa-solid fa-ban text-[11px]"></i>
                    <span>Confirm Rejection</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const alertInstance = window.MLSAKOAlert || (typeof Swal !== "undefined" ? Swal : null);

    // --- Search and Filtering State Elements ---
    const searchInput = document.getElementById("ajax-search");
    const searchIcon = document.getElementById("search-icon");
    const searchSpinner = document.getElementById("search-spinner");
    const btnClearSearch = document.getElementById("btn-clear-search");
    
    const filterStatus = document.getElementById("filter-status");
    const labelStatus = document.getElementById("label-status");
    
    const filterChannel = document.getElementById("filter-channel");
    const labelChannel = document.getElementById("label-channel");

    const statusChips = document.querySelectorAll(".status-chip");
    const tableBody = document.getElementById("withdrawals-table-body");
    const tableLoading = document.getElementById("table-loading");
    const paginationContainer = document.getElementById("pagination-container");
    const countNumber = document.getElementById("count-number");
    const btnResetFilters = document.getElementById("btn-reset-filters");

    // Bulk PDF Bar Elements
    const selectAllCheckbox = document.getElementById("select-all-withdrawals");
    const bulkPdfBar = document.getElementById("bulk-pdf-bar");
    const selectedCountSpan = document.getElementById("selected-count");
    const btnExportPdf = document.getElementById("btn-export-pdf");
    const btnClearSelection = document.getElementById("btn-clear-selection");
    const pdfExportForm = document.getElementById("pdf-export-form");
    const btnTriggerManifest = document.getElementById("btn-trigger-manifest");

    // Modal Elements: Release
    const releaseModal = document.getElementById("modal-release-withdrawal");
    const releaseModalBackdrop = document.getElementById("modal-release-backdrop");
    const releaseModalBox = document.getElementById("modal-release-box");
    const closeReleaseBtns = document.querySelectorAll(".btn-close-release");
    const releaseForm = document.getElementById("release-withdrawal-form");
    const releaseModalRef = document.getElementById("release-modal-ref");
    const releaseModalName = document.getElementById("release-modal-name");
    const releaseModalAmount = document.getElementById("release-modal-amount");
    const releaseRemarksInput = document.getElementById("release_remarks_input");

    // Modal Elements: Reject
    const rejectModal = document.getElementById("modal-reject-withdrawal");
    const rejectModalBackdrop = document.getElementById("modal-reject-backdrop");
    const rejectModalBox = document.getElementById("modal-reject-box");
    const closeRejectBtns = document.querySelectorAll(".btn-close-reject");
    const rejectForm = document.getElementById("reject-withdrawal-form");
    const rejectModalRef = document.getElementById("reject-modal-ref");
    const rejectModalName = document.getElementById("reject-modal-name");
    const rejectModalAmount = document.getElementById("reject-modal-amount");
    const rejectRemarksInput = document.getElementById("reject_remarks_input");

    // Setup Custom Dropdowns
    setupCustomDropdown("status", filterStatus, labelStatus, onFilterChanged);
    setupCustomDropdown("channel", filterChannel, labelChannel, onFilterChanged);

    function setupCustomDropdown(key, hiddenInput, labelEl, changeCallback) {
        const wrapper = document.getElementById(`dropdown-wrapper-${key}`);
        if (!wrapper) return;
        const btn = document.getElementById(`dropdown-btn-${key}`);
        const menu = document.getElementById(`dropdown-menu-${key}`);
        const arrow = btn.querySelector(".dropdown-arrow");
        const items = menu.querySelectorAll(".dropdown-item");

        function openMenu() {
            closeAllDropdowns(menu);
            menu.classList.remove("opacity-0", "scale-95", "pointer-events-none");
            menu.classList.add("opacity-100", "scale-100", "pointer-events-auto");
            if (arrow) arrow.classList.add("rotate-180");
        }

        function closeMenu() {
            menu.classList.add("opacity-0", "scale-95", "pointer-events-none");
            menu.classList.remove("opacity-100", "scale-100", "pointer-events-auto");
            if (arrow) arrow.classList.remove("rotate-180");
        }

        btn.addEventListener("click", function (e) {
            e.stopPropagation();
            const isOpen = menu.classList.contains("opacity-100");
            if (isOpen) closeMenu();
            else openMenu();
        });

        items.forEach(item => {
            item.addEventListener("click", function (e) {
                e.stopPropagation();
                const val = item.getAttribute("data-value");
                const text = item.querySelector("span").textContent.trim();

                hiddenInput.value = val;
                labelEl.textContent = text;

                // Update checks
                items.forEach(i => {
                    const check = i.querySelector(".check-icon");
                    if (check) check.classList.add("hidden");
                });
                const curCheck = item.querySelector(".check-icon");
                if (curCheck) curCheck.classList.remove("hidden");

                closeMenu();
                changeCallback(key, val);
            });
        });
    }

    function closeAllDropdowns(exceptMenu = null) {
        document.querySelectorAll(".custom-dropdown > div[id^='dropdown-menu-']").forEach(menu => {
            if (menu !== exceptMenu) {
                menu.classList.add("opacity-0", "scale-95", "pointer-events-none");
                menu.classList.remove("opacity-100", "scale-100", "pointer-events-auto");
                const wrapper = menu.closest(".custom-dropdown");
                if (wrapper) {
                    const arrow = wrapper.querySelector(".dropdown-arrow");
                    if (arrow) arrow.classList.remove("rotate-180");
                }
            }
        });
    }

    document.addEventListener("click", function () {
        closeAllDropdowns();
    });

    // Quick Filter Status Chips
    statusChips.forEach(chip => {
        chip.addEventListener("click", function () {
            const val = this.getAttribute("data-status");
            filterStatus.value = val;

            // Update dropdown display
            const matchItem = document.querySelector(`#dropdown-menu-status .dropdown-item[data-value="${val}"]`);
            if (matchItem) {
                labelStatus.textContent = matchItem.querySelector("span").textContent.trim();
                document.querySelectorAll("#dropdown-menu-status .check-icon").forEach(ci => ci.classList.add("hidden"));
                const check = matchItem.querySelector(".check-icon");
                if (check) check.classList.remove("hidden");
            }

            updateStatusChipsState(val);
            fetchWithdrawals();
        });
    });

    function updateStatusChipsState(selectedVal) {
        statusChips.forEach(chip => {
            const val = chip.getAttribute("data-status");
            chip.className = "status-chip px-2.5 py-1 rounded-lg text-[10px] font-bold border transition-all cursor-pointer ";
            if (val === selectedVal) {
                if (val === 'pending') chip.className += "bg-amber-500 text-white border-amber-500 shadow-2xs";
                else if (val === 'processing') chip.className += "bg-blue-600 text-white border-blue-600 shadow-2xs";
                else if (val === 'released') chip.className += "bg-emerald-600 text-white border-emerald-600 shadow-2xs";
                else if (val === 'rejected') chip.className += "bg-rose-600 text-white border-rose-600 shadow-2xs";
                else chip.className += "bg-slate-900 text-white border-slate-900 dark:bg-white dark:text-slate-900 dark:border-white shadow-2xs";
            } else {
                chip.className += "border-slate-200 dark:border-slate-700/80 text-slate-600 dark:text-slate-400 hover:border-slate-400";
            }
        });
    }

    function onFilterChanged(filterName, value) {
        if (filterName === "status") {
            updateStatusChipsState(value);
        }
        fetchWithdrawals();
    }

    // Debounced Search Handler
    let searchDebounceTimer = null;
    searchInput.addEventListener("input", function () {
        const val = this.value.trim();
        if (val.length > 0) btnClearSearch.classList.remove("hidden");
        else btnClearSearch.classList.add("hidden");

        clearTimeout(searchDebounceTimer);
        searchDebounceTimer = setTimeout(() => {
            fetchWithdrawals();
        }, 300);
    });

    btnClearSearch.addEventListener("click", function () {
        searchInput.value = "";
        btnClearSearch.classList.add("hidden");
        fetchWithdrawals();
    });

    // Reset Filters
    if (btnResetFilters) {
        btnResetFilters.addEventListener("click", function () {
            searchInput.value = "";
            btnClearSearch.classList.add("hidden");

            filterStatus.value = "all";
            labelStatus.textContent = "All Status";
            document.querySelectorAll("#dropdown-menu-status .check-icon").forEach(ci => ci.classList.add("hidden"));
            const stAll = document.querySelector("#dropdown-menu-status .dropdown-item[data-value='all'] .check-icon");
            if (stAll) stAll.classList.remove("hidden");
            updateStatusChipsState("all");

            filterChannel.value = "all";
            labelChannel.textContent = "All Channels";
            document.querySelectorAll("#dropdown-menu-channel .check-icon").forEach(ci => ci.classList.add("hidden"));
            const chAll = document.querySelector("#dropdown-menu-channel .dropdown-item[data-value='all'] .check-icon");
            if (chAll) chAll.classList.remove("hidden");

            fetchWithdrawals();
        });
    }

    // Core AJAX Fetcher
    function fetchWithdrawals() {
        const query = searchInput.value.trim();
        const st = filterStatus.value;
        const ch = filterChannel.value;

        // Toggle UI indicators
        if (searchIcon) searchIcon.classList.add("hidden");
        if (searchSpinner) searchSpinner.classList.remove("hidden");
        if (tableLoading) tableLoading.classList.remove("hidden");

        const params = new URLSearchParams();
        if (query) params.append("search", query);
        if (st && st !== "all") params.append("status", st);
        if (ch && ch !== "all") params.append("channel", ch);

        const url = `{{ route('admin.withdrawals') }}?${params.toString()}`;

        fetch(url, {
            headers: {
                "X-Requested-With": "XMLHttpRequest",
                "Accept": "application/json"
            }
        })
        .then(res => res.json())
        .then(data => {
            tableBody.innerHTML = data.html;
            if (countNumber) countNumber.textContent = data.total_count ?? 0;

            if (data.pagination_html && data.pagination_html.trim() !== "") {
                paginationContainer.innerHTML = data.pagination_html;
                paginationContainer.classList.remove("hidden");
            } else {
                paginationContainer.classList.add("hidden");
            }

            // Re-bind row events
            bindRowActionEvents();
            updateBulkBar();
        })
        .catch(err => {
            console.error("Filter fetch failed:", err);
        })
        .finally(() => {
            if (searchSpinner) searchSpinner.classList.add("hidden");
            if (searchIcon) searchIcon.classList.remove("hidden");
            if (tableLoading) tableLoading.classList.add("hidden");
        });
    }

    // Bulk PDF Selection Handlers
    function updateBulkBar() {
        const itemCheckboxes = document.querySelectorAll(".withdrawal-checkbox");
        const checkedBoxes = document.querySelectorAll(".withdrawal-checkbox:checked");
        const count = checkedBoxes.length;
        if (selectedCountSpan) selectedCountSpan.textContent = count;

        if (count > 0) {
            bulkPdfBar.classList.remove("translate-y-32", "opacity-0");
            bulkPdfBar.classList.add("translate-y-0", "opacity-100");
        } else {
            bulkPdfBar.classList.remove("translate-y-0", "opacity-100");
            bulkPdfBar.classList.add("translate-y-32", "opacity-0");
        }

        if (selectAllCheckbox) {
            const allChecked = itemCheckboxes.length > 0 && Array.from(itemCheckboxes).every(c => c.checked);
            const someChecked = Array.from(itemCheckboxes).some(c => c.checked);
            selectAllCheckbox.checked = allChecked;
            selectAllCheckbox.indeterminate = someChecked && !allChecked;
        }
    }

    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener("change", function () {
            const itemCheckboxes = document.querySelectorAll(".withdrawal-checkbox");
            itemCheckboxes.forEach(cb => {
                cb.checked = selectAllCheckbox.checked;
            });
            updateBulkBar();
        });
    }

    if (btnClearSelection) {
        btnClearSelection.addEventListener("click", function () {
            if (selectAllCheckbox) {
                selectAllCheckbox.checked = false;
                selectAllCheckbox.indeterminate = false;
            }
            document.querySelectorAll(".withdrawal-checkbox").forEach(cb => cb.checked = false);
            updateBulkBar();
        });
    }

    function submitBulkExport() {
        const checkedBoxes = document.querySelectorAll(".withdrawal-checkbox:checked");
        if (checkedBoxes.length === 0) {
            if (alertInstance) {
                alertInstance.fire({
                    icon: 'info',
                    title: 'Select Items',
                    text: 'Please select at least one withdrawal request checkbox to export the manifest PDF.',
                    confirmButtonText: 'Understood'
                });
            } else {
                alert('Please select at least one withdrawal request.');
            }
            return;
        }

        pdfExportForm.innerHTML = "";
        checkedBoxes.forEach(cb => {
            const hiddenInput = document.createElement("input");
            hiddenInput.type = "hidden";
            hiddenInput.name = "ids[]";
            hiddenInput.value = cb.value;
            pdfExportForm.appendChild(hiddenInput);
        });
        pdfExportForm.submit();
    }

    if (btnExportPdf) {
        btnExportPdf.addEventListener("click", submitBulkExport);
    }
    if (btnTriggerManifest) {
        btnTriggerManifest.addEventListener("click", submitBulkExport);
    }

    // Modal & Action Binding
    function bindRowActionEvents() {
        // Individual Checkboxes
        document.querySelectorAll(".withdrawal-checkbox").forEach(cb => {
            cb.addEventListener("change", updateBulkBar);
        });

        // Acknowledge Form Confirmation
        document.querySelectorAll(".form-ack-withdrawal").forEach(form => {
            form.addEventListener("submit", function (e) {
                if (alertInstance) {
                    e.preventDefault();
                    alertInstance.fire({
                        icon: 'question',
                        title: 'Acknowledge Request?',
                        text: 'Are you sure you want to acknowledge this withdrawal request and mark it as in-processing?',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, Acknowledge',
                        cancelButtonText: 'Cancel',
                        iconColor: '#3b82f6'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                }
            });
        });

        // Trigger Release Modal
        document.querySelectorAll(".btn-trigger-release").forEach(btn => {
            btn.addEventListener("click", function () {
                openReleaseModal(this);
            });
        });

        // Trigger Reject Modal
        document.querySelectorAll(".btn-trigger-reject").forEach(btn => {
            btn.addEventListener("click", function () {
                openRejectModal(this);
            });
        });
    }

    function openReleaseModal(btn) {
        const actionRoute = btn.getAttribute("data-action");
        const ref = btn.getAttribute("data-ref");
        const name = btn.getAttribute("data-name");
        const amount = btn.getAttribute("data-amount");

        releaseForm.action = actionRoute;
        releaseModalRef.textContent = ref;
        releaseModalName.textContent = name;
        releaseModalAmount.textContent = amount;
        releaseRemarksInput.value = "";

        releaseModal.classList.remove("hidden");
        setTimeout(() => {
            releaseModalBackdrop.classList.remove("opacity-0");
            releaseModalBackdrop.classList.add("opacity-100");
            releaseModalBox.classList.remove("scale-95", "opacity-0");
            releaseModalBox.classList.add("scale-100", "opacity-100");
            releaseRemarksInput.focus();
        }, 10);
    }

    function closeReleaseModal() {
        releaseModalBox.classList.remove("scale-100", "opacity-100");
        releaseModalBox.classList.add("scale-95", "opacity-0");
        releaseModalBackdrop.classList.remove("opacity-100", "opacity-0");
        releaseModalBackdrop.classList.add("opacity-0");

        setTimeout(() => {
            releaseModal.classList.add("hidden");
        }, 300);
    }

    closeReleaseBtns.forEach(btn => {
        btn.addEventListener("click", closeReleaseModal);
    });

    if (releaseModalBackdrop) {
        releaseModalBackdrop.addEventListener("click", closeReleaseModal);
    }

    function openRejectModal(btn) {
        const actionRoute = btn.getAttribute("data-action");
        const ref = btn.getAttribute("data-ref");
        const name = btn.getAttribute("data-name");
        const amount = btn.getAttribute("data-amount");

        rejectForm.action = actionRoute;
        rejectModalRef.textContent = ref;
        rejectModalName.textContent = name;
        rejectModalAmount.textContent = amount;
        rejectRemarksInput.value = "";

        rejectModal.classList.remove("hidden");
        setTimeout(() => {
            rejectModalBackdrop.classList.remove("opacity-0");
            rejectModalBackdrop.classList.add("opacity-100");
            rejectModalBox.classList.remove("scale-95", "opacity-0");
            rejectModalBox.classList.add("scale-100", "opacity-100");
            rejectRemarksInput.focus();
        }, 10);
    }

    function closeRejectModal() {
        rejectModalBox.classList.remove("scale-100", "opacity-100");
        rejectModalBox.classList.add("scale-95", "opacity-0");
        rejectModalBackdrop.classList.remove("opacity-100", "opacity-0");
        rejectModalBackdrop.classList.add("opacity-0");

        setTimeout(() => {
            rejectModal.classList.add("hidden");
        }, 300);
    }

    closeRejectBtns.forEach(btn => {
        btn.addEventListener("click", closeRejectModal);
    });

    if (rejectModalBackdrop) {
        rejectModalBackdrop.addEventListener("click", closeRejectModal);
    }

    // Immediate Releasing Animation on Form Submit
    if (releaseForm) {
        releaseForm.addEventListener("submit", function () {
            if (!releaseRemarksInput.value.trim()) return;

            closeReleaseModal();
            if (alertInstance) {
                alertInstance.fire({
                    title: 'Disbursing & Finalizing Release...',
                    html: `
                        <div class="flex flex-col items-center justify-center p-4 space-y-4">
                            <div class="relative w-16 h-16 flex items-center justify-center mx-auto">
                                <div class="w-16 h-16 rounded-full border-4 border-emerald-500/20 border-t-emerald-500 animate-spin"></div>
                                <span class="absolute text-2xl text-emerald-600 dark:text-emerald-400 animate-bounce">
                                    <i class="fa-solid fa-money-bill-transfer"></i>
                                </span>
                            </div>
                            <div class="space-y-1.5 text-center">
                                <p class="text-xs font-bold text-slate-800 dark:text-slate-100">
                                    Disbursing savings payout &amp; updating ledger...
                                </p>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 font-medium leading-relaxed">
                                    Please wait while the transaction is committed and the notification email is dispatched.
                                </p>
                            </div>
                        </div>
                    `,
                    showConfirmButton: false,
                    allowOutsideClick: false
                });
            }
        });
    }

    // Immediate Rejection Animation on Form Submit
    if (rejectForm) {
        rejectForm.addEventListener("submit", function () {
            if (!rejectRemarksInput.value.trim()) return;

            closeRejectModal();
            if (alertInstance) {
                alertInstance.fire({
                    title: 'Declining Request & Logging Remarks...',
                    html: `
                        <div class="flex flex-col items-center justify-center p-4 space-y-4">
                            <div class="relative w-16 h-16 flex items-center justify-center mx-auto">
                                <div class="w-16 h-16 rounded-full border-4 border-rose-500/20 border-t-rose-500 animate-spin"></div>
                                <span class="absolute text-2xl text-rose-600 dark:text-rose-400 animate-bounce">
                                    <i class="fa-solid fa-file-circle-xmark"></i>
                                </span>
                            </div>
                            <div class="space-y-1.5 text-center">
                                <p class="text-xs font-bold text-slate-800 dark:text-slate-100">
                                    Recording audit reason &amp; declining request...
                                </p>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 font-medium leading-relaxed">
                                    Please wait while the rejection is recorded and the decline notice email is dispatched.
                                </p>
                            </div>
                        </div>
                    `,
                    showConfirmButton: false,
                    allowOutsideClick: false
                });
            }
        });
    }

    // Initial binding
    bindRowActionEvents();
});
</script>
@endsection
