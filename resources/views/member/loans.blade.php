@extends('layouts.user')

@section('title', 'Loans Portal - ML Sako')

@section('header')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight serif-font">Cooperative Loans Hub</h1>
        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 font-semibold">Digitally submit cooperative application files and simulate repayment amortizations.</p>
    </div>
</div>
@endsection

@section('content')
<div class="space-y-8 animate-fade-in pb-12">

    <!-- MAIN TWO-COLUMN LAYOUT -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Left Column: Sticky Real-time Filing Summary Card -->
        <div class="lg:col-span-1">
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
                        </div>
                    </div>

                    <div class="pt-6 border-t border-slate-800/80 flex items-center justify-between text-[10px] text-slate-400 font-semibold font-mono">
                        <span>WIZARD STATUS</span>
                        <span id="summary-step-indicator" class="text-emerald-500 font-bold">STEP 1 OF 4</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Horizontal Step-by-Step Wizard Form -->
        <div class="lg:col-span-2">
            <form action="{{ route('member.loans.apply') }}" method="POST" id="loan-wizard-form" class="space-y-6">
                @csrf
                <input type="hidden" name="pin" id="loan-pin-input">
                @if(isset($resubmitApp))
                    <input type="hidden" name="resubmit_id" value="{{ $resubmitApp->id }}">
                @endif
                
                <!-- WIZARD MAIN CARD -->
                <div class="bg-white dark:bg-slate-800 rounded-[2.5rem] border-2 border-slate-100 dark:border-slate-700 shadow-sm p-5 sm:p-8 space-y-8 flex flex-col justify-between min-h-[500px]">
                    
                    <!-- DYNAMIC HORIZONTAL TIMELINE -->
                    <div class="bg-slate-50/50 dark:bg-slate-900/60 p-4 rounded-2xl border border-slate-100/80 dark:border-slate-800/80">
                        <div class="relative flex items-center justify-between w-full max-w-xl mx-auto">
                            <!-- Background connecting line -->
                            <div class="absolute left-0 right-0 top-[18px] h-0.5 bg-slate-200 dark:bg-slate-700 z-0 rounded-full"></div>
                            <!-- Active tracking line -->
                            <div id="timeline-progress-line" class="absolute left-0 top-[18px] h-0.5 bg-emerald-500 z-0 rounded-full transition-all duration-500 ease-in-out" style="width: 0%;"></div>

                            <!-- Step 1 Node -->
                            <button type="button" class="step-node relative flex flex-col items-center gap-2 z-10 focus:outline-none" data-step="1">
                                <div class="step-circle w-9 h-9 rounded-full flex items-center justify-center text-xs font-black transition-all duration-300 bg-emerald-500 text-white shadow-md shadow-emerald-500/20 ring-4 ring-emerald-500/10">
                                    1
                                </div>
                                <span class="step-label text-[10px] font-black uppercase tracking-widest text-slate-800 dark:text-slate-200">Package</span>
                            </button>

                            <!-- Step 2 Node -->
                            <button type="button" class="step-node relative flex flex-col items-center gap-2 z-10 focus:outline-none" data-step="2">
                                <div class="step-circle w-9 h-9 rounded-full flex items-center justify-center text-xs font-black transition-all duration-300 bg-slate-200 text-slate-500 dark:bg-slate-800 dark:text-slate-400">
                                    2
                                </div>
                                <span class="step-label text-[10px] font-bold uppercase tracking-widest text-slate-400 dark:text-slate-500">Repayment</span>
                            </button>

                            <!-- Step 3 Node -->
                            <button type="button" class="step-node relative flex flex-col items-center gap-2 z-10 focus:outline-none" data-step="3">
                                <div class="step-circle w-9 h-9 rounded-full flex items-center justify-center text-xs font-black transition-all duration-300 bg-slate-200 text-slate-500 dark:bg-slate-800 dark:text-slate-400">
                                    3
                                </div>
                                <span class="step-label text-[10px] font-bold uppercase tracking-widest text-slate-400 dark:text-slate-500">Co-Makers</span>
                            </button>

                            <!-- Step 4 Node -->
                            <button type="button" class="step-node relative flex flex-col items-center gap-2 z-10 focus:outline-none" data-step="4">
                                <div class="step-circle w-9 h-9 rounded-full flex items-center justify-center text-xs font-black transition-all duration-300 bg-slate-200 text-slate-500 dark:bg-slate-800 dark:text-slate-400">
                                    4
                                </div>
                                <span class="step-label text-[10px] font-bold uppercase tracking-widest text-slate-400 dark:text-slate-500">Filing</span>
                            </button>
                        </div>
                    </div>

                    <!-- STEP PANEL VIEWS -->
                    <div class="flex-grow pt-2">

                        <!-- PANEL 1: FACILITY SELECTION -->
                        <div class="wizard-panel space-y-5 transition-all duration-300 ease-out" id="panel-step-1">
                            <div>
                                <h3 class="text-sm font-extrabold text-slate-800 dark:text-slate-100 uppercase tracking-wider">Loan Facility Package Selection</h3>
                                <p class="text-2xs text-slate-500 dark:text-slate-400 mt-1 font-semibold">Select your desired cooperative loan product facility to configure your package.</p>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                <div class="space-y-1.5">
                                    <label class="text-[10px] font-bold uppercase text-slate-500 dark:text-slate-400 tracking-wider">Loan Category</label>
                                    <select name="category" id="loan-category" required class="w-full px-3 py-3 text-xs border border-slate-200 dark:border-slate-700 rounded-xl bg-white dark:bg-slate-950 text-slate-800 dark:text-slate-200 outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all shadow-xs">
                                        <option value="" disabled selected>Select category...</option>
                                        @foreach($loanConfig as $catSlug => $packages)
                                            <option value="{{ $catSlug }}">{{ ucwords($catSlug) }} Loan</option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div class="space-y-1.5">
                                    <label class="text-[10px] font-bold uppercase text-slate-500 dark:text-slate-400 tracking-wider">Loan Product Package</label>
                                    <select name="type" id="loan-type" required disabled class="w-full px-3 py-3 text-xs border border-slate-200 dark:border-slate-700 rounded-xl bg-white dark:bg-slate-950 text-slate-800 dark:text-slate-200 outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all disabled:opacity-50 shadow-xs">
                                        <option value="" disabled selected>Select package...</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Package Details Cards -->
                            <div id="package-info-card" class="hidden bg-slate-50 dark:bg-slate-950/60 border border-slate-200/60 dark:border-slate-800 p-5 rounded-2xl text-xs space-y-3 shadow-2xs">
                                <p class="font-extrabold text-emerald-600 dark:text-emerald-400 uppercase text-[10px] tracking-widest border-b border-slate-150 dark:border-slate-800 pb-2 flex items-center gap-1.5" id="info-package-name"></p>
                                <div class="grid grid-cols-2 gap-y-3 gap-x-6 text-[11px] font-semibold text-slate-600 dark:text-slate-400">
                                    <div>Limit: <span class="font-bold text-slate-850 dark:text-white block mt-0.5" id="info-limit"></span></div>
                                    <div>Max Term: <span class="font-bold text-slate-850 dark:text-white block mt-0.5" id="info-max-term"></span></div>
                                    <div>Fixed Deposit: <span class="font-bold text-slate-850 dark:text-white block mt-0.5" id="info-deposit"></span></div>
                                    <div>Comakers Needed: <span class="font-extrabold text-emerald-600 dark:text-emerald-400 block mt-0.5" id="info-comakers"></span></div>
                                </div>
                            </div>
                        </div>

                        <!-- PANEL 2: REPAYMENT SIMULATOR -->
                        <div class="wizard-panel space-y-5 transition-all duration-300 ease-out hidden" id="panel-step-2">
                            <div>
                                <h3 class="text-sm font-extrabold text-slate-800 dark:text-slate-100 uppercase tracking-wider">Repayment Amortization Simulator</h3>
                                <p class="text-2xs text-slate-500 dark:text-slate-400 mt-1 font-semibold">Simulate your repayments based on your desired loan amount and term.</p>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                <div class="space-y-1.5">
                                    <label class="text-[10px] font-bold uppercase text-slate-500 dark:text-slate-400 tracking-wider">Requested Amount</label>
                                    <div class="relative">
                                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">₱</span>
                                        <input type="number" name="amount" id="loan-amount" required disabled min="1" step="any" placeholder="0.00" class="w-full pl-7 pr-3 py-3 text-xs border border-slate-200 dark:border-slate-700 rounded-xl bg-white dark:bg-slate-950 text-slate-850 dark:text-white outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all font-bold shadow-xs">
                                    </div>
                                </div>
                                
                                <div class="space-y-1.5">
                                    <label class="text-[10px] font-bold uppercase text-slate-500 dark:text-slate-400 tracking-wider">Repayment Term (Months)</label>
                                    <input type="number" name="term" id="loan-term" required disabled min="1" placeholder="Months" class="w-full px-3 py-3 text-xs border border-slate-200 dark:border-slate-700 rounded-xl bg-white dark:bg-slate-950 text-slate-850 dark:text-white outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all font-bold shadow-xs">
                                </div>
                            </div>

                            <!-- Live Calculator Preview -->
                            <div id="calculator-preview" class="hidden bg-emerald-50/40 dark:bg-emerald-950/25 border border-emerald-100/80 dark:border-emerald-900/40 p-5 rounded-2xl space-y-4">
                                <h4 class="text-2xs font-extrabold uppercase tracking-widest text-emerald-700 dark:text-emerald-400">Amortization Details</h4>
                                <div class="space-y-2.5 text-xs text-slate-600 dark:text-slate-300 font-semibold">
                                    <div class="flex justify-between">
                                        <span>Monthly Principal Payment:</span>
                                        <span id="calc-monthly-principal" class="font-bold text-slate-800 dark:text-white">₱0.00</span>
                                    </div>
                                    <div class="flex justify-between border-b border-slate-200 dark:border-slate-800 pb-2.5">
                                        <span>Est. Monthly Interest (5% p.a.):</span>
                                        <span id="calc-monthly-interest" class="font-bold text-slate-800 dark:text-white">₱0.00</span>
                                    </div>
                                    <div class="flex justify-between text-sm text-slate-800 dark:text-white font-black pt-1">
                                        <span>Estimated Monthly Deduction:</span>
                                        <span id="calc-monthly-total" class="text-emerald-600 dark:text-emerald-400 font-black">₱0.00</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Partner/Product selection fields -->
                            <div id="partner-product-section" class="hidden space-y-4 pt-3 border-t border-slate-100 dark:border-slate-800">
                                <div class="space-y-1.5">
                                    <label class="text-[10px] font-bold uppercase text-slate-500 dark:text-slate-400 tracking-wider">Acquisition Partner / Supplier</label>
                                    <select name="partner" id="loan-partner" class="w-full px-3 py-3 text-xs border border-slate-200 dark:border-slate-700 rounded-xl bg-white dark:bg-slate-950 text-slate-800 dark:text-slate-200 outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all shadow-xs">
                                        <option value="" selected>Select partner...</option>
                                    </select>
                                </div>
                                <div class="space-y-1.5" id="product-input-wrapper">
                                    <label class="text-[10px] font-bold uppercase text-slate-500 dark:text-slate-400 tracking-wider">Commodity / Specific Product Name</label>
                                    <input type="text" name="product" id="loan-product" placeholder="Enter product name / specifications" class="w-full px-3 py-3 text-xs border border-slate-200 dark:border-slate-700 rounded-xl bg-white dark:bg-slate-950 text-slate-800 dark:text-slate-200 outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all shadow-xs">
                                </div>
                            </div>
                        </div>

                        <!-- PANEL 3: CO-MAKERS SEARCH & PICKER -->
                        <div class="wizard-panel space-y-5 transition-all duration-300 ease-out hidden" id="panel-step-3">
                            <div>
                                <h3 class="text-sm font-extrabold text-slate-800 dark:text-slate-100 uppercase tracking-wider">Cooperative Co-Makers Endorsements</h3>
                                <p class="text-2xs text-slate-500 dark:text-slate-400 mt-1 font-semibold">Select mandatory co-makers from active cooperative members to back your filing.</p>
                            </div>

                            <div id="comaker-zero-required" class="text-xs text-slate-500 dark:text-slate-400 italic p-5 bg-slate-50 dark:bg-slate-950/40 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-2xs">
                                Selected loan package does not require any co-makers. You may proceed to the next step!
                            </div>

                            <div id="comakers-selection-section" class="hidden space-y-4">
                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-b border-slate-100 dark:border-slate-800 pb-3">
                                    <div>
                                        <h5 class="text-[10px] font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider">Personnel Endorsement Picker</h5>
                                        <p class="text-2xs text-slate-500 dark:text-slate-400 mt-0.5">Designate exactly <span class="text-slate-800 dark:text-white font-extrabold font-mono" id="required-comaker-count">0</span> co-makers.</p>
                                    </div>
                                    
                                    <!-- Search Input -->
                                    <div class="relative w-full sm:w-60 flex-shrink-0">
                                        <input type="text" id="comaker-search" placeholder="🔍 Search member name/ID..." class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-800 dark:text-white outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all shadow-xs">
                                    </div>
                                </div>

                                <div class="bg-slate-50/50 dark:bg-slate-950/40 border border-slate-200/60 dark:border-slate-800 p-4 rounded-2xl space-y-3 shadow-2xs">
                                    <!-- Selection Counter Status -->
                                    <div class="flex items-center justify-between text-2xs text-slate-500 dark:text-slate-400 border-b border-slate-150 dark:border-slate-800 pb-2.5">
                                        <span class="font-semibold">Cooperative Directory Select:</span>
                                        <span class="font-black text-slate-600 dark:text-slate-300">Selected: <span id="selected-comaker-count" class="text-emerald-600 dark:text-emerald-400">0</span> / <span id="required-comaker-count-val" class="text-slate-850 dark:text-slate-200 font-mono">0</span></span>
                                    </div>

                                    <div id="comakers-container" class="grid grid-cols-1 sm:grid-cols-2 gap-3 max-h-[180px] overflow-y-auto pr-1">
                                        @foreach($members as $m)
                                            <label class="comaker-item flex items-center gap-3 cursor-pointer select-none p-2.5 bg-white dark:bg-slate-900 hover:bg-slate-50/80 dark:hover:bg-slate-800/80 border border-slate-200 dark:border-slate-800 rounded-xl transition-all shadow-3xs" data-name="{{ strtolower($m->name) }}" data-cid="{{ strtolower($m->company_id) }}">
                                                <input type="checkbox" name="comakers[]" value="{{ $m->id }}" class="comaker-checkbox rounded text-emerald-600 focus:ring-emerald-500/20 w-4 h-4 border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-950">
                                                <div class="text-[11px] text-slate-600 dark:text-slate-300 font-semibold leading-tight">
                                                    <span class="comaker-name block text-slate-850 dark:text-slate-100 font-bold">{{ $m->name }}</span>
                                                    <span class="comaker-cid font-mono text-[9px] text-slate-400 dark:text-slate-500">ID: {{ $m->company_id }}</span>
                                                </div>
                                            </label>
                                        @endforeach
                                    </div>
                                    
                                    <div id="comaker-warning" class="text-2xs text-rose-500 dark:text-rose-400 font-extrabold hidden flex items-center gap-1.5 pt-2 border-t border-slate-150 dark:border-slate-800">
                                        <span>⚠️ Please select exactly <span id="required-comaker-count-warn"></span> co-makers before proceeding.</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- PANEL 4: REMARKS & FILE DECK -->
                        <div class="wizard-panel space-y-5 transition-all duration-300 ease-out hidden" id="panel-step-4">
                            <div>
                                <h3 class="text-sm font-extrabold text-slate-800 dark:text-slate-100 uppercase tracking-wider">Final Filing &amp; Remarks</h3>
                                <p class="text-2xs text-slate-500 dark:text-slate-400 mt-1 font-semibold">Write support notes and finalize terms agreements to complete your submission.</p>
                            </div>

                            <div class="space-y-1.5">
                                <label class="text-[10px] font-bold uppercase text-slate-500 dark:text-slate-400 tracking-wider">Support Remarks</label>
                                <textarea name="remarks" rows="3" placeholder="Briefly state the purpose of this loan facility, or add supporting digital documentation notes..." class="w-full px-4 py-3 text-xs border border-slate-200 dark:border-slate-700 rounded-xl bg-white dark:bg-slate-950 text-slate-800 dark:text-slate-200 outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all resize-none leading-relaxed placeholder-slate-400 dark:placeholder-slate-500 shadow-xs"></textarea>
                            </div>

                            <!-- TERMS AND CONDITIONS SECTOR -->
                            <div class="p-5 bg-slate-50 dark:bg-slate-950/60 border border-slate-200/60 dark:border-slate-800 rounded-2xl space-y-4 shadow-3xs">
                                <div class="flex items-start gap-3">
                                    <div class="relative flex items-center h-5">
                                        <input type="checkbox" id="main-terms-agree" name="terms_agreed" value="1" disabled class="rounded text-emerald-600 focus:ring-emerald-500/20 w-4 h-4 border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 opacity-50 cursor-not-allowed">
                                    </div>
                                    <div class="text-[11px] text-slate-600 dark:text-slate-400 font-semibold leading-tight">
                                        <label for="main-terms-agree" class="text-slate-850 dark:text-slate-150 font-extrabold block cursor-pointer">Accept Loan Terms &amp; Conditions</label>
                                        <span class="block text-2xs text-slate-500 dark:text-slate-400 mt-1 font-medium leading-relaxed">
                                            By checking this box, you certify that you have read, understood, and agreed to the MLSAKO Cooperative Loan Contract, including irrevocable authority for payroll deduction.
                                        </span>
                                    </div>
                                </div>
                                <div class="pt-3 border-t border-slate-200/60 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 items-center">
                                    <span class="text-[10px] text-amber-600 dark:text-amber-400 font-bold flex items-center gap-1.5" id="terms-status-badge">
                                        ⚠️ Review required before submission
                                    </span>
                                    <button type="button" id="btn-open-terms" class="group inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-50 hover:bg-emerald-100/80 active:bg-emerald-200/80 border border-emerald-150/60 dark:bg-emerald-950/20 dark:hover:bg-emerald-900/30 dark:active:bg-emerald-900/50 dark:border-emerald-800/60 text-emerald-700 hover:text-emerald-800 dark:text-emerald-400 dark:hover:text-emerald-300 font-black text-xs rounded-xl shadow-3xs transition-all duration-200 transform hover:-translate-y-0.5 active:scale-95 select-none">
                                        <span class="text-sm transition-transform duration-200 group-hover:scale-115">📜</span>
                                        <span>Open &amp; Read Terms Agreement</span>
                                    </button>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- FOOTER NAVIGATION ACTIONS -->
                    <div class="pt-6 border-t border-slate-100 dark:border-slate-700 flex items-center justify-between gap-4">
                        <button type="button" id="prev-step-btn" class="hidden items-center gap-2 px-5 py-3 text-xs font-bold text-slate-600 dark:text-slate-300 bg-slate-100 dark:bg-slate-700 rounded-xl hover:bg-slate-200 dark:hover:bg-slate-600 transition-all select-none">
                            &larr; Previous Step
                        </button>
                        
                        <div class="ml-auto flex items-center gap-3">
                            <button type="button" id="next-step-btn" class="inline-flex items-center gap-2 px-6 py-3 text-xs font-extrabold text-white bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-850 rounded-xl transition-all shadow-md shadow-emerald-500/10 select-none">
                                Continue Step &rarr;
                            </button>
                            <button type="submit" id="btn-submit-loan" disabled class="hidden items-center gap-2 px-6 py-3 text-xs font-black text-slate-100 bg-slate-400 rounded-xl cursor-not-allowed opacity-50 transition-all select-none">
                                📥 File &amp; Submit Loan Application
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

        // Step 1 Event Listeners: Dynamic drop-downs populated from config/loans.php
        const selectCategory = document.getElementById("loan-category");
        const selectType = document.getElementById("loan-type");

        selectCategory.addEventListener("change", function() {
            const category = this.value;
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
                document.getElementById("info-max-term").textContent = currentMaxTerm + " Months";
                document.getElementById("info-deposit").textContent = config.fixed_deposit ? "₱" + config.fixed_deposit.toLocaleString() : "None";

                // Handle conditional comakers count
                requiredComakersCount = typeof config.comakers === 'number' ? config.comakers : 0;
                document.getElementById("info-comakers").textContent = requiredComakersCount || "None";

                // Populate and reveal dynamic form components
                document.getElementById("package-info-card").classList.remove("hidden");

                // Enable parameters
                const inputAmount = document.getElementById("loan-amount");
                const inputTerm = document.getElementById("loan-term");
                
                inputAmount.removeAttribute("disabled");
                inputTerm.removeAttribute("disabled");
                inputAmount.value = "";
                inputTerm.value = "";
                inputAmount.max = currentMaxLimit;
                inputTerm.max = currentMaxTerm;

                // Handle Acquisition Partners (optical, jewelry, appliances)
                const partnerSection = document.getElementById("partner-product-section");
                const selectPartner = document.getElementById("loan-partner");
                selectPartner.innerHTML = '<option value="" selected>Select partner...</option>';

                if (config.partner) {
                    partnerSection.classList.remove("hidden");
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
        const inputTerm = document.getElementById("loan-term");

        inputAmount.addEventListener("input", function() {
            performAmortizationCalculation();
            updateFilingSummary();
        });
        
        inputTerm.addEventListener("input", function() {
            performAmortizationCalculation();
            updateFilingSummary();
        });

        function performAmortizationCalculation() {
            const amount = parseFloat(inputAmount.value) || 0;
            const term = parseInt(inputTerm.value) || 0;
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
                
                // Amortization formulas
                const principalMonthly = amount / term;
                // Interest: 5% per annum = 0.05 / 12 monthly interest factor
                const interestMonthly = (amount * 0.05) / 12;
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

        function resetInteractiveWizard() {
            document.getElementById("package-info-card").classList.add("hidden");
            document.getElementById("calculator-preview").classList.add("hidden");
            document.getElementById("partner-product-section").classList.add("hidden");
            document.getElementById("comakers-selection-section").classList.add("hidden");
            document.getElementById("comaker-zero-required").classList.remove("hidden");
            document.getElementById("loan-amount").setAttribute("disabled", "true");
            document.getElementById("loan-term").setAttribute("disabled", "true");
            
            // Reset terms & conditions states
            const mainTermsCheckbox = document.getElementById("main-terms-agree");
            if (mainTermsCheckbox) {
                mainTermsCheckbox.checked = false;
                mainTermsCheckbox.disabled = true;
                mainTermsCheckbox.classList.add("opacity-50", "cursor-not-allowed");
            }
            
            const termsBadge = document.getElementById("terms-status-badge");
            if (termsBadge) {
                termsBadge.textContent = "⚠️ Review required before submission";
                termsBadge.className = "text-[10px] text-amber-600 dark:text-amber-400 font-bold flex items-center gap-1.5";
            }
            
            const submitLoanBtn = document.getElementById("btn-submit-loan");
            if (submitLoanBtn) {
                submitLoanBtn.setAttribute("disabled", "true");
                submitLoanBtn.className = "hidden items-center gap-2 px-6 py-3 text-xs font-black text-slate-100 bg-slate-400 rounded-xl cursor-not-allowed opacity-50 transition-all select-none";
            }
            
            document.querySelectorAll(".comaker-checkbox").forEach(cb => cb.checked = false);
            updateComakerCheckedCounter();
        }

        // --- STEP WIZARD REGISTRATION CONTROL SYSTEM ---
        let currentStep = 1;
        const totalSteps = 4;
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
                prevBtn.classList.remove("inline-flex");
            } else {
                prevBtn.classList.remove("hidden");
                prevBtn.classList.add("inline-flex");
            }

            if (currentStep === totalSteps) {
                nextBtn.classList.add("hidden");
                submitBtn.classList.remove("hidden");
                submitBtn.classList.add("inline-flex");
            } else {
                nextBtn.classList.remove("hidden");
                submitBtn.classList.add("hidden");
                submitBtn.classList.remove("inline-flex");
            }

            // Update timeline steps
            stepNodes.forEach((node, index) => {
                const stepNum = index + 1;
                const circle = node.querySelector(".step-circle");
                const label = node.querySelector(".step-label");

                if (stepNum === currentStep) {
                    // Active style
                    circle.className = "step-circle w-9 h-9 rounded-full flex items-center justify-center text-xs font-black transition-all duration-300 bg-emerald-500 text-white shadow-md shadow-emerald-500/20 ring-4 ring-emerald-500/10";
                    label.className = "step-label text-[10px] font-black uppercase tracking-widest text-slate-800 dark:text-slate-200";
                    circle.innerHTML = `${stepNum}`;
                } else if (stepNum < currentStep) {
                    // Completed style
                    circle.className = "step-circle w-9 h-9 rounded-full flex items-center justify-center text-xs font-black transition-all duration-300 bg-emerald-100 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-400 ring-4 ring-emerald-500/5";
                    label.className = "step-label text-[10px] font-extrabold uppercase tracking-widest text-emerald-600 dark:text-emerald-400";
                    circle.innerHTML = `<svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>`;
                } else {
                    // Pending style
                    circle.className = "step-circle w-9 h-9 rounded-full flex items-center justify-center text-xs font-black transition-all duration-300 bg-slate-100 text-slate-400 dark:bg-slate-800 dark:text-slate-500";
                    label.className = "step-label text-[10px] font-bold uppercase tracking-widest text-slate-400 dark:text-slate-500";
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
            const termVal = parseInt(inputTerm.value) || 0;
            document.getElementById("summary-term").textContent = termVal > 0 ? termVal + " Mos" : "--";

            // 5. Monthly deduction & payday deduction
            const summaryMonthlyEl = document.getElementById("summary-monthly");
            const summaryPaydayEl = document.getElementById("summary-payday");
            if (amountVal > 0 && termVal > 0) {
                const principalMonthly = amountVal / termVal;
                const interestMonthly = (amountVal * 0.05) / 12;
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
        }

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
                const amount = parseFloat(inputAmount.value) || 0;
                const term = parseInt(inputTerm.value) || 0;

                if (amount <= 0 || term <= 0) {
                    if (window.MLSAKOAlert) {
                        MLSAKOAlert.fire({
                            icon: 'warning',
                            title: 'Simulation Missing',
                            text: "Please fill in a valid Requested Amount and Term (Months) to simulate payments.",
                            confirmButtonText: 'OK'
                        });
                    } else {
                        alert("Please fill in a valid Requested Amount and Term (Months) to simulate payments.");
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

                // Validate Term Max Bounds
                if (term > currentMaxTerm) {
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

            const amount = parseFloat(inputAmount.value) || 0;
            const term = parseInt(inputTerm.value) || 0;

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

            // Validate Term Max Bounds
            if (term > currentMaxTerm) {
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
                        loanForm.submit();
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
                
                inputTerm.value = resub.form_data.term_months || "";
                inputTerm.dispatchEvent(new Event("input"));
                
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
