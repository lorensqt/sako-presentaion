@extends('layouts.admin')

@section('title', 'Loan Approvals Board - Sako Cooperative')
@section('page_title', 'Loan Approvals Board')
@section('page_subtitle', 'Oversee, inspect, and approve cooperative loan applications through sequential organizational review stages.')

@section('content')
<div class="space-y-6 animate-fade-in">

    <!-- My Active Roles & Workloads Hub -->
    <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-100 dark:border-slate-700/80 shadow-sm p-6 space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 pb-3 border-b border-slate-100 dark:border-slate-700/60">
            <div>
                <h3 class="text-xs font-black text-slate-900 dark:text-white tracking-wider uppercase flex items-center gap-1.5">
                    My Active Decision Workspaces
                </h3>
                <p class="text-[10px] text-slate-505 dark:text-slate-400 font-semibold mt-0.5">Dynamic queues assigned to your profile roles</p>
            </div>
            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-emerald-50 dark:bg-emerald-950/20 text-emerald-800 dark:text-emerald-400 text-[10px] font-extrabold border border-emerald-100/40 dark:border-emerald-800/20">
                🔒 Cryptographically Signed Workstation
            </span>
        </div>

        @php
            $inboxGrouped = $myInboxLoans->groupBy('current_stage');
            $allStagesConfig = [
                'sako_staff' => [
                    'name' => 'SAKO Staff Review',
                    'icon_url' => 'https://img.icons8.com/?size=100&id=8NGo_ebaZB64&format=png&color=000000',
                    'color' => 'sky',
                ],
                'hrmd_staff' => [
                    'name' => 'HRMD Verification',
                    'icon_url' => 'https://img.icons8.com/?size=100&id=MkDL506zTrpE&format=png&color=000000',
                    'color' => 'rose',
                ],
                'credit_committee' => [
                    'name' => 'Credit Committee',
                    'icon_url' => 'https://img.icons8.com/?size=100&id=jyDP2XjBiXdD&format=png&color=000000',
                    'color' => 'indigo',
                ],
                'accounting' => [
                    'name' => 'Accounting Computations',
                    'icon_url' => 'https://img.icons8.com/?size=100&id=22462&format=png&color=000000',
                    'color' => 'amber',
                ],
                'releasing_officer' => [
                    'name' => 'Releasing Officer',
                    'icon_url' => 'https://img.icons8.com/?size=100&id=HYdHmi0wO7zZ&format=png&color=000000',
                    'color' => 'teal',
                ],
            ];
        @endphp

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3.5">
            @foreach($allStagesConfig as $slug => $config)
                @php
                    $isActiveUserRole = in_array($slug, $myGroupSlugs);
                    $pendingCount = isset($inboxGrouped[$slug]) ? $inboxGrouped[$slug]->count() : 0;
                @endphp

                @if($isActiveUserRole)
                    <!-- Active Role card -->
                    <div class="relative bg-emerald-500/[0.02] dark:bg-emerald-500/[0.04] border border-emerald-500/25 dark:border-emerald-500/15 rounded-2xl p-4 flex flex-col justify-between min-h-[105px] transition-all hover:scale-[1.01] shadow-sm">
                        <div class="flex items-start justify-between">
                            <img src="{{ $config['icon_url'] }}" alt="{{ $config['name'] }}" class="w-6.5 h-6.5 object-contain">
                            <div class="flex items-center gap-1">
                                @if($slug === 'hrmd_staff')
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded-md bg-amber-500/15 text-amber-700 dark:text-amber-300 text-[8px] font-black tracking-wider uppercase border border-amber-500/30" title="Your Assigned HRMD Approval Sequence">
                                        Seq #{{ auth()->user()->hrmd_sequence ?? 'All' }}
                                    </span>
                                @endif
                                <span class="inline-flex items-center px-1.5 py-0.5 rounded-md bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 text-[8px] font-black tracking-wider uppercase border border-emerald-500/10">Active</span>
                            </div>
                        </div>
                        <div class="mt-2.5">
                            <span class="text-[10px] font-black text-slate-700 dark:text-slate-300 block truncate leading-tight">{{ $config['name'] }}</span>
                            @if($pendingCount > 0)
                                <span class="text-[10.5px] font-black text-rose-600 dark:text-rose-400 mt-1 block flex items-center gap-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500 animate-pulse"></span>
                                    {{ $pendingCount }} Pending Approval{{ $pendingCount > 1 ? 's' : '' }}
                                </span>
                            @else
                                <span class="text-[10.5px] font-black text-emerald-600 dark:text-emerald-400 mt-1 block">
                                    ✓ Queue Cleared
                                </span>
                            @endif
                        </div>
                    </div>
                @else
                    <!-- Inactive Role card -->
                    <div class="relative bg-slate-50/50 dark:bg-slate-900/30 border border-slate-100 dark:border-slate-800 rounded-2xl p-4 flex flex-col justify-between min-h-[105px] opacity-50">
                        <div class="flex items-start justify-between">
                            <img src="{{ $config['icon_url'] }}" alt="{{ $config['name'] }}" class="w-6.5 h-6.5 object-contain filter grayscale opacity-45">
                            <span class="inline-flex items-center px-1.5 py-0.5 rounded-md bg-slate-100 dark:bg-slate-800 text-slate-400 dark:text-slate-500 text-[8px] font-extrabold uppercase border border-slate-200/50 dark:border-slate-700/50">Restricted</span>
                        </div>
                        <div class="mt-2.5">
                            <span class="text-[10px] font-black text-slate-400 dark:text-slate-500 block truncate leading-tight">{{ $config['name'] }}</span>
                            <span class="text-[10.5px] font-semibold text-slate-400 dark:text-slate-500 mt-1 block">Not in My Roles</span>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    </div>

    <!-- Interactive Filter & Command Bar -->
    <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-100 dark:border-slate-700/80 shadow-sm p-4 sm:p-5 space-y-4">
        <!-- Top Row: Interactive Search and Customized Smooth Dropdowns -->
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3">
            
            <!-- Smooth Search Bar with Live Spinner and Instant Clear -->
            <div class="relative flex-1 min-w-[260px] max-w-md">
                <i id="search-icon" class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 dark:text-slate-500 text-xs transition-colors duration-200"></i>
                <input type="text" id="ajax-search" value="{{ $search }}" placeholder="Search borrower name, ID, or LN-XXXXX..." class="w-full pl-9 pr-14 py-2.5 text-xs font-semibold border border-slate-200 dark:border-slate-700/80 bg-slate-50/60 dark:bg-slate-900 text-slate-900 dark:text-slate-100 rounded-xl focus:bg-white dark:focus:bg-slate-900 focus:border-emerald-500 dark:focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 dark:focus:ring-emerald-500/10 placeholder-slate-400 dark:placeholder-slate-500 transition-all outline-none">
                
                <div class="absolute right-3 top-1/2 -translate-y-1/2 flex items-center gap-1.5">
                    <i id="search-spinner" class="fa-solid fa-circle-notch fa-spin text-emerald-500 text-xs hidden"></i>
                    <button type="button" id="btn-clear-search" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 text-xs cursor-pointer p-0.5 {{ $search ? '' : 'hidden' }}" title="Clear search">
                        <i class="fa-solid fa-circle-xmark"></i>
                    </button>
                </div>
            </div>

            <!-- Customized Smooth Dropdowns -->
            <div class="flex flex-wrap items-center gap-2.5">
                
                <!-- Stage Custom Dropdown -->
                <div class="relative custom-dropdown" id="dropdown-wrapper-stage">
                    <input type="hidden" id="filter-stage" value="{{ $stage ?: 'all' }}">
                    <button type="button" id="dropdown-btn-stage" class="inline-flex items-center gap-2 px-3.5 py-2.5 text-xs font-semibold border border-slate-200 dark:border-slate-700/80 bg-slate-50/70 dark:bg-slate-900 text-slate-800 dark:text-slate-200 rounded-xl hover:border-emerald-500/50 hover:bg-white dark:hover:bg-slate-800 transition-all cursor-pointer select-none">
                        <i class="fa-solid fa-bars-progress text-slate-400 text-xs"></i>
                        <span id="label-stage" class="truncate max-w-[120px]">
                            {{ $stage && $stage !== 'all' ? ucwords(str_replace('_', ' ', $stage)) : 'All Stages' }}
                        </span>
                        <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 transition-transform duration-200 dropdown-arrow"></i>
                    </button>
                    <!-- Floating Menu -->
                    <div id="dropdown-menu-stage" class="absolute top-full left-0 mt-1.5 w-52 z-40 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl shadow-xl shadow-slate-900/10 dark:shadow-slate-950/40 p-1.5 space-y-0.5 transform opacity-0 scale-95 pointer-events-none transition-all duration-200 ease-out origin-top-left">
                        <div class="dropdown-item px-3 py-2 text-xs font-semibold rounded-xl text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700/70 cursor-pointer flex items-center justify-between transition-colors" data-value="all">
                            <span>All Stages</span>
                            <i class="fa-solid fa-check text-emerald-600 dark:text-emerald-400 text-[11px] check-icon {{ (!$stage || $stage === 'all') ? '' : 'hidden' }}"></i>
                        </div>
                        <div class="dropdown-item px-3 py-2 text-xs font-semibold rounded-xl text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700/70 cursor-pointer flex items-center justify-between transition-colors" data-value="comakers">
                            <span>Co-Makers</span>
                            <i class="fa-solid fa-check text-emerald-600 dark:text-emerald-400 text-[11px] check-icon {{ $stage === 'comakers' ? '' : 'hidden' }}"></i>
                        </div>
                        <div class="dropdown-item px-3 py-2 text-xs font-semibold rounded-xl text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700/70 cursor-pointer flex items-center justify-between transition-colors" data-value="sako_staff">
                            <span>SAKO Staff Review</span>
                            <i class="fa-solid fa-check text-emerald-600 dark:text-emerald-400 text-[11px] check-icon {{ $stage === 'sako_staff' ? '' : 'hidden' }}"></i>
                        </div>
                        <div class="dropdown-item px-3 py-2 text-xs font-semibold rounded-xl text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700/70 cursor-pointer flex items-center justify-between transition-colors" data-value="hrmd_staff">
                            <span>HRMD Verification</span>
                            <i class="fa-solid fa-check text-emerald-600 dark:text-emerald-400 text-[11px] check-icon {{ $stage === 'hrmd_staff' ? '' : 'hidden' }}"></i>
                        </div>
                        <div class="dropdown-item px-3 py-2 text-xs font-semibold rounded-xl text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700/70 cursor-pointer flex items-center justify-between transition-colors" data-value="credit_committee">
                            <span>Credit Committee</span>
                            <i class="fa-solid fa-check text-emerald-600 dark:text-emerald-400 text-[11px] check-icon {{ $stage === 'credit_committee' ? '' : 'hidden' }}"></i>
                        </div>
                        <div class="dropdown-item px-3 py-2 text-xs font-semibold rounded-xl text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700/70 cursor-pointer flex items-center justify-between transition-colors" data-value="accounting">
                            <span>Accounting</span>
                            <i class="fa-solid fa-check text-emerald-600 dark:text-emerald-400 text-[11px] check-icon {{ $stage === 'accounting' ? '' : 'hidden' }}"></i>
                        </div>
                        <div class="dropdown-item px-3 py-2 text-xs font-semibold rounded-xl text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700/70 cursor-pointer flex items-center justify-between transition-colors" data-value="releasing_officer">
                            <span>Releasing Officer</span>
                            <i class="fa-solid fa-check text-emerald-600 dark:text-emerald-400 text-[11px] check-icon {{ $stage === 'releasing_officer' ? '' : 'hidden' }}"></i>
                        </div>
                    </div>
                </div>

                <!-- Status Custom Dropdown -->
                <div class="relative custom-dropdown" id="dropdown-wrapper-status">
                    <input type="hidden" id="filter-status" value="{{ $status ?: 'all' }}">
                    <button type="button" id="dropdown-btn-status" class="inline-flex items-center gap-2 px-3.5 py-2.5 text-xs font-semibold border border-slate-200 dark:border-slate-700/80 bg-slate-50/70 dark:bg-slate-900 text-slate-800 dark:text-slate-200 rounded-xl hover:border-emerald-500/50 hover:bg-white dark:hover:bg-slate-800 transition-all cursor-pointer select-none">
                        <i class="fa-solid fa-toggle-on text-slate-400 text-xs"></i>
                        <span id="label-status" class="truncate max-w-[100px]">
                            {{ $status === 'pending' ? 'Pending' : ($status === 'approved' ? 'Approved' : ($status === 'rejected' ? 'Rejected' : 'All Status')) }}
                        </span>
                        <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 transition-transform duration-200 dropdown-arrow"></i>
                    </button>
                    <!-- Floating Menu -->
                    <div id="dropdown-menu-status" class="absolute top-full left-0 mt-1.5 w-44 z-40 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl shadow-xl shadow-slate-900/10 dark:shadow-slate-950/40 p-1.5 space-y-0.5 transform opacity-0 scale-95 pointer-events-none transition-all duration-200 ease-out origin-top-left">
                        <div class="dropdown-item px-3 py-2 text-xs font-semibold rounded-xl text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700/70 cursor-pointer flex items-center justify-between transition-colors" data-value="all">
                            <span>All Status</span>
                            <i class="fa-solid fa-check text-emerald-600 dark:text-emerald-400 text-[11px] check-icon {{ (!$status || $status === 'all') ? '' : 'hidden' }}"></i>
                        </div>
                        <div class="dropdown-item px-3 py-2 text-xs font-semibold rounded-xl text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700/70 cursor-pointer flex items-center justify-between transition-colors" data-value="pending">
                            <span>Pending</span>
                            <i class="fa-solid fa-check text-emerald-600 dark:text-emerald-400 text-[11px] check-icon {{ $status === 'pending' ? '' : 'hidden' }}"></i>
                        </div>
                        <div class="dropdown-item px-3 py-2 text-xs font-semibold rounded-xl text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700/70 cursor-pointer flex items-center justify-between transition-colors" data-value="approved">
                            <span>Approved / Released</span>
                            <i class="fa-solid fa-check text-emerald-600 dark:text-emerald-400 text-[11px] check-icon {{ $status === 'approved' ? '' : 'hidden' }}"></i>
                        </div>
                        <div class="dropdown-item px-3 py-2 text-xs font-semibold rounded-xl text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700/70 cursor-pointer flex items-center justify-between transition-colors" data-value="rejected">
                            <span>Rejected</span>
                            <i class="fa-solid fa-check text-emerald-600 dark:text-emerald-400 text-[11px] check-icon {{ $status === 'rejected' ? '' : 'hidden' }}"></i>
                        </div>
                    </div>
                </div>

                <!-- Category Custom Dropdown -->
                <div class="relative custom-dropdown" id="dropdown-wrapper-category">
                    <input type="hidden" id="filter-category" value="{{ $category ?: 'all' }}">
                    <button type="button" id="dropdown-btn-category" class="inline-flex items-center gap-2 px-3.5 py-2.5 text-xs font-semibold border border-slate-200 dark:border-slate-700/80 bg-slate-50/70 dark:bg-slate-900 text-slate-800 dark:text-slate-200 rounded-xl hover:border-emerald-500/50 hover:bg-white dark:hover:bg-slate-800 transition-all cursor-pointer select-none">
                        <i class="fa-solid fa-layer-group text-slate-400 text-xs"></i>
                        <span id="label-category" class="truncate max-w-[110px]">
                            {{ $category && $category !== 'all' ? ucfirst(str_replace('_', ' ', $category)) : 'All Categories' }}
                        </span>
                        <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 transition-transform duration-200 dropdown-arrow"></i>
                    </button>
                    <!-- Floating Menu -->
                    <div id="dropdown-menu-category" class="absolute top-full left-0 mt-1.5 w-48 z-40 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl shadow-xl shadow-slate-900/10 dark:shadow-slate-950/40 p-1.5 space-y-0.5 transform opacity-0 scale-95 pointer-events-none transition-all duration-200 ease-out origin-top-left">
                        <div class="dropdown-item px-3 py-2 text-xs font-semibold rounded-xl text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700/70 cursor-pointer flex items-center justify-between transition-colors" data-value="all">
                            <span>All Categories</span>
                            <i class="fa-solid fa-check text-emerald-600 dark:text-emerald-400 text-[11px] check-icon {{ (!$category || $category === 'all') ? '' : 'hidden' }}"></i>
                        </div>
                        <div class="dropdown-item px-3 py-2 text-xs font-semibold rounded-xl text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700/70 cursor-pointer flex items-center justify-between transition-colors" data-value="regular">
                            <span>Regular</span>
                            <i class="fa-solid fa-check text-emerald-600 dark:text-emerald-400 text-[11px] check-icon {{ $category === 'regular' ? '' : 'hidden' }}"></i>
                        </div>
                        <div class="dropdown-item px-3 py-2 text-xs font-semibold rounded-xl text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700/70 cursor-pointer flex items-center justify-between transition-colors" data-value="commodity">
                            <span>Commodity</span>
                            <i class="fa-solid fa-check text-emerald-600 dark:text-emerald-400 text-[11px] check-icon {{ $category === 'commodity' ? '' : 'hidden' }}"></i>
                        </div>
                        <div class="dropdown-item px-3 py-2 text-xs font-semibold rounded-xl text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700/70 cursor-pointer flex items-center justify-between transition-colors" data-value="special">
                            <span>Special</span>
                            <i class="fa-solid fa-check text-emerald-600 dark:text-emerald-400 text-[11px] check-icon {{ $category === 'special' ? '' : 'hidden' }}"></i>
                        </div>
                        <div class="dropdown-item px-3 py-2 text-xs font-semibold rounded-xl text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700/70 cursor-pointer flex items-center justify-between transition-colors" data-value="bonus_buyout">
                            <span>Bonus Buyout</span>
                            <i class="fa-solid fa-check text-emerald-600 dark:text-emerald-400 text-[11px] check-icon {{ $category === 'bonus_buyout' ? '' : 'hidden' }}"></i>
                        </div>
                        <div class="dropdown-item px-3 py-2 text-xs font-semibold rounded-xl text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700/70 cursor-pointer flex items-center justify-between transition-colors" data-value="emergency">
                            <span>Emergency</span>
                            <i class="fa-solid fa-check text-emerald-600 dark:text-emerald-400 text-[11px] check-icon {{ $category === 'emergency' ? '' : 'hidden' }}"></i>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- Bottom Row: Quick Filter Stage Chips, Active Counter, and Reset -->
        <div class="flex flex-wrap items-center justify-between gap-2 pt-3 border-t border-slate-100 dark:border-slate-700/80">
            <div class="flex flex-wrap items-center gap-1.5" id="quick-stage-pills">
                <span class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mr-1">Quick Stage:</span>
                <button type="button" data-stage="all" class="stage-chip px-2.5 py-1 rounded-lg text-[10px] font-bold border transition-all cursor-pointer bg-slate-900 text-white border-slate-900 dark:bg-white dark:text-slate-900 dark:border-white shadow-2xs">All</button>
                <button type="button" data-stage="sako_staff" class="stage-chip px-2.5 py-1 rounded-lg text-[10px] font-bold border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 hover:border-emerald-500 hover:text-emerald-600 dark:hover:text-emerald-400 transition-all cursor-pointer">SAKO Staff</button>
                <button type="button" data-stage="hrmd_staff" class="stage-chip px-2.5 py-1 rounded-lg text-[10px] font-bold border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 hover:border-amber-500 hover:text-amber-600 dark:hover:text-amber-400 transition-all cursor-pointer">HRMD</button>
                <button type="button" data-stage="credit_committee" class="stage-chip px-2.5 py-1 rounded-lg text-[10px] font-bold border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 hover:border-indigo-500 hover:text-indigo-600 dark:hover:text-indigo-400 transition-all cursor-pointer">Credit Comm.</button>
                <button type="button" data-stage="accounting" class="stage-chip px-2.5 py-1 rounded-lg text-[10px] font-bold border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 hover:border-teal-500 hover:text-teal-600 dark:hover:text-teal-400 transition-all cursor-pointer">Accounting</button>
                <button type="button" data-stage="releasing_officer" class="stage-chip px-2.5 py-1 rounded-lg text-[10px] font-bold border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 hover:border-emerald-500 hover:text-emerald-600 dark:hover:text-emerald-400 transition-all cursor-pointer">Releasing</button>
            </div>
            
            <div class="flex items-center gap-3">
                <span id="results-count-badge" class="text-[11px] font-bold px-2.5 py-1 rounded-xl bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 border border-slate-200/80 dark:border-slate-600">
                    {{ $myInboxLoans->count() }} In Inbox
                </span>
                <button type="button" id="btn-reset-filters" class="text-[11px] font-bold text-slate-500 hover:text-rose-600 dark:text-slate-400 dark:hover:text-rose-400 transition-colors flex items-center gap-1.5 cursor-pointer">
                    <i class="fa-solid fa-arrows-rotate text-[10px]"></i>
                    <span>Reset Filters</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Ledger Tabs Wrapper -->
    <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-100 dark:border-slate-700/80 shadow-sm overflow-hidden flex flex-col">
        
        <!-- Tab Headers (Segmented Control style) -->
        <div class="flex border-b border-slate-100 dark:border-slate-700/80 bg-slate-50 dark:bg-slate-900/40 p-2 gap-2 overflow-x-auto">
            <button id="tab-inbox" class="tab-btn active px-4 py-2.5 rounded-xl font-bold text-xs transition-all duration-200 text-emerald-800 dark:text-emerald-400 bg-white dark:bg-slate-700 shadow-sm border border-slate-100 dark:border-slate-700 whitespace-nowrap cursor-pointer flex items-center gap-1.5 hover:scale-[1.01]">
                <i class="fa-solid fa-inbox text-xs"></i>
                <span>Awaiting My Group's Action (<span id="inbox-tab-count">{{ $myInboxLoans->count() }}</span>)</span>
            </button>
            <button id="tab-all" class="tab-btn px-4 py-2.5 rounded-xl font-bold text-xs text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200 hover:bg-white/60 dark:hover:bg-slate-700/40 transition-all duration-200 whitespace-nowrap cursor-pointer flex items-center gap-1.5 hover:scale-[1.01]">
                <i class="fa-solid fa-network-wired text-xs"></i>
                <span>All Cooperative Pipelines (<span id="all-tab-count">{{ $allLoans->total() }}</span>)</span>
            </button>
        </div>

        <!-- TAB CONTENT: MY GROUP INBOX -->
        <div id="content-inbox" class="tab-panel transition-opacity duration-300 animate-fade-in">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-slate-900/50 border-b border-slate-100 dark:border-slate-700/80 text-[10px] font-extrabold text-slate-400 dark:text-slate-500 uppercase tracking-widest">
                            <th class="px-6 py-4.5">Borrower Profile</th>
                            <th class="px-6 py-4.5">Loan Type</th>
                            <th class="px-6 py-4.5">Requested Amount</th>
                            <th class="px-6 py-4.5">Ledger</th>
                            <th class="px-6 py-4.5">Schedule</th>
                            <th class="px-6 py-4.5">Awaiting Verification</th>
                            <th class="px-6 py-4.5">Submitted Date</th>
                            <th class="px-6 py-4.5 text-right">Evaluation</th>
                        </tr>
                    </thead>
                    <tbody id="inbox-table-body" class="divide-y divide-slate-50 dark:divide-slate-700/50 text-xs font-medium text-slate-700 dark:text-slate-300">
                        @include('admin.partials.loan-approvals-inbox-rows')
                    </tbody>
                </table>
            </div>
        </div>

        <!-- TAB CONTENT: ALL COOPERATIVE PIPELINES -->
        <div id="content-all" class="tab-panel hidden transition-opacity duration-300">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-slate-900/50 border-b border-slate-100 dark:border-slate-700/80 text-[10px] font-extrabold text-slate-400 dark:text-slate-500 uppercase tracking-widest">
                            <th class="px-6 py-4.5">Borrower Profile</th>
                            <th class="px-6 py-4.5">Loan Type</th>
                            <th class="px-6 py-4.5">Requested Amount</th>
                            <th class="px-6 py-4.5">Ledger</th>
                            <th class="px-6 py-4.5">Schedule</th>
                            <th class="px-6 py-4.5">Current Stage</th>
                            <th class="px-6 py-4.5">Status</th>
                            <th class="px-6 py-4.5">Submitted Date</th>
                            <th class="px-6 py-4.5 text-right">Details</th>
                        </tr>
                    </thead>
                    <tbody id="all-table-body" class="divide-y divide-slate-50 dark:divide-slate-700/50 text-xs font-medium text-slate-700 dark:text-slate-300">
                        @include('admin.partials.loan-approvals-all-rows')
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination Links -->
            <div id="all-loans-pagination">
                @if($allLoans->hasPages())
                    <div class="px-6 py-4 bg-slate-50 dark:bg-slate-900/50 border-t border-slate-100 dark:border-slate-700/80">
                        {{ $allLoans->links() }}
                    </div>
                @endif
            </div>
        </div>

    </div>
