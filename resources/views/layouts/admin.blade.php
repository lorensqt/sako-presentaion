@php
    $pendingLoanCount = 0;
    if (auth()->check()) {
        $userRoles = auth()->user()->roles->pluck('slug')->toArray();
        if (!empty($userRoles)) {
            $pendingLoanCount = \App\Models\LoanApplication::where('status', 'pending')
                ->whereIn('current_stage', $userRoles)
                ->count();
        }
    }

    $pendingWithdrawalCount = \App\Models\WithdrawalRequest::where('status', 'pending')->count();
    $pendingDeductionCount = \App\Models\DeductionRequest::where('status', 'pending')->count();
    $pendingTreasuryCount = $pendingWithdrawalCount + $pendingDeductionCount;

    // Determine active menu states based on route
    $isLoansActive = request()->routeIs('admin.loans*');
    $isTreasuryActive = request()->routeIs('admin.withdrawals') || request()->routeIs('admin.deductions');
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50 dark:bg-slate-900">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Admin Console - Sako Cooperative')</title>

    <!-- Dark Mode Init -->
    <script>
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia(
                '(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>

    <!-- Favicon -->
    <link class="favicon" rel="icon" type="image/png" href="{{ asset('img/sako-logo-nobg.png') }}">

    <!-- Fonts -->
    <link class="preconnect" href="https://fonts.googleapis.com">
    <link class="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Styles / Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        /* Collapsible Sidebar Styles */
        #sidebar.collapsed {
            width: 5rem !important;
        }
        #sidebar.collapsed .sidebar-text {
            display: none !important;
        }
        #sidebar.collapsed .px-6 {
            padding-left: 1.25rem !important;
            padding-right: 1.25rem !important;
        }
        #sidebar.collapsed .p-4 {
            padding: 0.75rem !important;
        }
        #sidebar.collapsed .px-4 {
            padding-left: 0.75rem !important;
            padding-right: 0.75rem !important;
        }
        #sidebar.collapsed nav a {
            justify-content: center !important;
            padding-left: 0 !important;
            padding-right: 0 !important;
        }
        #sidebar.collapsed .bg-white\/60,
        #sidebar.collapsed .bg-slate-900\/40 {
            justify-content: center !important;
            padding: 0.5rem !important;
        }
        
        /* Tooltip implementation */
        .sidebar-tooltip {
            position: absolute;
            left: 100%;
            margin-left: 0.5rem;
            background: #1e293b; /* slate-800 */
            color: #f8fafc; /* slate-50 */
            padding: 0.375rem 0.75rem;
            border-radius: 0.5rem;
            font-size: 0.75rem;
            font-weight: 600;
            white-space: nowrap;
            opacity: 0;
            pointer-events: none;
            transition: all 0.15s ease-in-out;
            box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
            z-index: 50;
            border: 1px solid #334155; /* slate-700 */
        }
        #sidebar.collapsed nav a:hover .sidebar-tooltip {
            opacity: 1;
            transform: translateX(0.25rem);
        }

        /* Custom scrollbar for sidebar navigation */
        nav::-webkit-scrollbar {
            width: 5px;
        }
        nav::-webkit-scrollbar-track {
            background: transparent;
        }
        nav::-webkit-scrollbar-thumb {
            background: rgba(148, 163, 184, 0.2); /* muted light grey */
            border-radius: 9999px;
        }
        nav::-webkit-scrollbar-thumb:hover {
            background: rgba(148, 163, 184, 0.4);
        }
        
        /* Dark mode scrollbar for sidebar navigation */
        .dark nav::-webkit-scrollbar-thumb {
            background: rgba(148, 163, 184, 0.12); /* muted slate grey */
        }
        .dark nav::-webkit-scrollbar-thumb:hover {
            background: rgba(148, 163, 184, 0.25);
        }
        
        /* Standard scrollbar rules for Firefox */
        nav {
            scrollbar-width: thin;
            scrollbar-color: rgba(148, 163, 184, 0.2) transparent;
        }
        .dark nav {
            scrollbar-color: rgba(148, 163, 184, 0.12) transparent;
        }

        /* Collapsed Sidebar overrides for nested submenus */
        #sidebar.collapsed .submenu-container {
            display: block !important;
            border-left-width: 0px !important;
            margin-left: 0px !important;
            padding-left: 0px !important;
            margin-top: 0px !important;
        }
        #sidebar.collapsed .menu-collapse-trigger {
            display: none !important;
        }
    </style>
    @stack('styles')
