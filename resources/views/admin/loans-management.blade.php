@extends('layouts.admin')

@section('title', 'Loans Management - Sako Cooperative')

@section('header')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h1 class="text-2xl font-extrabold text-slate-900 dark:text-slate-100 tracking-tight serif-font">Loans Management</h1>
        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 font-semibold">Configure active loan facilities, interest rates, borrowing limits, co-maker matrix configs, and approval flow requirements.</p>
    </div>
    <div>
        <button id="btn-add-loan-product" class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs px-4 py-2.5 rounded-xl shadow-md shadow-emerald-600/10 hover:shadow-emerald-600/20 hover:-translate-y-0.5 transition-all duration-200 cursor-pointer">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
            Create Loan Facility
        </button>
    </div>
</div>
@endsection

@section('content')
<div class="space-y-6 animate-fade-in">

    <!-- Search & Filters Panel (Enhanced with AJAX dynamic selectors) -->
    <div class="bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700/80 p-5 rounded-3xl shadow-sm flex flex-col gap-4">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Real-time Text Search -->
            <div class="relative">
                <span class="absolute left-3.5 top-3.5 text-slate-400 dark:text-slate-500 text-xs">🔍</span>
                <input type="text" id="ajax-search" value="{{ $search }}" placeholder="Search by name or partner..." class="w-full pl-9 pr-4 py-2.5 text-xs font-medium border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 rounded-xl focus:border-emerald-500 dark:focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 dark:focus:ring-emerald-500/10 placeholder-slate-400 dark:placeholder-slate-500 transition-all outline-none">
            </div>

            <!-- Filter by Category -->
            <div>
                <select id="filter-category" class="w-full px-3 py-2.5 text-xs font-medium border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 rounded-xl outline-none focus:border-emerald-500 dark:focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 dark:focus:ring-emerald-500/10 transition-all cursor-pointer">
                    <option value="all">All Categories</option>
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

            <!-- Filter by Status -->
            <div>
                <select id="filter-status" class="w-full px-3 py-2.5 text-xs font-medium border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 rounded-xl outline-none focus:border-emerald-500 dark:focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 dark:focus:ring-emerald-500/10 transition-all cursor-pointer">
                    <option value="all">All Status</option>
                    <option value="active">Active Only</option>
                    <option value="inactive">Inactive Only</option>
                </select>
            </div>

            <!-- Filter by HRMD Needs -->
            <div>
                <select id="filter-hrmd" class="w-full px-3 py-2.5 text-xs font-medium border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 rounded-xl outline-none focus:border-emerald-500 dark:focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 dark:focus:ring-emerald-500/10 transition-all cursor-pointer">
                    <option value="all">All Workflow Needs</option>
                    <option value="yes">Requires HRMD Approval</option>
                    <option value="no">No HRMD Approval</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Active Facilities Ledger Table -->
    <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-100 dark:border-slate-700/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-900/50 border-b border-slate-100 dark:border-slate-700/80 text-[10px] font-extrabold text-slate-400 dark:text-slate-500 uppercase tracking-widest">
                        <th class="px-6 py-4.5">Facility Name / Category</th>
                        <th class="px-6 py-4.5">Loanable Limit</th>
                        <th class="px-6 py-4.5">Interest Rate (Flat)</th>
                        <th class="px-6 py-4.5">Required Share Capital</th>
                        <th class="px-6 py-4.5">Max Tenure</th>
                        <th class="px-6 py-4.5">Workflow Needs</th>
                        <th class="px-6 py-4.5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody id="loans-table-body" class="divide-y divide-slate-50 dark:divide-slate-700/50 text-slate-700 dark:text-slate-300 font-medium">
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
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-2xl shadow-slate-950/15 dark:shadow-slate-950/50 overflow-hidden h-[calc(100vh-2rem)] w-[calc(100vw-2rem)] sm:w-full sm:max-w-xl fixed right-4 top-4 bottom-4 z-50 transform translate-x-[calc(100%+2rem)] transition-transform duration-500 ease-[cubic-bezier(0.16,1,0.3,1)] modal-container p-6 sm:p-8 flex flex-col">
        <!-- Header -->
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-4">
            <div>
                <h3 class="text-xl font-extrabold text-slate-900 dark:text-slate-100 tracking-tight serif-font" id="loan-product-title">Create New Loan</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 font-medium mt-1">Parameters & Approval Requisites</p>
            </div>
            <button class="modal-close p-1.5 rounded-xl text-slate-400 hover:text-slate-700 dark:text-slate-500 dark:hover:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800/80 transition-all duration-200">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
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
                        <label class="text-xs font-semibold text-slate-700 dark:text-slate-300">Facility Name</label>
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
                                <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2-2v12a2 2 0 002 2z"/></svg>
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
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
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
                        <span class="ml-2 text-xs font-semibold text-slate-700 dark:text-slate-300">Facility is Active?</span>
                    </label>
                </div>

                <div class="space-y-2 col-span-1 sm:col-span-2">
                    <label class="text-xs font-semibold text-slate-700 dark:text-slate-300">Custom Approval Flow Stages</label>
                    <p class="text-[10px] text-slate-500 dark:text-slate-400 font-medium">Configure the exact path of stages this loan facility must undergo:</p>
                    <div class="grid grid-cols-2 gap-3 bg-slate-50 dark:bg-slate-950/40 border border-slate-200 dark:border-slate-800 p-3.5 rounded-2xl">
                        <label class="flex items-center gap-2 cursor-pointer select-none">
                            <input type="checkbox" class="prod-stage-checkbox rounded border-slate-350 dark:border-slate-700 text-emerald-600 focus:ring-emerald-500/20" value="comakers" checked>
                            <span class="text-xs font-medium text-slate-700 dark:text-slate-300">Co-Makers</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer select-none">
                            <input type="checkbox" class="prod-stage-checkbox rounded border-slate-350 dark:border-slate-700 text-emerald-600 focus:ring-emerald-500/20" value="sako_staff" checked>
                            <span class="text-xs font-medium text-slate-700 dark:text-slate-300">Sako Staff</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer select-none">
                            <input type="checkbox" class="prod-stage-checkbox rounded border-slate-350 dark:border-slate-700 text-emerald-600 focus:ring-emerald-500/20" value="hrmd_staff" checked>
                            <span class="text-xs font-medium text-slate-700 dark:text-slate-300">HRMD Staff</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer select-none">
                            <input type="checkbox" class="prod-stage-checkbox rounded border-slate-350 dark:border-slate-700 text-emerald-600 focus:ring-emerald-500/20" value="credit_committee" checked>
                            <span class="text-xs font-medium text-slate-700 dark:text-slate-300">Credit Comm.</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer select-none">
                            <input type="checkbox" class="prod-stage-checkbox rounded border-slate-350 dark:border-slate-700 text-emerald-600 focus:ring-emerald-500/20" value="accounting" checked>
                            <span class="text-xs font-medium text-slate-700 dark:text-slate-300">Accounting</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer select-none">
                            <input type="checkbox" class="prod-stage-checkbox rounded border-slate-350 dark:border-slate-700 text-emerald-600 focus:ring-emerald-500/20" value="releasing_officer" checked>
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
                    <span class="btn-text">Save Configuration</span>
                </button>
            </div>
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
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-4v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
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

        function openModal(modalId) {
            const modal = document.getElementById(modalId);
            const overlay = modal.querySelector(".modal-overlay");
            const container = modal.querySelector(".modal-container");

            // Reset submit button state
            if (saveBtn) {
                const btnSpinner = saveBtn.querySelector(".btn-spinner");
                const btnText = saveBtn.querySelector(".btn-text");
                if (btnSpinner) btnSpinner.classList.add("hidden");
                if (btnText) btnText.textContent = "Save Configuration";
                saveBtn.disabled = false;
                saveBtn.classList.remove("opacity-75", "cursor-wait");
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
                productTitle.textContent = "Create New Loan";
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

            productTitle.textContent = "Edit Loan Requirements";
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
                        title: 'Saving Loan Facility...',
                        html: `
                            <div class="flex flex-col items-center justify-center p-4 space-y-4">
                                <div class="relative w-16 h-16 flex items-center justify-center">
                                    <div class="w-16 h-16 rounded-full border-4 border-emerald-500/20 border-t-emerald-500 animate-spin"></div>
                                    <span class="absolute text-xl">🏛️</span>
                                </div>
                                <div class="space-y-1 text-center">
                                    <p class="text-xs font-bold text-slate-800 dark:text-slate-100">
                                        Applying facility parameters &amp; syncing approval workflow...
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

        // AJAX Filtering Logic
        const searchInput = document.getElementById("ajax-search");
        const categoryFilter = document.getElementById("filter-category");
        const statusFilter = document.getElementById("filter-status");
        const hrmdFilter = document.getElementById("filter-hrmd");
        const tableBody = document.getElementById("loans-table-body");

        let debounceTimeout = null;

        function fetchFilteredData() {
            const search = searchInput ? searchInput.value : "";
            const category = categoryFilter ? categoryFilter.value : "all";
            const status = statusFilter ? statusFilter.value : "all";
            const hrmd = hrmdFilter ? hrmdFilter.value : "all";

            // Show loading state
            if (tableBody) {
                tableBody.classList.add("opacity-50", "pointer-events-none");
            }

            const url = `{{ route('admin.loans.management') }}?search=${encodeURIComponent(search)}&category=${category}&status=${status}&hrmd=${hrmd}`;

            fetch(url, {
                headers: {
                    "X-Requested-With": "XMLHttpRequest"
                }
            })
            .then(response => response.json())
            .then(data => {
                if (tableBody) {
                    tableBody.innerHTML = data.html;
                    tableBody.classList.remove("opacity-50", "pointer-events-none");
                }
            })
            .catch(error => {
                console.error("Filtering error:", error);
                if (tableBody) {
                    tableBody.classList.remove("opacity-50", "pointer-events-none");
                }
            });
        }

        if (searchInput) {
            searchInput.addEventListener("input", function() {
                clearTimeout(debounceTimeout);
                debounceTimeout = setTimeout(fetchFilteredData, 250);
            });
        }

        [categoryFilter, statusFilter, hrmdFilter].forEach(el => {
            if (el) el.addEventListener("change", fetchFilteredData);
        });

    });
</script>
@endpush
