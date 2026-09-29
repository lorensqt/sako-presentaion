@extends('layouts.user')

@section('title', 'Loans Portal - ML Sako')

@section('navbar_title', 'Cooperative Loans Hub')
@section('navbar_subtitle', 'Digitally submit cooperative application files and simulate repayment amortizations.')

@section('content')
<div class="space-y-6 sm:space-y-8 animate-fade-in pb-12">

    <!-- Top Action Toolbar -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-2 border-b border-slate-100 dark:border-slate-800/60">
        <div class="flex flex-wrap items-center gap-2">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-50 dark:bg-emerald-950/30 text-emerald-800 dark:text-emerald-400 text-xs font-bold border border-emerald-100 dark:border-emerald-800/40">
                <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse"></span>
                Application Portal: Online
            </span>
            <span class="text-xs text-slate-500 dark:text-slate-400 font-bold bg-slate-100 dark:bg-slate-800 px-3 py-1.5 rounded-xl border border-slate-200 dark:border-slate-700">
                ID: {{ Auth::user()->company_id ?: 'N/A' }}
            </span>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('member.loans') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-bold transition-all duration-200">
                <svg class="w-4 h-4 flex-shrink-0 text-slate-500 dark:text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
                <span>View My Active Loans</span>
            </a>
        </div>
    </div>

    <!-- MAIN TWO-COLUMN LAYOUT -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Left Column: Sticky Real-time Filing Summary Card -->
        <div class="lg:col-span-1 order-2 lg:order-1">
            <div class="lg:sticky lg:top-8 space-y-5">
                <div class="bg-gradient-to-tr from-slate-900 to-emerald-950 text-white rounded-[2rem] border border-slate-800 dark:border-slate-700 shadow-2xl p-6 sm:p-8 space-y-6 relative overflow-hidden flex flex-col justify-between min-h-[480px]">
                    <div class="absolute -bottom-24 -right-24 w-56 h-56 bg-emerald-500/10 rounded-full blur-3xl"></div>
                    
                    <div class="space-y-6">
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] font-black uppercase tracking-widest text-emerald-400 bg-emerald-500/10 border border-emerald-500/20 px-3 py-1 rounded-full">Filing Summary</span>
                            <div class="flex items-center gap-1.5">
                                <span class="relative flex h-2 w-2">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                                </span>
                                <span class="text-[9px] font-bold uppercase tracking-wider text-emerald-400 font-mono">DRAFT ACTIVE</span>
                            </div>
                        </div>

                        <!-- LIVE RECEIPT/SUMMARY GRID -->
                        <div class="space-y-4 border-t border-slate-800/80 pt-4">
                            <div class="space-y-1">
                                <span class="text-[9px] font-bold uppercase tracking-widest text-slate-400 block">Selected Product</span>
                                <p id="summary-product" class="text-xs font-extrabold text-slate-100">None Selected</p>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div class="space-y-1">
                                    <span class="text-[9px] font-bold uppercase tracking-widest text-slate-400 block">Max Limit</span>
                                    <p id="summary-limit" class="text-xs font-bold text-slate-200">--</p>
                                </div>
                                <div class="space-y-1">
                                    <span class="text-[9px] font-bold uppercase tracking-widest text-slate-400 block">Max Term</span>
                                    <p id="summary-max-term" class="text-xs font-bold text-slate-200">--</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4 border-t border-slate-800/50 pt-3">
                                <div class="space-y-1">
                                    <span class="text-[9px] font-bold uppercase tracking-widest text-slate-400 block">Filing Amount</span>
                                    <p id="summary-amount" class="text-xs font-extrabold text-emerald-400">₱0.00</p>
                                </div>
                                <div class="space-y-1">
                                    <span class="text-[9px] font-bold uppercase tracking-widest text-slate-400 block">Filing Term</span>
                                    <p id="summary-term" class="text-xs font-bold text-slate-200">--</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4 border-t border-slate-800/50 pt-3">
                                <div class="space-y-1">
                                    <span class="text-[9px] font-bold uppercase tracking-widest text-slate-400 block">Monthly Deduction Est.</span>
                                    <p id="summary-monthly" class="text-xs font-black text-emerald-400">₱0.00 <span class="text-[10px] text-slate-400 font-semibold font-sans">/mo</span></p>
                                </div>
                                <div class="space-y-1">
                                    <span class="text-[9px] font-bold uppercase tracking-widest text-slate-400 block">Per Payday Est.</span>
                                    <p id="summary-payday" class="text-xs font-black text-emerald-400">₱0.00 <span class="text-[10px] text-slate-400 font-semibold font-sans">/semi</span></p>
                                </div>
                            </div>

                            <div class="space-y-1 border-t border-slate-800/50 pt-3">
                                <span class="text-[9px] font-bold uppercase tracking-widest text-slate-400 block">Co-Makers Endorsements</span>
                                <p id="summary-comakers" class="text-xs font-bold text-slate-200">None Required</p>
                            </div>

                            <div class="space-y-1 border-t border-slate-800/50 pt-3">
                                <span class="text-[9px] font-bold uppercase tracking-widest text-slate-400 block">Documents (PDF)</span>
                                <p id="summary-documents" class="text-xs font-bold text-slate-200">0 / 5 Max Attachments</p>
                            </div>
                        </div>
                    </div>

                    <div class="pt-6 border-t border-slate-800/80 flex items-center justify-between text-[10px] text-slate-400 font-semibold font-mono">
                        <span>WIZARD STATUS</span>
                        <span id="summary-step-indicator" class="text-emerald-500 font-bold">STEP 1 OF 5</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Horizontal Step-by-Step Wizard Form -->
        <div class="lg:col-span-2">
            <form action="{{ route('member.loans.apply') }}" method="POST" id="loan-wizard-form" class="space-y-6" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="pin" id="loan-pin-input">
                @if(isset($resubmitApp))
                    <input type="hidden" name="resubmit_id" value="{{ $resubmitApp->id }}">
                @endif
                
                <!-- WIZARD MAIN CARD -->
                <div class="bg-white dark:bg-slate-800 rounded-[2.5rem] border-2 border-slate-100 dark:border-slate-700 shadow-sm p-5 sm:p-8 space-y-8 flex flex-col justify-between min-h-[500px]">
                    
                    <!-- DYNAMIC HORIZONTAL TIMELINE -->
                    <div class="bg-slate-50/50 dark:bg-slate-900/60 p-3 sm:p-4 rounded-2xl border border-slate-100/80 dark:border-slate-800/80">
                        <div class="relative flex items-center justify-between w-full max-w-xl mx-auto">
                            <!-- Background connecting line -->
                            <div class="absolute left-0 right-0 top-[16px] sm:top-[18px] h-0.5 bg-slate-200 dark:bg-slate-700 z-0 rounded-full"></div>
                            <!-- Active tracking line -->
                            <div id="timeline-progress-line" class="absolute left-0 top-[16px] sm:top-[18px] h-0.5 bg-emerald-500 z-0 rounded-full transition-all duration-500 ease-in-out" style="width: 0%;"></div>

                            <!-- Step 1 Node -->
                            <button type="button" class="step-node relative flex flex-col items-center gap-1.5 sm:gap-2 z-10 focus:outline-none" data-step="1">
                                <div class="step-circle w-8 h-8 sm:w-9 sm:h-9 rounded-full flex items-center justify-center text-xs font-black transition-all duration-300 bg-emerald-500 text-white shadow-md shadow-emerald-500/20 ring-4 ring-emerald-500/10">
                                    1
                                </div>
                                <span class="step-label inline-flex items-center gap-1 sm:gap-1.5 text-xs sm:text-[10px] font-black uppercase tracking-wider text-slate-900 dark:text-slate-100 transition-colors" title="Package Selection">
                                    <i class="fa-solid fa-layer-group text-xs sm:text-[10px]"></i>
                                    <span class="hidden sm:inline">Package</span>
                                </span>
                            </button>

                            <!-- Step 2 Node -->
                            <button type="button" class="step-node relative flex flex-col items-center gap-1.5 sm:gap-2 z-10 focus:outline-none" data-step="2">
                                <div class="step-circle w-8 h-8 sm:w-9 sm:h-9 rounded-full flex items-center justify-center text-xs font-black transition-all duration-300 bg-slate-200 text-slate-500 dark:bg-slate-800 dark:text-slate-400">
                                    2
                                </div>
                                <span class="step-label inline-flex items-center gap-1 sm:gap-1.5 text-xs sm:text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 transition-colors" title="Repayment Simulation">
                                    <i class="fa-solid fa-calculator text-xs sm:text-[10px]"></i>
                                    <span class="hidden sm:inline">Repayment</span>
                                </span>
                            </button>

                            <!-- Step 3 Node -->
                            <button type="button" class="step-node relative flex flex-col items-center gap-1.5 sm:gap-2 z-10 focus:outline-none" data-step="3">
                                <div class="step-circle w-8 h-8 sm:w-9 sm:h-9 rounded-full flex items-center justify-center text-xs font-black transition-all duration-300 bg-slate-200 text-slate-500 dark:bg-slate-800 dark:text-slate-400">
                                    3
                                </div>
                                <span class="step-label inline-flex items-center gap-1 sm:gap-1.5 text-xs sm:text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 transition-colors" title="Co-Makers Endorsements">
                                    <i class="fa-solid fa-user-group text-xs sm:text-[10px]"></i>
                                    <span class="hidden sm:inline">Co-Makers</span>
                                </span>
                            </button>

                            <!-- Step 4 Node -->
                            <button type="button" class="step-node relative flex flex-col items-center gap-1.5 sm:gap-2 z-10 focus:outline-none" data-step="4">
                                <div class="step-circle w-8 h-8 sm:w-9 sm:h-9 rounded-full flex items-center justify-center text-xs font-black transition-all duration-300 bg-slate-200 text-slate-500 dark:bg-slate-800 dark:text-slate-400">
                                    4
                                </div>
                                <span class="step-label inline-flex items-center gap-1 sm:gap-1.5 text-xs sm:text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 transition-colors" title="Compliance Documents">
                                    <i class="fa-solid fa-file-lines text-xs sm:text-[10px]"></i>
                                    <span class="hidden sm:inline">Documents</span>
                                </span>
                            </button>

                            <!-- Step 5 Node -->
                            <button type="button" class="step-node relative flex flex-col items-center gap-1.5 sm:gap-2 z-10 focus:outline-none" data-step="5">
                                <div class="step-circle w-8 h-8 sm:w-9 sm:h-9 rounded-full flex items-center justify-center text-xs font-black transition-all duration-300 bg-slate-200 text-slate-500 dark:bg-slate-800 dark:text-slate-400">
                                    5
                                </div>
                                <span class="step-label inline-flex items-center gap-1 sm:gap-1.5 text-xs sm:text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 transition-colors" title="Final Filing & Remarks">
                                    <i class="fa-solid fa-clipboard-check text-xs sm:text-[10px]"></i>
                                    <span class="hidden sm:inline">Filing</span>
                                </span>
                            </button>
                        </div>
                    </div>

                    <!-- STEP PANEL VIEWS -->
                    <div class="flex-grow pt-2">

                        <!-- PANEL 1: FACILITY SELECTION -->
                        <div class="wizard-panel space-y-5 transition-all duration-300 ease-out" id="panel-step-1">
                            <div>
                                <h3 class="text-xs sm:text-sm font-extrabold text-slate-900 dark:text-white uppercase tracking-wider">Loan Facility Package Selection</h3>
                                <p class="text-[11px] sm:text-xs text-slate-500 dark:text-slate-400 mt-1 font-medium leading-snug">Select your desired cooperative loan product facility to configure your package.</p>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-5">
                                <!-- Category Custom Dropdown -->
                                <div class="space-y-1.5 custom-select-container relative" id="container-category">
                                    <label class="text-[10px] sm:text-[11px] font-bold uppercase text-slate-600 dark:text-slate-400 tracking-wider flex items-center justify-between">
                                        <span>Loan Category</span>
                                        <span class="text-[9px] sm:text-[10px] text-emerald-600 dark:text-emerald-400 font-extrabold" id="category-count-badge">{{ count($loanConfig) }} facilities</span>
                                    </label>
                                    
                                    <!-- Native Hidden Select for FormData & Validation -->
                                    <select name="category" id="loan-category" required class="hidden">
                                        <option value="" disabled selected>Select category...</option>
                                        @foreach($loanConfig as $catSlug => $packages)
                                            <option value="{{ $catSlug }}">{{ ucwords(str_replace('_', ' ', $catSlug)) }} Loan</option>
                                        @endforeach
                                    </select>

                                    <!-- Custom Trigger Button -->
                                    <button type="button" id="custom-category-trigger" aria-haspopup="listbox" aria-expanded="false" class="w-full flex items-center justify-between px-3.5 py-3 text-xs border border-slate-200 dark:border-slate-700/80 rounded-xl bg-white dark:bg-slate-950 text-slate-800 dark:text-slate-100 hover:border-emerald-500/60 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-all shadow-xs text-left cursor-pointer group">
                                        <div class="flex items-center gap-2.5 truncate">
                                            <div class="w-7 h-7 rounded-lg bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-100 dark:border-emerald-900/40 flex items-center justify-center text-emerald-600 dark:text-emerald-400 shrink-0 group-hover:scale-105 transition-transform">
                                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                                            </div>
                                            <div class="truncate">
                                                <span id="custom-category-label" class="font-bold text-slate-800 dark:text-slate-200 block truncate">Select category...</span>
                                                <span id="custom-category-sublabel" class="text-[10px] text-slate-400 block truncate font-medium">Choose loan facility category</span>
                                            </div>
                                        </div>
                                        <svg id="custom-category-chevron" class="w-4 h-4 text-slate-400 transition-transform duration-200 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                    </button>

                                    <!-- Custom Popover Menu -->
                                    <div id="custom-category-menu" role="listbox" class="hidden absolute left-0 right-0 top-full mt-1.5 z-40 bg-white/95 dark:bg-slate-900/95 backdrop-blur-md border border-slate-200 dark:border-slate-800 rounded-2xl shadow-xl p-1.5 space-y-1 max-h-64 overflow-y-auto">
                                        @foreach($loanConfig as $catSlug => $packages)
                                            <button type="button" role="option" data-value="{{ $catSlug }}" class="custom-category-item w-full flex items-center justify-between px-3 py-2.5 rounded-xl text-left text-xs hover:bg-emerald-50 dark:hover:bg-emerald-950/40 text-slate-700 dark:text-slate-300 hover:text-emerald-700 dark:hover:text-emerald-300 transition-colors cursor-pointer group">
                                                <div class="flex items-center gap-2 truncate">
                                                    <span class="w-2 h-2 rounded-full bg-slate-300 dark:bg-slate-700 group-hover:bg-emerald-500 transition-colors"></span>
                                                    <span class="font-bold">{{ ucwords(str_replace('_', ' ', $catSlug)) }} Loan</span>
                                                </div>
                                                <span class="text-[9px] sm:text-[10px] font-extrabold uppercase px-2 py-0.5 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 group-hover:bg-emerald-100 dark:group-hover:bg-emerald-900/60 group-hover:text-emerald-700 dark:group-hover:text-emerald-300 transition-colors shrink-0">
                                                    {{ count($packages) }} {{ count($packages) === 1 ? 'pkg' : 'pkgs' }}
                                                </span>
                                            </button>
                                        @endforeach
                                    </div>
                                </div>
                                
                                <!-- Package Custom Dropdown -->
                                <div class="space-y-1.5 custom-select-container relative" id="container-type">
                                    <label class="text-[10px] sm:text-[11px] font-bold uppercase text-slate-600 dark:text-slate-400 tracking-wider flex items-center justify-between">
                                        <span>Loan Product Package</span>
                                        <span class="text-[9px] sm:text-[10px] text-slate-400 font-semibold" id="type-count-badge">Step 1.2</span>
                                    </label>

                                    <!-- Native Hidden Select for FormData & Validation -->
                                    <select name="type" id="loan-type" required disabled class="hidden">
                                        <option value="" disabled selected>Select package...</option>
                                    </select>

                                    <!-- Custom Trigger Button -->
                                    <button type="button" id="custom-type-trigger" disabled aria-haspopup="listbox" aria-expanded="false" class="w-full flex items-center justify-between px-3.5 py-3 text-xs border border-slate-200 dark:border-slate-700/80 rounded-xl bg-white dark:bg-slate-950 text-slate-800 dark:text-slate-100 hover:border-emerald-500/60 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-all shadow-xs text-left cursor-not-allowed opacity-50 group">
                                        <div class="flex items-center gap-2.5 truncate">
                                            <div class="w-7 h-7 rounded-lg bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-100 dark:border-emerald-900/40 flex items-center justify-center text-emerald-600 dark:text-emerald-400 shrink-0 group-hover:scale-105 transition-transform">
                                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                            </div>
                                            <div class="truncate">
                                                <span id="custom-type-label" class="font-bold text-slate-800 dark:text-slate-200 block truncate">Select category first</span>
                                                <span id="custom-type-sublabel" class="text-[10px] text-slate-400 block truncate font-medium">Specific loan facility</span>
                                            </div>
                                        </div>
                                        <svg id="custom-type-chevron" class="w-4 h-4 text-slate-400 transition-transform duration-200 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                    </button>

                                    <!-- Custom Popover Menu -->
                                    <div id="custom-type-menu" role="listbox" class="hidden absolute left-0 right-0 top-full mt-1.5 z-40 bg-white/95 dark:bg-slate-900/95 backdrop-blur-md border border-slate-200 dark:border-slate-800 rounded-2xl shadow-xl p-1.5 space-y-1 max-h-64 overflow-y-auto">
                                        <!-- Dynamically populated via JS based on selected category -->
                                    </div>
                                </div>
                            </div>

                            <!-- Package Details Cards -->
                            <div id="package-info-card" class="hidden bg-slate-50 dark:bg-slate-950/60 border border-slate-200/80 dark:border-slate-800 p-4 sm:p-5 rounded-2xl text-xs space-y-3 shadow-xs">
                                <p class="font-extrabold text-emerald-600 dark:text-emerald-400 uppercase text-[10px] sm:text-[11px] tracking-wider border-b border-slate-200/80 dark:border-slate-800 pb-2 flex items-center gap-1.5" id="info-package-name"></p>
                                <div class="grid grid-cols-2 sm:grid-cols-3 gap-y-2.5 sm:gap-y-3 gap-x-4 sm:gap-x-6 text-[11px] font-semibold text-slate-600 dark:text-slate-400">
                                    <div>Limit: <span class="font-bold text-slate-800 dark:text-slate-100 block mt-0.5" id="info-limit"></span></div>
                                    <div>Max Term: <span class="font-bold text-slate-800 dark:text-slate-100 block mt-0.5" id="info-max-term"></span></div>
                                    <div>Interest Rate: <span class="font-extrabold text-emerald-600 dark:text-emerald-400 block mt-0.5" id="info-rate"></span></div>
                                    <div>Fixed Deposit: <span class="font-bold text-slate-800 dark:text-slate-100 block mt-0.5" id="info-deposit"></span></div>
                                    <div>Comakers Needed: <span class="font-extrabold text-emerald-600 dark:text-emerald-400 block mt-0.5" id="info-comakers"></span></div>
                                </div>
                            </div>
                        </div>

                        <!-- PANEL 2: REPAYMENT SIMULATOR -->
                        <div class="wizard-panel space-y-5 transition-all duration-300 ease-out hidden" id="panel-step-2">
                            <div>
                                <h3 class="text-xs sm:text-sm font-extrabold text-slate-900 dark:text-white uppercase tracking-wider">Repayment Amortization Simulator</h3>
                                <p class="text-[11px] sm:text-xs text-slate-500 dark:text-slate-400 mt-1 font-medium leading-snug">Simulate your repayments based on your desired loan amount and term.</p>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-5">
                                <div class="space-y-1.5">
                                    <label class="text-[10px] sm:text-[11px] font-bold uppercase text-slate-600 dark:text-slate-400 tracking-wider">Requested Amount</label>
                                    <div class="relative">
                                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">₱</span>
                                        <input type="number" name="amount" id="loan-amount" required disabled min="1" step="any" placeholder="0.00" class="w-full pl-8 pr-3.5 py-3 text-xs sm:text-sm border border-slate-200 dark:border-slate-700 rounded-xl bg-white dark:bg-slate-950 text-slate-800 dark:text-white outline-none focus:ring-4 focus:ring-emerald-500/15 focus:border-emerald-500 transition-all font-bold shadow-xs">
                                    </div>
                                </div>
                                
                                <!-- Repayment Term Custom Dropdown -->
                                <div class="space-y-1.5 custom-select-container relative" id="container-term">
                                    <div class="flex items-center justify-between">
                                        <label class="text-[10px] sm:text-[11px] font-bold uppercase text-slate-600 dark:text-slate-400 tracking-wider">Repayment Term</label>
                                        <span id="term-rate-badge" class="hidden text-[9px] sm:text-[10px] font-black text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/40 px-2 py-0.5 rounded-full border border-emerald-200 dark:border-emerald-800/60"></span>
                                    </div>
                                    
                                    <!-- Underlying synced input for term -->
                                    <input type="hidden" name="term" id="loan-term" required disabled value="">

                                    <!-- Custom Trigger Button -->
                                    <button type="button" id="custom-term-trigger" disabled aria-haspopup="listbox" aria-expanded="false" class="w-full flex items-center justify-between px-3.5 py-3 text-xs border border-slate-200 dark:border-slate-700/80 rounded-xl bg-white dark:bg-slate-950 text-slate-800 dark:text-slate-100 hover:border-emerald-500/60 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-all shadow-xs text-left cursor-not-allowed opacity-50 group">
                                        <div class="flex items-center gap-2.5 truncate">
                                            <div class="w-7 h-7 rounded-lg bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-100 dark:border-emerald-900/40 flex items-center justify-center text-emerald-600 dark:text-emerald-400 shrink-0 group-hover:scale-105 transition-transform">
                                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            </div>
                                            <div class="truncate">
                                                <span id="custom-term-label" class="font-bold text-slate-800 dark:text-slate-200 block truncate">Select package first</span>
                                                <span id="custom-term-sublabel" class="text-[10px] text-slate-400 block truncate font-medium">Repayment tenure &amp; interest rate</span>
                                            </div>
                                        </div>
                                        <svg id="custom-term-chevron" class="w-4 h-4 text-slate-400 transition-transform duration-200 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                    </button>

                                    <!-- Custom Popover Menu -->
                                    <div id="custom-term-menu" role="listbox" class="hidden absolute left-0 right-0 top-full mt-1.5 z-40 bg-white/95 dark:bg-slate-900/95 backdrop-blur-md border border-slate-200 dark:border-slate-800 rounded-2xl shadow-xl p-1.5 space-y-1 max-h-64 overflow-y-auto">
                                    </div>
                                </div>
                            </div>

                            <!-- Live Calculator Preview -->
                            <div id="calculator-preview" class="hidden bg-emerald-50/40 dark:bg-emerald-950/25 border border-emerald-100/80 dark:border-emerald-900/40 p-4 sm:p-5 rounded-2xl space-y-3.5 shadow-xs">
                                <h4 class="text-[10px] sm:text-[11px] font-extrabold uppercase tracking-wider text-emerald-700 dark:text-emerald-400">Amortization Details</h4>
                                <div class="space-y-2.5 text-xs text-slate-600 dark:text-slate-300 font-semibold">
                                    <div class="flex justify-between items-center">
                                        <span>Monthly Principal Payment:</span>
                                        <span id="calc-monthly-principal" class="font-bold text-slate-800 dark:text-white font-mono">₱0.00</span>
                                    </div>
                                    <div class="flex justify-between items-center border-b border-slate-200 dark:border-slate-800 pb-2.5">
                                        <span id="calc-interest-label">Est. Monthly Interest (5% p.a.):</span>
                                        <span id="calc-monthly-interest" class="font-bold text-slate-800 dark:text-white font-mono">₱0.00</span>
                                    </div>
                                    <div class="flex justify-between items-center text-xs sm:text-sm text-slate-800 dark:text-white font-black pt-1">
                                        <span>Estimated Monthly Deduction:</span>
                                        <span id="calc-monthly-total" class="text-sm sm:text-base text-emerald-600 dark:text-emerald-400 font-black font-mono">₱0.00</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Partner/Product selection fields -->
                            <div id="partner-product-section" class="hidden space-y-4 pt-3 border-t border-slate-100 dark:border-slate-800">
                                <div class="space-y-1.5 custom-select-container relative" id="container-partner">
                                    <label class="text-[10px] sm:text-[11px] font-bold uppercase text-slate-600 dark:text-slate-400 tracking-wider">Acquisition Partner / Supplier</label>
                                    <select name="partner" id="loan-partner" class="hidden">
                                        <option value="" selected>Select partner...</option>
                                    </select>
                                    <button type="button" id="custom-partner-trigger" aria-haspopup="listbox" aria-expanded="false" class="w-full flex items-center justify-between px-3.5 py-3 text-xs border border-slate-200 dark:border-slate-700/80 rounded-xl bg-white dark:bg-slate-950 text-slate-800 dark:text-slate-100 hover:border-emerald-500/60 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-all shadow-xs text-left cursor-pointer group">
                                        <div class="flex items-center gap-2.5 truncate">
                                            <div class="w-7 h-7 rounded-lg bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-100 dark:border-emerald-900/40 flex items-center justify-center text-emerald-600 dark:text-emerald-400 shrink-0">
                                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                                            </div>
                                            <div class="truncate">
                                                <span id="custom-partner-label" class="font-bold text-slate-800 dark:text-slate-200 block truncate">Select partner...</span>
                                                <span class="text-[10px] text-slate-400 block truncate font-medium">Authorized merchant</span>
                                            </div>
                                        </div>
                                        <svg id="custom-partner-chevron" class="w-4 h-4 text-slate-400 transition-transform duration-200 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                    </button>
                                    <div id="custom-partner-menu" role="listbox" class="hidden absolute left-0 right-0 top-full mt-1.5 z-40 bg-white/95 dark:bg-slate-900/95 backdrop-blur-md border border-slate-200 dark:border-slate-800 rounded-2xl shadow-xl p-1.5 space-y-1 max-h-60 overflow-y-auto">
                                    </div>
                                </div>
                                <div class="space-y-1.5" id="product-input-wrapper">
                                    <label class="text-[10px] sm:text-[11px] font-bold uppercase text-slate-600 dark:text-slate-400 tracking-wider">Commodity / Specific Product Name</label>
                                    <input type="text" name="product" id="loan-product" placeholder="Enter product name / specifications" class="w-full px-3.5 py-3 text-xs sm:text-sm border border-slate-200 dark:border-slate-700 rounded-xl bg-white dark:bg-slate-950 text-slate-800 dark:text-white outline-none focus:ring-4 focus:ring-emerald-500/15 focus:border-emerald-500 transition-all shadow-xs">
                                </div>
                            </div>
                        </div>

                        <!-- PANEL 3: CO-MAKERS SEARCH & PICKER -->
                        <div class="wizard-panel space-y-5 transition-all duration-300 ease-out hidden" id="panel-step-3">
                            <div>
                                <h3 class="text-xs sm:text-sm font-extrabold text-slate-900 dark:text-white uppercase tracking-wider">Cooperative Co-Makers Endorsements</h3>
                                <p class="text-[11px] sm:text-xs text-slate-500 dark:text-slate-400 mt-1 font-medium leading-snug">Select mandatory co-makers from active cooperative members to back your filing.</p>
                            </div>

                            <div id="comaker-zero-required" class="text-xs text-slate-500 dark:text-slate-400 italic p-4 sm:p-5 bg-slate-50 dark:bg-slate-950/40 border border-slate-200/80 dark:border-slate-800 rounded-2xl shadow-xs">
                                Selected loan package does not require any co-makers. You may proceed to the next step!
                            </div>

                            <div id="comakers-selection-section" class="hidden space-y-4">
                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-b border-slate-100 dark:border-slate-800 pb-3">
                                    <div>
                                        <h5 class="text-[10px] sm:text-[11px] font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider">Personnel Endorsement Picker</h5>
                                        <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">Designate exactly <span class="text-slate-800 dark:text-white font-extrabold font-mono" id="required-comaker-count">0</span> co-makers.</p>
                                    </div>
                                    
                                    <!-- Search Input -->
                                    <div class="relative w-full sm:w-60 flex-shrink-0">
                                        <input type="text" id="comaker-search" placeholder="🔍 Search member name/ID..." class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-800 dark:text-white outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all shadow-xs">
                                    </div>
                                </div>

                                <div class="bg-slate-50/50 dark:bg-slate-950/40 border border-slate-200/80 dark:border-slate-800 p-3.5 sm:p-4 rounded-2xl space-y-3 shadow-xs">
                                    <!-- Selection Counter Status -->
                                    <div class="flex items-center justify-between text-[11px] text-slate-500 dark:text-slate-400 border-b border-slate-200/70 dark:border-slate-800 pb-2.5">
                                        <span class="font-semibold">Cooperative Directory Select:</span>
                                        <span class="font-black text-slate-600 dark:text-slate-300">Selected: <span id="selected-comaker-count" class="text-emerald-600 dark:text-emerald-400 font-bold">0</span> / <span id="required-comaker-count-val" class="text-slate-800 dark:text-slate-100 font-mono">0</span></span>
                                    </div>

                                    <div id="comakers-container" class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 sm:gap-3 max-h-[220px] overflow-y-auto pr-1">
                                        @foreach($members as $m)
                                            <label class="comaker-item flex items-center gap-3 cursor-pointer select-none p-2.5 sm:p-3 bg-white dark:bg-slate-900 hover:bg-slate-50/80 dark:hover:bg-slate-800/80 border border-slate-200/80 dark:border-slate-800 rounded-xl transition-all shadow-xs" data-name="{{ strtolower($m->name) }}" data-cid="{{ strtolower($m->company_id) }}">
                                                <input type="checkbox" name="comakers[]" value="{{ $m->id }}" class="comaker-checkbox rounded text-emerald-600 focus:ring-emerald-500/20 w-4 h-4 border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950">
                                                <div class="text-[11px] text-slate-600 dark:text-slate-300 font-semibold leading-tight min-w-0">
                                                    <span class="comaker-name block text-slate-800 dark:text-slate-100 font-bold truncate">{{ $m->name }}</span>
                                                    <span class="comaker-cid font-mono text-[9.5px] text-slate-400 dark:text-slate-500">ID: {{ $m->company_id }}</span>
                                                </div>
                                            </label>
                                        @endforeach
                                    </div>
                                    
                                    <div id="comaker-warning" class="text-[11px] text-rose-500 dark:text-rose-400 font-extrabold hidden flex items-center gap-1.5 pt-2 border-t border-slate-200/80 dark:border-slate-800">
                                        <span>⚠️ Please select exactly <span id="required-comaker-count-warn"></span> co-makers before proceeding.</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- PANEL 4: REQUIRED COMPLIANCE DOCUMENTS -->
                        <div class="wizard-panel space-y-5 sm:space-y-6 transition-all duration-300 ease-out hidden" id="panel-step-4">
                            <div>
                                <h3 class="text-xs sm:text-sm font-extrabold text-slate-900 dark:text-white uppercase tracking-wider">Required Compliance Documents</h3>
                                <p class="text-[11px] sm:text-xs text-slate-500 dark:text-slate-400 mt-1 font-medium leading-snug">Please upload your digital compliance documents in PDF format to complete your loan application.</p>
                            </div>

                            <!-- INSTRUCTION CARDS (SPECIFYING COMPANY ID AND LAST 2 MONTHS SALARY PAYSLIP) -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                                <!-- Document 1: Company ID Card -->
                                <div class="p-3.5 sm:p-4 bg-emerald-50/50 dark:bg-emerald-950/20 border border-emerald-200/60 dark:border-emerald-900/40 rounded-2xl space-y-2">
                                    <div class="flex items-center gap-2">
                                        <span class="w-6 h-6 sm:w-7 sm:h-7 rounded-lg bg-emerald-500 text-white flex items-center justify-center text-xs font-black shadow-xs shrink-0">1</span>
                                        <h4 class="text-xs font-black text-slate-800 dark:text-slate-100 uppercase tracking-wide">Company ID</h4>
                                    </div>
                                    <ul class="text-[10.5px] sm:text-[11px] text-slate-600 dark:text-slate-400 space-y-1 font-medium pl-1 leading-snug">
                                        <li class="flex items-start gap-1.5">
                                            <span class="text-emerald-500 font-bold">✓</span>
                                            <span>Clear copy of <strong>Company ID</strong> (front and back / back-to-back)</span>
                                        </li>
                                        <li class="flex items-start gap-1.5">
                                            <span class="text-emerald-500 font-bold">✓</span>
                                            <span>Must include <strong>3 specimen signatures and signature</strong></span>
                                        </li>
                                        <li class="flex items-start gap-1.5">
                                            <span class="text-emerald-500 font-bold">✓</span>
                                            <span>Must be saved or scanned into a legible PDF file</span>
                                        </li>
                                    </ul>
                                </div>

                                <!-- Document 2: Last 2 Months Payslips Card -->
                                <div class="p-3.5 sm:p-4 bg-blue-50/50 dark:bg-blue-950/20 border border-blue-200/60 dark:border-blue-900/40 rounded-2xl space-y-2">
                                    <div class="flex items-center gap-2">
                                        <span class="w-6 h-6 sm:w-7 sm:h-7 rounded-lg bg-blue-500 text-white flex items-center justify-center text-xs font-black shadow-xs shrink-0">2</span>
                                        <h4 class="text-xs font-black text-slate-800 dark:text-slate-100 uppercase tracking-wide">Last 2 Months Payslips</h4>
                                    </div>
                                    <ul class="text-[10.5px] sm:text-[11px] text-slate-600 dark:text-slate-400 space-y-1 font-medium pl-1 leading-snug">
                                        <li class="flex items-start gap-1.5">
                                            <span class="text-blue-500 font-bold">✓</span>
                                            <span>Official payslips covering the <strong>last 2 consecutive months</strong></span>
                                        </li>
                                        <li class="flex items-start gap-1.5">
                                            <span class="text-blue-500 font-bold">✓</span>
                                            <span>Showing complete compensation breakdown &amp; deductions</span>
                                        </li>
                                        <li class="flex items-start gap-1.5">
                                            <span class="text-blue-500 font-bold">✓</span>
                                            <span>Official employer payslip format in PDF</span>
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            <!-- RESUBMISSION EXISTING DOCUMENTS PREVIEW (IF RETURNING) -->
                            @if(isset($resubmitApp) && $resubmitApp->documents->isNotEmpty())
                                <div class="p-3.5 sm:p-4 bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-900/50 rounded-2xl space-y-2">
                                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1">
                                        <span class="text-xs font-extrabold text-amber-800 dark:text-amber-300 uppercase tracking-wider flex items-center gap-1.5">
                                            <span>📁</span> Previously Submitted Documents ({{ $resubmitApp->documents->count() }})
                                        </span>
                                        <span class="text-[10px] text-amber-700 dark:text-amber-400 font-semibold">You may keep these or upload new replacement PDFs below</span>
                                    </div>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 pt-1">
                                        @foreach($resubmitApp->documents as $prevDoc)
                                            <div class="flex items-center justify-between p-2.5 bg-white dark:bg-slate-900 border border-amber-200/60 dark:border-amber-900/40 rounded-xl text-xs">
                                                <div class="flex items-center gap-2 truncate">
                                                    <span class="text-rose-500 font-bold text-sm">📄</span>
                                                    <span class="font-bold text-slate-800 dark:text-slate-200 truncate">{{ $prevDoc->original_name }}</span>
                                                    <span class="text-[10px] font-mono text-slate-400">({{ $prevDoc->formatted_file_size }})</span>
                                                </div>
                                                <a href="{{ $prevDoc->file_url }}" target="_blank" class="text-[11px] font-bold text-emerald-600 dark:text-emerald-400 hover:underline flex-shrink-0 ml-2">Preview</a>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <!-- INTERACTIVE DRAG & DROP DROPZONE -->
                            <div class="space-y-3">
                                <div class="flex items-center justify-between">
                                    <label class="text-[10px] sm:text-[11px] font-bold uppercase text-slate-600 dark:text-slate-400 tracking-wider">
                                        Upload PDF Documents <span class="text-rose-500">*</span>
                                    </label>
                                    <span id="doc-counter-badge" class="px-2.5 py-0.5 rounded-full text-[10px] font-black tracking-wider bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-300 border border-slate-200 dark:border-slate-700">
                                        0 / 5 Files
                                    </span>
                                </div>

                                <div id="dropzone-area" class="relative group cursor-pointer border-2 border-dashed border-slate-300 dark:border-slate-700 hover:border-emerald-500 dark:hover:border-emerald-500 rounded-2xl p-5 sm:p-8 text-center bg-slate-50/50 dark:bg-slate-900/50 hover:bg-emerald-50/20 dark:hover:bg-emerald-950/10 transition-all duration-200">
                                    <!-- Hidden native file input handled via JS DataTransfer -->
                                    <input type="file" id="loan-documents-input" name="documents[]" multiple accept=".pdf,application/pdf" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">

                                    <div class="flex flex-col items-center justify-center space-y-2 pointer-events-none">
                                        <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-2xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-lg sm:text-xl transition-transform duration-200 group-hover:scale-110">
                                            <i class="fa-solid fa-cloud-arrow-up"></i>
                                        </div>
                                        <div>
                                            <p class="text-xs sm:text-sm font-bold text-slate-800 dark:text-slate-200">
                                                <span class="text-emerald-600 dark:text-emerald-400 underline">Click to browse</span> or drag and drop your PDF files here
                                            </p>
                                            <p class="text-[10px] sm:text-[11px] text-slate-500 dark:text-slate-400 mt-1 font-medium">
                                                PDF format only &bull; Maximum 5 files &bull; Up to 10MB per file
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- CLIENT-SIDE VERIFIER ALERTS -->
                                <div id="doc-verifier-alert" class="hidden p-3 rounded-xl text-xs font-bold flex items-center gap-2"></div>

                                <!-- ATTACHED DOCUMENTS QUEUE LIST -->
                                <div id="documents-queue-container" class="space-y-2 hidden">
                                    <h5 class="text-[10px] sm:text-[11px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-500 pt-2">
                                        Attached Files Queue
                                    </h5>
                                    <div id="documents-list" class="space-y-2">
                                        <!-- Dynamically generated PDF file cards -->
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- PANEL 5: REMARKS & FILE DECK -->
                        <div class="wizard-panel space-y-5 sm:space-y-6 transition-all duration-300 ease-out hidden" id="panel-step-5">
                            <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                                <div class="flex items-center gap-2.5 sm:gap-3">
                                    <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-sm sm:text-base shrink-0 shadow-xs">
                                        <i class="fa-solid fa-clipboard-check"></i>
                                    </div>
                                    <div>
                                        <h3 class="text-xs sm:text-sm font-extrabold text-slate-900 dark:text-white uppercase tracking-wider">Final Filing &amp; Remarks</h3>
                                        <p class="text-[11px] sm:text-xs text-slate-500 dark:text-slate-400 font-medium mt-0.5 leading-snug">Review filing summary, add support notes, and authorize agreement.</p>
                                    </div>
                                </div>
                                <span class="hidden sm:inline-flex items-center gap-1.5 text-[10px] font-black uppercase tracking-wider px-3 py-1 rounded-full bg-emerald-50 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-300 border border-emerald-200/80 dark:border-emerald-800/60 shadow-xs shrink-0">
                                    <i class="fa-solid fa-flag-checkered text-emerald-500"></i> Step 5 of 5
                                </span>
                            </div>

                            <!-- Dynamic Filing Confirmation Snapshot -->
                            <div class="p-3.5 sm:p-5 rounded-2xl bg-gradient-to-br from-slate-50 via-slate-50 to-emerald-50/40 dark:from-slate-900/90 dark:via-slate-900/70 dark:to-emerald-950/20 border border-slate-200/80 dark:border-slate-800 space-y-2.5 sm:space-y-3 shadow-xs">
                                <div class="flex items-center justify-between gap-2">
                                    <span class="text-[10px] sm:text-[11px] font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300 flex items-center gap-1.5 truncate">
                                        <i class="fa-solid fa-file-invoice text-emerald-500 shrink-0"></i> Application Parameters Verification
                                    </span>
                                    <span class="hidden sm:inline-block text-[10px] text-slate-400 font-medium shrink-0">Verify parameters before filing</span>
                                </div>
                                
                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 sm:gap-3">
                                    <div class="group p-2.5 sm:p-3 bg-white dark:bg-slate-950 rounded-xl border border-slate-200/80 dark:border-slate-800 shadow-xs hover:border-emerald-500/50 hover:shadow-sm hover:-translate-y-0.5 transition-all duration-200">
                                        <span class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-0.5 sm:mb-1 truncate">
                                            <i class="fa-solid fa-layer-group text-emerald-500 mr-0.5 sm:mr-1"></i> Loan Package
                                        </span>
                                        <span id="step5-overview-package" class="text-xs sm:text-[13px] font-black text-slate-800 dark:text-slate-100 truncate block">--</span>
                                    </div>
                                    
                                    <div class="group p-2.5 sm:p-3 bg-white dark:bg-slate-950 rounded-xl border border-slate-200/80 dark:border-slate-800 shadow-xs hover:border-emerald-500/50 hover:shadow-sm hover:-translate-y-0.5 transition-all duration-200">
                                        <span class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-0.5 sm:mb-1 truncate">
                                            <i class="fa-solid fa-coins text-amber-500 mr-0.5 sm:mr-1"></i> Requested Amount
                                        </span>
                                        <span id="step5-overview-amount" class="text-xs sm:text-[13px] font-black text-emerald-600 dark:text-emerald-400 truncate block font-mono">₱0.00</span>
                                    </div>
                                    
                                    <div class="group p-2.5 sm:p-3 bg-white dark:bg-slate-950 rounded-xl border border-slate-200/80 dark:border-slate-800 shadow-xs hover:border-emerald-500/50 hover:shadow-sm hover:-translate-y-0.5 transition-all duration-200">
                                        <span class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-0.5 sm:mb-1 truncate">
                                            <i class="fa-solid fa-calendar-check text-blue-500 mr-0.5 sm:mr-1"></i> Tenure &amp; Rate
                                        </span>
                                        <span id="step5-overview-term" class="text-xs sm:text-[13px] font-black text-slate-800 dark:text-slate-100 truncate block">--</span>
                                    </div>
                                    
                                    <div class="group p-2.5 sm:p-3 bg-white dark:bg-slate-950 rounded-xl border border-slate-200/80 dark:border-slate-800 shadow-xs hover:border-emerald-500/50 hover:shadow-sm hover:-translate-y-0.5 transition-all duration-200">
                                        <span class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-0.5 sm:mb-1 truncate">
                                            <i class="fa-solid fa-paperclip text-slate-400 mr-0.5 sm:mr-1"></i> Attached Files
                                        </span>
                                        <span id="step5-overview-docs" class="text-xs sm:text-[13px] font-black text-slate-800 dark:text-slate-100 truncate block">0 PDFs</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Borrower Support Remarks -->
                            <div class="space-y-2">
                                <div class="flex items-center justify-between gap-2">
                                    <label class="text-[10px] sm:text-[11px] font-bold uppercase text-slate-700 dark:text-slate-300 tracking-wider flex items-center gap-1.5 truncate">
                                        <i class="fa-solid fa-pen-to-square text-emerald-500 shrink-0"></i>
                                        <span class="truncate">Borrower Purpose &amp; Remarks</span>
                                        <span class="text-[9px] sm:text-[10px] text-slate-400 font-normal lowercase shrink-0">(optional)</span>
                                    </label>
                                    <span class="text-[9px] sm:text-[10px] font-mono text-slate-400 font-semibold shrink-0" id="remarks-char-counter">0 / 500 chars</span>
                                </div>
                                
                                <textarea name="remarks" id="loan-remarks-input" rows="3" maxlength="500" placeholder="State your loan purpose (e.g. medical emergency, tuition fees, home improvement) or any special remarks for the credit committee..." class="w-full px-3.5 py-2.5 sm:px-4 sm:py-3 text-xs sm:text-sm border border-slate-200 dark:border-slate-700 rounded-xl bg-white dark:bg-slate-950 text-slate-800 dark:text-white outline-none focus:ring-4 focus:ring-emerald-500/15 focus:border-emerald-500 transition-all resize-none leading-relaxed placeholder-slate-400 dark:placeholder-slate-500 shadow-xs"></textarea>
                                
                                <!-- Quick Purpose Tags -->
                                <div class="flex items-center gap-1.5 sm:gap-2 flex-wrap pt-0.5 sm:pt-1">
                                    <span class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider text-slate-400 mr-0.5">Quick Tags:</span>
                                    <button type="button" data-tag="Medical & Health Expenses" class="btn-purpose-tag inline-flex items-center gap-1.5 px-2.5 py-1 sm:px-3 sm:py-1.5 rounded-lg text-[10px] sm:text-[11px] font-semibold bg-slate-100 hover:bg-emerald-50 hover:text-emerald-700 hover:border-emerald-300 dark:bg-slate-800 dark:hover:bg-emerald-950/40 dark:hover:text-emerald-300 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700 transition-all duration-150 hover:-translate-y-0.5 active:scale-95 shadow-xs cursor-pointer">
                                        <i class="fa-solid fa-heart-pulse text-rose-500"></i> Medical
                                    </button>
                                    <button type="button" data-tag="Tuition & Education Fees" class="btn-purpose-tag inline-flex items-center gap-1.5 px-2.5 py-1 sm:px-3 sm:py-1.5 rounded-lg text-[10px] sm:text-[11px] font-semibold bg-slate-100 hover:bg-emerald-50 hover:text-emerald-700 hover:border-emerald-300 dark:bg-slate-800 dark:hover:bg-emerald-950/40 dark:hover:text-emerald-300 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700 transition-all duration-150 hover:-translate-y-0.5 active:scale-95 shadow-xs cursor-pointer">
                                        <i class="fa-solid fa-graduation-cap text-blue-500"></i> Education
                                    </button>
                                    <button type="button" data-tag="Home Repair & Improvements" class="btn-purpose-tag inline-flex items-center gap-1.5 px-2.5 py-1 sm:px-3 sm:py-1.5 rounded-lg text-[10px] sm:text-[11px] font-semibold bg-slate-100 hover:bg-emerald-50 hover:text-emerald-700 hover:border-emerald-300 dark:bg-slate-800 dark:hover:bg-emerald-950/40 dark:hover:text-emerald-300 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700 transition-all duration-150 hover:-translate-y-0.5 active:scale-95 shadow-xs cursor-pointer">
                                        <i class="fa-solid fa-house-chimney text-emerald-500"></i> Home
                                    </button>
                                    <button type="button" data-tag="Emergency Family Assistance" class="btn-purpose-tag inline-flex items-center gap-1.5 px-2.5 py-1 sm:px-3 sm:py-1.5 rounded-lg text-[10px] sm:text-[11px] font-semibold bg-slate-100 hover:bg-emerald-50 hover:text-emerald-700 hover:border-emerald-300 dark:bg-slate-800 dark:hover:bg-emerald-950/40 dark:hover:text-emerald-300 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700 transition-all duration-150 hover:-translate-y-0.5 active:scale-95 shadow-xs cursor-pointer">
                                        <i class="fa-solid fa-triangle-exclamation text-amber-500"></i> Emergency
                                    </button>
                                    <button type="button" data-tag="Vehicle & Travel Necessities" class="btn-purpose-tag inline-flex items-center gap-1.5 px-2.5 py-1 sm:px-3 sm:py-1.5 rounded-lg text-[10px] sm:text-[11px] font-semibold bg-slate-100 hover:bg-emerald-50 hover:text-emerald-700 hover:border-emerald-300 dark:bg-slate-800 dark:hover:bg-emerald-950/40 dark:hover:text-emerald-300 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700 transition-all duration-150 hover:-translate-y-0.5 active:scale-95 shadow-xs cursor-pointer">
                                        <i class="fa-solid fa-motorcycle text-indigo-500"></i> Travel
                                    </button>
                                </div>
                            </div>

                            <!-- TERMS AND CONDITIONS SECTOR -->
                            <div class="p-4 sm:p-5 bg-slate-50 dark:bg-slate-900/60 border border-slate-200/80 dark:border-slate-800 rounded-2xl space-y-3.5 sm:space-y-4 shadow-xs">
                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-3 border-b border-slate-200/70 dark:border-slate-800">
                                    <div class="flex items-center gap-2.5">
                                        <span class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-xs sm:text-sm shrink-0">
                                            <i class="fa-solid fa-shield-halved"></i>
                                        </span>
                                        <h4 class="text-[11px] sm:text-xs font-black text-slate-900 dark:text-white uppercase tracking-wider">
                                            Loan Contract &amp; Payroll Authority
                                        </h4>
                                    </div>
                                    <button type="button" id="btn-open-terms" class="group inline-flex items-center justify-center gap-2 px-3.5 py-2 sm:px-4 sm:py-2 w-full sm:w-auto bg-emerald-50 hover:bg-emerald-600 active:bg-emerald-700 border border-emerald-200/80 hover:border-emerald-600 dark:bg-emerald-950/40 dark:hover:bg-emerald-600 dark:border-emerald-800/60 text-emerald-700 hover:text-white dark:text-emerald-300 dark:hover:text-white font-extrabold text-[11px] sm:text-xs rounded-xl shadow-xs hover:shadow-md hover:shadow-emerald-600/20 transition-all duration-200 hover:-translate-y-0.5 active:scale-95 select-none cursor-pointer">
                                        <i class="fa-solid fa-file-contract text-emerald-600 group-hover:text-white dark:text-emerald-400 transition-all group-hover:scale-110"></i>
                                        <span>Read Contract Agreement</span>
                                    </button>
                                </div>

                                <div class="flex items-start gap-2.5 sm:gap-3 pt-1">
                                    <div class="relative flex items-center h-5 mt-0.5 shrink-0">
                                        <input type="checkbox" id="main-terms-agree" name="terms_agreed" value="1" disabled class="rounded text-emerald-600 focus:ring-emerald-500/20 w-4 h-4 border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 opacity-50 cursor-not-allowed transition-all">
                                    </div>
                                    <div class="space-y-1 min-w-0">
                                        <label for="main-terms-agree" class="text-[11px] sm:text-xs font-extrabold text-slate-800 dark:text-slate-100 block cursor-pointer leading-snug">
                                            I have read, understood, and agree to the MLSAKO Cooperative Loan Contract
                                        </label>
                                        <p class="text-[10px] sm:text-[11px] text-slate-500 dark:text-slate-400 leading-relaxed font-normal">
                                            By checking this box, you certify that all information and documents uploaded are true, authentic, and accurate. You grant irrevocable authority for automatic semi-monthly payroll deduction for the full amortization amount until the loan is fully satisfied.
                                        </p>
                                    </div>
                                </div>

                                <div class="pt-3 border-t border-slate-200/60 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1.5 sm:gap-2">
                                    <span class="text-[10px] sm:text-[11px] text-amber-600 dark:text-amber-400 font-bold flex items-center gap-1.5" id="terms-status-badge">
                                        <i class="fa-solid fa-triangle-exclamation text-amber-500 shrink-0"></i>
                                        <span>Review contract agreement before submission</span>
                                    </span>
                                    <span class="text-[9px] sm:text-[10px] text-slate-400 font-mono font-medium">
                                        <i class="fa-solid fa-lock text-emerald-500 mr-1"></i> Authorized via 6-digit Security PIN
                                    </span>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- FOOTER NAVIGATION ACTIONS -->
                    <div class="pt-5 sm:pt-6 border-t border-slate-100 dark:border-slate-700 flex flex-col-reverse sm:flex-row items-stretch sm:items-center justify-between gap-3 sm:gap-4">
                        <button type="button" id="prev-step-btn" class="group hidden items-center justify-center gap-2 px-4 py-2.5 sm:px-5 sm:py-3 w-full sm:w-auto text-xs font-bold text-slate-600 dark:text-slate-300 bg-slate-100 hover:bg-slate-200/90 dark:bg-slate-800 dark:hover:bg-slate-700/80 rounded-xl transition-all duration-200 hover:-translate-y-0.5 active:translate-y-0 cursor-pointer select-none">
                            <svg class="w-3.5 h-3.5 transition-transform duration-200 group-hover:-translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                            <span>Previous Step</span>
                        </button>
                        
                        <div class="w-full sm:w-auto sm:ml-auto flex items-stretch sm:items-center gap-2.5 sm:gap-3">
                            <button type="button" id="next-step-btn" class="group inline-flex items-center justify-center gap-2.5 px-5 py-3 sm:px-6 sm:py-3 w-full sm:w-auto text-xs font-extrabold text-white bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 active:from-emerald-700 active:to-teal-700 rounded-xl transition-all duration-200 shadow-md shadow-emerald-500/20 hover:shadow-xl hover:shadow-emerald-500/30 hover:-translate-y-0.5 active:translate-y-0 cursor-pointer select-none">
                                <span>Continue Step</span>
                                <svg class="w-4 h-4 transition-transform duration-200 group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                                </svg>
                            </button>
                            <button type="submit" id="btn-submit-loan" disabled class="group relative hidden items-center justify-center gap-2.5 sm:gap-3 px-5 py-3 sm:px-7 sm:py-3.5 w-full sm:w-auto text-xs sm:text-[13px] font-black tracking-wider uppercase text-white bg-gradient-to-r from-emerald-600 via-teal-600 to-emerald-500 hover:from-emerald-500 hover:via-teal-500 hover:to-emerald-400 active:from-emerald-700 active:to-teal-700 rounded-xl sm:rounded-2xl shadow-lg shadow-emerald-600/25 hover:shadow-xl hover:shadow-emerald-600/40 hover:-translate-y-0.5 active:translate-y-0 active:scale-[0.98] transition-all duration-200 cursor-not-allowed opacity-50 select-none overflow-hidden text-center">
                                <!-- Ambient glowing reflection on hover -->
                                <span class="absolute inset-0 w-full h-full bg-gradient-to-r from-white/0 via-white/20 to-white/0 -translate-x-full group-hover:translate-x-full transition-transform duration-700 ease-out pointer-events-none"></span>

                                <!-- Leading circular icon badge -->
                                <span class="w-6 h-6 rounded-lg bg-white/20 dark:bg-black/20 flex items-center justify-center text-xs shrink-0 shadow-xs transition-transform duration-200 group-hover:scale-110">
                                    <i class="fa-solid fa-lock" id="btn-submit-icon"></i>
                                </span>

                                <span class="relative z-10 font-black truncate sm:whitespace-nowrap">Confirm &amp; Submit Application</span>

                                <!-- Trailing dynamic arrow -->
                                <i class="fa-solid fa-arrow-right text-xs shrink-0 transition-transform duration-200 group-hover:translate-x-1 opacity-60" id="btn-submit-arrow"></i>
                            </button>
                        </div>
                    </div>

                </div>
            </form>
        </div>

    </div>