</div>

@include('admin.partials.review-loan-modal')
@include('admin.partials.pdf-viewer-modal')

@endsection

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function () {
        
        // Parse active roles slugs and user context
        const myGroupSlugs = @json($myGroupSlugs);
        const currentAdminRole = @json(auth()->user()->role);
        const currentAdminHrmdSequence = @json(auth()->user()->hrmd_sequence);

        // Tab Switching logic
        const tabInbox = document.getElementById("tab-inbox");
        const tabAll = document.getElementById("tab-all");
        const panelInbox = document.getElementById("content-inbox");
        const panelAll = document.getElementById("content-all");

        tabInbox.addEventListener("click", function() {
            setTabActive(this, panelInbox);
            setTabInactive(tabAll, panelAll);
        });

        tabAll.addEventListener("click", function() {
            setTabActive(this, panelAll);
            setTabInactive(tabInbox, panelInbox);
        });

        function setTabActive(btn, panel) {
            btn.classList.add("active", "text-emerald-800", "dark:text-emerald-400", "bg-white", "dark:bg-slate-700", "shadow-sm", "border", "border-slate-100", "dark:border-slate-700");
            btn.classList.remove("text-slate-500", "dark:text-slate-400", "hover:text-slate-900", "dark:hover:text-slate-200", "hover:bg-white/60", "dark:hover:bg-slate-700/40");
            panel.classList.remove("hidden");
            // Trigger animation
            setTimeout(() => { panel.classList.add("opacity-100"); }, 50);
        }

        function setTabInactive(btn, panel) {
            btn.classList.remove("active", "text-emerald-800", "dark:text-emerald-400", "bg-white", "dark:bg-slate-700", "shadow-sm", "border", "border-slate-100", "dark:border-slate-700");
            btn.classList.add("text-slate-500", "dark:text-slate-400", "hover:text-slate-900", "dark:hover:text-slate-200", "hover:bg-white/60", "dark:hover:bg-slate-700/40");
            panel.classList.add("hidden");
            panel.classList.remove("opacity-100");
        }

        function openModal(modalId) {
            const modal = document.getElementById(modalId);
            const overlay = modal.querySelector(".modal-overlay");
            const container = modal.querySelector(".modal-container");
            
            modal.classList.remove("hidden");
            modal.classList.add("flex");
            
            // Allow browser layout pass to register classes
            setTimeout(() => {
                if (overlay) {
                    overlay.classList.remove("opacity-0", "pointer-events-none");
                    overlay.classList.add("opacity-100", "pointer-events-auto");
                }
                
                if (container) {
                    // Slide fullscreen modal in beautifully (scale + opacity fade)
                    container.classList.remove("scale-95", "opacity-0");
                    container.classList.add("scale-100", "opacity-100");
                }
            }, 50);
        }

        function closeModal(modalId) {
            const modal = document.getElementById(modalId);
            const overlay = modal.querySelector(".modal-overlay");
            const container = modal.querySelector(".modal-container");
            
            if (overlay) {
                overlay.classList.add("opacity-0", "pointer-events-none");
                overlay.classList.remove("opacity-100", "pointer-events-auto");
            }
            
            if (container) {
                // Return to scale 95 and fade out
                container.classList.add("scale-95", "opacity-0");
                container.classList.remove("scale-100", "opacity-100");
            }
            
            // Hide the wrapper after animation completes
            setTimeout(() => {
                modal.classList.add("hidden");
                modal.classList.remove("flex");
            }, 300);
        }

        // Setup click listeners for cancel/close controls
        document.querySelectorAll(".modal-close, .modal-overlay").forEach(btn => {
            btn.addEventListener("click", function() {
                const modal = this.closest('[id^="modal-"]');
                if (modal) closeModal(modal.id);
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

            // Show loader
            if (pdfLoader) pdfLoader.classList.remove("opacity-0", "pointer-events-none");
            
            // Set source
            if (pdfIframe) {
                pdfIframe.src = targetUrl + '#toolbar=1&navpanes=0';
                pdfIframe.onload = function() {
                    setTimeout(() => {
                        if (pdfLoader) pdfLoader.classList.add("opacity-0", "pointer-events-none");
                    }, 250);
                };
            }

            openModal("modal-pdf-viewer");
        }

        function closePdfPreview() {
            closeModal("modal-pdf-viewer");
            setTimeout(() => {
                if (pdfIframe) pdfIframe.src = "about:blank";
            }, 300);
        }

        if (btnClosePdf) {
            btnClosePdf.addEventListener("click", closePdfPreview);
        }

        if (backdropPdf) {
            backdropPdf.addEventListener("click", closePdfPreview);
        }

        // ESC Key handling for stacked modals and custom dropdowns
        document.addEventListener("keydown", function(e) {
            if (e.key === "Escape") {
                // Close custom dropdowns
                document.querySelectorAll("[id^='dropdown-menu']").forEach(menu => {
                    menu.classList.add("opacity-0", "scale-95", "pointer-events-none");
                    menu.classList.remove("opacity-100", "scale-100", "pointer-events-auto");
                    const arrow = menu.closest(".custom-dropdown")?.querySelector(".dropdown-arrow");
                    if (arrow) arrow.classList.remove("rotate-180");
                });

                if (modalPdf && !modalPdf.classList.contains("hidden")) {
                    closePdfPreview();
                    e.stopPropagation();
                    return;
                }
                const reviewModal = document.getElementById("modal-review");
                if (reviewModal && !reviewModal.classList.contains("hidden")) {
                    closeModal("modal-review");
                }
            }
        });

        // ==========================================
        // REVIEW MODAL POPULATOR
        // ==========================================
        function populateAndOpenReviewModal(loan) {
            // Populate Borrower dossier
            document.getElementById("view-borrower-name").textContent = loan.borrower_name;
            document.getElementById("view-borrower-email").textContent = loan.borrower_email;
            document.getElementById("view-company-id").textContent = loan.borrower_company_id || 'N/A';
            document.getElementById("view-address").textContent = loan.borrower_address || 'N/A';
            document.getElementById("view-address").title = loan.borrower_address || 'N/A';
            document.getElementById("view-borrower-avatar").textContent = (loan.borrower_name ? loan.borrower_name.substring(0, 1).toUpperCase() : '--');

            // Populate loan details
            document.getElementById("view-amount").textContent = loan.amount;
            document.getElementById("view-type-name").textContent = loan.type_name + " (" + loan.category + ")";
            document.getElementById("view-term").textContent = loan.term;
            document.getElementById("view-member-remarks").textContent = loan.form_data.member_remarks || 'No special remarks provided.';

            // Populate Submitted Documents
            const docsContainer = document.getElementById("view-documents-container");
            const docsCountBadge = document.getElementById("view-docs-count-badge");
            const docs = loan.documents || [];

            if (docsCountBadge) {
                docsCountBadge.textContent = `${docs.length} File${docs.length === 1 ? '' : 's'}`;
            }

            if (docsContainer) {
                docsContainer.innerHTML = "";
                if (docs.length > 0) {
                    docs.forEach(doc => {
                        const docDiv = document.createElement("div");
                        docDiv.className = "flex items-center justify-between p-3 bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800/80 rounded-xl shadow-3xs hover:border-emerald-300 dark:hover:border-emerald-800 transition-all";
                        docDiv.innerHTML = `
                            <div class="flex items-center gap-2.5 min-w-0 pr-2">
                                <div class="w-7 h-7 rounded-lg bg-rose-500/10 text-rose-600 dark:text-rose-400 flex items-center justify-center flex-shrink-0 text-[10px] font-black">
                                    PDF
                                </div>
                                <div class="min-w-0 truncate">
                                    <span class="block text-xs font-bold text-slate-800 dark:text-slate-200 truncate" title="${doc.original_name}">${doc.original_name}</span>
                                    <span class="text-[9.5px] text-slate-400 font-mono">${doc.file_size}</span>
                                </div>
                            </div>
                            <button type="button" class="btn-preview-pdf inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 dark:bg-emerald-950/40 dark:hover:bg-emerald-900/50 dark:text-emerald-400 rounded-lg text-[10px] font-extrabold flex-shrink-0 transition-all cursor-pointer shadow-3xs"
                                data-url="${doc.file_url}" 
                                data-name="${doc.original_name}" 
                                data-size="${doc.file_size}">
                                <i class="fa-solid fa-eye text-xs"></i>
                                <span>Preview</span>
                            </button>
                        `;
                        docsContainer.appendChild(docDiv);
                    });

                    // Attach preview click listeners
                    docsContainer.querySelectorAll(".btn-preview-pdf").forEach(pBtn => {
                        pBtn.addEventListener("click", function(e) {
                            e.preventDefault();
                            e.stopPropagation();
                            openPdfPreview(this.dataset.url, this.dataset.name, this.dataset.size);
                        });
                    });
                } else {
                    docsContainer.innerHTML = `
                        <div class="p-3 bg-slate-50 dark:bg-slate-950/40 border border-slate-100 dark:border-slate-800/50 rounded-xl text-center">
                            <p class="text-[10.5px] text-slate-400 italic">No attached compliance documents</p>
                        </div>
                    `;
                }
            }

            // Build Complete Dynamic Stepper Vertical Timeline
            const timelineContainer = document.getElementById("view-history-timeline");
            timelineContainer.innerHTML = "";

            if (!loan.workflow_steps || loan.workflow_steps.length === 0) {
                timelineContainer.innerHTML = '<div class="text-center w-full py-12 text-slate-400 dark:text-slate-500 font-semibold italic text-xs">This application has no sequential workflow path.</div>';
            } else {
                loan.workflow_steps.forEach((step, idx) => {
                    let iconHtml = "";
                    let stepBgClass = "";
                    let stepBorderClass = "";
                    let labelColorClass = "";
                    let statusBadgeHtml = "";
                    let detailsHtml = "";

                    switch (step.status) {
                        case 'approved':
                        case 'completed':
                            iconHtml = `<i class="fa-solid fa-check text-emerald-600 dark:text-emerald-400 text-sm"></i>`;
                            stepBgClass = "bg-emerald-50 dark:bg-emerald-950/25 text-emerald-600 dark:text-emerald-400";
                            stepBorderClass = "border-emerald-100 dark:border-emerald-900/30";
                            labelColorClass = "text-slate-900 dark:text-slate-100 font-bold";
                            statusBadgeHtml = `<span class="text-[9px] font-extrabold uppercase tracking-wider px-2 py-0.5 rounded bg-emerald-50 dark:bg-emerald-950/45 text-emerald-700 dark:text-emerald-400 border border-emerald-100/60 dark:border-emerald-900/20 shadow-3xs">Approved</span>`;
                            detailsHtml = `
                                <p class="text-[10px] text-slate-400 dark:text-slate-500 mt-0.5">By: <span class="font-bold text-slate-700 dark:text-slate-300">${step.actor || 'System/Staff'}</span></p>
                                ${step.remarks ? `<p class="text-[10.5px] text-slate-600 dark:text-slate-350 italic mt-1.5 p-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl shadow-3xs font-sans leading-relaxed">"${step.remarks}"</p>` : ''}
                                <p class="text-[9px] text-slate-400 dark:text-slate-500 mt-1.5 text-right font-semibold font-mono">${step.date}</p>
                            `;
                            break;
                        case 'rejected':
                            iconHtml = `<i class="fa-solid fa-xmark text-rose-600 dark:text-rose-400 text-sm"></i>`;
                            stepBgClass = "bg-rose-50 dark:bg-rose-950/25 text-rose-600 dark:text-rose-400 border border-rose-100/30 dark:border-rose-900/20";
                            stepBorderClass = "border-rose-100 dark:border-rose-900/30";
                            labelColorClass = "text-rose-700 dark:text-rose-400 font-bold";
                            statusBadgeHtml = `<span class="text-[9px] font-extrabold uppercase tracking-wider px-2 py-0.5 rounded bg-rose-50 dark:bg-rose-950/45 text-rose-700 dark:text-rose-400 border border-rose-100/60 dark:border-rose-900/20 shadow-3xs">Rejected</span>`;
                            detailsHtml = `
                                <p class="text-[10px] text-slate-400 dark:text-slate-500 mt-0.5">By: <span class="font-bold text-slate-700 dark:text-slate-300">${step.actor || 'System/Staff'}</span></p>
                                ${step.remarks ? `<p class="text-[10.5px] text-rose-600 dark:text-rose-400/80 italic mt-1.5 p-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl shadow-3xs font-sans leading-relaxed">"${step.remarks}"</p>` : ''}
                                <p class="text-[9px] text-slate-400 dark:text-slate-500 mt-1.5 text-right font-semibold font-mono">${step.date}</p>
                            `;
                            break;
                        case 'skipped':
                            iconHtml = `<i class="fa-solid fa-forward text-slate-400 dark:text-slate-500 text-xs"></i>`;
                            stepBgClass = "bg-slate-100 dark:bg-slate-800/40 text-slate-400 dark:text-slate-500";
                            stepBorderClass = "border-slate-200 dark:border-slate-800";
                            labelColorClass = "text-slate-400 dark:text-slate-500 line-through font-medium";
                            statusBadgeHtml = `<span class="text-[9px] font-bold uppercase tracking-wider px-2 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 border border-slate-200/40 dark:border-slate-700/50 shadow-3xs">Skipped</span>`;
                            detailsHtml = `<p class="text-[10px] text-slate-400 dark:text-slate-550 mt-1 italic leading-relaxed">Verification stage automatically skipped per rules.</p>`;
                            break;
                        case 'current':
                            iconHtml = `<span class="flex h-2.5 w-2.5 relative"><span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-sky-400 opacity-75"></span><span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-sky-500"></span></span>`;
                            stepBgClass = "bg-sky-50 dark:bg-sky-950/25 border-2 border-sky-400/80 text-sky-500";
                            stepBorderClass = "border-sky-200 dark:border-sky-800";
                            labelColorClass = "text-slate-900 dark:text-slate-100 font-extrabold";
                            statusBadgeHtml = `<span class="text-[9px] font-extrabold uppercase tracking-wider px-2 py-0.5 rounded bg-sky-50 dark:bg-sky-950/45 text-sky-700 dark:text-sky-400 border border-sky-100/60 dark:border-sky-900/20 shadow-3xs">Active Stage</span>`;
                            detailsHtml = `<p class="text-[10px] text-slate-500 dark:text-slate-400 mt-1 font-semibold leading-relaxed flex items-center gap-1"><span class="inline-block w-1.5 h-1.5 rounded-full bg-sky-500 animate-pulse"></span> Awaiting specialist sign-off.</p>`;
                            break;
                        case 'cancelled':
                            iconHtml = `<i class="fa-solid fa-ban text-rose-400 dark:text-slate-500 text-xs"></i>`;
                            stepBgClass = "bg-rose-50/10 dark:bg-slate-900/40 text-rose-350 dark:text-slate-600";
                            stepBorderClass = "border-rose-100/30 dark:border-slate-800 border-dashed";
                            labelColorClass = "text-slate-400 dark:text-slate-500 font-medium";
                            statusBadgeHtml = `<span class="text-[9px] font-bold uppercase tracking-wider px-2 py-0.5 rounded bg-rose-50/15 dark:bg-slate-900 text-rose-900/40 dark:text-slate-600 border border-rose-100/20 dark:border-rose-800 shadow-3xs">Cancelled</span>`;
                            detailsHtml = `<p class="text-[10px] text-slate-400 dark:text-slate-500 mt-1 italic font-sans">Workflow terminated prior to this stage.</p>`;
                            break;
                        case 'pending':
                        default:
                            iconHtml = `<span class="w-2 h-2 rounded-full bg-slate-300 dark:bg-slate-600"></span>`;
                            stepBgClass = "bg-slate-50/30 dark:bg-slate-900/20 text-slate-400 dark:text-slate-500";
                            stepBorderClass = "border-slate-100 dark:border-slate-800 border-dashed";
                            labelColorClass = "text-slate-400 dark:text-slate-500 font-semibold";
                            statusBadgeHtml = `<span class="text-[9px] font-bold uppercase tracking-wider px-2 py-0.5 rounded bg-slate-50 dark:bg-slate-900/50 text-slate-400 dark:text-slate-500 border border-slate-100 dark:border-slate-800/60 shadow-3xs">Pending</span>`;
                            detailsHtml = `<p class="text-[10px] text-slate-400 dark:text-slate-500 mt-1 italic font-sans">Upcoming sequential stage.</p>`;
                            break;
                    }

                    const stepBlock = document.createElement("div");
                    stepBlock.className = "relative flex items-start gap-4";
                    
                    stepBlock.innerHTML = `
                        <!-- Node Circle -->
                        <div class="w-9 h-9 rounded-full ${stepBgClass} flex items-center justify-center flex-shrink-0 border shadow-2xs z-10 bg-white dark:bg-slate-900">
                            ${iconHtml}
                        </div>
                        <!-- Card Panel -->
                        <div class="flex-grow bg-white dark:bg-slate-900 border ${stepBorderClass} p-4 rounded-2xl text-[11px] leading-relaxed transition-all hover:bg-slate-50/50 dark:hover:bg-slate-900/45 duration-200 shadow-sm flex flex-col gap-1.5">
                            <div class="flex items-center justify-between gap-3">
                                <p class="${labelColorClass} text-xs tracking-tight truncate font-bold" title="${step.label}">${step.label}</p>
                                <div class="flex-shrink-0">${statusBadgeHtml}</div>
                            </div>
                            <div class="pt-2 border-t border-slate-100 dark:border-slate-800/60">
                                ${detailsHtml}
                            </div>
                        </div>
                    `;

                    timelineContainer.appendChild(stepBlock);
                });
            }

            // Show action signatory box only if it's currently sitting at the user's role stage!
            const actionPanel = document.getElementById("review-action-panel");
            const infoPanel = document.getElementById("review-info-panel");
            const formAction = document.getElementById("form-action");
            const txtRemarks = document.getElementById("action-remarks");

            // Update dynamic PDF link in Info Panel
            const pdfBtn = document.getElementById("btn-export-pdf");
            if (pdfBtn) {
                pdfBtn.href = `/admin/loans/${loan.id}/pdf`;
            }

            // Update active stage label in Info Panel
            let activeStageLabel = "Completed";
            if (loan.workflow_steps && loan.workflow_steps.length > 0) {
                const activeStep = loan.workflow_steps.find(s => s.status === 'current');
                if (activeStep) {
                    activeStageLabel = activeStep.label;
                }
            }
            const infoCurrentStage = document.getElementById("info-current-stage");
            if (infoCurrentStage) {
                infoCurrentStage.textContent = activeStageLabel;
            }

            // Handle Accounting Compliance Files (Ledger & Schedule)
            const accountingDocsBlock = document.getElementById("view-accounting-docs-block");
            const ledgerItem = document.getElementById("view-ledger-item");
            const scheduleItem = document.getElementById("view-schedule-item");
            const btnPreviewLedger = document.getElementById("btn-preview-ledger");
            const btnPreviewSchedule = document.getElementById("btn-preview-schedule");

            let hasAccountingDocs = false;
            if (loan.ledger_url) {
                hasAccountingDocs = true;
                if (ledgerItem) ledgerItem.classList.remove("hidden");
                if (btnPreviewLedger) {
                    btnPreviewLedger.onclick = () => openPdfPreview(loan.ledger_url, `Loan_Ledger_LN-${loan.id}.pdf`, 'PDF');
                }
            } else {
                if (ledgerItem) ledgerItem.classList.add("hidden");
            }

            if (loan.schedule_url) {
                hasAccountingDocs = true;
                if (scheduleItem) scheduleItem.classList.remove("hidden");
                if (btnPreviewSchedule) {
                    btnPreviewSchedule.onclick = () => openPdfPreview(loan.schedule_url, `Amortization_Schedule_LN-${loan.id}.pdf`, 'PDF');
                }
            } else {
                if (scheduleItem) scheduleItem.classList.add("hidden");
            }

            if (accountingDocsBlock) {
                if (hasAccountingDocs) {
                    accountingDocsBlock.classList.remove("hidden");
                } else {
                    accountingDocsBlock.classList.add("hidden");
                }
            }

            // Handle Releasing Officer Upload Dock inside Signatory Decision Panel
            const accountingUploadSection = document.getElementById("accounting-upload-section");
            const inputLedger = document.getElementById("input-accounting-ledger");
            const inputSchedule = document.getElementById("input-accounting-schedule");
            const statusLedger = document.getElementById("ledger-file-status");
            const statusSchedule = document.getElementById("schedule-file-status");

            if (accountingUploadSection) {
                if (loan.current_stage === 'releasing_officer') {
                    accountingUploadSection.classList.remove("hidden");
                    if (inputLedger) {
                        inputLedger.value = "";
                        inputLedger.required = true;
                        if (statusLedger) {
                            statusLedger.textContent = "Required";
                            statusLedger.className = "text-[9px] font-semibold text-slate-400";
                        }
                    }
                    if (inputSchedule) {
                        inputSchedule.value = "";
                        inputSchedule.required = true;
                        if (statusSchedule) {
                            statusSchedule.textContent = "Required";
                            statusSchedule.className = "text-[9px] font-semibold text-slate-400";
                        }
                    }
                } else {
                    accountingUploadSection.classList.add("hidden");
                    if (inputLedger) inputLedger.required = false;
                    if (inputSchedule) inputSchedule.required = false;
                }
            }

            let canActOnStage = myGroupSlugs.includes(loan.current_stage);

            // Granular check for sequential HRMD stage: only active sequence can approve
            if (canActOnStage && loan.current_stage === 'hrmd_staff') {
                const activeHrmdSeq = loan.current_hrmd_sequence || 1;
                if (currentAdminRole !== 'super_admin' && Number(currentAdminHrmdSequence) !== Number(activeHrmdSeq)) {
                    canActOnStage = false;
                }
            }

            if (canActOnStage) {
                actionPanel.classList.remove("hidden");
                if (infoPanel) infoPanel.classList.add("hidden");
                txtRemarks.value = "";

                // Assign routes dynamic URLs
                const btnApprove = document.getElementById("btn-action-approve");
                const btnReject = document.getElementById("btn-action-reject");
                const btnReturn = document.getElementById("btn-action-return");

                const approveSpinner = document.getElementById("btn-action-approve-spinner");
                const approveIcon = document.getElementById("btn-action-approve-icon");
                const approveText = document.getElementById("btn-action-approve-text");

                const rejectSpinner = document.getElementById("btn-action-reject-spinner");
                const rejectIcon = document.getElementById("btn-action-reject-icon");
                const rejectText = document.getElementById("btn-action-reject-text");

                const returnSpinner = document.getElementById("btn-action-return-spinner");
                const returnIcon = document.getElementById("btn-action-return-icon");
                const returnText = document.getElementById("btn-action-return-text");

                // Reset all action buttons & close triggers
                [btnApprove, btnReject, btnReturn].forEach(btn => {
                    if (btn) {
                        btn.disabled = false;
                        btn.classList.remove("opacity-75", "cursor-wait");
                    }
                });
                document.querySelectorAll(".modal-close").forEach(c => {
                    c.disabled = false;
                });

                if (approveSpinner) approveSpinner.classList.add("hidden");
                if (approveIcon) approveIcon.classList.remove("hidden");
                if (rejectSpinner) rejectSpinner.classList.add("hidden");
                if (rejectIcon) rejectIcon.classList.remove("hidden");
                if (returnSpinner) returnSpinner.classList.add("hidden");
                if (returnIcon) returnIcon.classList.remove("hidden");

                const isReleasing = loan.current_stage === 'releasing_officer';

                if (approveText) {
                    approveText.textContent = isReleasing ? "Finalize & Disburse Loan" : "Sign & Approve";
                }
                if (approveIcon) {
                    approveIcon.innerHTML = isReleasing
                        ? '<i class="fa-solid fa-money-bill-transfer text-emerald-100 group-hover:scale-110 transition-transform duration-200"></i>'
                        : '<svg class="w-4 h-4 text-emerald-100 group-hover:scale-110 transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>';
                }
                if (rejectText) rejectText.textContent = "Reject Application";
                if (returnText) returnText.textContent = "Return Application";

                if (['sako_staff', 'hrmd_staff'].includes(loan.current_stage)) {
                    btnReturn.classList.remove("hidden");
                } else {
                    btnReturn.classList.add("hidden");
                }

                btnApprove.onclick = function(e) {
                    e.preventDefault();
                    const alertInstance = window.MLSAKOAlert || Swal;

                    if (loan.current_stage === 'releasing_officer') {
                        const ledgerFile = inputLedger ? inputLedger.files[0] : null;
                        const scheduleFile = inputSchedule ? inputSchedule.files[0] : null;

                        if (!ledgerFile) {
                            alertInstance.fire({
                                icon: 'warning',
                                title: 'General Ledger Required',
                                text: 'Releasing Officer must attach the General Ledger PDF file before finalizing loan disbursement.',
                                iconColor: '#f59e0b',
                                confirmButtonText: 'Select Ledger File'
                            });
                            return;
                        }

                        if (!scheduleFile) {
                            alertInstance.fire({
                                icon: 'warning',
                                title: 'Payment Schedule Required',
                                text: 'Releasing Officer must attach the Payment Schedule PDF file before finalizing loan disbursement.',
                                iconColor: '#f59e0b',
                                confirmButtonText: 'Select Schedule File'
                            });
                            return;
                        }
                    }

                    if (!txtRemarks.value.trim()) {
                        alertInstance.fire({
                            icon: 'warning',
                            title: 'Remarks Required',
                            text: 'Please enter evaluation remarks before signing off.',
                            iconColor: '#f59e0b',
                            confirmButtonText: 'Understood'
                        });
                        return;
                    }

                    const isReleasing = loan.current_stage === 'releasing_officer';

                    alertInstance.fire({
                        icon: 'question',
                        title: isReleasing ? 'Confirm Disbursement' : 'Confirm Signature',
                        text: isReleasing 
                            ? 'Are you sure you want to sign off and finalize loan disbursement with the attached Ledger & Schedule?' 
                            : 'Are you sure you want to sign and approve this loan facility application?',
                        showCancelButton: true,
                        confirmButtonText: isReleasing ? 'Yes, Sign & Disburse' : 'Yes, Sign & Approve',
                        cancelButtonText: 'Cancel',
                        iconColor: '#10b981'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // 1. Lock all controls to prevent duplicate submissions
                            [btnApprove, btnReject, btnReturn].forEach(btn => {
                                if (btn) {
                                    btn.disabled = true;
                                    btn.classList.add("cursor-wait");
                                }
                            });
                            btnApprove.classList.add("opacity-75");
                            document.querySelectorAll(".modal-close").forEach(c => {
                                c.disabled = true;
                            });

                            // 2. Button Micro-Loading State
                            if (approveSpinner) approveSpinner.classList.remove("hidden");
                            if (approveIcon) approveIcon.classList.add("hidden");
                            if (approveText) {
                                approveText.textContent = isReleasing ? "Disbursing & Finalizing..." : "Signing & Approving...";
                            }

                            // 3. Macro Fullscreen/Modal Loading Overlay
                            if (alertInstance) {
                                alertInstance.fire({
                                    title: isReleasing ? 'Disbursing Loan & Archiving Ledger...' : 'Signing & Processing Approval...',
                                    html: isReleasing ? `
                                        <div class="flex flex-col items-center justify-center p-4 space-y-4">
                                            <div class="relative w-16 h-16 flex items-center justify-center">
                                                <div class="w-16 h-16 rounded-full border-4 border-emerald-500/20 border-t-emerald-500 animate-spin"></div>
                                                <span class="absolute text-xl"><i class="fa-solid fa-money-bill-wave text-emerald-600 dark:text-emerald-400"></i></span>
                                            </div>
                                            <div class="space-y-1.5 text-center">
                                                <p class="text-xs font-bold text-slate-800 dark:text-slate-100">
                                                    Uploading General Ledger &amp; Payment Schedule PDFs...
                                                </p>
                                                <p class="text-[11px] text-slate-500 dark:text-slate-400 font-medium leading-relaxed">
                                                    Calculating financial amortizations, executing disbursement, and dispatching notification email to the borrower. Please do not close or refresh this page.
                                                </p>
                                            </div>
                                        </div>
                                    ` : `
                                        <div class="flex flex-col items-center justify-center p-4 space-y-4">
                                            <div class="relative w-16 h-16 flex items-center justify-center">
                                                <div class="w-16 h-16 rounded-full border-4 border-emerald-500/20 border-t-emerald-500 animate-spin"></div>
                                                <span class="absolute text-xl"><i class="fa-solid fa-file-signature text-emerald-600 dark:text-emerald-400"></i></span>
                                            </div>
                                            <div class="space-y-1.5 text-center">
                                                <p class="text-xs font-bold text-slate-800 dark:text-slate-100">
                                                    Recording cryptographic stage approval...
                                                </p>
                                                <p class="text-[11px] text-slate-500 dark:text-slate-400 font-medium leading-relaxed">
                                                    Advancing loan application to the next sequential verification pipeline. Please wait a moment.
                                                </p>
                                            </div>
                                        </div>
                                    `,
                                    showConfirmButton: false,
                                    allowOutsideClick: false,
                                    allowEscapeKey: false
                                });
                            }

                            formAction.action = `/loans/${loan.id}/approve`;
                            formAction.submit();
                        }
                    });
                };

                btnReject.onclick = function(e) {
                    e.preventDefault();
                    const alertInstance = window.MLSAKOAlert || Swal;

                    if (!txtRemarks.value.trim()) {
                        alertInstance.fire({
                            icon: 'warning',
                            title: 'Remarks Required',
                            text: 'Please enter rejection remarks to record the decision.',
                            iconColor: '#f59e0b',
                            confirmButtonText: 'Understood'
                        });
                        return;
                    }

                    alertInstance.fire({
                        icon: 'warning',
                        title: 'Confirm Rejection',
                        text: 'Are you sure you want to decline and reject this loan facility application? This will terminate the workflow path.',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, Decline & Reject',
                        cancelButtonText: 'Cancel',
                        iconColor: '#f43f5e'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            [btnApprove, btnReject, btnReturn].forEach(btn => {
                                if (btn) {
                                    btn.disabled = true;
                                    btn.classList.add("cursor-wait");
                                }
                            });
                            btnReject.classList.add("opacity-75");
                            document.querySelectorAll(".modal-close").forEach(c => {
                                c.disabled = true;
                            });

                            if (rejectSpinner) rejectSpinner.classList.remove("hidden");
                            if (rejectIcon) rejectIcon.classList.add("hidden");
                            if (rejectText) rejectText.textContent = "Declining Application...";

                            if (alertInstance) {
                                alertInstance.fire({
                                    title: 'Processing Rejection...',
                                    html: `
                                        <div class="flex flex-col items-center justify-center p-4 space-y-4">
                                            <div class="relative w-16 h-16 flex items-center justify-center">
                                                <div class="w-16 h-16 rounded-full border-4 border-rose-500/20 border-t-rose-500 animate-spin"></div>
                                                <span class="absolute text-xl"><i class="fa-solid fa-ban text-rose-600 dark:text-rose-400"></i></span>
                                            </div>
                                            <div class="space-y-1.5 text-center">
                                                <p class="text-xs font-bold text-slate-800 dark:text-slate-100">
                                                    Recording rejection &amp; terminating workflow...
                                                </p>
                                                <p class="text-[11px] text-slate-500 dark:text-slate-400 font-medium leading-relaxed">
                                                    Archiving remarks in audit log. Please wait a moment.
                                                </p>
                                            </div>
                                        </div>
                                    `,
                                    showConfirmButton: false,
                                    allowOutsideClick: false,
                                    allowEscapeKey: false
                                });
                            }

                            formAction.action = `/loans/${loan.id}/reject`;
                            formAction.submit();
                        }
                    });
                };

                btnReturn.onclick = function(e) {
                    e.preventDefault();
                    const alertInstance = window.MLSAKOAlert || Swal;

                    if (!txtRemarks.value.trim()) {
                        alertInstance.fire({
                            icon: 'warning',
                            title: 'Remarks Required',
                            text: 'Please enter remarks explaining which requirements are lacking.',
                            iconColor: '#f59e0b',
                            confirmButtonText: 'Understood'
                        });
                        return;
                    }

                    alertInstance.fire({
                        icon: 'warning',
                        title: 'Confirm Return',
                        text: 'Are you sure you want to return this loan application back to the member for requirement corrections?',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, Return Application',
                        cancelButtonText: 'Cancel',
                        iconColor: '#f59e0b'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            [btnApprove, btnReject, btnReturn].forEach(btn => {
                                if (btn) {
                                    btn.disabled = true;
                                    btn.classList.add("cursor-wait");
                                }
                            });
                            btnReturn.classList.add("opacity-75");
                            document.querySelectorAll(".modal-close").forEach(c => {
                                c.disabled = true;
                            });

                            if (returnSpinner) returnSpinner.classList.remove("hidden");
                            if (returnIcon) returnIcon.classList.add("hidden");
                            if (returnText) returnText.textContent = "Returning Application...";

                            if (alertInstance) {
                                alertInstance.fire({
                                    title: 'Returning Application...',
                                    html: `
                                        <div class="flex flex-col items-center justify-center p-4 space-y-4">
                                            <div class="relative w-16 h-16 flex items-center justify-center">
                                                <div class="w-16 h-16 rounded-full border-4 border-amber-500/20 border-t-amber-500 animate-spin"></div>
                                                <span class="absolute text-xl"><i class="fa-solid fa-arrow-rotate-left text-amber-600 dark:text-amber-400"></i></span>
                                            </div>
                                            <div class="space-y-1.5 text-center">
                                                <p class="text-xs font-bold text-slate-800 dark:text-slate-100">
                                                    Returning loan application for corrections...
                                                </p>
                                                <p class="text-[11px] text-slate-500 dark:text-slate-400 font-medium leading-relaxed">
                                                    Sending correction feedback to member and resetting queue status. Please wait a moment.
                                                </p>
                                            </div>
                                        </div>
                                    `,
                                    showConfirmButton: false,
                                    allowOutsideClick: false,
                                    allowEscapeKey: false
                                });
                            }

                            formAction.action = `/loans/${loan.id}/return`;
                            formAction.submit();
                        }
                    });
                };
            } else {
                actionPanel.classList.add("hidden");
                if (infoPanel) infoPanel.classList.remove("hidden");
            }

            openModal("modal-review");
        }

        // Event delegation for Review Button and PDF previews across dynamic rows
        document.addEventListener("click", function(e) {
            const reviewBtn = e.target.closest(".btn-review-loan");
            if (reviewBtn) {
                const loan = JSON.parse(reviewBtn.getAttribute("data-loan"));
                populateAndOpenReviewModal(loan);
                return;
            }

            const pdfBtn = e.target.closest(".btn-preview-pdf");
            if (pdfBtn && !pdfBtn.closest("#view-documents-container")) {
                const url = pdfBtn.getAttribute("data-url");
                const name = pdfBtn.getAttribute("data-name");
                const size = pdfBtn.getAttribute("data-size");
                openPdfPreview(url, name, size);
                return;
            }
        });

        // ==========================================
        // CUSTOM SMOOTH DROPDOWNS CONTROLLER
        // ==========================================
        document.querySelectorAll(".custom-dropdown").forEach(wrapper => {
            const btn = wrapper.querySelector("button");
            const menu = wrapper.querySelector("[id^='dropdown-menu']");
            const input = wrapper.querySelector("input[type='hidden']");
            const label = wrapper.querySelector("[id^='label-']");
            const arrow = wrapper.querySelector(".dropdown-arrow");

            if (!btn || !menu) return;

            function toggleDropdown(forceState) {
                const isOpen = !menu.classList.contains("pointer-events-none");
                const shouldOpen = forceState !== undefined ? forceState : !isOpen;

                // Close other open dropdowns
                document.querySelectorAll("[id^='dropdown-menu']").forEach(otherMenu => {
                    if (otherMenu !== menu) {
                        otherMenu.classList.add("opacity-0", "scale-95", "pointer-events-none");
                        otherMenu.classList.remove("opacity-100", "scale-100", "pointer-events-auto");
                        const otherArrow = otherMenu.closest(".custom-dropdown")?.querySelector(".dropdown-arrow");
                        if (otherArrow) otherArrow.classList.remove("rotate-180");
                    }
                });

                if (shouldOpen) {
                    menu.classList.remove("opacity-0", "scale-95", "pointer-events-none");
                    menu.classList.add("opacity-100", "scale-100", "pointer-events-auto");
                    if (arrow) arrow.classList.add("rotate-180");
                } else {
                    menu.classList.add("opacity-0", "scale-95", "pointer-events-none");
                    menu.classList.remove("opacity-100", "scale-100", "pointer-events-auto");
                    if (arrow) arrow.classList.remove("rotate-180");
                }
            }

            btn.addEventListener("click", function(e) {
                e.stopPropagation();
                toggleDropdown();
            });

            menu.querySelectorAll(".dropdown-item").forEach(item => {
                item.addEventListener("click", function(e) {
                    e.stopPropagation();
                    const val = this.getAttribute("data-value");
                    const itemText = this.querySelector("span").textContent;

                    if (input) input.value = val;
                    if (label) label.textContent = itemText;

                    // Update checkmarks
                    menu.querySelectorAll(".dropdown-item").forEach(i => {
                        const check = i.querySelector(".check-icon");
                        if (check) {
                            check.classList.toggle("hidden", i !== item);
                        }
                    });

                    toggleDropdown(false);

                    // Sync stage chips if stage was selected
                    if (input && input.id === "filter-stage") {
                        updateStageChipStyles(val);
                    }

                    fetchApprovalsData();
                });
            });
        });

        // Click outside closes any active dropdown
        document.addEventListener("click", function(e) {
            if (!e.target.closest(".custom-dropdown")) {
                document.querySelectorAll("[id^='dropdown-menu']").forEach(menu => {
                    menu.classList.add("opacity-0", "scale-95", "pointer-events-none");
                    menu.classList.remove("opacity-100", "scale-100", "pointer-events-auto");
                    const arrow = menu.closest(".custom-dropdown")?.querySelector(".dropdown-arrow");
                    if (arrow) arrow.classList.remove("rotate-180");
                });
            }
        });

        // ==========================================
        // REAL-TIME SEARCH & FILTER ENGINE
        // ==========================================
        const searchInput = document.getElementById("ajax-search");
        const btnClearSearch = document.getElementById("btn-clear-search");
        const stageFilter = document.getElementById("filter-stage");
        const statusFilter = document.getElementById("filter-status");
        const categoryFilter = document.getElementById("filter-category");
        const inboxTableBody = document.getElementById("inbox-table-body");
        const allTableBody = document.getElementById("all-table-body");
        const allPagination = document.getElementById("all-loans-pagination");
        const inboxTabCount = document.getElementById("inbox-tab-count");
        const allTabCount = document.getElementById("all-tab-count");
        const resultsCountBadge = document.getElementById("results-count-badge");
        const stageChips = document.querySelectorAll(".stage-chip");
        const btnResetFilters = document.getElementById("btn-reset-filters");
        const searchSpinner = document.getElementById("search-spinner");
        const searchIcon = document.getElementById("search-icon");

        let currentActiveTab = "inbox";
        let debounceTimer = null;
        let searchAbort = null;

        tabInbox.addEventListener("click", () => {
            currentActiveTab = "inbox";
            updateResultsBadge();
        });

        tabAll.addEventListener("click", () => {
            currentActiveTab = "all";
            updateResultsBadge();
        });

        function updateResultsBadge() {
            if (!resultsCountBadge) return;
            if (currentActiveTab === "inbox") {
                const count = inboxTabCount ? inboxTabCount.textContent : "0";
                resultsCountBadge.textContent = `${count} In Inbox`;
            } else {
                const count = allTabCount ? allTabCount.textContent : "0";
                resultsCountBadge.textContent = `${count} In Total Pipelines`;
            }
        }

        function updateStageChipStyles(activeStage) {
            stageChips.forEach(chip => {
                if (chip.getAttribute("data-stage") === activeStage) {
                    chip.className = "stage-chip px-2.5 py-1 rounded-lg text-[10px] font-bold border transition-all cursor-pointer bg-slate-900 text-white border-slate-900 dark:bg-white dark:text-slate-900 dark:border-white shadow-2xs";
                } else {
                    chip.className = "stage-chip px-2.5 py-1 rounded-lg text-[10px] font-bold border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 hover:border-emerald-500 hover:text-emerald-600 dark:hover:text-emerald-400 transition-all cursor-pointer";
                }
            });
        }

        function fetchApprovalsData() {
            const search = searchInput ? searchInput.value.trim() : "";
            const stage = stageFilter ? stageFilter.value : "all";
            const status = statusFilter ? statusFilter.value : "all";
            const category = categoryFilter ? categoryFilter.value : "all";

            // Toggle clear button
            if (btnClearSearch) {
                btnClearSearch.classList.toggle("hidden", search.length === 0);
            }

            // Visual feedback
            if (searchSpinner) searchSpinner.classList.remove("hidden");
            if (searchIcon) searchIcon.classList.add("text-emerald-500");

            if (inboxTableBody) inboxTableBody.classList.add("opacity-40", "transition-opacity", "duration-200");
            if (allTableBody) allTableBody.classList.add("opacity-40", "transition-opacity", "duration-200");

            if (searchAbort) searchAbort.abort();
            searchAbort = new AbortController();

            const url = `{{ route('admin.loans.approvals') }}?search=${encodeURIComponent(search)}&stage=${stage}&status=${status}&category=${category}`;

            fetch(url, {
                signal: searchAbort.signal,
                headers: {
                    "X-Requested-With": "XMLHttpRequest"
                }
            })
            .then(res => res.json())
            .then(data => {
                if (inboxTableBody) inboxTableBody.innerHTML = data.inbox_html;
                if (allTableBody) allTableBody.innerHTML = data.all_html;
                if (allPagination) allPagination.innerHTML = data.pagination_html;
                if (inboxTabCount) inboxTabCount.textContent = data.inbox_count;
                if (allTabCount) allTabCount.textContent = data.all_count;

                updateResultsBadge();
            })
            .catch(err => {
                if (err.name !== "AbortError") {
                    console.error("Filter error:", err);
                }
            })
            .finally(() => {
                if (searchSpinner) searchSpinner.classList.add("hidden");
                if (searchIcon && search.length === 0) searchIcon.classList.remove("text-emerald-500");
                if (inboxTableBody) inboxTableBody.classList.remove("opacity-40");
                if (allTableBody) allTableBody.classList.remove("opacity-40");
            });
        }

        // Live search with debounce
        if (searchInput) {
            searchInput.addEventListener("input", function() {
                if (searchSpinner) searchSpinner.classList.remove("hidden");
                if (searchIcon) searchIcon.classList.add("text-emerald-500");

                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(fetchApprovalsData, 220);
            });

            searchInput.addEventListener("focus", function() {
                if (searchIcon) searchIcon.classList.add("text-emerald-500");
            });

            searchInput.addEventListener("blur", function() {
                if (this.value.trim().length === 0 && searchIcon) {
                    searchIcon.classList.remove("text-emerald-500");
                }
            });
        }

        // Clear search input
        if (btnClearSearch) {
            btnClearSearch.addEventListener("click", function() {
                if (searchInput) {
                    searchInput.value = "";
                    searchInput.focus();
                }
                btnClearSearch.classList.add("hidden");
                fetchApprovalsData();
            });
        }

        // Quick Stage Chip Clicks
        stageChips.forEach(chip => {
            chip.addEventListener("click", function() {
                const selectedStage = this.getAttribute("data-stage");
                const stageInput = document.getElementById("filter-stage");
                const stageLabel = document.getElementById("label-stage");

                if (stageInput) stageInput.value = selectedStage;
                if (stageLabel) {
                    const matchItem = document.querySelector(`#dropdown-menu-stage .dropdown-item[data-value="${selectedStage}"]`);
                    stageLabel.textContent = matchItem ? matchItem.querySelector("span").textContent : "All Stages";
                }

                document.querySelectorAll("#dropdown-menu-stage .dropdown-item").forEach(i => {
                    const check = i.querySelector(".check-icon");
                    if (check) check.classList.toggle("hidden", i.getAttribute("data-value") !== selectedStage);
                });

                updateStageChipStyles(selectedStage);
                fetchApprovalsData();
            });
        });

        // Reset Filters Button
        if (btnResetFilters) {
            btnResetFilters.addEventListener("click", function() {
                if (searchInput) searchInput.value = "";
                if (btnClearSearch) btnClearSearch.classList.add("hidden");

                // Reset Stage
                const sInput = document.getElementById("filter-stage");
                const sLabel = document.getElementById("label-stage");
                if (sInput) sInput.value = "all";
                if (sLabel) sLabel.textContent = "All Stages";
                document.querySelectorAll("#dropdown-menu-stage .dropdown-item").forEach(i => {
                    const check = i.querySelector(".check-icon");
                    if (check) check.classList.toggle("hidden", i.getAttribute("data-value") !== "all");
                });

                // Reset Status
                const stInput = document.getElementById("filter-status");
                const stLabel = document.getElementById("label-status");
                if (stInput) stInput.value = "all";
                if (stLabel) stLabel.textContent = "All Status";
                document.querySelectorAll("#dropdown-menu-status .dropdown-item").forEach(i => {
                    const check = i.querySelector(".check-icon");
                    if (check) check.classList.toggle("hidden", i.getAttribute("data-value") !== "all");
                });

                // Reset Category
                const cInput = document.getElementById("filter-category");
                const cLabel = document.getElementById("label-category");
                if (cInput) cInput.value = "all";
                if (cLabel) cLabel.textContent = "All Categories";
                document.querySelectorAll("#dropdown-menu-category .dropdown-item").forEach(i => {
                    const check = i.querySelector(".check-icon");
                    if (check) check.classList.toggle("hidden", i.getAttribute("data-value") !== "all");
                });

                updateStageChipStyles("all");
                fetchApprovalsData();
            });
        }

        // Global file input change listeners for accounting files
        const inputLedgerGlobal = document.getElementById("input-accounting-ledger");
        const inputScheduleGlobal = document.getElementById("input-accounting-schedule");
        const statusLedgerGlobal = document.getElementById("ledger-file-status");
        const statusScheduleGlobal = document.getElementById("schedule-file-status");

        if (inputLedgerGlobal && statusLedgerGlobal) {
            inputLedgerGlobal.addEventListener("change", function() {
                if (this.files && this.files[0]) {
                    statusLedgerGlobal.textContent = "✓ " + this.files[0].name;
                    statusLedgerGlobal.className = "text-[9px] font-bold text-emerald-600 dark:text-emerald-400 truncate max-w-[120px]";
                } else {
                    statusLedgerGlobal.textContent = "Required";
                    statusLedgerGlobal.className = "text-[9px] font-semibold text-slate-400";
                }
            });
        }

        if (inputScheduleGlobal && statusScheduleGlobal) {
            inputScheduleGlobal.addEventListener("change", function() {
                if (this.files && this.files[0]) {
                    statusScheduleGlobal.textContent = "✓ " + this.files[0].name;
                    statusScheduleGlobal.className = "text-[9px] font-bold text-blue-600 dark:text-blue-400 truncate max-w-[120px]";
                } else {
                    statusScheduleGlobal.textContent = "Required";
                    statusScheduleGlobal.className = "text-[9px] font-semibold text-slate-400";
                }
            });
        }

    });
</script>
@endpush