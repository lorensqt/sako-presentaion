@extends('layouts.admin')

@section('title', 'Loans Management - Sako Cooperative')
@section('page_title', 'Loans Management')
@section('page_subtitle', 'Configure active loan facilities, interest rates, borrowing limits, co-maker matrix configs, and approval flow requirements.')

@section('header_actions')
<div class="flex items-center gap-2">
    <span class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 text-xs font-bold border border-emerald-200/60 dark:border-emerald-800/60 shadow-2xs">
        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
        <span>Catalog Active</span>
    </span>
</div>
@endsection

@section('content')
<div class="space-y-6 animate-fade-in">

    @php
        $totalProducts = $loanProducts->count();
        $activeCount = $loanProducts->where('is_active', true)->count();
        $tieredCount = $loanProducts->filter(fn($p) => $p->hasCustomTerms())->count();
        $hrmdCount = $loanProducts->where('hrmd_approval', true)->count();
        $avgRate = $totalProducts > 0 ? $loanProducts->avg('interest_rate') : 0;
    @endphp

    <!-- Fintech KPI Overview Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3.5 sm:gap-4">
        <!-- Card 1: Active Loan Products -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-4 sm:p-5 shadow-xs relative overflow-hidden group hover:border-emerald-300 dark:hover:border-emerald-700/60 transition-all duration-200">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Active Catalog</span>
                <span class="w-8 h-8 rounded-xl bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-xs">
                    <i class="fa-solid fa-building-columns"></i>
                </span>
            </div>
            <div class="mt-3 flex items-baseline gap-2">
                <span class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white font-mono">{{ $activeCount }}</span>
                <span class="text-xs font-semibold text-slate-400 dark:text-slate-500">/ {{ $totalProducts }} Total</span>
            </div>
            <div class="mt-2 flex items-center gap-1.5 text-[11px] text-emerald-600 dark:text-emerald-400 font-semibold">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                <span>Available for Applications</span>
            </div>
        </div>

        <!-- Card 2: Average Base Rate -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-4 sm:p-5 shadow-xs relative overflow-hidden group hover:border-blue-300 dark:hover:border-blue-700/60 transition-all duration-200">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Avg Flat Rate</span>
                <span class="w-8 h-8 rounded-xl bg-blue-50 dark:bg-blue-950/50 text-blue-600 dark:text-blue-400 flex items-center justify-center text-xs">
                    <i class="fa-solid fa-chart-line"></i>
                </span>
            </div>
            <div class="mt-3 flex items-baseline gap-1">
                <span class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white font-mono">{{ number_format($avgRate, 2) }}</span>
                <span class="text-base font-bold text-blue-600 dark:text-blue-400 font-mono">%</span>
            </div>
            <div class="mt-2 flex items-center gap-1.5 text-[11px] text-slate-500 dark:text-slate-400 font-medium">
                <span>Cooperative base interest rate</span>
            </div>
        </div>

        <!-- Card 3: Tiered Tenure Products -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-4 sm:p-5 shadow-xs relative overflow-hidden group hover:border-purple-300 dark:hover:border-purple-700/60 transition-all duration-200">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Tiered Tenures</span>
                <span class="w-8 h-8 rounded-xl bg-purple-50 dark:bg-purple-950/50 text-purple-600 dark:text-purple-400 flex items-center justify-center text-xs">
                    <i class="fa-solid fa-bolt"></i>
                </span>
            </div>
            <div class="mt-3 flex items-baseline gap-2">
                <span class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white font-mono">{{ $tieredCount }}</span>
                <span class="text-xs font-semibold text-purple-600 dark:text-purple-400">Configured</span>
            </div>
            <div class="mt-2 flex items-center gap-1.5 text-[11px] text-slate-500 dark:text-slate-400 font-medium">
                <span>Dynamic tenure &amp; rate matrices</span>
            </div>
        </div>

        <!-- Card 4: HRMD Linked Products -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-4 sm:p-5 shadow-xs relative overflow-hidden group hover:border-amber-300 dark:hover:border-amber-700/60 transition-all duration-200">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">HRMD Clearance</span>
                <span class="w-8 h-8 rounded-xl bg-amber-50 dark:bg-amber-950/50 text-amber-600 dark:text-amber-400 flex items-center justify-center text-xs">
                    <i class="fa-solid fa-clipboard-check"></i>
                </span>
            </div>
            <div class="mt-3 flex items-baseline gap-2">
                <span class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white font-mono">{{ $hrmdCount }}</span>
                <span class="text-xs font-semibold text-amber-600 dark:text-amber-400">Products</span>
            </div>
            <div class="mt-2 flex items-center gap-1.5 text-[11px] text-slate-500 dark:text-slate-400 font-medium">
                <span>Requires payroll approval gate</span>
            </div>
        </div>
    </div>

    <!-- Interactive Filter & Command Bar with Relocated Action Button -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 p-4 sm:p-5 rounded-2xl sm:rounded-3xl shadow-xs space-y-4">
        <!-- Top Row: Interactive Search, Customized Smooth Dropdowns, and Create Action -->
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3">
            
            <!-- Smooth Search Bar with Live Spinner and Instant Clear -->
            <div class="relative flex-1 min-w-[260px] max-w-md">
                <i id="search-icon" class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 dark:text-slate-500 text-xs transition-colors duration-200"></i>
                <input type="text" id="ajax-search" value="{{ $search }}" placeholder="Search loan product, category, or partner..." class="w-full pl-9 pr-14 py-2.5 text-xs font-semibold border border-slate-200 dark:border-slate-700/80 bg-slate-50/60 dark:bg-slate-950 text-slate-900 dark:text-slate-100 rounded-xl focus:bg-white dark:focus:bg-slate-950 focus:border-emerald-500 dark:focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 dark:focus:ring-emerald-500/10 placeholder-slate-400 dark:placeholder-slate-500 transition-all outline-none">
                
                <div class="absolute right-3 top-1/2 -translate-y-1/2 flex items-center gap-1.5">
                    <i id="search-spinner" class="fa-solid fa-circle-notch fa-spin text-emerald-500 text-xs hidden"></i>
                    <button type="button" id="btn-clear-search" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 text-xs cursor-pointer p-0.5 {{ $search ? '' : 'hidden' }}" title="Clear search">
                        <i class="fa-solid fa-circle-xmark"></i>
                    </button>
                </div>
            </div>

            <!-- Customized Smooth Dropdowns -->
            <div class="flex flex-wrap items-center gap-2.5">
                
                <!-- Category Custom Dropdown -->
                <div class="relative custom-dropdown" id="dropdown-wrapper-category">
                    <input type="hidden" id="filter-category" value="{{ $category ?: 'all' }}">
                    <button type="button" id="dropdown-btn-category" class="inline-flex items-center gap-2 px-3.5 py-2.5 text-xs font-semibold border border-slate-200 dark:border-slate-700/80 bg-slate-50/70 dark:bg-slate-950 text-slate-800 dark:text-slate-200 rounded-xl hover:border-emerald-500/50 hover:bg-white dark:hover:bg-slate-900 transition-all cursor-pointer select-none">
                        <i class="fa-solid fa-layer-group text-slate-400 text-xs"></i>
                        <span id="label-category" class="truncate max-w-[110px]">
                            {{ $category && $category !== 'all' ? ucfirst(str_replace('_', ' ', $category)) : 'All Categories' }}
                        </span>
                        <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 transition-transform duration-200 dropdown-arrow"></i>
                    </button>
                    <!-- Floating Menu -->
                    <div id="dropdown-menu-category" class="absolute top-full left-0 mt-1.5 w-48 z-40 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-xl shadow-slate-900/10 dark:shadow-slate-950/40 p-1.5 space-y-0.5 transform opacity-0 scale-95 pointer-events-none transition-all duration-200 ease-out origin-top-left">
                        <div class="dropdown-item px-3 py-2 text-xs font-semibold rounded-xl text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800/70 cursor-pointer flex items-center justify-between transition-colors" data-value="all">
                            <span>All Categories</span>
                            <i class="fa-solid fa-check text-emerald-600 dark:text-emerald-400 text-[11px] check-icon {{ (!$category || $category === 'all') ? '' : 'hidden' }}"></i>
                        </div>
                        <div class="dropdown-item px-3 py-2 text-xs font-semibold rounded-xl text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800/70 cursor-pointer flex items-center justify-between transition-colors" data-value="regular">
                            <span>Regular</span>
                            <i class="fa-solid fa-check text-emerald-600 dark:text-emerald-400 text-[11px] check-icon {{ $category === 'regular' ? '' : 'hidden' }}"></i>
                        </div>
                        <div class="dropdown-item px-3 py-2 text-xs font-semibold rounded-xl text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800/70 cursor-pointer flex items-center justify-between transition-colors" data-value="commodity">
                            <span>Commodity</span>
                            <i class="fa-solid fa-check text-emerald-600 dark:text-emerald-400 text-[11px] check-icon {{ $category === 'commodity' ? '' : 'hidden' }}"></i>
                        </div>
                        <div class="dropdown-item px-3 py-2 text-xs font-semibold rounded-xl text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800/70 cursor-pointer flex items-center justify-between transition-colors" data-value="special">
                            <span>Special</span>
                            <i class="fa-solid fa-check text-emerald-600 dark:text-emerald-400 text-[11px] check-icon {{ $category === 'special' ? '' : 'hidden' }}"></i>
                        </div>
                        <div class="dropdown-item px-3 py-2 text-xs font-semibold rounded-xl text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800/70 cursor-pointer flex items-center justify-between transition-colors" data-value="seasonal">
                            <span>Seasonal</span>
                            <i class="fa-solid fa-check text-emerald-600 dark:text-emerald-400 text-[11px] check-icon {{ $category === 'seasonal' ? '' : 'hidden' }}"></i>
                        </div>
                        <div class="dropdown-item px-3 py-2 text-xs font-semibold rounded-xl text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800/70 cursor-pointer flex items-center justify-between transition-colors" data-value="bonus_buyout">
                            <span>Bonus Buyout</span>
                            <i class="fa-solid fa-check text-emerald-600 dark:text-emerald-400 text-[11px] check-icon {{ $category === 'bonus_buyout' ? '' : 'hidden' }}"></i>
                        </div>
                        <div class="dropdown-item px-3 py-2 text-xs font-semibold rounded-xl text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800/70 cursor-pointer flex items-center justify-between transition-colors" data-value="emergency">
                            <span>Emergency</span>
                            <i class="fa-solid fa-check text-emerald-600 dark:text-emerald-400 text-[11px] check-icon {{ $category === 'emergency' ? '' : 'hidden' }}"></i>
                        </div>
                        <div class="dropdown-item px-3 py-2 text-xs font-semibold rounded-xl text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800/70 cursor-pointer flex items-center justify-between transition-colors" data-value="health">
                            <span>Health</span>
                            <i class="fa-solid fa-check text-emerald-600 dark:text-emerald-400 text-[11px] check-icon {{ $category === 'health' ? '' : 'hidden' }}"></i>
                        </div>
                        <div class="dropdown-item px-3 py-2 text-xs font-semibold rounded-xl text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800/70 cursor-pointer flex items-center justify-between transition-colors" data-value="upcoming">
                            <span>Upcoming</span>
                            <i class="fa-solid fa-check text-emerald-600 dark:text-emerald-400 text-[11px] check-icon {{ $category === 'upcoming' ? '' : 'hidden' }}"></i>
                        </div>
                        <div class="dropdown-item px-3 py-2 text-xs font-semibold rounded-xl text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800/70 cursor-pointer flex items-center justify-between transition-colors" data-value="travel">
                            <span>Travel</span>
                            <i class="fa-solid fa-check text-emerald-600 dark:text-emerald-400 text-[11px] check-icon {{ $category === 'travel' ? '' : 'hidden' }}"></i>
                        </div>
                    </div>
                </div>

                <!-- Status Custom Dropdown -->
                <div class="relative custom-dropdown" id="dropdown-wrapper-status">
                    <input type="hidden" id="filter-status" value="{{ $status ?: 'all' }}">
                    <button type="button" id="dropdown-btn-status" class="inline-flex items-center gap-2 px-3.5 py-2.5 text-xs font-semibold border border-slate-200 dark:border-slate-700/80 bg-slate-50/70 dark:bg-slate-950 text-slate-800 dark:text-slate-200 rounded-xl hover:border-emerald-500/50 hover:bg-white dark:hover:bg-slate-900 transition-all cursor-pointer select-none">
                        <i class="fa-solid fa-toggle-on text-slate-400 text-xs"></i>
                        <span id="label-status" class="truncate max-w-[100px]">
                            {{ $status === 'active' ? 'Active Only' : ($status === 'inactive' ? 'Inactive Only' : 'All Status') }}
                        </span>
                        <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 transition-transform duration-200 dropdown-arrow"></i>
                    </button>
                    <!-- Floating Menu -->
                    <div id="dropdown-menu-status" class="absolute top-full left-0 mt-1.5 w-44 z-40 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-xl shadow-slate-900/10 dark:shadow-slate-950/40 p-1.5 space-y-0.5 transform opacity-0 scale-95 pointer-events-none transition-all duration-200 ease-out origin-top-left">
                        <div class="dropdown-item px-3 py-2 text-xs font-semibold rounded-xl text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800/70 cursor-pointer flex items-center justify-between transition-colors" data-value="all">
                            <span>All Status</span>
                            <i class="fa-solid fa-check text-emerald-600 dark:text-emerald-400 text-[11px] check-icon {{ (!$status || $status === 'all') ? '' : 'hidden' }}"></i>
                        </div>
                        <div class="dropdown-item px-3 py-2 text-xs font-semibold rounded-xl text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800/70 cursor-pointer flex items-center justify-between transition-colors" data-value="active">
                            <span>Active Only</span>
                            <i class="fa-solid fa-check text-emerald-600 dark:text-emerald-400 text-[11px] check-icon {{ $status === 'active' ? '' : 'hidden' }}"></i>
                        </div>
                        <div class="dropdown-item px-3 py-2 text-xs font-semibold rounded-xl text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800/70 cursor-pointer flex items-center justify-between transition-colors" data-value="inactive">
                            <span>Inactive Only</span>
                            <i class="fa-solid fa-check text-emerald-600 dark:text-emerald-400 text-[11px] check-icon {{ $status === 'inactive' ? '' : 'hidden' }}"></i>
                        </div>
                    </div>
                </div>

                <!-- HRMD Clearance Custom Dropdown -->
                <div class="relative custom-dropdown" id="dropdown-wrapper-hrmd">
                    <input type="hidden" id="filter-hrmd" value="{{ $hrmd ?: 'all' }}">
                    <button type="button" id="dropdown-btn-hrmd" class="inline-flex items-center gap-2 px-3.5 py-2.5 text-xs font-semibold border border-slate-200 dark:border-slate-700/80 bg-slate-50/70 dark:bg-slate-950 text-slate-800 dark:text-slate-200 rounded-xl hover:border-emerald-500/50 hover:bg-white dark:hover:bg-slate-900 transition-all cursor-pointer select-none">
                        <i class="fa-solid fa-id-card-clip text-slate-400 text-xs"></i>
                        <span id="label-hrmd" class="truncate max-w-[110px]">
                            {{ $hrmd === 'yes' ? 'Requires HRMD' : ($hrmd === 'no' ? 'No HRMD' : 'All HRMD') }}
                        </span>
                        <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 transition-transform duration-200 dropdown-arrow"></i>
                    </button>
                    <!-- Floating Menu -->
                    <div id="dropdown-menu-hrmd" class="absolute top-full left-0 mt-1.5 w-48 z-40 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-xl shadow-slate-900/10 dark:shadow-slate-950/40 p-1.5 space-y-0.5 transform opacity-0 scale-95 pointer-events-none transition-all duration-200 ease-out origin-top-left">
                        <div class="dropdown-item px-3 py-2 text-xs font-semibold rounded-xl text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800/70 cursor-pointer flex items-center justify-between transition-colors" data-value="all">
                            <span>All HRMD</span>
                            <i class="fa-solid fa-check text-emerald-600 dark:text-emerald-400 text-[11px] check-icon {{ (!$hrmd || $hrmd === 'all') ? '' : 'hidden' }}"></i>
                        </div>
                        <div class="dropdown-item px-3 py-2 text-xs font-semibold rounded-xl text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800/70 cursor-pointer flex items-center justify-between transition-colors" data-value="yes">
                            <span>Requires HRMD</span>
                            <i class="fa-solid fa-check text-emerald-600 dark:text-emerald-400 text-[11px] check-icon {{ $hrmd === 'yes' ? '' : 'hidden' }}"></i>
                        </div>
                        <div class="dropdown-item px-3 py-2 text-xs font-semibold rounded-xl text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800/70 cursor-pointer flex items-center justify-between transition-colors" data-value="no">
                            <span>No HRMD Clearance</span>
                            <i class="fa-solid fa-check text-emerald-600 dark:text-emerald-400 text-[11px] check-icon {{ $hrmd === 'no' ? '' : 'hidden' }}"></i>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Perfectly Positioned Create Loan Product Action Button -->
            <div class="flex items-center gap-2 flex-shrink-0">
                <button id="btn-add-loan-product" type="button" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs px-4 py-2.5 rounded-xl shadow-md shadow-emerald-600/20 hover:shadow-emerald-600/30 hover:-translate-y-0.5 active:translate-y-0 transition-all duration-200 cursor-pointer">
                    <i class="fa-solid fa-plus text-xs"></i>
                    <span>Create Loan Product</span>
                </button>
            </div>
        </div>

        <!-- Bottom Row: Quick Filter Category Chips & Counter / Reset -->
        <div class="flex flex-wrap items-center justify-between gap-2 pt-3 border-t border-slate-100 dark:border-slate-800">
            <div class="flex flex-wrap items-center gap-1.5" id="quick-category-pills">
                <span class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mr-1">Quick Filter:</span>
                <button type="button" data-category="all" class="category-chip px-2.5 py-1 rounded-lg text-[10px] font-bold border transition-all cursor-pointer bg-slate-900 text-white border-slate-900 dark:bg-white dark:text-slate-900 dark:border-white shadow-2xs">All</button>
                <button type="button" data-category="regular" class="category-chip px-2.5 py-1 rounded-lg text-[10px] font-bold border border-slate-200 dark:border-slate-700/80 text-slate-600 dark:text-slate-400 hover:border-emerald-500 hover:text-emerald-600 dark:hover:text-emerald-400 transition-all cursor-pointer">Regular</button>
                <button type="button" data-category="commodity" class="category-chip px-2.5 py-1 rounded-lg text-[10px] font-bold border border-slate-200 dark:border-slate-700/80 text-slate-600 dark:text-slate-400 hover:border-blue-500 hover:text-blue-600 dark:hover:text-blue-400 transition-all cursor-pointer">Commodity</button>
                <button type="button" data-category="special" class="category-chip px-2.5 py-1 rounded-lg text-[10px] font-bold border border-slate-200 dark:border-slate-700/80 text-slate-600 dark:text-slate-400 hover:border-amber-500 hover:text-amber-600 dark:hover:text-amber-400 transition-all cursor-pointer">Special</button>
                <button type="button" data-category="bonus_buyout" class="category-chip px-2.5 py-1 rounded-lg text-[10px] font-bold border border-slate-200 dark:border-slate-700/80 text-slate-600 dark:text-slate-400 hover:border-purple-500 hover:text-purple-600 dark:hover:text-purple-400 transition-all cursor-pointer">Bonus Buyout</button>
                <button type="button" data-category="emergency" class="category-chip px-2.5 py-1 rounded-lg text-[10px] font-bold border border-slate-200 dark:border-slate-700/80 text-slate-600 dark:text-slate-400 hover:border-rose-500 hover:text-rose-600 dark:hover:text-rose-400 transition-all cursor-pointer">Emergency</button>
            </div>
            
            <div class="flex items-center gap-3">
                <span id="product-count-badge" class="text-[11px] font-bold px-2.5 py-1 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700">
                    {{ $totalProducts }} Products
                </span>
                <button type="button" id="btn-reset-filters" class="text-[11px] font-bold text-slate-500 hover:text-rose-600 dark:text-slate-400 dark:hover:text-rose-400 transition-colors flex items-center gap-1.5 cursor-pointer">
                    <i class="fa-solid fa-arrows-rotate text-[10px]"></i>
                    <span>Reset Filters</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Active Facilities Ledger Table -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl sm:rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50/75 dark:bg-slate-950/60 border-b border-slate-200/80 dark:border-slate-800 text-[10px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                        <th class="px-6 py-4">Product Name &amp; Category</th>
                        <th class="px-6 py-4">Borrowing Limit</th>
                        <th class="px-6 py-4">Interest Rate</th>
                        <th class="px-6 py-4">Req. Share Capital</th>
                        <th class="px-6 py-4">Tenure Options</th>
                        <th class="px-6 py-4">Approval Chain</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody id="loans-table-body" class="divide-y divide-slate-100 dark:divide-slate-800/80 text-slate-700 dark:text-slate-300 font-medium">
                    @include('admin.partials.loans-table-rows')
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- MODAL: CREATE / EDIT LOAN PRODUCT -->
<div id="modal-loan-product" class="fixed inset-0 z-50 hidden">
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-slate-950/40 dark:bg-slate-950/60 backdrop-blur-md opacity-0 transition-opacity duration-500 ease-[cubic-bezier(0.16,1,0.3,1)] pointer-events-none modal-overlay"></div>
    
    <!-- Floating Premium Panel -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-2xl shadow-slate-950/15 dark:shadow-slate-950/50 overflow-hidden h-[calc(100vh-2rem)] w-[calc(100vw-2rem)] sm:w-full sm:max-w-xl fixed right-4 top-4 bottom-4 z-50 transform translate-x-[calc(100%+2rem)] transition-transform duration-500 ease-[cubic-bezier(0.16,1,0.3,1)] modal-container p-6 sm:p-8 flex flex-col">
        <!-- Header -->
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-4">
            <div>
                <h3 class="text-xl font-extrabold text-slate-900 dark:text-slate-100 tracking-tight serif-font" id="loan-product-title">Create Loan Product</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 font-medium mt-1">Product Specifications &amp; Approval Requisites</p>
            </div>
            <button class="modal-close p-1.5 rounded-xl text-slate-400 hover:text-slate-700 dark:text-slate-500 dark:hover:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800/80 transition-all duration-200 cursor-pointer" aria-label="Close dialog">
                <i class="fa-solid fa-xmark text-base"></i>
            </button>
        </div>

        <!-- Form Wrapper -->
        <form id="form-loan-product" method="POST" class="flex-1 flex flex-col overflow-hidden mt-6">
            @csrf
            <div id="method-field-container"></div>

            <!-- Scrollable content area -->
            <div class="flex-1 overflow-y-auto pr-1 -mr-1 space-y-5">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-slate-700 dark:text-slate-300">Loan Category</label>
                        <select name="category" id="prod-category" required class="w-full px-4 py-2.5 text-xs font-medium border border-slate-200 dark:border-slate-700/80 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 rounded-xl outline-none focus:border-emerald-500 dark:focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 dark:focus:ring-emerald-500/10 transition-all duration-200">
                            <option value="regular">Regular</option>
                            <option value="commodity">Commodity</option>
                            <option value="special">Special</option>
                            <option value="seasonal">Seasonal</option>
                            <option value="bonus_buyout">Bonus Buyout</option>
                            <option value="emergency">Emergency</option>
                            <option value="health">Health</option>
                            <option value="upcoming">Upcoming</option>
                            <option value="travel">Travel</option>
                        </select>
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-slate-700 dark:text-slate-300">Product Name</label>
                        <input type="text" name="name" id="prod-name" required placeholder="e.g. Maxi Loan" class="w-full px-4 py-2.5 text-xs font-medium border border-slate-200 dark:border-slate-700/80 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 rounded-xl outline-none focus:border-emerald-500 dark:focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 dark:focus:ring-emerald-500/10 placeholder-slate-400 dark:placeholder-slate-600 transition-all duration-200">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between">
                            <label class="text-xs font-semibold text-slate-700 dark:text-slate-300">Base / Flat Interest Rate (%)</label>
                            <span class="text-[10px] text-slate-400">Fallback Rate</span>
                        </div>
                        <input type="number" step="0.01" name="interest_rate" id="prod-interest-rate" required placeholder="5.5" class="w-full px-4 py-2.5 text-xs font-medium border border-slate-200 dark:border-slate-700/80 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 rounded-xl outline-none focus:border-emerald-500 dark:focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 dark:focus:ring-emerald-500/10 placeholder-slate-400 dark:placeholder-slate-600 transition-all duration-200">
                    </div>

                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between">
                            <label class="text-xs font-semibold text-slate-700 dark:text-slate-300">Max Tenure (Months)</label>
                            <span class="text-[10px] text-slate-400">Auto-synced</span>
                        </div>
                        <input type="number" name="max_term_months" id="prod-max-term" placeholder="24" class="w-full px-4 py-2.5 text-xs font-medium border border-slate-200 dark:border-slate-700/80 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 rounded-xl outline-none focus:border-emerald-500 dark:focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 dark:focus:ring-emerald-500/10 placeholder-slate-400 dark:placeholder-slate-600 transition-all duration-200">
                    </div>
                </div>

                <!-- Custom Available Tenures & Tiered Rates Matrix Component -->
                <div class="space-y-3 p-4 bg-slate-50/80 dark:bg-slate-950/50 border border-slate-200/80 dark:border-slate-800 rounded-2xl">
                    <div class="flex items-center justify-between">
                        <div>
                            <label class="text-xs font-extrabold text-slate-800 dark:text-slate-200 flex items-center gap-1.5">
                                <i class="fa-solid fa-calendar-days text-emerald-600 dark:text-emerald-400 text-xs"></i>
                                <span>Available Tenures & Tiered Interest Rates</span>
                                <span class="text-[10px] font-normal text-slate-400 dark:text-slate-500">(Optional)</span>
                            </label>
                            <p class="text-[10px] text-slate-500 dark:text-slate-400 mt-0.5">Restrict this loan to specific months (min 2 mos up to 60 mos) and assign custom interest rates per tenure.</p>
                        </div>
                        <button type="button" id="btn-clear-all-terms" class="text-[10px] font-bold text-rose-500 hover:text-rose-600 dark:text-rose-400 cursor-pointer hidden">Clear All</button>
                    </div>

                    <!-- Quick Presets -->
                    <div class="space-y-1.5">
                        <span class="text-[9px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Quick Presets</span>
                        <div class="flex flex-wrap gap-1.5">
                            <button type="button" class="btn-term-preset px-2.5 py-1 rounded-lg text-[10px] font-bold bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:border-emerald-500 dark:hover:border-emerald-500 hover:text-emerald-600 dark:hover:text-emerald-400 transition-all cursor-pointer shadow-2xs" data-months="3">+ 3 Mos</button>
                            <button type="button" class="btn-term-preset px-2.5 py-1 rounded-lg text-[10px] font-bold bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:border-emerald-500 dark:hover:border-emerald-500 hover:text-emerald-600 dark:hover:text-emerald-400 transition-all cursor-pointer shadow-2xs" data-months="6">+ 6 Mos</button>
                            <button type="button" class="btn-term-preset px-2.5 py-1 rounded-lg text-[10px] font-bold bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:border-emerald-500 dark:hover:border-emerald-500 hover:text-emerald-600 dark:hover:text-emerald-400 transition-all cursor-pointer shadow-2xs" data-months="12">+ 12 Mos (1 Yr)</button>
                            <button type="button" class="btn-term-preset px-2.5 py-1 rounded-lg text-[10px] font-bold bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:border-emerald-500 dark:hover:border-emerald-500 hover:text-emerald-600 dark:hover:text-emerald-400 transition-all cursor-pointer shadow-2xs" data-months="24">+ 24 Mos (2 Yrs)</button>
                            <button type="button" class="btn-term-preset px-2.5 py-1 rounded-lg text-[10px] font-bold bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:border-emerald-500 dark:hover:border-emerald-500 hover:text-emerald-600 dark:hover:text-emerald-400 transition-all cursor-pointer shadow-2xs" data-months="36">+ 36 Mos (3 Yrs)</button>
                            <button type="button" class="btn-term-preset px-2.5 py-1 rounded-lg text-[10px] font-bold bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:border-emerald-500 dark:hover:border-emerald-500 hover:text-emerald-600 dark:hover:text-emerald-400 transition-all cursor-pointer shadow-2xs" data-months="60">+ 60 Mos (5 Yrs)</button>
                        </div>
                    </div>

                    <!-- Custom Add Inputs -->
                    <div class="grid grid-cols-12 gap-2 pt-1">
                        <div class="col-span-5">
                            <input type="number" id="input-new-term-months" min="2" max="60" placeholder="Tenure (2-60 mos)" class="w-full px-3 py-2 text-xs font-medium border border-slate-200 dark:border-slate-700/80 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 rounded-xl outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500/20">
                        </div>
                        <div class="col-span-4">
                            <input type="number" step="0.01" min="0" max="100" id="input-new-term-rate" placeholder="Interest %" class="w-full px-3 py-2 text-xs font-medium border border-slate-200 dark:border-slate-700/80 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 rounded-xl outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500/20">
                        </div>
                        <div class="col-span-3">
                            <button type="button" id="btn-add-custom-term" class="w-full h-full py-2 px-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 dark:bg-emerald-600 dark:hover:bg-emerald-500 text-white font-bold text-xs flex items-center justify-center gap-1 transition-all cursor-pointer">
                                <i class="fa-solid fa-plus text-xs"></i>
                                <span>Add</span>
                            </button>
                        </div>
                    </div>

                    <!-- Active Terms Container -->
                    <div id="terms-list-container" class="pt-1">
                        <!-- Rendered by JS -->
                    </div>

                    <input type="hidden" name="available_terms" id="prod-available-terms">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-slate-700 dark:text-slate-300">Loanable Limit</label>
                        <input type="text" name="loanable_amount" id="prod-loanable-amount" placeholder="e.g. 100000 or 80% of Basic Salary" class="w-full px-4 py-2.5 text-xs font-medium border border-slate-200 dark:border-slate-700/80 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 rounded-xl outline-none focus:border-emerald-500 dark:focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 dark:focus:ring-emerald-500/10 placeholder-slate-400 dark:placeholder-slate-600 transition-all duration-200">
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-slate-700 dark:text-slate-300">Req. Share Capital (₱)</label>
                        <input type="number" step="1" name="fixed_deposit" id="prod-fixed-deposit" required placeholder="10000" class="w-full px-4 py-2.5 text-xs font-medium border border-slate-200 dark:border-slate-700/80 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 rounded-xl outline-none focus:border-emerald-500 dark:focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 dark:focus:ring-emerald-500/10 placeholder-slate-400 dark:placeholder-slate-600 transition-all duration-200">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-slate-700 dark:text-slate-300">Required Co-Makers</label>
                        <input type="text" name="comakers" id="prod-comakers" required placeholder="0, 4, or JSON rule" class="w-full px-4 py-2.5 text-xs font-medium border border-slate-200 dark:border-slate-700/80 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 rounded-xl outline-none focus:border-emerald-500 dark:focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 dark:focus:ring-emerald-500/10 placeholder-slate-400 dark:placeholder-slate-600 transition-all duration-200">
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-slate-700 dark:text-slate-300">Partner Vendor (if any)</label>
                        <input type="text" name="partner" id="prod-partner" placeholder="e.g. Abenson" class="w-full px-4 py-2.5 text-xs font-medium border border-slate-200 dark:border-slate-700/80 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 rounded-xl outline-none focus:border-emerald-500 dark:focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 dark:focus:ring-emerald-500/10 placeholder-slate-400 dark:placeholder-slate-600 transition-all duration-200">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-1.5 col-span-1 sm:col-span-2">
                        <label class="text-xs font-semibold text-slate-700 dark:text-slate-300">Min Membership (Mos)</label>
                        <input type="number" name="minimum_membership_months" id="prod-min-membership" placeholder="3" class="w-full px-4 py-2.5 text-xs font-medium border border-slate-200 dark:border-slate-700/80 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 rounded-xl outline-none focus:border-emerald-500 dark:focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 dark:focus:ring-emerald-500/10 placeholder-slate-400 dark:placeholder-slate-600 transition-all duration-200">
                    </div>
                </div>

                <div class="space-y-1.5 hidden" id="prod-active-container">
                    <label class="relative inline-flex items-center cursor-pointer select-none">
                        <input type="checkbox" name="is_active" id="prod-is-active" class="sr-only peer" value="1" checked>
                        <div class="w-9 h-5 bg-slate-200 dark:bg-slate-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-emerald-600"></div>
                        <span class="ml-2 text-xs font-semibold text-slate-700 dark:text-slate-300">Product is Active?</span>
                    </label>
                </div>

                <div class="space-y-2 col-span-1 sm:col-span-2">
                    <label class="text-xs font-semibold text-slate-700 dark:text-slate-300">Custom Approval Flow Stages</label>
                    <p class="text-[10px] text-slate-500 dark:text-slate-400 font-medium">Configure the exact path of stages this loan product must undergo:</p>
                    <div class="grid grid-cols-2 gap-3 bg-slate-50 dark:bg-slate-950/40 border border-slate-200 dark:border-slate-800 p-3.5 rounded-2xl">
                        <label class="flex items-center gap-2 cursor-pointer select-none">
                            <input type="checkbox" class="prod-stage-checkbox rounded border-slate-300 dark:border-slate-700 text-emerald-600 focus:ring-emerald-500/20" value="comakers" checked>
                            <span class="text-xs font-medium text-slate-700 dark:text-slate-300">Co-Makers</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer select-none">
                            <input type="checkbox" class="prod-stage-checkbox rounded border-slate-300 dark:border-slate-700 text-emerald-600 focus:ring-emerald-500/20" value="sako_staff" checked>
                            <span class="text-xs font-medium text-slate-700 dark:text-slate-300">Sako Staff</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer select-none">
                            <input type="checkbox" class="prod-stage-checkbox rounded border-slate-300 dark:border-slate-700 text-emerald-600 focus:ring-emerald-500/20" value="hrmd_staff" checked>
                            <span class="text-xs font-medium text-slate-700 dark:text-slate-300">HRMD Staff</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer select-none">
                            <input type="checkbox" class="prod-stage-checkbox rounded border-slate-300 dark:border-slate-700 text-emerald-600 focus:ring-emerald-500/20" value="credit_committee" checked>
                            <span class="text-xs font-medium text-slate-700 dark:text-slate-300">Credit Comm.</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer select-none">
                            <input type="checkbox" class="prod-stage-checkbox rounded border-slate-300 dark:border-slate-700 text-emerald-600 focus:ring-emerald-500/20" value="accounting" checked>
                            <span class="text-xs font-medium text-slate-700 dark:text-slate-300">Accounting</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer select-none">
                            <input type="checkbox" class="prod-stage-checkbox rounded border-slate-300 dark:border-slate-700 text-emerald-600 focus:ring-emerald-500/20" value="releasing_officer" checked>
                            <span class="text-xs font-medium text-slate-700 dark:text-slate-300">Releasing Officer</span>
                        </label>
                    </div>
                    <input type="hidden" name="approval_flow" id="prod-approval-flow">
                </div>
            </div>

            <!-- Fixed Footer Action Bar -->
            <div class="pt-4 border-t border-slate-100 dark:border-slate-800/60 flex items-center justify-end gap-3 mt-4">
                <button type="button" class="modal-close px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-xs font-bold text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-800 dark:hover:text-slate-100 transition-colors cursor-pointer">Cancel</button>
                <button type="submit" id="btn-save-loan-product" class="bg-emerald-600 hover:bg-emerald-700 dark:bg-emerald-600 dark:hover:bg-emerald-500 text-white font-bold text-xs px-5 py-2.5 rounded-xl shadow-md shadow-emerald-600/15 dark:shadow-emerald-900/25 transition-all flex items-center justify-center gap-2 cursor-pointer">
                    <span class="btn-spinner hidden w-3.5 h-3.5 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
                    <span class="btn-text">Save Product Configuration</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL: CONFIRM DELETE LOAN PRODUCT -->
<div id="modal-delete-loan" class="fixed inset-0 z-50 overflow-y-auto hidden flex items-center justify-center p-4">
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-slate-950/50 backdrop-blur-sm opacity-0 pointer-events-none transition-opacity duration-300 modal-overlay"></div>
    
    <!-- Modal Card (Soft border in light mode eliminates any black perimeter) -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl sm:rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xl overflow-hidden w-full max-w-md relative z-10 p-6 space-y-4 transform scale-95 opacity-0 transition-all duration-300 modal-container">
        
        <!-- Header with Hazard Badge -->
        <div class="flex items-start justify-between">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-2xl bg-rose-50 dark:bg-rose-950/40 border border-rose-100 dark:border-rose-900/40 flex items-center justify-center flex-shrink-0 text-rose-600 dark:text-rose-400 shadow-2xs">
                    <i class="fa-solid fa-triangle-exclamation text-base"></i>
                </div>
                <div>
                    <h3 class="text-base font-bold text-slate-900 dark:text-slate-100">Delete Loan Product</h3>
                    <p class="text-[10px] text-rose-600 dark:text-rose-400 font-extrabold uppercase tracking-wider">Destructive Action</p>
                </div>
            </div>
            <button type="button" class="modal-close p-1.5 rounded-xl text-slate-400 hover:text-slate-700 dark:text-slate-500 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors cursor-pointer" aria-label="Close dialog">
                <i class="fa-solid fa-xmark text-base"></i>
            </button>
        </div>

        <!-- Facility Preview Card -->
        <div class="p-4 rounded-2xl bg-slate-50/80 dark:bg-slate-950/60 border border-slate-200 dark:border-slate-800 space-y-2">
            <div class="flex items-center justify-between gap-2">
                <span id="delete-loan-name" class="font-bold text-slate-900 dark:text-slate-100 text-sm"></span>
                <span id="delete-loan-category" class="text-[9px] px-2 py-0.5 rounded bg-slate-200/80 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold uppercase tracking-wide"></span>
            </div>
            <div class="flex items-center gap-3 text-[11px] text-slate-500 dark:text-slate-400 font-medium pt-2 border-t border-slate-200 dark:border-slate-800">
                <span>Interest: <strong id="delete-loan-rate" class="text-emerald-600 dark:text-emerald-400 font-semibold font-mono"></strong></span>
                <span>•</span>
                <span>Tenure: <strong id="delete-loan-term" class="text-slate-700 dark:text-slate-300 font-semibold"></strong></span>
            </div>
        </div>

        <!-- Advisory Notice -->
        <div class="text-xs text-slate-600 dark:text-slate-300 font-medium leading-relaxed bg-amber-50/70 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-900/40 rounded-xl p-3.5">
            <p class="flex items-start gap-2.5">
                <i class="fa-solid fa-circle-exclamation text-amber-500 mt-0.5 flex-shrink-0"></i>
                <span>
                    Are you sure you want to delete this loan product? If this product is already referenced by active member loan applications or ledgers, it will be safely <strong class="text-amber-700 dark:text-amber-400 font-semibold">deactivated</strong> rather than deleted to preserve financial audit history.
                </span>
            </p>
        </div>

        <!-- Action Form -->
        <form id="form-delete-loan" method="POST" class="pt-3 border-t border-slate-200 dark:border-slate-800 flex items-center justify-end gap-2.5">
            @csrf
            @method('DELETE')
            
            <button type="button" class="modal-close px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-xs font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors cursor-pointer">
                Cancel
            </button>
            <button type="submit" id="btn-confirm-delete-loan" class="inline-flex items-center justify-center gap-2 bg-rose-600 hover:bg-rose-700 text-white font-semibold text-xs px-4 py-2.5 rounded-xl shadow-xs transition-all cursor-pointer">
                <i class="btn-spinner hidden fa-solid fa-circle-notch fa-spin text-xs"></i>
                <span class="btn-text">Confirm Deletion</span>
            </button>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function () {
        
        // Custom approval flow mapping
        const stageCheckboxes = document.querySelectorAll(".prod-stage-checkbox");
        const hiddenApprovalFlow = document.getElementById("prod-approval-flow");

        function updateApprovalFlow() {
            const checkedStages = [];
            const standardOrder = ['comakers', 'sako_staff', 'hrmd_staff', 'credit_committee', 'accounting', 'releasing_officer'];
            
            standardOrder.forEach(stage => {
                const cb = Array.from(stageCheckboxes).find(c => c.value === stage);
                if (cb && cb.checked) {
                    checkedStages.push(stage);
                }
            });

            hiddenApprovalFlow.value = JSON.stringify(checkedStages);
        }

        stageCheckboxes.forEach(cb => {
            cb.addEventListener("change", updateApprovalFlow);
        });

        // Available Tenures & Tiered Rates Manager
        let currentTerms = []; // [{ months: 2, interest_rate: 2.50 }, ...]
        const termsContainer = document.getElementById("terms-list-container");
        const hiddenAvailableTerms = document.getElementById("prod-available-terms");
        const btnClearAllTerms = document.getElementById("btn-clear-all-terms");
        const inputMaxTerm = document.getElementById("prod-max-term");

        function renderTermsList() {
            if (!termsContainer || !hiddenAvailableTerms) return;

            // Sort ascending by month
            currentTerms.sort((a, b) => a.months - b.months);

            if (currentTerms.length === 0) {
                hiddenAvailableTerms.value = "";
                if (btnClearAllTerms) btnClearAllTerms.classList.add("hidden");
                termsContainer.innerHTML = `
                    <div class="text-[11px] text-slate-400 dark:text-slate-500 italic p-3.5 bg-white/60 dark:bg-slate-900/60 border border-dashed border-slate-200 dark:border-slate-800 rounded-xl text-center">
                        No specific tenures configured. Loan defaults to standard open tenure up to Max Tenure.
                    </div>
                `;
                return;
            }

            if (btnClearAllTerms) btnClearAllTerms.classList.remove("hidden");
            hiddenAvailableTerms.value = JSON.stringify(currentTerms);

            // Auto-sync max term input with highest month
            const highestTerm = Math.max(...currentTerms.map(t => t.months));
            if (highestTerm > 0 && inputMaxTerm) {
                inputMaxTerm.value = highestTerm;
            }

            let html = `
                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl divide-y divide-slate-100 dark:divide-slate-800/80 overflow-hidden shadow-2xs">
                    <div class="grid grid-cols-12 px-3 py-2 bg-slate-100/70 dark:bg-slate-800/60 text-[9px] font-extrabold text-slate-400 dark:text-slate-500 uppercase tracking-wider">
                        <div class="col-span-5">Allowed Tenure</div>
                        <div class="col-span-5">Custom Interest Rate</div>
                        <div class="col-span-2 text-right">Remove</div>
                    </div>
            `;

            currentTerms.forEach((term, index) => {
                const yearsStr = term.months >= 12 ? ` (${(term.months / 12).toFixed(term.months % 12 === 0 ? 0 : 1)} yr${term.months > 12 ? 's' : ''})` : '';
                html += `
                    <div class="grid grid-cols-12 items-center px-3 py-2 text-xs">
                        <div class="col-span-5 font-bold text-slate-800 dark:text-slate-200 flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 flex-shrink-0"></span>
                            <span>${term.months} Months</span>
                            <span class="text-[10px] text-slate-400 dark:text-slate-500 font-medium">${yearsStr}</span>
                        </div>
                        <div class="col-span-5 flex items-center gap-1">
                            <input type="number" step="0.01" min="0" max="100" value="${term.interest_rate}" data-index="${index}" class="term-rate-input w-20 px-2 py-1 text-xs font-bold text-emerald-600 dark:text-emerald-400 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-950 rounded-lg outline-none focus:border-emerald-500">
                            <span class="text-[11px] font-bold text-slate-400 dark:text-slate-500">%</span>
                        </div>
                        <div class="col-span-2 text-right">
                            <button type="button" data-index="${index}" class="btn-remove-term p-1 text-slate-400 hover:text-rose-600 dark:hover:text-rose-400 transition-colors cursor-pointer rounded-lg hover:bg-rose-50 dark:hover:bg-rose-950/30">
                                <i class="fa-solid fa-trash-can text-xs"></i>
                            </button>
                        </div>
                    </div>
                `;
            });

            html += `</div>`;
            termsContainer.innerHTML = html;
        }

        function addOrUpdateTerm(months, rate) {
            const existingIndex = currentTerms.findIndex(t => t.months === months);
            const rateFormatted = Math.round(rate * 100) / 100;
            if (existingIndex > -1) {
                currentTerms[existingIndex].interest_rate = rateFormatted;
            } else {
                currentTerms.push({
                    months: months,
                    interest_rate: rateFormatted
                });
            }
            renderTermsList();
        }

        // Quick presets
        document.querySelectorAll(".btn-term-preset").forEach(btn => {
            btn.addEventListener("click", function() {
                const months = parseInt(this.getAttribute("data-months"));
                const baseRate = parseFloat(document.getElementById("prod-interest-rate").value) || 5.0;
                addOrUpdateTerm(months, baseRate);
            });
        });

        // Custom Add Term Button
        const btnAddCustomTerm = document.getElementById("btn-add-custom-term");
        if (btnAddCustomTerm) {
            btnAddCustomTerm.addEventListener("click", function() {
                const monthsInput = document.getElementById("input-new-term-months");
                const rateInput = document.getElementById("input-new-term-rate");
                const months = parseInt(monthsInput.value);
                const rate = parseFloat(rateInput.value);
                const alertInstance = window.MLSAKOAlert || Swal;

                if (isNaN(months) || months < 2 || months > 60) {
                    if (alertInstance) {
                        alertInstance.fire({
                            icon: 'warning',
                            title: 'Invalid Tenure Range',
                            text: 'Please enter a valid tenure between 2 and 60 months (up to 5 years).',
                            iconColor: '#f59e0b',
                            confirmButtonText: 'Understood'
                        });
                    } else {
                        alert("Please enter a valid tenure between 2 and 60 months.");
                    }
                    monthsInput.focus();
                    return;
                }

                if (isNaN(rate) || rate < 0 || rate > 100) {
                    if (alertInstance) {
                        alertInstance.fire({
                            icon: 'warning',
                            title: 'Invalid Interest Rate',
                            text: 'Please enter a valid interest rate percentage between 0% and 100%.',
                            iconColor: '#f59e0b',
                            confirmButtonText: 'Understood'
                        });
                    } else {
                        alert("Please enter a valid interest rate between 0% and 100%.");
                    }
                    rateInput.focus();
                    return;
                }

                addOrUpdateTerm(months, rate);
                monthsInput.value = "";
                rateInput.value = "";
            });
        }

        // Clear All Terms Button
        if (btnClearAllTerms) {
            btnClearAllTerms.addEventListener("click", function() {
                currentTerms = [];
                renderTermsList();
            });
        }

        // Event delegation for removal and inline rate adjustments
        if (termsContainer) {
            termsContainer.addEventListener("click", function(e) {
                const removeBtn = e.target.closest(".btn-remove-term");
                if (removeBtn) {
                    const index = parseInt(removeBtn.getAttribute("data-index"));
                    if (!isNaN(index) && currentTerms[index]) {
                        currentTerms.splice(index, 1);
                        renderTermsList();
                    }
                }
            });

            termsContainer.addEventListener("input", function(e) {
                const rateInput = e.target.closest(".term-rate-input");
                if (rateInput) {
                    const index = parseInt(rateInput.getAttribute("data-index"));
                    const val = parseFloat(rateInput.value);
                    if (!isNaN(index) && currentTerms[index] && !isNaN(val)) {
                        const clamped = Math.min(100, Math.max(0, val));
                        currentTerms[index].interest_rate = Math.round(clamped * 100) / 100;
                        hiddenAvailableTerms.value = JSON.stringify(currentTerms);
                    }
                }
            });
        }

        const saveBtn = document.getElementById("btn-save-loan-product");
        const confirmDeleteBtn = document.getElementById("btn-confirm-delete-loan");

        function openModal(modalId) {
            const modal = document.getElementById(modalId);
            const overlay = modal.querySelector(".modal-overlay");
            const container = modal.querySelector(".modal-container");

            // Reset submit button state
            if (saveBtn) {
                const btnSpinner = saveBtn.querySelector(".btn-spinner");
                const btnText = saveBtn.querySelector(".btn-text");
                if (btnSpinner) btnSpinner.classList.add("hidden");
                if (btnText) btnText.textContent = "Save Product Configuration";
                saveBtn.disabled = false;
                saveBtn.classList.remove("opacity-75", "cursor-wait");
            }

            if (confirmDeleteBtn) {
                const deleteSpinner = confirmDeleteBtn.querySelector(".btn-spinner");
                const deleteText = confirmDeleteBtn.querySelector(".btn-text");
                if (deleteSpinner) deleteSpinner.classList.add("hidden");
                if (deleteText) deleteText.textContent = "Confirm Deletion";
                confirmDeleteBtn.disabled = false;
                confirmDeleteBtn.classList.remove("opacity-75", "cursor-wait");
            }
            
            modal.classList.remove("hidden");
            
            // Allow browser layout pass to register classes
            setTimeout(() => {
                if (overlay) {
                    overlay.classList.remove("opacity-0", "pointer-events-none");
                    overlay.classList.add("opacity-100", "pointer-events-auto");
                }
                
                if (container) {
                    if (modalId === "modal-loan-product") {
                        // Slide drawer from the right
                        container.classList.remove("translate-x-[calc(100%+2rem)]");
                        container.classList.add("translate-x-0");
                    } else {
                        // Zoom in standard centered modal
                        container.classList.remove("scale-95", "opacity-0");
                        container.classList.add("scale-100", "opacity-100");
                    }
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
                if (modalId === "modal-loan-product") {
                    // Slide drawer back to the right
                    container.classList.add("translate-x-[calc(100%+2rem)]");
                    container.classList.remove("translate-x-0");
                } else {
                    // Zoom out standard centered modal
                    container.classList.add("scale-95", "opacity-0");
                    container.classList.remove("scale-100", "opacity-100");
                }
            }
            
            // Hide the wrapper after animation completes
            setTimeout(() => {
                modal.classList.add("hidden");
            }, 500);
        }

        // Setup click listeners for cancel/close controls
        document.querySelectorAll(".modal-close, .modal-overlay").forEach(btn => {
            btn.addEventListener("click", function() {
                const modal = this.closest('[id^="modal-"]');
                if (modal) closeModal(modal.id);
            });
        });

        // Add Loan Product Drawer Trigger
        const btnAddProduct = document.getElementById("btn-add-loan-product");
        const formProduct = document.getElementById("form-loan-product");
        const productTitle = document.getElementById("loan-product-title");
        const methodContainer = document.getElementById("method-field-container");
        const activeContainer = document.getElementById("prod-active-container");

        if (btnAddProduct) {
            btnAddProduct.addEventListener("click", function() {
                productTitle.textContent = "Create Loan Product";
                formProduct.action = "{{ route('admin.loans.store') }}";
                methodContainer.innerHTML = ""; // Empty implies POST
                activeContainer.classList.add("hidden");

                // Reset inputs
                document.getElementById("prod-category").value = "regular";
                document.getElementById("prod-name").value = "";
                document.getElementById("prod-interest-rate").value = "";
                document.getElementById("prod-max-term").value = "";
                document.getElementById("prod-loanable-amount").value = "";
                document.getElementById("prod-fixed-deposit").value = "";
                document.getElementById("prod-comakers").value = "0";
                document.getElementById("prod-partner").value = "";
                document.getElementById("prod-min-membership").value = "3";

                // Reset custom approval flow checkboxes (check all by default)
                stageCheckboxes.forEach(cb => {
                    cb.checked = true;
                });
                updateApprovalFlow();

                // Reset available terms builder
                currentTerms = [];
                renderTermsList();
                document.getElementById("input-new-term-months").value = "";
                document.getElementById("input-new-term-rate").value = "";

                openModal("modal-loan-product");
            });
        }

        // Edit Loan Product Trigger (Event Delegation to support dynamic AJAX rows!)
        document.addEventListener("click", function(e) {
            const btn = e.target.closest(".btn-edit-loan-product");
            if (!btn) return;

            const product = JSON.parse(btn.getAttribute("data-product"));

            productTitle.textContent = "Edit Loan Product";
            formProduct.action = `/admin/loans/products/${product.id}`;
            methodContainer.innerHTML = '<input type="hidden" name="_method" value="PUT">';
            activeContainer.classList.remove("hidden");

            // Populate fields
            document.getElementById("prod-category").value = product.category;
            document.getElementById("prod-name").value = product.name;
            document.getElementById("prod-interest-rate").value = product.interest_rate;
            document.getElementById("prod-max-term").value = product.max_term_months || "";
            document.getElementById("prod-loanable-amount").value = product.loanable_amount || "";
            document.getElementById("prod-fixed-deposit").value = Math.round(product.fixed_deposit);
            
            // Comakers (if object, stringify, else keep)
            let comakersVal = product.comakers;
            if (comakersVal !== null && typeof comakersVal === "object") {
                comakersVal = JSON.stringify(comakersVal);
            }
            document.getElementById("prod-comakers").value = comakersVal !== null ? comakersVal : "0";
            
            document.getElementById("prod-partner").value = product.partner || "";
            document.getElementById("prod-min-membership").value = product.minimum_membership_months || "";
            document.getElementById("prod-is-active").checked = !!product.is_active;

            // Handle custom approval flow populate
            let flow = product.approval_flow;
            if (flow && typeof flow === "string") {
                try {
                    flow = JSON.parse(flow);
                } catch(e) {
                    flow = flow.split(',').map(s => s.trim());
                }
            }
            if (!flow || !Array.isArray(flow)) {
                flow = ['comakers', 'sako_staff', 'hrmd_staff', 'credit_committee', 'accounting', 'releasing_officer'];
            }
            stageCheckboxes.forEach(cb => {
                cb.checked = flow.includes(cb.value);
            });
            updateApprovalFlow();

            // Populate available terms
            let terms = product.available_terms;
            if (terms && typeof terms === "string") {
                try {
                    terms = JSON.parse(terms);
                } catch(e) {
                    terms = [];
                }
            }
            currentTerms = Array.isArray(terms) ? JSON.parse(JSON.stringify(terms)) : [];
            renderTermsList();
            document.getElementById("input-new-term-months").value = "";
            document.getElementById("input-new-term-rate").value = "";

            openModal("modal-loan-product");
        });

        // Intercept form submit to show modern loading overlay before redirect
        if (formProduct) {
            formProduct.addEventListener("submit", function(e) {
                if (!formProduct.checkValidity()) {
                    return;
                }

                const alertInstance = window.MLSAKOAlert || Swal;
                const btnSpinner = saveBtn ? saveBtn.querySelector(".btn-spinner") : null;
                const btnText = saveBtn ? saveBtn.querySelector(".btn-text") : null;

                if (btnSpinner) btnSpinner.classList.remove("hidden");
                if (btnText) btnText.textContent = "Saving...";
                if (saveBtn) {
                    saveBtn.disabled = true;
                    saveBtn.classList.add("opacity-75", "cursor-wait");
                }

                if (alertInstance) {
                    alertInstance.fire({
                        title: 'Saving Loan Product...',
                        html: `
                            <div class="flex flex-col items-center justify-center p-4 space-y-4">
                                <div class="relative w-16 h-16 flex items-center justify-center">
                                    <div class="w-16 h-16 rounded-full border-4 border-emerald-500/20 border-t-emerald-500 animate-spin"></div>
                                    <span class="absolute text-xl"><i class="fa-solid fa-building-columns text-emerald-600"></i></span>
                                </div>
                                <div class="space-y-1 text-center">
                                    <p class="text-xs font-bold text-slate-800 dark:text-slate-100">
                                        Applying product parameters &amp; syncing approval workflow...
                                    </p>
                                    <p class="text-[11px] text-slate-500 dark:text-slate-400 font-medium">
                                        Saving configuration to cooperative ledger. Please wait a moment.
                                    </p>
                                </div>
                            </div>
                        `,
                        showConfirmButton: false,
                        allowOutsideClick: false,
                        allowEscapeKey: false
                    });
                }
            });
        }

        // Delete Loan Product Trigger (Event Delegation to support dynamic AJAX rows)
        const formDeleteLoan = document.getElementById("form-delete-loan");

        document.addEventListener("click", function(e) {
            const btn = e.target.closest(".btn-delete-loan-product");
            if (!btn) return;

            const id = btn.getAttribute("data-id");
            const name = btn.getAttribute("data-name") || "Loan Product";
            const category = btn.getAttribute("data-category") || "";
            const rate = btn.getAttribute("data-rate") || "";
            const term = btn.getAttribute("data-term") || "";

            const nameEl = document.getElementById("delete-loan-name");
            const categoryEl = document.getElementById("delete-loan-category");
            const rateEl = document.getElementById("delete-loan-rate");
            const termEl = document.getElementById("delete-loan-term");

            if (nameEl) nameEl.textContent = name;
            if (categoryEl) categoryEl.textContent = category;
            if (rateEl) rateEl.textContent = rate;
            if (termEl) termEl.textContent = term;
            if (formDeleteLoan) formDeleteLoan.action = `/admin/loans/products/${id}`;

            openModal("modal-delete-loan");
        });

        // Delete Form Submit Feedback
        if (formDeleteLoan) {
            formDeleteLoan.addEventListener("submit", function() {
                if (confirmDeleteBtn) {
                    const deleteSpinner = confirmDeleteBtn.querySelector(".btn-spinner");
                    const deleteText = confirmDeleteBtn.querySelector(".btn-text");
                    if (deleteSpinner) deleteSpinner.classList.remove("hidden");
                    if (deleteText) deleteText.textContent = "Deleting...";
                    confirmDeleteBtn.disabled = true;
                    confirmDeleteBtn.classList.add("opacity-75", "cursor-wait");
                }
            });
        }

        // Global Escape Key Listener to dismiss any open modal or custom dropdown
        document.addEventListener("keydown", function(e) {
            if (e.key === "Escape") {
                // Close dropdowns
                document.querySelectorAll("[id^='dropdown-menu']").forEach(menu => {
                    menu.classList.add("opacity-0", "scale-95", "pointer-events-none");
                    menu.classList.remove("opacity-100", "scale-100", "pointer-events-auto");
                    const arrow = menu.closest(".custom-dropdown")?.querySelector(".dropdown-arrow");
                    if (arrow) arrow.classList.remove("rotate-180");
                });

                // Close modals
                const openModals = document.querySelectorAll('[id^="modal-"]:not(.hidden)');
                openModals.forEach(m => closeModal(m.id));
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

                    // Sync category chips if category was selected
                    if (input && input.id === "filter-category") {
                        updateChipStyles(val);
                    }

                    fetchFilteredData();
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
        // INTERACTIVE FILTERING & SMOOTH SEARCHING
        // ==========================================
        const searchInput = document.getElementById("ajax-search");
        const btnClearSearch = document.getElementById("btn-clear-search");
        const categoryFilter = document.getElementById("filter-category");
        const statusFilter = document.getElementById("filter-status");
        const hrmdFilter = document.getElementById("filter-hrmd");
        const tableBody = document.getElementById("loans-table-body");
        const productCountBadge = document.getElementById("product-count-badge");
        const categoryChips = document.querySelectorAll(".category-chip");
        const btnResetFilters = document.getElementById("btn-reset-filters");
        const searchSpinner = document.getElementById("search-spinner");
        const searchIcon = document.getElementById("search-icon");

        let debounceTimeout = null;
        let searchAbortController = null;

        function updateChipStyles(activeCategory) {
            categoryChips.forEach(chip => {
                if (chip.getAttribute("data-category") === activeCategory) {
                    chip.className = "category-chip px-2.5 py-1 rounded-lg text-[10px] font-bold border transition-all cursor-pointer bg-slate-900 text-white border-slate-900 dark:bg-white dark:text-slate-900 dark:border-white shadow-2xs";
                } else {
                    chip.className = "category-chip px-2.5 py-1 rounded-lg text-[10px] font-bold border border-slate-200 dark:border-slate-700/80 text-slate-600 dark:text-slate-400 hover:border-emerald-500 hover:text-emerald-600 dark:hover:text-emerald-400 transition-all cursor-pointer";
                }
            });
        }

        function fetchFilteredData() {
            const search = searchInput ? searchInput.value.trim() : "";
            const category = categoryFilter ? categoryFilter.value : "all";
            const status = statusFilter ? statusFilter.value : "all";
            const hrmd = hrmdFilter ? hrmdFilter.value : "all";

            // Update clear button visibility
            if (btnClearSearch) {
                if (search.length > 0) {
                    btnClearSearch.classList.remove("hidden");
                } else {
                    btnClearSearch.classList.add("hidden");
                }
            }

            // Visual search indicators
            if (searchSpinner) searchSpinner.classList.remove("hidden");
            if (searchIcon) searchIcon.classList.add("text-emerald-500");

            // Smooth table dimming
            if (tableBody) {
                tableBody.classList.add("opacity-40", "transition-opacity", "duration-200");
            }

            // Abort ongoing in-flight fetch to prevent race conditions
            if (searchAbortController) {
                searchAbortController.abort();
            }
            searchAbortController = new AbortController();

            const url = `{{ route('admin.loans.management') }}?search=${encodeURIComponent(search)}&category=${category}&status=${status}&hrmd=${hrmd}`;

            fetch(url, {
                signal: searchAbortController.signal,
                headers: {
                    "X-Requested-With": "XMLHttpRequest"
                }
            })
            .then(response => response.json())
            .then(data => {
                if (tableBody) {
                    tableBody.innerHTML = data.html;

                    // Update count badge dynamically
                    if (productCountBadge) {
                        const rowCount = tableBody.querySelectorAll("tr:not(:has(td[colspan]))").length;
                        productCountBadge.textContent = `${rowCount} Product${rowCount === 1 ? '' : 's'}`;
                    }
                }
            })
            .catch(error => {
                if (error.name !== "AbortError") {
                    console.error("Filtering error:", error);
                }
            })
            .finally(() => {
                if (searchSpinner) searchSpinner.classList.add("hidden");
                if (searchIcon && search.length === 0) searchIcon.classList.remove("text-emerald-500");
                if (tableBody) tableBody.classList.remove("opacity-40");
            });
        }

        // Live smooth search with debounce
        if (searchInput) {
            searchInput.addEventListener("input", function() {
                if (searchSpinner) searchSpinner.classList.remove("hidden");
                if (searchIcon) searchIcon.classList.add("text-emerald-500");
                
                clearTimeout(debounceTimeout);
                debounceTimeout = setTimeout(fetchFilteredData, 220);
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

        // Instant Clear Search Button
        if (btnClearSearch) {
            btnClearSearch.addEventListener("click", function() {
                if (searchInput) {
                    searchInput.value = "";
                    searchInput.focus();
                }
                btnClearSearch.classList.add("hidden");
                fetchFilteredData();
            });
        }

        // Quick Category Chip Clicks
        categoryChips.forEach(chip => {
            chip.addEventListener("click", function() {
                const selectedCat = this.getAttribute("data-category");
                const catInput = document.getElementById("filter-category");
                const catLabel = document.getElementById("label-category");
                
                if (catInput) catInput.value = selectedCat;
                if (catLabel) {
                    const matchingItem = document.querySelector(`#dropdown-menu-category .dropdown-item[data-value="${selectedCat}"]`);
                    catLabel.textContent = matchingItem ? matchingItem.querySelector("span").textContent : "All Categories";
                }

                // Update checkmarks in category menu
                document.querySelectorAll("#dropdown-menu-category .dropdown-item").forEach(i => {
                    const check = i.querySelector(".check-icon");
                    if (check) check.classList.toggle("hidden", i.getAttribute("data-value") !== selectedCat);
                });

                updateChipStyles(selectedCat);
                fetchFilteredData();
            });
        });

        // Reset All Filters Button
        if (btnResetFilters) {
            btnResetFilters.addEventListener("click", function() {
                if (searchInput) searchInput.value = "";
                if (btnClearSearch) btnClearSearch.classList.add("hidden");

                // Reset category
                const catInput = document.getElementById("filter-category");
                const catLabel = document.getElementById("label-category");
                if (catInput) catInput.value = "all";
                if (catLabel) catLabel.textContent = "All Categories";
                document.querySelectorAll("#dropdown-menu-category .dropdown-item").forEach(i => {
                    const check = i.querySelector(".check-icon");
                    if (check) check.classList.toggle("hidden", i.getAttribute("data-value") !== "all");
                });

                // Reset status
                const statusInput = document.getElementById("filter-status");
                const statusLabel = document.getElementById("label-status");
                if (statusInput) statusInput.value = "all";
                if (statusLabel) statusLabel.textContent = "All Status";
                document.querySelectorAll("#dropdown-menu-status .dropdown-item").forEach(i => {
                    const check = i.querySelector(".check-icon");
                    if (check) check.classList.toggle("hidden", i.getAttribute("data-value") !== "all");
                });

                // Reset hrmd
                const hrmdInput = document.getElementById("filter-hrmd");
                const hrmdLabel = document.getElementById("label-hrmd");
                if (hrmdInput) hrmdInput.value = "all";
                if (hrmdLabel) hrmdLabel.textContent = "All HRMD";
                document.querySelectorAll("#dropdown-menu-hrmd .dropdown-item").forEach(i => {
                    const check = i.querySelector(".check-icon");
                    if (check) check.classList.toggle("hidden", i.getAttribute("data-value") !== "all");
                });

                updateChipStyles("all");
                fetchFilteredData();
            });
        }

    });
</script>
@endpush