</head>
<body class="h-full bg-slate-50 dark:bg-slate-900 text-slate-800 dark:text-slate-100 antialiased overflow-hidden">
    @include('components.pin-security-overlay')

    <div class="flex h-full overflow-hidden">
        
        <!-- Sidebar for Desktop -->
        <aside id="sidebar" class="hidden lg:flex lg:flex-col lg:w-64 bg-slate-100 dark:bg-slate-800 border-r border-slate-200 dark:border-slate-700 flex-shrink-0 transition-all duration-300 overflow-x-hidden">
            <script>
                if (localStorage.getItem("sidebar-collapsed") === "true") {
                    document.getElementById("sidebar").classList.add("collapsed");
                }
            </script>
            <!-- Sidebar Header / Branding -->
            <div class="h-16 flex items-center px-6 border-b border-slate-200/80 dark:border-slate-700/80">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5 group">
                    <img src="{{ asset('img/sako-logo-nobg.png') }}" alt="ML Sako Logo" class="h-9 w-auto object-contain transition-transform duration-200 group-hover:scale-105 flex-shrink-0">
                    <span class="text-base font-black tracking-widest text-slate-900 dark:text-white uppercase sidebar-text">
                        ML<span class="text-emerald-600 font-black">Sako</span>
                    </span>
                    <span class="inline-flex items-center px-1.5 py-0.5 rounded bg-emerald-500/10 text-emerald-600 text-[9px] font-black tracking-wider uppercase border border-emerald-500/20 sidebar-text">Admin</span>
                </a>
            </div>

            <!-- Admin Profile Mini-Card -->
            <div class="p-4 border-b border-slate-200/60 dark:border-slate-700/60">
                <div class="bg-white/60 dark:bg-slate-800/60 rounded-xl p-3 border border-slate-200/80 dark:border-slate-700/80 flex items-center gap-3 shadow-sm transition-all duration-200">
                    <div class="w-10 h-10 rounded-xl bg-emerald-600 flex items-center justify-center text-white font-bold text-sm shadow-md shadow-emerald-500/10 flex-shrink-0">
                        {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}
                    </div>
                    <div class="min-w-0 flex-1 sidebar-text">
                        <p class="text-[10px] text-emerald-600 font-extrabold tracking-wider uppercase">{{ Auth::user()->role === 'super_admin' ? 'System Root' : 'Administrator' }}</p>
                        <p class="text-xs text-slate-800 dark:text-slate-200 font-bold truncate max-w-[130px]" title="{{ Auth::user()->name ?? 'Admin Executive' }}">{{ Auth::user()->name ?? 'Admin Executive' }}</p>
                    </div>
                </div>
            </div>

            <!-- Sidebar Navigation -->
            <nav class="flex-1 px-4 py-4 space-y-1 overflow-y-auto overflow-x-hidden">
                <!-- Group: Core -->
                <div class="sidebar-text px-2.5 pt-1 pb-1 text-[9px] font-extrabold tracking-widest text-slate-400/80 dark:text-slate-500/80 uppercase flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500/40"></span>
                    <span>Core Panel</span>
                </div>
                <a href="{{ route('admin.dashboard') }}" class="relative flex items-center gap-2.5 px-2.5 py-2 text-[13px] font-semibold rounded-xl transition-all duration-200 {{ request()->routeIs('admin.dashboard') ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-600/15' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-200/70 dark:hover:bg-slate-700/50 hover:text-slate-900 dark:hover:text-slate-100' }}">
                    <!-- Dashboard Icon -->
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <span class="sidebar-text">Overview Panel</span>
                    <span class="sidebar-tooltip hidden lg:block">Overview Panel</span>
                </a>

                <!-- Group: Registry -->
                <div class="sidebar-text px-2.5 pt-4 pb-1 text-[9px] font-extrabold tracking-widest text-slate-400/80 dark:text-slate-500/80 uppercase flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500/40"></span>
                    <span>Registry</span>
                </div>
                <a href="{{ route('admin.members') }}" class="relative flex items-center gap-2.5 px-2.5 py-2 text-[13px] font-semibold rounded-xl transition-all duration-200 {{ request()->routeIs('admin.members') ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-600/15' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-200/70 dark:hover:bg-slate-700/50 hover:text-slate-900 dark:hover:text-slate-100' }}">
                    <!-- Members Icon -->
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    <span class="sidebar-text">Members Directory</span>
                    <span class="sidebar-tooltip hidden lg:block">Members Directory</span>
                </a>

                <!-- Group: Credit & Loans (Collapsible) -->
                <div class="sidebar-text px-2.5 pt-4 pb-1 text-[9px] font-extrabold tracking-widest text-slate-400/80 dark:text-slate-500/80 uppercase flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500/40"></span>
                    <span>Credit & Loans</span>
                </div>
                
                <!-- Trigger Button -->
                <button type="button" class="relative flex items-center justify-between w-full px-2.5 py-2 text-[13px] font-semibold rounded-xl transition-all duration-200 cursor-pointer menu-collapse-trigger {{ $isLoansActive ? 'text-emerald-600 dark:text-emerald-400 font-bold bg-slate-200/50 dark:bg-slate-700/30' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-200/70 dark:hover:bg-slate-700/50 hover:text-slate-900 dark:hover:text-slate-100' }}" data-target="loans-submenu">
                    <div class="flex items-center gap-2.5">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                        <span class="sidebar-text">Credit & Loans</span>
                    </div>
                    <!-- Bubbled up Badge + Chevron container -->
                    <div class="flex items-center gap-1.5 sidebar-text">
                        @if($pendingLoanCount > 0)
                            <span class="parent-badge flex h-4.5 w-4.5 items-center justify-center rounded-full bg-rose-500 text-[9px] font-black text-white animate-pulse {{ $isLoansActive ? 'hidden' : '' }}">
                                {{ $pendingLoanCount }}
                            </span>
                        @endif
                        <svg class="w-4 h-4 transition-transform duration-200 chevron-icon {{ $isLoansActive ? 'rotate-180' : '' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                </button>

                <!-- Submenu items -->
                <div id="loans-submenu" class="pl-4 ml-5 border-l border-slate-200 dark:border-slate-700/80 space-y-1 mt-1 transition-all duration-300 {{ $isLoansActive ? 'block' : 'hidden' }} submenu-container">
                    <a href="{{ route('admin.loans') }}" class="relative flex items-center gap-2.5 px-2.5 py-2 text-[13px] font-semibold rounded-xl transition-all duration-200 {{ request()->routeIs('admin.loans') ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-600/15' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-200/70 dark:hover:bg-slate-700/50 hover:text-slate-900 dark:hover:text-slate-100' }}">
                        <!-- List/Directory Icon -->
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                        <span class="sidebar-text">Loans Directory</span>
                        <span class="sidebar-tooltip hidden lg:block">Loans Directory</span>
                    </a>

                    <a href="{{ route('admin.loans.approvals') }}" class="relative flex items-center gap-2.5 px-2.5 py-2 text-[13px] font-semibold rounded-xl transition-all duration-200 {{ request()->routeIs('admin.loans.approvals') ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-600/15' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-200/70 dark:hover:bg-slate-700/50 hover:text-slate-900 dark:hover:text-slate-100' }}">
                        <!-- Approvals Icon -->
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        <span class="sidebar-text">Loan Approvals</span>
                        @if($pendingLoanCount > 0)
                            <span class="sidebar-text ml-auto flex h-4.5 w-4.5 items-center justify-center rounded-full bg-rose-500 text-[9px] font-black text-white animate-pulse">
                                {{ $pendingLoanCount }}
                            </span>
                        @endif
                        <span class="sidebar-tooltip hidden lg:block">Loan Approvals</span>
                    </a>

                    <a href="{{ route('admin.loans.management') }}" class="relative flex items-center gap-2.5 px-2.5 py-2 text-[13px] font-semibold rounded-xl transition-all duration-200 {{ request()->routeIs('admin.loans.management') ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-600/15' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-200/70 dark:hover:bg-slate-700/50 hover:text-slate-900 dark:hover:text-slate-100' }}">
                        <!-- Management Gear Icon -->
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31(2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                        <span class="sidebar-text">Loans Management</span>
                        <span class="sidebar-tooltip hidden lg:block">Loans Management</span>
                    </a>
                </div>

                <!-- Group: Treasury (Collapsible) -->
                <div class="sidebar-text px-2.5 pt-4 pb-1 text-[9px] font-extrabold tracking-widest text-slate-400/80 dark:text-slate-500/80 uppercase flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500/40"></span>
                    <span>Treasury</span>
                </div>

                <!-- Trigger Button -->
                <button type="button" class="relative flex items-center justify-between w-full px-2.5 py-2 text-[13px] font-semibold rounded-xl transition-all duration-200 cursor-pointer menu-collapse-trigger {{ $isTreasuryActive ? 'text-emerald-600 dark:text-emerald-400 font-bold bg-slate-200/50 dark:bg-slate-700/30' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-200/70 dark:hover:bg-slate-700/50 hover:text-slate-900 dark:hover:text-slate-100' }}" data-target="treasury-submenu">
                    <div class="flex items-center gap-2.5">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="sidebar-text">Treasury</span>
                    </div>
                    <!-- Bubbled up Badge + Chevron container -->
                    <div class="flex items-center gap-1.5 sidebar-text">
                        @if($pendingTreasuryCount > 0)
                            <span class="parent-badge flex h-4.5 w-4.5 items-center justify-center rounded-full bg-rose-500 text-[9px] font-black text-white animate-pulse {{ $isTreasuryActive ? 'hidden' : '' }}">
                                {{ $pendingTreasuryCount }}
                            </span>
                        @endif
                        <svg class="w-4 h-4 transition-transform duration-200 chevron-icon {{ $isTreasuryActive ? 'rotate-180' : '' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                </button>

                <!-- Submenu items -->
                <div id="treasury-submenu" class="pl-4 ml-5 border-l border-slate-200 dark:border-slate-700/80 space-y-1 mt-1 transition-all duration-300 {{ $isTreasuryActive ? 'block' : 'hidden' }} submenu-container">
                    <a href="{{ route('admin.withdrawals') }}" class="relative flex items-center gap-2.5 px-2.5 py-2 text-[13px] font-semibold rounded-xl transition-all duration-200 {{ request()->routeIs('admin.withdrawals') ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-600/15' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-200/70 dark:hover:bg-slate-700/50 hover:text-slate-900 dark:hover:text-slate-100' }}">
                        <!-- Withdrawals Icon -->
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        <span class="sidebar-text">Withdrawal Approvals</span>
                        @if($pendingWithdrawalCount > 0)
                            <span class="sidebar-text ml-auto flex h-4.5 w-4.5 items-center justify-center rounded-full bg-rose-500 text-[9px] font-black text-white animate-pulse">
                                {{ $pendingWithdrawalCount }}
                            </span>
                        @endif
                        <span class="sidebar-tooltip hidden lg:block">Withdrawal Approvals</span>
                    </a>

                    <a href="{{ route('admin.deductions') }}" class="relative flex items-center gap-2.5 px-2.5 py-2 text-[13px] font-semibold rounded-xl transition-all duration-200 {{ request()->routeIs('admin.deductions') ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-600/15' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-200/70 dark:hover:bg-slate-700/50 hover:text-slate-900 dark:hover:text-slate-100' }}">
                        <!-- Deductions Icon -->
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span class="sidebar-text">Deduction Approvals</span>
                        @if($pendingDeductionCount > 0)
                            <span class="sidebar-text ml-auto flex h-4.5 w-4.5 items-center justify-center rounded-full bg-rose-500 text-[9px] font-black text-white animate-pulse">
                                {{ $pendingDeductionCount }}
                            </span>
                        @endif
                        <span class="sidebar-tooltip hidden lg:block">Deduction Approvals</span>
                    </a>
                </div>

                <!-- Group: Governance -->
                <div class="sidebar-text px-2.5 pt-4 pb-1 text-[9px] font-extrabold tracking-widest text-slate-400/80 dark:text-slate-500/80 uppercase flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500/40"></span>
                    <span>Governance</span>
                </div>
                <a href="{{ route('admin.elections.index') }}" class="relative flex items-center gap-2.5 px-2.5 py-2 text-[13px] font-semibold rounded-xl transition-all duration-200 {{ request()->routeIs('admin.elections.*') ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-600/15' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-200/70 dark:hover:bg-slate-700/50 hover:text-slate-900 dark:hover:text-slate-100' }}">
                    <!-- Elections Icon -->
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                    <span class="sidebar-text">Elections</span>
                    <span class="sidebar-tooltip hidden lg:block">Elections</span>
                </a>

                <!-- Group: Security -->
                <div class="sidebar-text px-2.5 pt-4 pb-1 text-[9px] font-extrabold tracking-widest text-slate-400/80 dark:text-slate-500/80 uppercase flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500/40"></span>
                    <span>System Security</span>
                </div>
                <a href="{{ route('admin.audit-logs') }}" class="relative flex items-center gap-2.5 px-2.5 py-2 text-[13px] font-semibold rounded-xl transition-all duration-200 {{ request()->routeIs('admin.audit-logs') ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-600/15' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-200/70 dark:hover:bg-slate-700/50 hover:text-slate-900 dark:hover:text-slate-100' }}">
                    <!-- Shield Icon -->
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    <span class="sidebar-text">Audit & Security Logs</span>
                    <span class="sidebar-tooltip hidden lg:block">Audit & Security Logs</span>
                </a>
            </nav>

            <!-- Sidebar Footer -->
            <div class="p-4 border-t border-slate-200 dark:border-slate-700">
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                    @csrf
                </form>
                <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="relative flex items-center gap-2.5 px-2.5 py-2 text-[13px] font-semibold text-rose-600 dark:text-rose-400 rounded-xl hover:bg-rose-50 dark:hover:bg-rose-950/20 hover:text-rose-700 dark:hover:text-rose-300 transition-all duration-200">
                    <!-- Logout Icon -->
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    <span class="sidebar-text">Sign Out</span>
                    <span class="sidebar-tooltip hidden lg:block">Sign Out</span>
                </a>
            </div>
        </aside>

        <!-- Mobile Sidebar / Off-canvas Menu Overlay -->
        <div id="mobile-sidebar" class="fixed inset-0 z-40 hidden lg:hidden">
            <div id="mobile-sidebar-backdrop" class="fixed inset-0 bg-slate-900/40 dark:bg-slate-950/60 backdrop-blur-sm"></div>
            <nav id="mobile-sidebar-panel" class="fixed top-0 bottom-0 left-0 w-64 bg-slate-100 dark:bg-slate-800 border-r border-slate-200 dark:border-slate-700 flex flex-col z-50 transform -translate-x-full transition-transform duration-300 ease-in-out">
                <div class="h-16 flex items-center justify-between px-6 border-b border-slate-200/80 dark:border-slate-700/80">
                    <div class="flex items-center gap-2">
                        <img src="{{ asset('img/sako-logo-nobg.png') }}" alt="ML Sako Logo" class="h-8 w-auto object-contain">
                        <span class="text-base font-black tracking-widest text-slate-900 dark:text-white uppercase">
                            ML<span class="text-emerald-600 font-black">Sako</span>
                        </span>
                    </div>
                    <button id="mobile-sidebar-close" class="p-1.5 rounded-lg text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-slate-100 hover:bg-slate-200/60 dark:hover:bg-slate-700/50 transition-all">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                
                <div class="p-4 border-b border-slate-200/60 dark:border-slate-700/60">
                    <div class="bg-white/60 dark:bg-slate-800/60 rounded-xl p-3 border border-slate-200/80 dark:border-slate-700/80 flex items-center gap-3 shadow-sm">
                        <div class="w-10 h-10 rounded-xl bg-emerald-600 flex items-center justify-center text-white font-bold text-sm shadow-md">
                            {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-[10px] text-emerald-600 font-extrabold tracking-wider uppercase">{{ Auth::user()->role === 'super_admin' ? 'System Root' : 'Administrator' }}</p>
                            <p class="text-xs text-slate-800 dark:text-slate-200 font-bold truncate max-w-[130px]">{{ Auth::user()->name ?? 'Admin Executive' }}</p>
                        </div>
                    </div>
                </div>

                <div class="flex-1 px-4 py-4 space-y-1 overflow-y-auto">
                    <!-- Group: Core -->
                    <div class="px-3 pt-1 pb-1 text-[9px] font-extrabold tracking-widest text-slate-400/80 dark:text-slate-500/80 uppercase flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500/40"></span>
                        <span>Core Panel</span>
                    </div>
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-semibold rounded-xl transition-all duration-200 {{ request()->routeIs('admin.dashboard') ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-600/15' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-200/60 dark:hover:bg-slate-700/50 hover:text-slate-900 dark:hover:text-slate-100' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2"/></svg>
                        <span>Overview Panel</span>
                    </a>

                    <!-- Group: Registry -->
                    <div class="px-3 pt-4 pb-1 text-[9px] font-extrabold tracking-widest text-slate-400/80 dark:text-slate-500/80 uppercase flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500/40"></span>
                        <span>Registry</span>
                    </div>
                    <a href="{{ route('admin.members') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-semibold rounded-xl transition-all duration-200 {{ request()->routeIs('admin.members') ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-600/15' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-200/60 dark:hover:bg-slate-700/50 hover:text-slate-900 dark:hover:text-slate-100' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292"/></svg>
                        <span>Members Directory</span>
                    </a>

                    <!-- Group: Credit & Loans (Collapsible) -->
                    <div class="px-3 pt-4 pb-1 text-[9px] font-extrabold tracking-widest text-slate-400/80 dark:text-slate-500/80 uppercase flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500/40"></span>
                        <span>Credit & Loans</span>
                    </div>
                    
                    <button type="button" class="flex items-center justify-between w-full px-3 py-2.5 text-sm font-semibold rounded-xl transition-all duration-200 cursor-pointer menu-collapse-trigger {{ $isLoansActive ? 'text-emerald-600 dark:text-emerald-400 font-bold bg-slate-200/60 dark:bg-slate-700/30' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-200/60 dark:hover:bg-slate-700/50 hover:text-slate-900 dark:hover:text-slate-100' }}" data-target="mobile-loans-submenu">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                            <span>Credit & Loans</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            @if($pendingLoanCount > 0)
                                <span class="parent-badge flex h-4.5 w-4.5 items-center justify-center rounded-full bg-rose-500 text-[9px] font-black text-white {{ $isLoansActive ? 'hidden' : '' }}">
                                    {{ $pendingLoanCount }}
                                </span>
                            @endif
                            <svg class="w-4 h-4 transition-transform duration-200 chevron-icon {{ $isLoansActive ? 'rotate-180' : '' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                    </button>

                    <div id="mobile-loans-submenu" class="pl-4 ml-5 border-l border-slate-200 dark:border-slate-700/80 space-y-1 mt-1 {{ $isLoansActive ? 'block' : 'hidden' }}">
                        <a href="{{ route('admin.loans') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-semibold rounded-xl transition-all duration-200 {{ request()->routeIs('admin.loans') ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-600/15' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-200/60 dark:hover:bg-slate-700/50 hover:text-slate-900 dark:hover:text-slate-100' }}">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                            <span>Loans Directory</span>
                        </a>
                        <a href="{{ route('admin.loans.approvals') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-semibold rounded-xl transition-all duration-200 {{ request()->routeIs('admin.loans.approvals') ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-600/15' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-200/60 dark:hover:bg-slate-700/50 hover:text-slate-900 dark:hover:text-slate-100' }}">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29(9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            <span>Loan Approvals</span>
                            @if($pendingLoanCount > 0)
                                <span class="ml-auto flex h-4.5 w-4.5 items-center justify-center rounded-full bg-rose-500 text-[9px] font-black text-white">
                                    {{ $pendingLoanCount }}
                                </span>
                            @endif
                        </a>
                        <a href="{{ route('admin.loans.management') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-semibold rounded-xl transition-all duration-200 {{ request()->routeIs('admin.loans.management') ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-600/15' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-200/60 dark:hover:bg-slate-700/50 hover:text-slate-900 dark:hover:text-slate-100' }}">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31(2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                            <span>Loans Management</span>
                        </a>
                    </div>

                    <!-- Group: Treasury (Collapsible) -->
                    <div class="px-3 pt-4 pb-1 text-[9px] font-extrabold tracking-widest text-slate-400/80 dark:text-slate-500/80 uppercase flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500/40"></span>
                        <span>Treasury</span>
                    </div>

                    <button type="button" class="flex items-center justify-between w-full px-3 py-2.5 text-sm font-semibold rounded-xl transition-all duration-200 cursor-pointer menu-collapse-trigger {{ $isTreasuryActive ? 'text-emerald-600 dark:text-emerald-400 font-bold bg-slate-200/60 dark:bg-slate-700/30' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-200/60 dark:hover:bg-slate-700/50 hover:text-slate-900 dark:hover:text-slate-100' }}" data-target="mobile-treasury-submenu">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>Treasury</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            @if($pendingTreasuryCount > 0)
                                <span class="parent-badge flex h-4.5 w-4.5 items-center justify-center rounded-full bg-rose-500 text-[9px] font-black text-white {{ $isTreasuryActive ? 'hidden' : '' }}">
                                    {{ $pendingTreasuryCount }}
                                </span>
                            @endif
                            <svg class="w-4 h-4 transition-transform duration-200 chevron-icon {{ $isTreasuryActive ? 'rotate-180' : '' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                    </button>

                    <div id="mobile-treasury-submenu" class="pl-4 ml-5 border-l border-slate-200 dark:border-slate-700/80 space-y-1 mt-1 {{ $isTreasuryActive ? 'block' : 'hidden' }}">
                        <a href="{{ route('admin.withdrawals') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-semibold rounded-xl transition-all duration-200 {{ request()->routeIs('admin.withdrawals') ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-600/15' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-200/60 dark:hover:bg-slate-700/50 hover:text-slate-900 dark:hover:text-slate-100' }}">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            <span>Withdrawal Approvals</span>
                            @if($pendingWithdrawalCount > 0)
                                <span class="ml-auto flex h-4.5 w-4.5 items-center justify-center rounded-full bg-rose-500 text-[9px] font-black text-white">
                                    {{ $pendingWithdrawalCount }}
                                </span>
                            @endif
                        </a>
                        <a href="{{ route('admin.deductions') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-semibold rounded-xl transition-all duration-200 {{ request()->routeIs('admin.deductions') ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-600/15' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-200/60 dark:hover:bg-slate-700/50 hover:text-slate-900 dark:hover:text-slate-100' }}">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <span>Deduction Approvals</span>
                            @if($pendingDeductionCount > 0)
                                <span class="ml-auto flex h-4.5 w-4.5 items-center justify-center rounded-full bg-rose-500 text-[9px] font-black text-white">
                                    {{ $pendingDeductionCount }}
                                </span>
                            @endif
                        </a>
                    </div>

                    <!-- Group: Governance -->
                    <div class="px-3 pt-4 pb-1 text-[9px] font-extrabold tracking-widest text-slate-400/80 dark:text-slate-500/80 uppercase flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500/40"></span>
                        <span>Governance</span>
                    </div>
                    <a href="{{ route('admin.elections.index') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-semibold rounded-xl transition-all duration-200 {{ request()->routeIs('admin.elections.*') ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-600/15' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-200/60 dark:hover:bg-slate-700/50 hover:text-slate-900 dark:hover:text-slate-100' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                        <span>Elections</span>
                    </a>

                    <!-- Group: Security -->
                    <div class="px-3 pt-4 pb-1 text-[9px] font-extrabold tracking-widest text-slate-400/80 dark:text-slate-500/80 uppercase flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500/40"></span>
                        <span>System Security</span>
                    </div>
                    <a href="{{ route('admin.audit-logs') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-semibold rounded-xl transition-all duration-200 {{ request()->routeIs('admin.audit-logs') ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-600/15' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-200/60 dark:hover:bg-slate-700/50 hover:text-slate-900 dark:hover:text-slate-100' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        <span>Audit & Security Logs</span>
                    </a>
                </div>
                
                <div class="p-4 border-t border-slate-200/80 dark:border-slate-700/80">
                    <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium text-rose-600 dark:text-rose-400 rounded-xl hover:bg-rose-50 dark:hover:bg-rose-950/20 hover:text-rose-700 dark:hover:text-rose-300 transition-all duration-200">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4"/></svg>
                        <span>Sign Out</span>
                    </a>
                </div>
            </nav>
        </div>

        <!-- Main Window Container -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Topbar Header -->
            <header class="h-16 bg-white dark:bg-slate-800 border-b border-slate-100 dark:border-slate-700/80 flex items-center justify-between px-4 sm:px-6 flex-shrink-0 z-10">
                <div class="flex items-center gap-3">
                    <!-- Hamburger button for mobile -->
                    <button id="mobile-sidebar-toggle" class="p-1.5 rounded-lg text-slate-500 dark:text-slate-400 hover:text-emerald-600 hover:bg-slate-50 dark:hover:bg-slate-700/50 lg:hidden transition-all">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    
                    <!-- Sidebar collapse button for desktop -->
                    <button id="desktop-sidebar-toggle" class="hidden lg:flex p-1.5 rounded-xl text-slate-500 dark:text-slate-400 hover:text-emerald-600 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-all" title="Toggle Sidebar">
                        <!-- Modern Panel Collapse Icon -->
                        <svg class="w-5.5 h-5.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v14a1 1 0 01-1 1H5a1 1 0 01-1-1V5z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 4v16"/>
                        </svg>
                    </button>

                    <!-- My Affiliations (Moved here globally next to toggle) -->
                    <div class="hidden sm:flex items-center gap-2 border-l border-slate-100 dark:border-slate-700 pl-3">
                        <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 dark:text-slate-500 whitespace-nowrap">My Affiliations:</span>
                        <div class="flex flex-wrap gap-1">
                            @forelse($userRoles as $slug)
                                <span class="text-emerald-700 dark:text-emerald-400 font-extrabold bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-100/40 dark:border-emerald-900/30 px-2 py-0.5 rounded-lg text-[9px] uppercase tracking-wider transition-all hover:bg-emerald-100 dark:hover:bg-emerald-950/50">
                                    {{ str_replace('_', ' ', $slug) }}
                                </span>
                            @empty
                                <span class="text-slate-400 dark:text-slate-550 font-semibold italic text-[9px] bg-slate-100 dark:bg-slate-900/50 px-2 py-0.5 rounded-lg border border-slate-200/40 dark:border-slate-800/40">Auditor</span>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Right Toolbar -->
                <div class="flex items-center gap-4">
                    <!-- Theme Switcher Button (80% Size) -->
                    <button id="theme-toggle" type="button"
                        class="relative inline-flex h-6.5 w-11 flex-shrink-0 cursor-pointer items-center rounded-full border-2 border-transparent bg-sky-400 transition-colors duration-300 ease-in-out focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 dark:bg-black dark:focus:ring-offset-slate-900 shadow-[inset_0_2px_4px_rgba(0,0,0,0.06)]"
                        role="switch" aria-checked="false" title="Toggle Theme">
                        <span class="sr-only">Toggle Theme</span>
                        <!-- Toggle Button handle -->
                        <span
                            class="pointer-events-none relative inline-flex h-5 w-5 transform items-center justify-center transition duration-300 ease-in-out translate-x-0 dark:translate-x-4.5">
                            <!-- Sun Icon (for light mode) -->
                            <img src="https://img.icons8.com/?size=100&id=8EUmYhfLPTCF&format=png&color=000000"
                                alt="Sun"
                                class="absolute h-4.5 w-4.5 object-contain transition-all duration-300 opacity-100 scale-100 dark:opacity-0 dark:scale-50">
                            <!-- Moon Icon (for dark mode) -->
                            <img src="https://img.icons8.com/?size=100&id=13477&format=png&color=000000"
                                alt="Moon"
                                class="absolute h-4.5 w-4.5 object-contain transition-all duration-300 opacity-0 scale-50 dark:opacity-100 dark:scale-100">
                        </span>
                    </button>

                    <!-- Divider -->
                    <span class="w-px h-6 bg-slate-100 dark:bg-slate-700"></span>

                    <!-- Profile Dropdown Wrapper -->
                    <div class="relative" id="profile-dropdown-wrapper">
                        <!-- Profile Dropdown Trigger -->
                        <button id="profile-dropdown-trigger" type="button"
                            class="group flex items-center gap-2.5 rounded-lg p-1.5 transition-colors hover:bg-slate-100 dark:hover:bg-slate-800/60 focus:outline-none focus:ring-2 focus:ring-emerald-500/20">

                            <!-- Avatar -->
                            <div
                                class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-lg bg-emerald-600 font-bold text-sm text-white shadow-sm transition-transform duration-200 group-hover:scale-105">
                                {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}
                            </div>

                            <!-- User Info -->
                            <div class="hidden text-left sm:block">
                                <p
                                    class="text-xs font-bold tracking-tight text-slate-900 dark:text-slate-100 leading-snug">
                                    {{ Auth::user()->name ?? 'Admin Executive' }}
                                </p>
                                <p class="text-[11px] font-semibold text-slate-500 dark:text-slate-400">
                                    {{ Auth::user()->role === 'super_admin' ? 'Super Administrator' : 'Administrator' }}
                                </p>
                            </div>

                            <!-- Chevron Icon -->
                            <svg class="h-4 w-4 text-slate-400 transition-transform duration-200 group-hover:text-slate-600 dark:text-slate-400 dark:group-hover:text-slate-200"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <!-- Dropdown Menu -->
                        <div id="profile-dropdown-menu"
                            class="hidden absolute right-0 z-50 mt-2 w-56 transform origin-top-right rounded-xl border border-slate-200/80 bg-white py-1.5 shadow-xl transition-all duration-150 dark:border-slate-700/80 dark:bg-slate-800 dark:shadow-2xl">

                            <!-- User Info Header (Mobile View) -->
                            <div class="border-b border-slate-100 px-4 py-2.5 dark:border-slate-700/60 md:hidden">
                                <p class="text-xs font-bold text-slate-900 dark:text-slate-100">
                                    {{ Auth::user()->name ?? 'Admin Executive' }}
                                </p>
                                <p class="mt-0.5 text-[11px] font-semibold text-slate-500 dark:text-slate-400">
                                    {{ Auth::user()->role === 'super_admin' ? 'Super Administrator' : 'Administrator' }}
                                </p>
                            </div>

                            <!-- Navigation Links -->
                            <div class="py-1">
                                <!-- Settings -->
                                <a href="{{ route('member.settings') }}"
                                    class="group flex items-center gap-3 px-4 py-2 text-xs font-semibold text-slate-700 transition-colors hover:bg-slate-50 dark:text-slate-200 dark:hover:bg-slate-700/50">
                                    <svg class="h-4 w-4 text-slate-400 transition-colors group-hover:text-slate-600 dark:text-slate-400 dark:group-hover:text-slate-200"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31C2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    <span>Settings</span>
                                </a>
                            </div>

                            <div class="my-1 border-t border-slate-100 dark:border-slate-700/60"></div>

                            <!-- Logout Form -->
                            <form action="{{ route('logout') }}" method="POST" class="m-0">
                                @csrf
                                <button type="submit"
                                    class="group flex w-full items-center gap-3 px-4 py-2 text-left text-xs font-semibold text-rose-600 transition-colors hover:bg-rose-50/80 focus:outline-none dark:text-rose-400 dark:hover:bg-rose-950/30">
                                    <svg class="h-4 w-4 text-rose-500 transition-colors group-hover:text-rose-600 dark:text-rose-400 dark:group-hover:text-rose-300"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                    </svg>
                                    <span>Sign Out</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Scrollable Content Window -->
            <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8 bg-slate-50 dark:bg-slate-900">
                <!-- Page Title Header -->
                <div class="mb-8">
                    @yield('header')
                </div>

                <!-- Main Content Slot -->
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Vanilla Javascript for Mobile Sidebar Interactions -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const toggleBtn = document.getElementById("mobile-sidebar-toggle");
            const closeBtn = document.getElementById("mobile-sidebar-close");
            const backdrop = document.getElementById("mobile-sidebar-backdrop");
            const sidebar = document.getElementById("mobile-sidebar");
            const panel = document.getElementById("mobile-sidebar-panel");

            function openSidebar() {
                sidebar.classList.remove("hidden");
                // Allow browser to render display before slide in
                setTimeout(() => {
                    panel.classList.remove("-translate-x-full");
                }, 10);
            }

            function closeSidebar() {
                panel.classList.add("-translate-x-full");
                // Allow slide animation to finish
                setTimeout(() => {
                    sidebar.classList.add("hidden");
                }, 300);
            }

            if(toggleBtn) toggleBtn.addEventListener("click", openSidebar);
            if(closeBtn) closeBtn.addEventListener("click", closeSidebar);
            if(backdrop) backdrop.addEventListener("click", closeSidebar);

            // Collapsible Sidebar handler
            const desktopToggleBtn = document.getElementById("desktop-sidebar-toggle");
            const desktopSidebar = document.getElementById("sidebar");

            if (desktopToggleBtn && desktopSidebar) {
                desktopToggleBtn.addEventListener("click", function () {
                    desktopSidebar.classList.toggle("collapsed");
                    const isCollapsed = desktopSidebar.classList.contains("collapsed");
                    localStorage.setItem("sidebar-collapsed", isCollapsed ? "true" : "false");
                });
            }

            // Theme Toggle Logic
            const themeToggleBtn = document.getElementById('theme-toggle');

            if (themeToggleBtn) {
                themeToggleBtn.addEventListener('click', function() {
                    // Toggle dark class and save preference
                    if (document.documentElement.classList.contains('dark')) {
                        document.documentElement.classList.remove('dark');
                        localStorage.setItem('theme', 'light');
                    } else {
                        document.documentElement.classList.add('dark');
                        localStorage.setItem('theme', 'dark');
                    }
                });
            }

            // Profile Dropdown Toggle Logic
            const dropdownTrigger = document.getElementById("profile-dropdown-trigger");
            const dropdownMenu = document.getElementById("profile-dropdown-menu");
            const dropdownWrapper = document.getElementById("profile-dropdown-wrapper");
            
            if (dropdownTrigger && dropdownMenu && dropdownWrapper) {
                dropdownTrigger.addEventListener("click", function(e) {
                    e.stopPropagation();
                    dropdownMenu.classList.toggle("hidden");
                });

                document.addEventListener("click", function(e) {
                    if (!dropdownWrapper.contains(e.target)) {
                        dropdownMenu.classList.add("hidden");
                    }
                });
            }

            // Collapsible Navigation Groups (Submenus)
            const collapseTriggers = document.querySelectorAll(".menu-collapse-trigger");
            collapseTriggers.forEach(trigger => {
                trigger.addEventListener("click", function () {
                    const targetId = this.getAttribute("data-target");
                    const submenu = document.getElementById(targetId);
                    const chevron = this.querySelector(".chevron-icon");
                    const parentBadge = this.querySelector(".parent-badge");
                    
                    if (submenu) {
                        const isHidden = submenu.classList.contains("hidden");
                        if (isHidden) {
                            submenu.classList.remove("hidden");
                            submenu.classList.add("block");
                            this.classList.add("text-emerald-600", "dark:text-emerald-400", "font-bold", "bg-slate-200/50", "dark:bg-slate-700/30");
                            this.classList.remove("text-slate-600", "dark:text-slate-400");
                            if (chevron) chevron.classList.add("rotate-180");
                            if (parentBadge) parentBadge.classList.add("hidden");
                        } else {
                            submenu.classList.add("hidden");
                            submenu.classList.remove("block");
                            this.classList.remove("text-emerald-600", "dark:text-emerald-400", "font-bold", "bg-slate-200/50", "dark:bg-slate-700/30");
                            this.classList.add("text-slate-600", "dark:text-slate-400");
                            if (chevron) chevron.classList.remove("rotate-180");
                            if (parentBadge) parentBadge.classList.remove("hidden");
                        }
                    }
                });
            });
        });
    </script>

    <!-- SweetAlert2 Integration -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Configure a custom SweetAlert2 instance styled for MLSAKO Cooperative Admin Panel
            const MLSAKOAlert = Swal.mixin({
                customClass: {
                    popup: 'rounded-[2rem] border border-slate-150 dark:border-slate-800 shadow-2xl p-6 sm:p-8 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 font-sans',
                    title: 'text-base font-extrabold text-slate-900 dark:text-white tracking-tight pt-2',
                    htmlContainer: 'text-xs font-semibold text-slate-600 dark:text-slate-400 leading-relaxed mt-2',
                    confirmButton: 'bg-emerald-600 hover:bg-emerald-700 dark:bg-emerald-600 dark:hover:bg-emerald-500 text-white text-xs font-bold px-6 py-3 rounded-xl transition-all shadow-md focus:ring-2 focus:ring-emerald-500/20 dark:focus:ring-emerald-500/10 outline-none',
                    cancelButton: 'bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-bold px-6 py-3 rounded-xl transition-all border border-slate-200 dark:border-slate-700 outline-none'
                },
                buttonsStyling: false
            });

            // Expose globally
            window.MLSAKOAlert = MLSAKOAlert;

            // Check and trigger Laravel Session Alerts
            @if(session('success'))
                MLSAKOAlert.fire({
                    icon: 'success',
                    title: {!! json_encode(session('success_title') ?? 'Success') !!},
                    text: {!! json_encode(session('success')) !!},
                    iconColor: '#10b981',
                    confirmButtonText: 'Great, Thank You'
                });
            @endif

            @if(session('error'))
                MLSAKOAlert.fire({
                    icon: 'error',
                    title: 'Action Failed',
                    text: {!! json_encode(session('error')) !!},
                    iconColor: '#f43f5e',
                    confirmButtonText: 'Acknowledge'
                });
            @endif

            @if($errors->any())
                MLSAKOAlert.fire({
                    icon: 'warning',
                    title: 'Validation Corrective Action Required',
                    html: `<div class="text-left space-y-1.5 max-h-[200px] overflow-y-auto pr-2">
                        @foreach ($errors->all() as $error)
                            <div class="flex items-start gap-1.5">
                                <span class="text-amber-500 flex-shrink-0 mt-0.5">•</span>
                                <span class="text-slate-600 dark:text-slate-400 font-semibold text-[11px]">{{ $error }}</span>
                            </div>
                        @endforeach
                    </div>`,
                    iconColor: '#f59e0b',
                    confirmButtonText: 'Resolve Issues'
                });
            @endif
        });
    </script>
    @stack('scripts')
</body>
</html>