</div>

@include('components.terms-modal')

@endsection

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Parse the exact php loan configurations dynamically
        const loanConfig = @json($loanConfig);

        // State variables for package limit checks
        let currentMaxLimit = 0;
        let currentMaxTerm = 24;
        let requiredComakersCount = 0;

        // Step 1 References: Native select elements for data binding & validation
        const selectCategory = document.getElementById("loan-category");
        const selectType = document.getElementById("loan-type");

        // Custom Dropdown Trigger & Popover References
        const categoryTrigger = document.getElementById("custom-category-trigger");
        const categoryMenu = document.getElementById("custom-category-menu");
        const categoryChevron = document.getElementById("custom-category-chevron");
        const categoryLabel = document.getElementById("custom-category-label");
        const categorySublabel = document.getElementById("custom-category-sublabel");

        const typeTrigger = document.getElementById("custom-type-trigger");
        const typeMenu = document.getElementById("custom-type-menu");
        const typeChevron = document.getElementById("custom-type-chevron");
        const typeLabel = document.getElementById("custom-type-label");
        const typeSublabel = document.getElementById("custom-type-sublabel");

        const termTrigger = document.getElementById("custom-term-trigger");
        const termMenu = document.getElementById("custom-term-menu");
        const termChevron = document.getElementById("custom-term-chevron");
        const termLabel = document.getElementById("custom-term-label");
        const termSublabel = document.getElementById("custom-term-sublabel");

        const partnerTrigger = document.getElementById("custom-partner-trigger");
        const partnerMenu = document.getElementById("custom-partner-menu");
        const partnerChevron = document.getElementById("custom-partner-chevron");
        const partnerLabel = document.getElementById("custom-partner-label");

        function closeAllCustomDropdowns() {
            if (categoryMenu) {
                categoryMenu.classList.add("hidden");
                if (categoryChevron) categoryChevron.classList.remove("rotate-180");
            }
            if (typeMenu) {
                typeMenu.classList.add("hidden");
                if (typeChevron) typeChevron.classList.remove("rotate-180");
            }
            if (termMenu) {
                termMenu.classList.add("hidden");
                if (termChevron) termChevron.classList.remove("rotate-180");
            }
            if (partnerMenu) {
                partnerMenu.classList.add("hidden");
                if (partnerChevron) partnerChevron.classList.remove("rotate-180");
            }
        }

        // Global dismiss on click outside or Escape
        document.addEventListener("click", function(e) {
            if (!e.target.closest(".custom-select-container")) {
                closeAllCustomDropdowns();
            }
        });

        document.addEventListener("keydown", function(e) {
            if (e.key === "Escape") {
                closeAllCustomDropdowns();
            }
        });

        // Toggle Category Menu
        if (categoryTrigger && categoryMenu) {
            categoryTrigger.addEventListener("click", function(e) {
                e.stopPropagation();
                const isClosed = categoryMenu.classList.contains("hidden");
                closeAllCustomDropdowns();
                if (isClosed) {
                    categoryMenu.classList.remove("hidden");
                    if (categoryChevron) categoryChevron.classList.add("rotate-180");
                }
            });
        }

        // Category items click
        document.querySelectorAll(".custom-category-item").forEach(btn => {
            btn.addEventListener("click", function(e) {
                e.stopPropagation();
                const val = this.getAttribute("data-value");
                selectCategory.value = val;
                closeAllCustomDropdowns();
                selectCategory.dispatchEvent(new Event("change"));
            });
        });

        // Toggle Type Menu
        if (typeTrigger && typeMenu) {
            typeTrigger.addEventListener("click", function(e) {
                e.stopPropagation();
                if (this.disabled) return;
                const isClosed = typeMenu.classList.contains("hidden");
                closeAllCustomDropdowns();
                if (isClosed) {
                    typeMenu.classList.remove("hidden");
                    if (typeChevron) typeChevron.classList.add("rotate-180");
                }
            });
        }

        // Toggle Term Menu
        if (termTrigger && termMenu) {
            termTrigger.addEventListener("click", function(e) {
                e.stopPropagation();
                if (this.disabled) return;
                const isClosed = termMenu.classList.contains("hidden");
                closeAllCustomDropdowns();
                if (isClosed) {
                    termMenu.classList.remove("hidden");
                    if (termChevron) termChevron.classList.add("rotate-180");
                }
            });
        }

        // Toggle Partner Menu
        if (partnerTrigger && partnerMenu) {
            partnerTrigger.addEventListener("click", function(e) {
                e.stopPropagation();
                const isClosed = partnerMenu.classList.contains("hidden");
                closeAllCustomDropdowns();
                if (isClosed) {
                    partnerMenu.classList.remove("hidden");
                    if (partnerChevron) partnerChevron.classList.add("rotate-180");
                }
            });
        }

        selectCategory.addEventListener("change", function() {
            const category = this.value;
            selectType.innerHTML = '<option value="" disabled selected>Select package...</option>';
            if (typeMenu) typeMenu.innerHTML = '';
            
            // Update Category Trigger Label
            if (categoryLabel) {
                const prettyCat = category.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase()) + " Loan";
                categoryLabel.textContent = prettyCat;
                if (categorySublabel) categorySublabel.textContent = "Facility selected";
            }

            // Update active item highlight in Category Menu
            document.querySelectorAll(".custom-category-item").forEach(item => {
                if (item.getAttribute("data-value") === category) {
                    item.classList.add("bg-emerald-50", "dark:bg-emerald-950/60", "text-emerald-700", "dark:text-emerald-300");
                } else {
                    item.classList.remove("bg-emerald-50", "dark:bg-emerald-950/60", "text-emerald-700", "dark:text-emerald-300");
                }
            });
            
            if (loanConfig[category]) {
                selectType.removeAttribute("disabled");
                if (typeTrigger) {
                    typeTrigger.removeAttribute("disabled");
                    typeTrigger.classList.remove("cursor-not-allowed", "opacity-50");
                    if (typeLabel) typeLabel.textContent = "Select loan package...";
                    if (typeSublabel) typeSublabel.textContent = Object.keys(loanConfig[category]).length + " options available";
                }

                Object.keys(loanConfig[category]).forEach(key => {
                    const pkg = loanConfig[category][key];
                    // Native option
                    const option = document.createElement("option");
                    option.value = key;
                    option.textContent = pkg.name;
                    selectType.appendChild(option);

                    // Custom popover option item
                    if (typeMenu) {
                        const itemBtn = document.createElement("button");
                        itemBtn.type = "button";
                        itemBtn.role = "option";
                        itemBtn.setAttribute("data-value", key);
                        itemBtn.className = "custom-type-item w-full flex items-center justify-between px-3 py-2.5 rounded-xl text-left text-xs hover:bg-emerald-50 dark:hover:bg-emerald-950/40 text-slate-700 dark:text-slate-300 hover:text-emerald-700 dark:hover:text-emerald-300 transition-colors cursor-pointer group";

                        const limitStr = typeof pkg.loanable_amount === 'number' ? '₱' + pkg.loanable_amount.toLocaleString() : 'Max limit';
                        const rateStr = pkg.available_terms && pkg.available_terms.length > 0 ? 'Tiered rate' : (pkg.interest_rate || 5) + '%';

                        itemBtn.innerHTML = `
                            <div class="flex items-center gap-2 truncate">
                                <span class="w-2 h-2 rounded-full bg-slate-300 dark:bg-slate-700 group-hover:bg-emerald-500 transition-colors"></span>
                                <div class="truncate">
                                    <span class="font-bold block truncate">${pkg.name}</span>
                                    <span class="text-[10px] text-slate-400 block font-medium">${rateStr}</span>
                                </div>
                            </div>
                            <span class="text-[9.5px] font-extrabold uppercase px-2 py-0.5 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 group-hover:bg-emerald-100 dark:group-hover:bg-emerald-900/60 group-hover:text-emerald-700 dark:group-hover:text-emerald-300 transition-colors shrink-0">
                                ${limitStr}
                            </span>
                        `;

                        itemBtn.addEventListener("click", function(e) {
                            e.stopPropagation();
                            selectType.value = key;
                            closeAllCustomDropdowns();
                            selectType.dispatchEvent(new Event("change"));
                        });

                        typeMenu.appendChild(itemBtn);
                    }
                });
            } else {
                selectType.setAttribute("disabled", "true");
                if (typeTrigger) {
                    typeTrigger.setAttribute("disabled", "true");
                    typeTrigger.classList.add("cursor-not-allowed", "opacity-50");
                    if (typeLabel) typeLabel.textContent = "Select category first";
                    if (typeSublabel) typeSublabel.textContent = "Specific loan facility";
                }
            }

            // Reset inputs
            resetInteractiveWizard();
            updateFilingSummary();
        });

        selectType.addEventListener("change", function() {
            const category = selectCategory.value;
            const type = this.value;
            const config = loanConfig[category][type];

            if (config) {
                // Parse package limits
                currentMaxLimit = typeof config.loanable_amount === 'number' ? config.loanable_amount : 100000; // fallback for complex formulas
                currentMaxTerm = config.max_term_months || 24;
                
                // Show dynamic card details
                document.getElementById("info-package-name").textContent = config.name;
                document.getElementById("info-limit").textContent = typeof config.loanable_amount === 'number' ? "₱" + currentMaxLimit.toLocaleString() : config.loanable_amount;
                
                // Handle custom tenure display
                const rateEl = document.getElementById("info-rate");
                if (config.has_custom_terms && config.available_terms && config.available_terms.length > 0) {
                    const tenuresStr = config.available_terms.map(t => t.months + 'm').join(', ');
                    document.getElementById("info-max-term").textContent = `${tenuresStr} (Tiered)`;
                    const rates = config.available_terms.map(t => parseFloat(t.interest_rate)).filter(r => !isNaN(r));
                    if (rates.length > 0) {
                        const minRate = Math.min(...rates);
                        const maxRate = Math.max(...rates);
                        if (rateEl) {
                            rateEl.textContent = minRate === maxRate ? `${minRate}% p.a.` : `${minRate}% - ${maxRate}% (Tiered)`;
                        }
                    } else if (rateEl) {
                        rateEl.textContent = (config.interest_rate || 5.0) + "% p.a.";
                    }
                } else {
                    document.getElementById("info-max-term").textContent = currentMaxTerm + " Months";
                    if (rateEl) {
                        rateEl.textContent = (config.interest_rate || 5.0) + "% p.a.";
                    }
                }

                document.getElementById("info-deposit").textContent = config.fixed_deposit ? "₱" + config.fixed_deposit.toLocaleString() : "None";

                // Handle conditional comakers count
                requiredComakersCount = typeof config.comakers === 'number' ? config.comakers : 0;
                document.getElementById("info-comakers").textContent = requiredComakersCount || "None";

                // Populate and reveal dynamic form components
                document.getElementById("package-info-card").classList.remove("hidden");

                // Enable parameters
                const inputAmount = document.getElementById("loan-amount");
                inputAmount.removeAttribute("disabled");
                inputAmount.value = "";
                inputAmount.max = currentMaxLimit;

                // Handle Repayment Term Custom Popover Dropdown
                const inputTerm = document.getElementById("loan-term");
                inputTerm.removeAttribute("disabled");
                inputTerm.value = "";

                if (termTrigger) {
                    termTrigger.removeAttribute("disabled");
                    termTrigger.classList.remove("cursor-not-allowed", "opacity-50");
                }
                if (termLabel) termLabel.textContent = "Select repayment tenure...";
                if (termSublabel) termSublabel.textContent = config.available_terms?.length ? config.available_terms.length + " tiered options available" : "Up to " + currentMaxTerm + " months allowed";
                if (termMenu) termMenu.innerHTML = '';

                const termRateBadge = document.getElementById("term-rate-badge");
                if (termRateBadge) termRateBadge.classList.add("hidden");

                if (config.has_custom_terms && config.available_terms && config.available_terms.length > 0) {
                    config.available_terms.forEach(t => {
                        const yearsStr = t.months >= 12 ? ` (${(t.months / 12).toFixed(t.months % 12 === 0 ? 0 : 1)} yr${t.months > 12 ? 's' : ''})` : '';
                        const optBtn = document.createElement("button");
                        optBtn.type = "button";
                        optBtn.role = "option";
                        optBtn.setAttribute("data-term", t.months);
                        optBtn.setAttribute("data-rate", t.interest_rate);
                        optBtn.className = "custom-term-item w-full flex items-center justify-between px-3 py-2.5 rounded-xl text-left text-xs hover:bg-emerald-50 dark:hover:bg-emerald-950/40 text-slate-700 dark:text-slate-300 hover:text-emerald-700 dark:hover:text-emerald-300 transition-colors cursor-pointer group";
                        optBtn.innerHTML = `
                            <div class="flex items-center gap-2 truncate">
                                <span class="w-2 h-2 rounded-full bg-slate-300 dark:bg-slate-700 group-hover:bg-emerald-500 transition-colors"></span>
                                <div>
                                    <span class="font-bold block">${t.months} Months${yearsStr}</span>
                                    <span class="text-[10px] text-slate-400 block font-medium">Tiered tenure option</span>
                                </div>
                            </div>
                            <span class="text-[9.5px] font-extrabold uppercase px-2 py-0.5 rounded-full bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 border border-emerald-200/60 dark:border-emerald-800/60 group-hover:bg-emerald-100 dark:group-hover:bg-emerald-900/60 transition-colors shrink-0">
                                ${t.interest_rate}% Interest
                            </span>
                        `;

                        optBtn.addEventListener("click", function(e) {
                            e.stopPropagation();
                            const termVal = parseInt(this.getAttribute("data-term"));
                            const rateVal = this.getAttribute("data-rate");

                            inputTerm.value = termVal;
                            if (termLabel) termLabel.textContent = `${termVal} Months (${rateVal}% Interest)`;
                            if (termSublabel) termSublabel.textContent = "Selected repayment tenure";

                            if (termRateBadge) {
                                termRateBadge.textContent = `${rateVal}% Interest`;
                                termRateBadge.classList.remove("hidden");
                            }

                            document.querySelectorAll(".custom-term-item").forEach(item => {
                                if (item.getAttribute("data-term") == termVal) {
                                    item.classList.add("bg-emerald-50", "dark:bg-emerald-950/60", "text-emerald-700", "dark:text-emerald-300");
                                } else {
                                    item.classList.remove("bg-emerald-50", "dark:bg-emerald-950/60", "text-emerald-700", "dark:text-emerald-300");
                                }
                            });

                            closeAllCustomDropdowns();
                            performAmortizationCalculation();
                            updateFilingSummary();
                        });

                        termMenu.appendChild(optBtn);
                    });
                } else {
                    const baseRate = config.interest_rate || 5.0;
                    if (termRateBadge) {
                        termRateBadge.textContent = `${baseRate}% Interest`;
                        termRateBadge.classList.remove("hidden");
                    }
                    const termsList = [3, 6, 12, 18, 24, 36, 48, 60].filter(m => m <= currentMaxTerm);
                    if (!termsList.includes(currentMaxTerm)) termsList.push(currentMaxTerm);
                    termsList.sort((a, b) => a - b);

                    termsList.forEach(m => {
                        const yearsStr = m >= 12 ? ` (${(m / 12).toFixed(m % 12 === 0 ? 0 : 1)} yr${m > 12 ? 's' : ''})` : '';
                        const optBtn = document.createElement("button");
                        optBtn.type = "button";
                        optBtn.role = "option";
                        optBtn.setAttribute("data-term", m);
                        optBtn.className = "custom-term-item w-full flex items-center justify-between px-3 py-2.5 rounded-xl text-left text-xs hover:bg-emerald-50 dark:hover:bg-emerald-950/40 text-slate-700 dark:text-slate-300 hover:text-emerald-700 dark:hover:text-emerald-300 transition-colors cursor-pointer group";
                        optBtn.innerHTML = `
                            <div class="flex items-center gap-2 truncate">
                                <span class="w-2 h-2 rounded-full bg-slate-300 dark:bg-slate-700 group-hover:bg-emerald-500 transition-colors"></span>
                                <span class="font-bold">${m} Months${yearsStr}</span>
                            </div>
                            <span class="text-[9.5px] font-extrabold uppercase px-2 py-0.5 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 group-hover:bg-emerald-100 dark:group-hover:bg-emerald-900/60 group-hover:text-emerald-700 dark:group-hover:text-emerald-300 transition-colors shrink-0">
                                ${baseRate}% Interest
                            </span>
                        `;

                        optBtn.addEventListener("click", function(e) {
                            e.stopPropagation();
                            inputTerm.value = m;
                            if (termLabel) termLabel.textContent = `${m} Months (${baseRate}% Interest)`;
                            if (termSublabel) termSublabel.textContent = "Selected repayment tenure";

                            document.querySelectorAll(".custom-term-item").forEach(item => {
                                if (item.getAttribute("data-term") == m) {
                                    item.classList.add("bg-emerald-50", "dark:bg-emerald-950/60", "text-emerald-700", "dark:text-emerald-300");
                                } else {
                                    item.classList.remove("bg-emerald-50", "dark:bg-emerald-950/60", "text-emerald-700", "dark:text-emerald-300");
                                }
                            });

                            closeAllCustomDropdowns();
                            performAmortizationCalculation();
                            updateFilingSummary();
                        });

                        termMenu.appendChild(optBtn);
                    });
                }

                // Reset product input wrapper to original text input state to avoid silent HTML5 required blocks
                const wrapper = document.getElementById("product-input-wrapper");
                if (wrapper) {
                    wrapper.innerHTML = '<label class="text-[10px] sm:text-[11px] font-bold uppercase text-slate-600 dark:text-slate-400 tracking-wider">Commodity / Specific Product Name</label>' +
                        '<input type="text" name="product" id="loan-product" placeholder="Enter product name / specifications" class="w-full px-3.5 py-3 text-xs sm:text-sm border border-slate-200 dark:border-slate-700 rounded-xl bg-white dark:bg-slate-950 text-slate-800 dark:text-white outline-none focus:ring-4 focus:ring-emerald-500/15 focus:border-emerald-500 transition-all shadow-xs">';
                }

                // Handle Acquisition Partners with custom popover
                const partnerSection = document.getElementById("partner-product-section");
                const selectPartner = document.getElementById("loan-partner");
                selectPartner.innerHTML = '<option value="" selected>Select partner...</option>';
                if (partnerMenu) partnerMenu.innerHTML = '';
                if (partnerLabel) partnerLabel.textContent = 'Select partner...';

                if (config.partner) {
                    partnerSection.classList.remove("hidden");
                    const partnerList = Array.isArray(config.partner) ? config.partner : [config.partner];
                    partnerList.forEach(p => {
                        // Native option
                        const opt = document.createElement("option");
                        opt.value = p;
                        opt.textContent = p;
                        selectPartner.appendChild(opt);

                        // Custom popover option
                        if (partnerMenu) {
                            const pBtn = document.createElement("button");
                            pBtn.type = "button";
                            pBtn.role = "option";
                            pBtn.className = "w-full flex items-center justify-between px-3 py-2.5 rounded-xl text-left text-xs hover:bg-emerald-50 dark:hover:bg-emerald-950/40 text-slate-700 dark:text-slate-300 hover:text-emerald-700 dark:hover:text-emerald-300 transition-colors cursor-pointer group";
                            pBtn.innerHTML = `
                                <div class="flex items-center gap-2 truncate">
                                    <span class="w-2 h-2 rounded-full bg-slate-300 dark:bg-slate-700 group-hover:bg-emerald-500 transition-colors"></span>
                                    <span class="font-bold truncate">${p}</span>
                                </div>
                                <span class="text-[9.5px] text-emerald-600 dark:text-emerald-400 font-extrabold uppercase">Partner</span>
                            `;
                            pBtn.addEventListener("click", function(e) {
                                e.stopPropagation();
                                selectPartner.value = p;
                                if (partnerLabel) partnerLabel.textContent = p;
                                closeAllCustomDropdowns();
                            });
                            partnerMenu.appendChild(pBtn);
                        }
                    });
                } else {
                    partnerSection.classList.add("hidden");
                }

                // Handle dynamic products lists (ADTEL)
                const inputProduct = document.getElementById("loan-product");
                if (config.products && Array.isArray(config.products)) {
                    partnerSection.classList.remove("hidden");
                    const wrapper = document.getElementById("product-input-wrapper");
                    wrapper.innerHTML = '<label class="text-[10px] font-bold uppercase text-slate-500 dark:text-slate-400 tracking-wider">Product Option</label>' +
                        '<select name="product" id="loan-product" required class="w-full px-3 py-3 text-xs border border-slate-200 dark:border-slate-700 rounded-xl bg-white dark:bg-slate-950 text-slate-800 dark:text-slate-200 outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all shadow-xs"></select>';
                    const productSelect = document.getElementById("loan-product");
                    config.products.forEach(prod => {
                        const opt = document.createElement("option");
                        opt.value = prod;
                        opt.textContent = prod;
                        productSelect.appendChild(opt);
                    });
                }

                // Configure comakers checklist section
                updateComakersUI();
            }
            updateFilingSummary();
        });

        // Live calculator triggers
        const inputAmount = document.getElementById("loan-amount");

        inputAmount.addEventListener("input", function() {
            performAmortizationCalculation();
            updateFilingSummary();
        });

        function performAmortizationCalculation() {
            const currentTermInput = document.getElementById("loan-term");
            const amount = parseFloat(inputAmount.value) || 0;
            const term = parseInt(currentTermInput ? currentTermInput.value : 0) || 0;
            const calcPreview = document.getElementById("calculator-preview");

            // Evaluate comaker limits dynamically for Instant and Petty Cash loans
            const category = selectCategory.value;
            const type = selectType.value;
            const config = loanConfig[category]?.[type];

            if (config && typeof config.comakers === 'object' && !Array.isArray(config.comakers)) {
                // Dynamic limits
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
            }

            updateComakersUI();

            if (amount > 0 && term > 0) {
                calcPreview.classList.remove("hidden");
                
                // Determine applied rate (custom tiered or base)
                let appliedRate = config ? (config.interest_rate || 5.0) : 5.0;
                if (config && config.available_terms && config.available_terms.length > 0) {
                    const matched = config.available_terms.find(t => t.months === term);
                    if (matched) {
                        appliedRate = parseFloat(matched.interest_rate);
                    }
                }

                // Update rate badge/label in preview
                const interestLabel = document.getElementById("calc-interest-label");
                if (interestLabel) {
                    interestLabel.textContent = `Est. Monthly Interest (${appliedRate}% p.a.):`;
                }

                // Amortization formulas
                const principalMonthly = amount / term;
                const interestMonthly = (amount * (appliedRate / 100)) / 12;
                const totalMonthly = principalMonthly + interestMonthly;

                document.getElementById("calc-monthly-principal").textContent = "₱" + principalMonthly.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                document.getElementById("calc-monthly-interest").textContent = "₱" + interestMonthly.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                document.getElementById("calc-monthly-total").textContent = "₱" + totalMonthly.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
            } else {
                calcPreview.classList.add("hidden");
            }
        }

        function updateComakersUI() {
            const comakersSection = document.getElementById("comakers-selection-section");
            const zeroRequiredSection = document.getElementById("comaker-zero-required");
            
            // Uncheck any extras if dynamic count reduced
            const activeCheckboxes = document.querySelectorAll(".comaker-checkbox");
            
            if (requiredComakersCount > 0) {
                comakersSection.classList.remove("hidden");
                zeroRequiredSection.classList.add("hidden");
                
                document.getElementById("required-comaker-count").textContent = requiredComakersCount;
                document.getElementById("required-comaker-count-val").textContent = requiredComakersCount;
                document.getElementById("required-comaker-count-warn").textContent = requiredComakersCount;
                
                document.getElementById("comaker-warning").classList.remove("hidden");
            } else {
                comakersSection.classList.add("hidden");
                zeroRequiredSection.classList.remove("hidden");
                document.getElementById("comaker-warning").classList.add("hidden");
                
                // Reset checks
                activeCheckboxes.forEach(cb => cb.checked = false);
            }
            
            updateComakerCheckedCounter();
        }

        function updateComakerCheckedCounter() {
            const checkedCount = document.querySelectorAll(".comaker-checkbox:checked").length;
            const counterEl = document.getElementById("selected-comaker-count");
            if (counterEl) {
                counterEl.textContent = checkedCount;
            }
        }

        // Handle checkbox events to prevent exceeding limit
        document.querySelectorAll(".comaker-checkbox").forEach(cb => {
            cb.addEventListener("change", function() {
                const checkedCount = document.querySelectorAll(".comaker-checkbox:checked").length;
                if (checkedCount > requiredComakersCount) {
                    this.checked = false;
                    if (window.MLSAKOAlert) {
                        MLSAKOAlert.fire({
                            icon: 'warning',
                            title: 'Selection Limit Exceeded',
                            text: "You can select a maximum of " + requiredComakersCount + " co-makers for this loan facility.",
                            confirmButtonText: 'Understood'
                        });
                    } else {
                        alert("You can select a maximum of " + requiredComakersCount + " co-makers for this loan facility.");
                    }
                }
                updateComakerCheckedCounter();
                updateFilingSummary();
            });
        });

        // Dynamic search/filtering for co-makers
        const searchInput = document.getElementById("comaker-search");
        if (searchInput) {
            searchInput.addEventListener("input", function() {
                const query = this.value.toLowerCase().trim();
                const items = document.querySelectorAll(".comaker-item");
                
                items.forEach(item => {
                    const name = item.getAttribute("data-name");
                    const cid = item.getAttribute("data-cid");
                    
                    if (name.includes(query) || cid.includes(query)) {
                        item.style.display = "flex";
                    } else {
                        item.style.display = "none";
                    }
                });
            });
        }

        // --- STEP 4: COMPLIANCE DOCUMENTS UPLOAD & VERIFIER ---
        const docInput = document.getElementById("loan-documents-input");
        const dropzoneArea = document.getElementById("dropzone-area");
        const docCounterBadge = document.getElementById("doc-counter-badge");
        const docAlert = document.getElementById("doc-verifier-alert");
        const queueContainer = document.getElementById("documents-queue-container");
        const docList = document.getElementById("documents-list");
        const MAX_DOCS = 5;
        const MAX_FILE_SIZE_BYTES = 10 * 1024 * 1024; // 10MB

        let docDataTransfer = new DataTransfer();
        const hasExistingDocs = {{ (isset($resubmitApp) && $resubmitApp->documents->isNotEmpty()) ? 'true' : 'false' }};

        function formatBytes(bytes) {
            if (bytes >= 1048576) {
                return (bytes / 1048576).toFixed(2) + ' MB';
            } else if (bytes >= 1024) {
                return (bytes / 1024).toFixed(1) + ' KB';
            }
            return bytes + ' B';
        }

        function showDocAlert(message, type = 'error') {
            if (!docAlert) return;
            docAlert.classList.remove("hidden", "bg-rose-50", "text-rose-700", "border", "border-rose-200", "dark:bg-rose-950/40", "dark:text-rose-300", "dark:border-rose-900/50", "bg-amber-50", "text-amber-700", "border-amber-200");
            
            if (type === 'error') {
                docAlert.className = "p-3 rounded-xl text-xs font-bold flex items-center gap-2 bg-rose-50 text-rose-700 border border-rose-200 dark:bg-rose-950/40 dark:text-rose-300 dark:border-rose-900/50";
                docAlert.innerHTML = `<span>⚠️</span> <span>${message}</span>`;
            } else if (type === 'warning') {
                docAlert.className = "p-3 rounded-xl text-xs font-bold flex items-center gap-2 bg-amber-50 text-amber-700 border border-amber-200 dark:bg-amber-950/40 dark:text-amber-300 dark:border-amber-900/50";
                docAlert.innerHTML = `<span>⚠️</span> <span>${message}</span>`;
            }
            setTimeout(() => {
                docAlert.classList.add("hidden");
            }, 6000);
        }

        function handleIncomingFiles(fileList) {
            let errorOccurred = false;

            Array.from(fileList).forEach(file => {
                // Check 1: Maximum 5 files
                if (docDataTransfer.items.length >= MAX_DOCS) {
                    showDocAlert(`Maximum ${MAX_DOCS} PDF files permitted. "${file.name}" was not added.`, 'warning');
                    errorOccurred = true;
                    return;
                }

                // Check 2: Strictly PDF format
                const isPdf = file.name.toLowerCase().endsWith(".pdf") || file.type === "application/pdf";
                if (!isPdf) {
                    showDocAlert(`"${file.name}" is not a PDF file. Please upload Company ID and payslips in PDF format only.`, 'error');
                    errorOccurred = true;
                    return;
                }

                // Check 3: File size <= 10MB
                if (file.size > MAX_FILE_SIZE_BYTES) {
                    showDocAlert(`"${file.name}" exceeds the maximum allowed file size of 10MB (${formatBytes(file.size)}).`, 'error');
                    errorOccurred = true;
                    return;
                }

                // Check 4: Duplicate file
                const isDuplicate = Array.from(docDataTransfer.files).some(existing => 
                    existing.name === file.name && existing.size === file.size
                );
                if (isDuplicate) {
                    return; // Skip identical duplicate
                }

                // Add valid file to DataTransfer
                docDataTransfer.items.add(file);
            });

            // Sync with actual native file input element
            if (docInput) {
                docInput.files = docDataTransfer.files;
            }

            renderDocumentsQueue();
            updateFilingSummary();
        }

        function renderDocumentsQueue() {
            const count = docDataTransfer.files.length;
            if (docCounterBadge) {
                docCounterBadge.textContent = `${count} / ${MAX_DOCS} Files`;
                if (count > 0) {
                    docCounterBadge.className = "px-2.5 py-0.5 rounded-full text-[10px] font-black tracking-wider bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-400 border border-emerald-300 dark:border-emerald-800";
                } else {
                    docCounterBadge.className = "px-2.5 py-0.5 rounded-full text-[10px] font-black tracking-wider bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-300 border border-slate-200 dark:border-slate-700";
                }
            }

            if (count > 0) {
                queueContainer.classList.remove("hidden");
            } else {
                queueContainer.classList.add("hidden");
            }

            docList.innerHTML = "";
            Array.from(docDataTransfer.files).forEach((file, index) => {
                const itemDiv = document.createElement("div");
                itemDiv.className = "flex items-center justify-between p-3 bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-xl shadow-xs transition-all hover:border-slate-300 dark:hover:border-slate-700";
                itemDiv.innerHTML = `
                    <div class="flex items-center gap-3 min-w-0 pr-2">
                        <div class="w-8 h-8 rounded-lg bg-rose-500/10 text-rose-600 dark:text-rose-400 flex items-center justify-center flex-shrink-0 text-xs font-black">
                            PDF
                        </div>
                        <div class="min-w-0 truncate">
                            <span class="block text-xs font-bold text-slate-800 dark:text-slate-200 truncate">${file.name}</span>
                            <div class="flex items-center gap-2 mt-0.5">
                                <span class="text-[10px] text-slate-400 font-mono">${formatBytes(file.size)}</span>
                                <span class="text-[9px] font-extrabold uppercase px-1.5 py-0.2 bg-emerald-50 text-emerald-600 dark:bg-emerald-950/40 dark:text-emerald-400 rounded">✓ Verified PDF</span>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn-remove-doc flex-shrink-0 p-1.5 text-slate-400 hover:text-rose-600 dark:hover:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/40 rounded-lg transition-all" data-index="${index}" title="Remove file">
                        <svg class="w-4 h-4 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                `;
                docList.appendChild(itemDiv);
            });

            // Attach remove file listeners
            docList.querySelectorAll(".btn-remove-doc").forEach(btn => {
                btn.addEventListener("click", function(e) {
                    e.stopPropagation();
                    const removeIdx = parseInt(this.getAttribute("data-index"));
                    const newDT = new DataTransfer();
                    Array.from(docDataTransfer.files).forEach((f, i) => {
                        if (i !== removeIdx) {
                            newDT.items.add(f);
                        }
                    });
                    docDataTransfer = newDT;
                    if (docInput) {
                        docInput.files = docDataTransfer.files;
                    }
                    renderDocumentsQueue();
                    updateFilingSummary();
                });
            });
        }

        if (docInput) {
            docInput.addEventListener("change", function(e) {
                handleIncomingFiles(this.files);
            });
        }

        // Drag and drop events on dropzone
        if (dropzoneArea) {
            ['dragenter', 'dragover'].forEach(eventName => {
                dropzoneArea.addEventListener(eventName, (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    dropzoneArea.classList.add("border-emerald-500", "bg-emerald-50/30", "dark:bg-emerald-950/20");
                }, false);
            });

            ['dragleave', 'drop'].forEach(eventName => {
                dropzoneArea.addEventListener(eventName, (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    dropzoneArea.classList.remove("border-emerald-500", "bg-emerald-50/30", "dark:bg-emerald-950/20");
                }, false);
            });

            dropzoneArea.addEventListener("drop", (e) => {
                const dt = e.dataTransfer;
                if (dt && dt.files && dt.files.length > 0) {
                    handleIncomingFiles(dt.files);
                }
            }, false);
        }

        function resetInteractiveWizard() {
            document.getElementById("package-info-card").classList.add("hidden");
            document.getElementById("calculator-preview").classList.add("hidden");
            document.getElementById("partner-product-section").classList.add("hidden");
            document.getElementById("comakers-selection-section").classList.add("hidden");
            document.getElementById("comaker-zero-required").classList.remove("hidden");
            document.getElementById("loan-amount").setAttribute("disabled", "true");
            
            const loanTermEl = document.getElementById("loan-term");
            if (loanTermEl) {
                loanTermEl.setAttribute("disabled", "true");
                loanTermEl.value = "";
            }

            if (termTrigger) {
                termTrigger.setAttribute("disabled", "true");
                termTrigger.classList.add("cursor-not-allowed", "opacity-50");
            }
            if (termLabel) termLabel.textContent = "Select package first";
            if (termSublabel) termSublabel.textContent = "Repayment tenure & interest rate";
            if (termMenu) termMenu.innerHTML = '';

            const termRateBadge = document.getElementById("term-rate-badge");
            if (termRateBadge) termRateBadge.classList.add("hidden");
            
            // Reset terms & conditions states
            const mainTermsCheckbox = document.getElementById("main-terms-agree");
            if (mainTermsCheckbox) {
                mainTermsCheckbox.checked = false;
                mainTermsCheckbox.disabled = true;
                mainTermsCheckbox.classList.add("opacity-50", "cursor-not-allowed");
            }
            
            const termsBadge = document.getElementById("terms-status-badge");
            if (termsBadge) {
                termsBadge.innerHTML = `<i class="fa-solid fa-triangle-exclamation text-amber-500 mr-1.5"></i> Review contract agreement before submission`;
                termsBadge.className = "text-[11px] text-amber-600 dark:text-amber-400 font-bold flex items-center gap-1.5";
            }
            
            if (window.setSubmitButtonState) {
                window.setSubmitButtonState(false);
            }
            
            document.querySelectorAll(".comaker-checkbox").forEach(cb => cb.checked = false);
            updateComakerCheckedCounter();

            // Reset documents
            docDataTransfer = new DataTransfer();
            if (docInput) docInput.files = docDataTransfer.files;
            renderDocumentsQueue();
        }

        // Dedicated helper for submit button dual-state styling
        window.setSubmitButtonState = function(enabled) {
            const btn = document.getElementById("btn-submit-loan");
            if (!btn) return;
            const icon = document.getElementById("btn-submit-icon");
            const arrow = document.getElementById("btn-submit-arrow");

            if (enabled) {
                btn.removeAttribute("disabled");
                btn.classList.remove("opacity-50", "cursor-not-allowed", "saturate-50");
                btn.classList.add("cursor-pointer");
                if (icon) {
                    icon.className = "fa-solid fa-check";
                }
                if (arrow) {
                    arrow.classList.remove("opacity-40");
                    arrow.classList.add("opacity-100");
                }
            } else {
                btn.setAttribute("disabled", "true");
                btn.classList.add("opacity-50", "cursor-not-allowed", "saturate-50");
                btn.classList.remove("cursor-pointer");
                if (icon) {
                    icon.className = "fa-solid fa-lock";
                }
                if (arrow) {
                    arrow.classList.remove("opacity-100");
                    arrow.classList.add("opacity-40");
                }
            }
        };

        // --- STEP WIZARD REGISTRATION CONTROL SYSTEM ---
        let currentStep = 1;
        const totalSteps = 5;
        const wizardForm = document.getElementById("loan-wizard-form");

        // UI references
        const prevBtn = document.getElementById("prev-step-btn");
        const nextBtn = document.getElementById("next-step-btn");
        const submitBtn = document.getElementById("btn-submit-loan");
        const stepNodes = document.querySelectorAll(".step-node");
        const stepPanels = document.querySelectorAll(".wizard-panel");
        const progressBarLine = document.getElementById("timeline-progress-line");

        function updateWizardUI() {
            // Update panels visibility
            stepPanels.forEach((panel, index) => {
                const stepNum = index + 1;
                if (stepNum === currentStep) {
                    panel.classList.remove("hidden");
                    panel.classList.add("animate-fade-in");
                } else {
                    panel.classList.add("hidden");
                    panel.classList.remove("animate-fade-in");
                }
            });

            // Update navigation buttons
            if (currentStep === 1) {
                prevBtn.classList.add("hidden");
                prevBtn.classList.remove("flex", "inline-flex");
            } else {
                prevBtn.classList.remove("hidden");
                prevBtn.classList.add("flex");
            }

            if (currentStep === totalSteps) {
                nextBtn.classList.add("hidden");
                nextBtn.classList.remove("flex", "inline-flex");
                submitBtn.classList.remove("hidden");
                submitBtn.classList.add("flex");
            } else {
                nextBtn.classList.remove("hidden");
                nextBtn.classList.add("flex");
                submitBtn.classList.add("hidden");
                submitBtn.classList.remove("flex", "inline-flex");
            }

            // Update timeline steps
            stepNodes.forEach((node, index) => {
                const stepNum = index + 1;
                const circle = node.querySelector(".step-circle");
                const label = node.querySelector(".step-label");

                if (stepNum === currentStep) {
                    // Active style
                    circle.className = "step-circle w-8 h-8 sm:w-9 sm:h-9 rounded-full flex items-center justify-center text-xs font-black transition-all duration-300 bg-emerald-500 text-white shadow-md shadow-emerald-500/20 ring-4 ring-emerald-500/10";
                    label.className = "step-label inline-flex items-center gap-1 sm:gap-1.5 text-xs sm:text-[10px] font-black uppercase tracking-wider text-slate-900 dark:text-slate-100 transition-colors duration-200";
                    circle.innerHTML = `${stepNum}`;
                } else if (stepNum < currentStep) {
                    // Completed style
                    circle.className = "step-circle w-8 h-8 sm:w-9 sm:h-9 rounded-full flex items-center justify-center text-xs font-black transition-all duration-300 bg-emerald-100 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-400 ring-4 ring-emerald-500/5";
                    label.className = "step-label inline-flex items-center gap-1 sm:gap-1.5 text-xs sm:text-[10px] font-extrabold uppercase tracking-wider text-emerald-600 dark:text-emerald-400 transition-colors duration-200";
                    circle.innerHTML = `<svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>`;
                } else {
                    // Pending style
                    circle.className = "step-circle w-8 h-8 sm:w-9 sm:h-9 rounded-full flex items-center justify-center text-xs font-black transition-all duration-300 bg-slate-100 text-slate-400 dark:bg-slate-800 dark:text-slate-500";
                    label.className = "step-label inline-flex items-center gap-1 sm:gap-1.5 text-xs sm:text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 transition-colors duration-200";
                    circle.innerHTML = `${stepNum}`;
                }
            });

            // Update progress line bar width
            const lineProgress = ((currentStep - 1) / (totalSteps - 1)) * 100;
            progressBarLine.style.width = `${lineProgress}%`;

            // Update sidebar indicator text
            document.getElementById("summary-step-indicator").textContent = `STEP ${currentStep} OF ${totalSteps}`;
        }

        // Live Summary Card Synchronization
        function updateFilingSummary() {
            const category = selectCategory.value;
            const type = selectType.value;
            const config = loanConfig[category]?.[type];

            // 1. Package name
            const productSummaryEl = document.getElementById("summary-product");
            if (config) {
                productSummaryEl.textContent = config.name;
            } else {
                productSummaryEl.textContent = "None Selected";
            }

            // 2. Limits & term
            const limitEl = document.getElementById("summary-limit");
            const maxTermEl = document.getElementById("summary-max-term");
            if (config) {
                limitEl.textContent = typeof config.loanable_amount === 'number' ? "₱" + currentMaxLimit.toLocaleString() : config.loanable_amount;
                maxTermEl.textContent = currentMaxTerm + " Mos";
            } else {
                limitEl.textContent = "--";
                maxTermEl.textContent = "--";
            }

            // 3. Amount requested
            const amountVal = parseFloat(inputAmount.value) || 0;
            document.getElementById("summary-amount").textContent = "₱" + amountVal.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});

            // 4. Term
            const inputTerm = document.getElementById("loan-term");
            const termVal = parseInt(inputTerm ? inputTerm.value : 0) || 0;
            let appliedRate = config ? (config.interest_rate || 5.0) : 5.0;
            if (config && config.available_terms && config.available_terms.length > 0) {
                const matched = config.available_terms.find(t => t.months === termVal);
                if (matched) {
                    appliedRate = matched.interest_rate;
                }
            }
            document.getElementById("summary-term").textContent = termVal > 0 ? `${termVal} Mos (${appliedRate}% rate)` : "--";

            // 5. Monthly deduction & payday deduction
            const summaryMonthlyEl = document.getElementById("summary-monthly");
            const summaryPaydayEl = document.getElementById("summary-payday");
            if (amountVal > 0 && termVal > 0) {
                const principalMonthly = amountVal / termVal;
                const interestMonthly = (amountVal * (appliedRate / 100)) / 12;
                const totalMonthly = principalMonthly + interestMonthly;
                const totalPayday = totalMonthly / 2;
                
                summaryMonthlyEl.innerHTML = `₱${totalMonthly.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})} <span class="text-[10px] text-slate-450 font-semibold font-sans">/mo</span>`;
                summaryPaydayEl.innerHTML = `₱${totalPayday.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})} <span class="text-[10px] text-slate-455 font-semibold font-sans">/semi</span>`;
            } else {
                summaryMonthlyEl.innerHTML = `₱0.00 <span class="text-[10px] text-slate-450 font-semibold font-sans">/mo</span>`;
                summaryPaydayEl.innerHTML = `₱0.00 <span class="text-[10px] text-slate-455 font-semibold font-sans">/semi</span>`;
            }

            // 6. Co-makers status
            const summaryComakersEl = document.getElementById("summary-comakers");
            if (requiredComakersCount > 0) {
                const checkedCount = document.querySelectorAll(".comaker-checkbox:checked").length;
                summaryComakersEl.innerHTML = `<span class="${checkedCount === requiredComakersCount ? 'text-emerald-400 font-bold' : 'text-slate-200'}">Selected: ${checkedCount} of ${requiredComakersCount}</span>`;
            } else {
                summaryComakersEl.textContent = "None Required";
            }

            // 7. Compliance Documents status
            const summaryDocsEl = document.getElementById("summary-documents");
            if (summaryDocsEl) {
                const count = docDataTransfer.files.length;
                if (count > 0) {
                    summaryDocsEl.innerHTML = `<span class="text-emerald-400 font-bold">${count} / 5 Attached</span>`;
                } else if (hasExistingDocs) {
                    summaryDocsEl.innerHTML = `<span class="text-amber-400 font-semibold">Kept Existing</span>`;
                } else {
                    summaryDocsEl.textContent = "0 / 5 Attached";
                }
            }

            // 8. Step 5 Snapshot Synchronization
            const s5Package = document.getElementById("step5-overview-package");
            const s5Amount = document.getElementById("step5-overview-amount");
            const s5Term = document.getElementById("step5-overview-term");
            const s5Docs = document.getElementById("step5-overview-docs");

            if (s5Package) s5Package.textContent = config ? config.name : "--";
            if (s5Amount) s5Amount.textContent = "₱" + amountVal.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
            if (s5Term) s5Term.textContent = termVal > 0 ? `${termVal} Mos (${appliedRate}% rate)` : "--";
            if (s5Docs) {
                const docCount = docDataTransfer.files.length;
                if (docCount > 0) {
                    s5Docs.textContent = `${docCount} PDF${docCount > 1 ? 's' : ''}`;
                } else if (hasExistingDocs) {
                    s5Docs.textContent = `Kept Existing`;
                } else {
                    s5Docs.textContent = `0 PDFs`;
                }
            }
        }

        // Step 5: Remarks character counter & Quick purpose tags
        const remarksInput = document.getElementById("loan-remarks-input");
        const remarksCharCounter = document.getElementById("remarks-char-counter");
        if (remarksInput && remarksCharCounter) {
            remarksInput.addEventListener("input", function() {
                remarksCharCounter.textContent = `${this.value.length} / 500 chars`;
            });
        }

        document.querySelectorAll(".btn-purpose-tag").forEach(btn => {
            btn.addEventListener("click", function() {
                const tag = this.getAttribute("data-tag");
                if (remarksInput) {
                    if (remarksInput.value.trim().length > 0) {
                        remarksInput.value += ", " + tag;
                    } else {
                        remarksInput.value = tag;
                    }
                    remarksInput.dispatchEvent(new Event("input"));
                }
            });
        });

        // Validate individual steps to allow forward progression
        function validateStep(stepNum) {
            if (stepNum === 1) {
                if (!selectCategory.value || !selectType.value) {
                    if (window.MLSAKOAlert) {
                        MLSAKOAlert.fire({
                            icon: 'warning',
                            title: 'Selection Required',
                            text: "Please select a loan category and product package first.",
                            confirmButtonText: 'OK'
                        });
                    } else {
                        alert("Please select a loan category and product package first.");
                    }
                    return false;
                }
            }

            if (stepNum === 2) {
                const inputTerm = document.getElementById("loan-term");
                const amount = parseFloat(inputAmount.value) || 0;
                const term = parseInt(inputTerm ? inputTerm.value : 0) || 0;

                if (amount <= 0 || term <= 0) {
                    let errTitle = 'Simulation Incomplete';
                    let errText = 'Please enter a valid Requested Amount and select a Repayment Term from the dropdown.';

                    if (amount <= 0 && term > 0) {
                        errTitle = 'Requested Amount Required';
                        errText = 'Please enter a valid Requested Amount greater than ₱0.00.';
                        inputAmount.focus();
                    } else if (amount > 0 && term <= 0) {
                        errTitle = 'Repayment Term Required';
                        errText = 'Please click the "Repayment Term" dropdown to select your repayment tenure.';
                        if (termTrigger) {
                            setTimeout(() => {
                                termTrigger.focus();
                                termTrigger.click();
                            }, 300);
                        }
                    }

                    if (window.MLSAKOAlert) {
                        MLSAKOAlert.fire({
                            icon: 'warning',
                            title: errTitle,
                            text: errText,
                            confirmButtonText: 'Got It'
                        });
                    } else {
                        alert(errText);
                    }
                    return false;
                }

                // Validate Limit Max Bounds
                if (currentMaxLimit > 0 && amount > currentMaxLimit) {
                    if (window.MLSAKOAlert) {
                        MLSAKOAlert.fire({
                            icon: 'warning',
                            title: 'Limit Exceeded',
                            text: "The requested amount exceeds the maximum limit of ₱" + currentMaxLimit.toLocaleString() + " for this package.",
                            confirmButtonText: 'Correct Amount'
                        });
                    } else {
                        alert("The requested amount exceeds the maximum limit of ₱" + currentMaxLimit.toLocaleString() + " for this package.");
                    }
                    return false;
                }

                const category = selectCategory.value;
                const type = selectType.value;
                const config = loanConfig[category]?.[type];

                // Validate Term Max Bounds or Custom Allowed Tenures
                if (config && config.has_custom_terms && config.available_terms && config.available_terms.length > 0) {
                    const isAllowed = config.available_terms.some(t => t.months === term);
                    if (!isAllowed) {
                        const alertInstance = window.MLSAKOAlert || Swal;
                        const allowedListStr = config.available_terms.map(t => t.months + ' mos').join(', ');
                        if (alertInstance) {
                            alertInstance.fire({
                                icon: 'warning',
                                title: 'Invalid Tenure Selected',
                                text: `Please select one of the authorized tenures for this loan facility: ${allowedListStr}.`,
                                confirmButtonText: 'Correct Tenure'
                            });
                        } else {
                            alert(`Please select one of the authorized tenures: ${allowedListStr}.`);
                        }
                        return false;
                    }
                } else if (term > currentMaxTerm) {
                    if (window.MLSAKOAlert) {
                        MLSAKOAlert.fire({
                            icon: 'warning',
                            title: 'Repayment Term Exceeded',
                            text: "The selected repayment term exceeds the maximum term of " + currentMaxTerm + " months allowed.",
                            confirmButtonText: 'Correct Term'
                        });
                    } else {
                        alert("The selected repayment term exceeds the maximum term of " + currentMaxTerm + " months allowed.");
                    }
                    return false;
                }
            }

            if (stepNum === 3) {
                if (requiredComakersCount > 0) {
                    const checkedComakers = document.querySelectorAll(".comaker-checkbox:checked").length;
                    if (checkedComakers !== requiredComakersCount) {
                        if (window.MLSAKOAlert) {
                            MLSAKOAlert.fire({
                                icon: 'warning',
                                title: 'Co-Makers Required',
                                text: "This loan package requires exactly " + requiredComakersCount + " co-makers. You currently have selected " + checkedComakers + ".",
                                confirmButtonText: 'Correct Selection'
                            });
                        } else {
                            alert("This loan package requires exactly " + requiredComakersCount + " co-makers. You currently have selected " + checkedComakers + ".");
                        }
                        return false;
                    }
                }
            }

            if (stepNum === 4) {
                const count = docDataTransfer.files.length;
                if (count === 0 && !hasExistingDocs) {
                    if (window.MLSAKOAlert) {
                        MLSAKOAlert.fire({
                            icon: 'warning',
                            title: 'Compliance Documents Required',
                            text: "Please upload your Company ID (back-to-back with 3 specimen signatures and signature) and your last 2 months salary payslip in PDF format before proceeding.",
                            confirmButtonText: 'Upload Documents'
                        });
                    } else {
                        alert("Please upload your Company ID (back-to-back with 3 specimen signatures and signature) and your last 2 months salary payslip in PDF format before proceeding.");
                    }
                    return false;
                }

                if (count > MAX_DOCS) {
                    if (window.MLSAKOAlert) {
                        MLSAKOAlert.fire({
                            icon: 'warning',
                            title: 'File Limit Exceeded',
                            text: `You have attached ${count} files. The maximum permitted is ${MAX_DOCS} PDF files. Please remove extra files before continuing.`,
                            confirmButtonText: 'OK'
                        });
                    } else {
                        alert(`You have attached ${count} files. The maximum permitted is ${MAX_DOCS} PDF files.`);
                    }
                    return false;
                }
            }

            return true;
        }

        // Click next transition
        nextBtn.addEventListener("click", function(e) {
            e.preventDefault();
            if (validateStep(currentStep)) {
                currentStep++;
                updateWizardUI();
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        });

        // Click prev transition
        prevBtn.addEventListener("click", function(e) {
            e.preventDefault();
            if (currentStep > 1) {
                currentStep--;
                updateWizardUI();
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        });

        // Timeline Node quick clicks (only allows jumps to already-validated steps)
        stepNodes.forEach((node) => {
            node.addEventListener("click", function(e) {
                const targetStep = parseInt(this.getAttribute("data-step"));
                if (targetStep === currentStep) return;

                // Validate forward steps sequentially
                if (targetStep > currentStep) {
                    for (let s = currentStep; s < targetStep; s++) {
                        if (!validateStep(s)) return;
                    }
                }

                currentStep = targetStep;
                updateWizardUI();
            });
        });

        // Initialize display states
        updateWizardUI();
        updateFilingSummary();


        // Form submission logic validating dynamic constraints
        const loanForm = document.getElementById("loan-wizard-form");
        loanForm.addEventListener("submit", function(e) {
            if (loanForm.dataset.confirmed === "true") {
                return;
            }

            const inputTerm = document.getElementById("loan-term");
            const amount = parseFloat(inputAmount.value) || 0;
            const term = parseInt(inputTerm ? inputTerm.value : 0) || 0;

            // Validate Limit Max Bounds
            if (currentMaxLimit > 0 && amount > currentMaxLimit) {
                e.preventDefault();
                if (window.MLSAKOAlert) {
                    MLSAKOAlert.fire({
                        icon: 'warning',
                        title: 'Limit Exceeded',
                        text: "The requested amount exceeds the maximum limit of ₱" + currentMaxLimit.toLocaleString() + " for this package.",
                        confirmButtonText: 'Acknowledge'
                    });
                } else {
                    alert("The requested amount exceeds the maximum limit of ₱" + currentMaxLimit.toLocaleString() + " for this package.");
                }
                return;
            }

            const category = selectCategory.value;
            const type = selectType.value;
            const config = loanConfig[category]?.[type];

            // Validate Term Bounds
            if (config && config.has_custom_terms && config.available_terms && config.available_terms.length > 0) {
                const isAllowed = config.available_terms.some(t => t.months === term);
                if (!isAllowed) {
                    e.preventDefault();
                    const alertInstance = window.MLSAKOAlert || Swal;
                    const allowedListStr = config.available_terms.map(t => t.months + ' mos').join(', ');
                    if (alertInstance) {
                        alertInstance.fire({
                            icon: 'warning',
                            title: 'Invalid Tenure Selected',
                            text: `Please select one of the authorized tenures for this loan facility: ${allowedListStr}.`,
                            confirmButtonText: 'Correct Tenure'
                        });
                    } else {
                        alert(`Please select one of the authorized tenures for this loan facility: ${allowedListStr}.`);
                    }
                    return;
                }
            } else if (term > currentMaxTerm) {
                e.preventDefault();
                if (window.MLSAKOAlert) {
                    MLSAKOAlert.fire({
                        icon: 'warning',
                        title: 'Repayment Term Exceeded',
                        text: "The selected repayment term exceeds the maximum term of " + currentMaxTerm + " months allowed.",
                        confirmButtonText: 'Acknowledge'
                    });
                } else {
                    alert("The selected repayment term exceeds the maximum term of " + currentMaxTerm + " months allowed.");
                }
                return;
            }

            // Validate Comakers selection count
            if (requiredComakersCount > 0) {
                const checkedComakers = document.querySelectorAll(".comaker-checkbox:checked").length;
                if (checkedComakers !== requiredComakersCount) {
                    e.preventDefault();
                    if (window.MLSAKOAlert) {
                        MLSAKOAlert.fire({
                            icon: 'warning',
                            title: 'Co-Makers Endorsement Missing',
                            text: "This loan package requires exactly " + requiredComakersCount + " co-makers. You currently have selected " + checkedComakers + ".",
                            confirmButtonText: 'Correct Selection'
                        });
                    } else {
                        alert("This loan package requires exactly " + requiredComakersCount + " co-makers. You currently have selected " + checkedComakers + ".");
                    }
                    return;
                }
            }

            // Validate Terms and Conditions agreement
            const termsAgreed = document.getElementById("main-terms-agree");
            if (!termsAgreed || !termsAgreed.checked) {
                e.preventDefault();
                if (window.MLSAKOAlert) {
                    MLSAKOAlert.fire({
                        icon: 'info',
                        title: 'Agreement Signature Required',
                        text: "Please read and accept the Loan Agreement and Terms of Service before filing your application.",
                        confirmButtonText: 'Read Terms Now'
                    }).then((result) => {
                        if (window.openTermsAndConditionsModal) {
                            window.openTermsAndConditionsModal();
                        }
                    });
                } else {
                    alert("Please read and accept the Loan Agreement and Terms of Service before filing your application.");
                    if (window.openTermsAndConditionsModal) {
                        window.openTermsAndConditionsModal();
                    }
                }
                return;
            }

            // Validate Documents upload before PIN authorization
            const docCount = docDataTransfer.files.length;
            if (docCount === 0 && !hasExistingDocs) {
                e.preventDefault();
                if (window.MLSAKOAlert) {
                    MLSAKOAlert.fire({
                        icon: 'warning',
                        title: 'Compliance Documents Required',
                        text: "Please upload your Company ID (back-to-back with 3 specimen signatures and signature) and your last 2 months salary payslip in PDF format before submitting.",
                        confirmButtonText: 'Upload Documents'
                    });
                } else {
                    alert("Please upload your Company ID (back-to-back with 3 specimen signatures and signature) and your last 2 months salary payslip in PDF format before submitting.");
                }
                currentStep = 4;
                updateWizardUI();
                window.scrollTo({ top: 0, behavior: 'smooth' });
                return;
            }

            // If we get here, validation is successful! We ask for the 6-digit PIN before submitting!
            e.preventDefault();

            if (window.MLSAKOAlert) {
                MLSAKOAlert.fire({
                    icon: 'question',
                    title: 'Confirm Loan Filing',
                    html: `
                        <div class="space-y-4 text-center">
                            <p class="text-xs font-semibold text-slate-600 dark:text-slate-400">
                                Are you sure you want to submit this cooperative loan application?
                            </p>
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
                    confirmButtonText: 'Authorize Submission',
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
                        document.getElementById('loan-pin-input').value = result.value;
                        loanForm.dataset.confirmed = "true";

                        // Trigger loading effect while documents and loan application are uploaded and processed
                        MLSAKOAlert.fire({
                            title: 'Processing Loan Application...',
                            html: `
                                <div class="flex flex-col items-center justify-center p-4 space-y-4">
                                    <div class="relative w-16 h-16 flex items-center justify-center">
                                        <div class="w-16 h-16 rounded-full border-4 border-emerald-500/20 border-t-emerald-500 animate-spin"></div>
                                        <span class="absolute text-xl">📄</span>
                                    </div>
                                    <div class="space-y-1 text-center">
                                        <p class="text-xs font-bold text-slate-800 dark:text-slate-100">
                                            Uploading compliance documents &amp; encrypting submission...
                                        </p>
                                        <p class="text-[11px] text-slate-500 dark:text-slate-400 font-medium">
                                            Registering your application in the cooperative queue. Please wait a moment.
                                        </p>
                                    </div>
                                </div>
                            `,
                            showConfirmButton: false,
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            didOpen: () => {
                                const formData = new FormData(loanForm);
                                
                                fetch(loanForm.action, {
                                    method: 'POST',
                                    body: formData,
                                    headers: {
                                        'X-Requested-With': 'XMLHttpRequest',
                                        'Accept': 'application/json'
                                    }
                                })
                                .then(async (response) => {
                                    const data = await response.json();
                                    if (!response.ok) {
                                        throw new Error(data.message || data.error || (data.errors ? Object.values(data.errors).flat().join('\n') : 'Submission failed.'));
                                    }
                                    
                                    // Smooth transition: brief pause to allow loading animation to settle
                                    setTimeout(() => {
                                        MLSAKOAlert.fire({
                                            icon: 'success',
                                            title: 'Success',
                                            text: data.message || 'Your loan application was successfully submitted and has entered the approval queue.',
                                            iconColor: '#10b981',
                                            confirmButtonText: 'Great, View My Loans',
                                            allowOutsideClick: false,
                                            allowEscapeKey: false
                                        }).then(() => {
                                            window.location.href = data.redirect_url || "{{ route('member.loans') }}";
                                        });
                                    }, 900);
                                })
                                .catch((err) => {
                                    MLSAKOAlert.fire({
                                        icon: 'error',
                                        title: 'Submission Failed',
                                        text: err.message || 'An unexpected error occurred during submission. Please try again.',
                                        confirmButtonText: 'Review Form'
                                    });
                                });
                            }
                        });
                    }
                });
            } else {
                const pinPrompt = prompt('Are you sure you want to submit this loan application? Please enter your 6-digit security PIN to authorize:');
                if (pinPrompt) {
                    if (pinPrompt.length === 6 && !isNaN(pinPrompt)) {
                        document.getElementById('loan-pin-input').value = pinPrompt;
                        loanForm.dataset.confirmed = "true";
                        loanForm.submit();
                    } else {
                        alert('Invalid PIN format. Submission cancelled.');
                    }
                }
            }
        });

        @if(isset($resubmitApp))
            // Pre-populate resubmission form values
            const resub = @json($resubmitApp);
            console.log("Resubmission data:", resub);
            
            // Set category
            selectCategory.value = resub.loan_category;
            selectCategory.dispatchEvent(new Event("change"));
            
            // Wait briefly for the type dropdown to populate
            setTimeout(() => {
                selectType.value = resub.loan_type;
                selectType.dispatchEvent(new Event("change"));
                
                // Set requested amount and term
                inputAmount.value = resub.requested_amount;
                inputAmount.dispatchEvent(new Event("input"));
                
                const termMonths = resub.form_data.term_months || "";
                const loanTermInput = document.getElementById("loan-term");
                if (loanTermInput) {
                    loanTermInput.value = termMonths;
                }
                const targetOption = document.querySelector(`.custom-term-item[data-term="${termMonths}"]`);
                if (targetOption) {
                    targetOption.click();
                }
                
                // Set partner
                const selectPartner = document.getElementById("loan-partner");
                if (selectPartner && resub.form_data.partner) {
                    selectPartner.value = resub.form_data.partner;
                    selectPartner.dispatchEvent(new Event("change"));
                }
                
                // Set member remarks
                const remarksTextArea = document.querySelector('textarea[name="remarks"]');
                if (remarksTextArea && resub.form_data.member_remarks) {
                    remarksTextArea.value = resub.form_data.member_remarks;
                }
                
                // Pre-check co-makers if any
                const comakers = resub.form_data.comakers || [];
                comakers.forEach(cid => {
                    const cb = document.querySelector(`.comaker-checkbox[value="${cid}"]`);
                    if (cb) {
                        cb.checked = true;
                        cb.dispatchEvent(new Event("change"));
                    }
                });
            }, 150);
        @endif

    });
</script>
@endpush
