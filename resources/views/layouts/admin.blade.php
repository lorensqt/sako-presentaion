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

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />

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

            <!-- Sidebar Navigation -->
            <nav class="flex-1 px-4 py-4 space-y-4 overflow-y-auto overflow-x-hidden sidebar-nav">
                <!-- Group: Core -->
                <div class="space-y-1">
                    <p class="text-[10px] font-extrabold uppercase tracking-widest text-slate-400 dark:text-slate-500 sidebar-text px-2.5 pb-1 select-none">
                        Core Panel
                    </p>
                    <a href="{{ route('admin.dashboard') }}"
                        class="group relative w-full flex items-center gap-3 px-3 py-2 text-xs font-semibold rounded-xl transition-all duration-200 text-left {{ request()->routeIs('admin.dashboard') ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/20 font-bold' : 'text-slate-600 dark:text-slate-400 hover:bg-emerald-500/10 dark:hover:bg-slate-700/60 hover:text-emerald-700 dark:hover:text-emerald-300' }}">
                        <i class="fa-solid fa-chart-pie w-5 text-center flex-shrink-0 text-sm transition-transform duration-200 group-hover:scale-110 group-hover:translate-x-0.5 {{ request()->routeIs('admin.dashboard') ? 'text-white' : 'text-slate-400 group-hover:text-emerald-600 dark:text-slate-400 dark:group-hover:text-emerald-400' }}"></i>
                        <span class="sidebar-text flex-1 truncate">Overview Panel</span>
                        <span class="sidebar-tooltip hidden lg:block">Overview Panel</span>
                    </a>
                </div>

                <!-- Group: Registry -->
                <div class="space-y-1">
                    <p class="text-[10px] font-extrabold uppercase tracking-widest text-slate-400 dark:text-slate-500 sidebar-text px-2.5 pt-2 pb-1 select-none">
                        Registry
                    </p>
                    <a href="{{ route('admin.members') }}"
                        class="group relative w-full flex items-center gap-3 px-3 py-2 text-xs font-semibold rounded-xl transition-all duration-200 text-left {{ request()->routeIs('admin.members') ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/20 font-bold' : 'text-slate-600 dark:text-slate-400 hover:bg-emerald-500/10 dark:hover:bg-slate-700/60 hover:text-emerald-700 dark:hover:text-emerald-300' }}">
                        <i class="fa-solid fa-users w-5 text-center flex-shrink-0 text-sm transition-transform duration-200 group-hover:scale-110 group-hover:translate-x-0.5 {{ request()->routeIs('admin.members') ? 'text-white' : 'text-slate-400 group-hover:text-emerald-600 dark:text-slate-400 dark:group-hover:text-emerald-400' }}"></i>
                        <span class="sidebar-text flex-1 truncate">Members Directory</span>
                        <span class="sidebar-tooltip hidden lg:block">Members Directory</span>
                    </a>
                </div>

                <!-- Group: Credit & Loans (Collapsible) -->
                <div class="space-y-1">
                    <p class="text-[10px] font-extrabold uppercase tracking-widest text-slate-400 dark:text-slate-500 sidebar-text px-2.5 pt-2 pb-1 select-none">
                        Credit &amp; Loans
                    </p>
                    
                    <!-- Trigger Button -->
                    <button type="button" class="group relative flex items-center justify-between w-full px-3 py-2 text-xs font-semibold rounded-xl transition-all duration-200 cursor-pointer menu-collapse-trigger {{ $isLoansActive ? 'text-emerald-600 dark:text-emerald-400 font-bold bg-slate-200/50 dark:bg-slate-700/30' : 'text-slate-600 dark:text-slate-400 hover:bg-emerald-500/10 dark:hover:bg-slate-700/60 hover:text-emerald-700 dark:hover:text-emerald-300' }}" data-target="loans-submenu">
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-hand-holding-dollar w-5 text-center flex-shrink-0 text-sm transition-transform duration-200 group-hover:scale-110 group-hover:translate-x-0.5 {{ $isLoansActive ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-400 group-hover:text-emerald-600 dark:text-slate-400 dark:group-hover:text-emerald-400' }}"></i>
                            <span class="sidebar-text">Credit &amp; Loans</span>
                        </div>
                        <div class="flex items-center gap-1.5 sidebar-text">
                            @if($pendingLoanCount > 0)
                                <span class="parent-badge flex h-4.5 w-4.5 items-center justify-center rounded-full bg-rose-500 text-[9px] font-black text-white animate-pulse {{ $isLoansActive ? 'hidden' : '' }}">
                                    {{ $pendingLoanCount }}
                                </span>
                            @endif
                            <i class="fa-solid fa-chevron-down text-[10px] transition-transform duration-200 chevron-icon {{ $isLoansActive ? 'rotate-180' : '' }}"></i>
                        </div>
                    </button>

                    <!-- Submenu items -->
                    <div id="loans-submenu" class="pl-4 ml-4 border-l border-slate-200 dark:border-slate-700/80 space-y-1 mt-1 transition-all duration-300 {{ $isLoansActive ? 'block' : 'hidden' }} submenu-container">
                        <a href="{{ route('admin.loans') }}"
                            class="group relative flex items-center gap-3 px-3 py-2 text-xs font-semibold rounded-xl transition-all duration-200 {{ request()->routeIs('admin.loans') ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/20 font-bold' : 'text-slate-600 dark:text-slate-400 hover:bg-emerald-500/10 dark:hover:bg-slate-700/60 hover:text-emerald-700 dark:hover:text-emerald-300' }}">
                            <i class="fa-solid fa-list-check w-5 text-center flex-shrink-0 text-sm transition-transform duration-200 group-hover:scale-110 {{ request()->routeIs('admin.loans') ? 'text-white' : 'text-slate-400 group-hover:text-emerald-600 dark:text-slate-400 dark:group-hover:text-emerald-400' }}"></i>
                            <span class="sidebar-text flex-1 truncate">Loans Directory</span>
                            <span class="sidebar-tooltip hidden lg:block">Loans Directory</span>
                        </a>

                        <a href="{{ route('admin.loans.approvals') }}"
                            class="group relative flex items-center gap-3 px-3 py-2 text-xs font-semibold rounded-xl transition-all duration-200 {{ request()->routeIs('admin.loans.approvals') ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/20 font-bold' : 'text-slate-600 dark:text-slate-400 hover:bg-emerald-500/10 dark:hover:bg-slate-700/60 hover:text-emerald-700 dark:hover:text-emerald-300' }}">
                            <i class="fa-solid fa-signature w-5 text-center flex-shrink-0 text-sm transition-transform duration-200 group-hover:scale-110 {{ request()->routeIs('admin.loans.approvals') ? 'text-white' : 'text-slate-400 group-hover:text-emerald-600 dark:text-slate-400 dark:group-hover:text-emerald-400' }}"></i>
                            <span class="sidebar-text flex-1 truncate">Loan Approvals</span>
                            @if($pendingLoanCount > 0)
                                <span class="sidebar-text ml-auto flex h-4.5 w-4.5 items-center justify-center rounded-full bg-rose-500 text-[9px] font-black text-white animate-pulse">
                                    {{ $pendingLoanCount }}
                                </span>
                            @endif
                            <span class="sidebar-tooltip hidden lg:block">Loan Approvals</span>
                        </a>

                        <a href="{{ route('admin.loans.management') }}"
                            class="group relative flex items-center gap-3 px-3 py-2 text-xs font-semibold rounded-xl transition-all duration-200 {{ request()->routeIs('admin.loans.management') ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/20 font-bold' : 'text-slate-600 dark:text-slate-400 hover:bg-emerald-500/10 dark:hover:bg-slate-700/60 hover:text-emerald-700 dark:hover:text-emerald-300' }}">
                            <i class="fa-solid fa-sliders w-5 text-center flex-shrink-0 text-sm transition-transform duration-200 group-hover:scale-110 {{ request()->routeIs('admin.loans.management') ? 'text-white' : 'text-slate-400 group-hover:text-emerald-600 dark:text-slate-400 dark:group-hover:text-emerald-400' }}"></i>
                            <span class="sidebar-text flex-1 truncate">Loans Management</span>
                            <span class="sidebar-tooltip hidden lg:block">Loans Management</span>
                        </a>
                    </div>
                </div>

                <!-- Group: Treasury (Collapsible) -->
                <div class="space-y-1">
                    <p class="text-[10px] font-extrabold uppercase tracking-widest text-slate-400 dark:text-slate-500 sidebar-text px-2.5 pt-2 pb-1 select-none">
                        Treasury
                    </p>

                    <!-- Trigger Button -->
                    <button type="button" class="group relative flex items-center justify-between w-full px-3 py-2 text-xs font-semibold rounded-xl transition-all duration-200 cursor-pointer menu-collapse-trigger {{ $isTreasuryActive ? 'text-emerald-600 dark:text-emerald-400 font-bold bg-slate-200/50 dark:bg-slate-700/30' : 'text-slate-600 dark:text-slate-400 hover:bg-emerald-500/10 dark:hover:bg-slate-700/60 hover:text-emerald-700 dark:hover:text-emerald-300' }}" data-target="treasury-submenu">
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-vault w-5 text-center flex-shrink-0 text-sm transition-transform duration-200 group-hover:scale-110 group-hover:translate-x-0.5 {{ $isTreasuryActive ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-400 group-hover:text-emerald-600 dark:text-slate-400 dark:group-hover:text-emerald-400' }}"></i>
                            <span class="sidebar-text">Treasury</span>
                        </div>
                        <div class="flex items-center gap-1.5 sidebar-text">
                            @if($pendingTreasuryCount > 0)
                                <span class="parent-badge flex h-4.5 w-4.5 items-center justify-center rounded-full bg-rose-500 text-[9px] font-black text-white animate-pulse {{ $isTreasuryActive ? 'hidden' : '' }}">
                                    {{ $pendingTreasuryCount }}
                                </span>
                            @endif
                            <i class="fa-solid fa-chevron-down text-[10px] transition-transform duration-200 chevron-icon {{ $isTreasuryActive ? 'rotate-180' : '' }}"></i>
                        </div>
                    </button>

                    <!-- Submenu items -->
                    <div id="treasury-submenu" class="pl-4 ml-4 border-l border-slate-200 dark:border-slate-700/80 space-y-1 mt-1 transition-all duration-300 {{ $isTreasuryActive ? 'block' : 'hidden' }} submenu-container">
                        <a href="{{ route('admin.withdrawals') }}"
                            class="group relative flex items-center gap-3 px-3 py-2 text-xs font-semibold rounded-xl transition-all duration-200 {{ request()->routeIs('admin.withdrawals') ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/20 font-bold' : 'text-slate-600 dark:text-slate-400 hover:bg-emerald-500/10 dark:hover:bg-slate-700/60 hover:text-emerald-700 dark:hover:text-emerald-300' }}">
                            <i class="fa-solid fa-arrow-up-from-bracket w-5 text-center flex-shrink-0 text-sm transition-transform duration-200 group-hover:scale-110 {{ request()->routeIs('admin.withdrawals') ? 'text-white' : 'text-slate-400 group-hover:text-emerald-600 dark:text-slate-400 dark:group-hover:text-emerald-400' }}"></i>
                            <span class="sidebar-text flex-1 truncate">Withdrawals</span>
                            @if($pendingWithdrawalCount > 0)
                                <span class="sidebar-text ml-auto flex h-4.5 w-4.5 items-center justify-center rounded-full bg-rose-500 text-[9px] font-black text-white animate-pulse">
                                    {{ $pendingWithdrawalCount }}
                                </span>
                            @endif
                            <span class="sidebar-tooltip hidden lg:block">Withdrawals</span>
                        </a>

                        <a href="{{ route('admin.deductions') }}"
                            class="group relative flex items-center gap-3 px-3 py-2 text-xs font-semibold rounded-xl transition-all duration-200 {{ request()->routeIs('admin.deductions') ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/20 font-bold' : 'text-slate-600 dark:text-slate-400 hover:bg-emerald-500/10 dark:hover:bg-slate-700/60 hover:text-emerald-700 dark:hover:text-emerald-300' }}">
                            <i class="fa-solid fa-receipt w-5 text-center flex-shrink-0 text-sm transition-transform duration-200 group-hover:scale-110 {{ request()->routeIs('admin.deductions') ? 'text-white' : 'text-slate-400 group-hover:text-emerald-600 dark:text-slate-400 dark:group-hover:text-emerald-400' }}"></i>
                            <span class="sidebar-text flex-1 truncate">Deduction Approvals</span>
                            @if($pendingDeductionCount > 0)
                                <span class="sidebar-text ml-auto flex h-4.5 w-4.5 items-center justify-center rounded-full bg-rose-500 text-[9px] font-black text-white animate-pulse">
                                    {{ $pendingDeductionCount }}
                                </span>
                            @endif
                            <span class="sidebar-tooltip hidden lg:block">Deduction Approvals</span>
                        </a>
                    </div>
                </div>

                <!-- Group: Governance -->
                <div class="space-y-1">
                    <p class="text-[10px] font-extrabold uppercase tracking-widest text-slate-400 dark:text-slate-500 sidebar-text px-2.5 pt-2 pb-1 select-none">
                        Governance &amp; Elections
                    </p>
                    <a href="{{ route('admin.elections.index') }}"
                        class="group relative w-full flex items-center gap-3 px-3 py-2 text-xs font-semibold rounded-xl transition-all duration-200 text-left {{ request()->routeIs('admin.elections.*') ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/20 font-bold' : 'text-slate-600 dark:text-slate-400 hover:bg-emerald-500/10 dark:hover:bg-slate-700/60 hover:text-emerald-700 dark:hover:text-emerald-300' }}">
                        <i class="fa-solid fa-check-to-slot w-5 text-center flex-shrink-0 text-sm transition-transform duration-200 group-hover:scale-110 group-hover:translate-x-0.5 {{ request()->routeIs('admin.elections.*') ? 'text-white' : 'text-slate-400 group-hover:text-emerald-600 dark:text-slate-400 dark:group-hover:text-emerald-400' }}"></i>
                        <span class="sidebar-text flex-1 truncate">Elections</span>
                        <span class="sidebar-tooltip hidden lg:block">Elections</span>
                    </a>
                </div>

                <!-- Group: Security -->
                <div class="space-y-1">
                    <p class="text-[10px] font-extrabold uppercase tracking-widest text-slate-400 dark:text-slate-500 sidebar-text px-2.5 pt-2 pb-1 select-none">
                        System Security
                    </p>
                    <a href="{{ route('admin.audit-logs') }}"
                        class="group relative w-full flex items-center gap-3 px-3 py-2 text-xs font-semibold rounded-xl transition-all duration-200 text-left {{ request()->routeIs('admin.audit-logs') ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/20 font-bold' : 'text-slate-600 dark:text-slate-400 hover:bg-emerald-500/10 dark:hover:bg-slate-700/60 hover:text-emerald-700 dark:hover:text-emerald-300' }}">
                        <i class="fa-solid fa-shield-halved w-5 text-center flex-shrink-0 text-sm transition-transform duration-200 group-hover:scale-110 group-hover:translate-x-0.5 {{ request()->routeIs('admin.audit-logs') ? 'text-white' : 'text-slate-400 group-hover:text-emerald-600 dark:text-slate-400 dark:group-hover:text-emerald-400' }}"></i>
                        <span class="sidebar-text flex-1 truncate">Audit &amp; Security Logs</span>
                        <span class="sidebar-tooltip hidden lg:block">Audit &amp; Security Logs</span>
                    </a>
                </div>
            </nav>

            <!-- Sidebar Footer -->
            {{-- <div class="p-4 border-t border-slate-200 dark:border-slate-700">
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                    @csrf
                </form>
                <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="relative flex items-center gap-2.5 px-2.5 py-2 text-[13px] font-semibold text-rose-600 dark:text-rose-400 rounded-xl hover:bg-rose-50 dark:hover:bg-rose-950/20 hover:text-rose-700 dark:hover:text-rose-300 transition-all duration-200">
                    <!-- Logout Icon -->
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    <span class="sidebar-text">Sign Out</span>
                    <span class="sidebar-tooltip hidden lg:block">Sign Out</span>
                </a>
            </div> --}}
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

                <div class="flex-1 px-4 py-4 space-y-4 overflow-y-auto sidebar-nav">
                    <!-- Group: Core -->
                    <div class="space-y-1">
                        <p class="text-[10px] font-extrabold uppercase tracking-widest text-slate-400 dark:text-slate-500 px-2.5 pb-1 select-none">
                            Core Panel
                        </p>
                        <a href="{{ route('admin.dashboard') }}"
                            class="group w-full flex items-center gap-3 px-3 py-2.5 text-xs font-semibold rounded-xl transition-all duration-200 text-left {{ request()->routeIs('admin.dashboard') ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/20 font-bold' : 'text-slate-600 dark:text-slate-400 hover:bg-emerald-500/10 dark:hover:bg-slate-700/60 hover:text-emerald-700 dark:hover:text-emerald-300' }}">
                            <i class="fa-solid fa-chart-pie w-5 text-center flex-shrink-0 text-sm {{ request()->routeIs('admin.dashboard') ? 'text-white' : 'text-slate-400 group-hover:text-emerald-600 dark:text-slate-400 dark:group-hover:text-emerald-400' }}"></i>
                            <span>Overview Panel</span>
                        </a>
                    </div>

                    <!-- Group: Registry -->
                    <div class="space-y-1">
                        <p class="text-[10px] font-extrabold uppercase tracking-widest text-slate-400 dark:text-slate-500 px-2.5 pt-2 pb-1 select-none">
                            Registry
                        </p>
                        <a href="{{ route('admin.members') }}"
                            class="group w-full flex items-center gap-3 px-3 py-2.5 text-xs font-semibold rounded-xl transition-all duration-200 text-left {{ request()->routeIs('admin.members') ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/20 font-bold' : 'text-slate-600 dark:text-slate-400 hover:bg-emerald-500/10 dark:hover:bg-slate-700/60 hover:text-emerald-700 dark:hover:text-emerald-300' }}">
                            <i class="fa-solid fa-users w-5 text-center flex-shrink-0 text-sm {{ request()->routeIs('admin.members') ? 'text-white' : 'text-slate-400 group-hover:text-emerald-600 dark:text-slate-400 dark:group-hover:text-emerald-400' }}"></i>
                            <span>Members Directory</span>
                        </a>
                    </div>

                    <!-- Group: Credit & Loans (Collapsible) -->
                    <div class="space-y-1">
                        <p class="text-[10px] font-extrabold uppercase tracking-widest text-slate-400 dark:text-slate-500 px-2.5 pt-2 pb-1 select-none">
                            Credit &amp; Loans
                        </p>
                        
                        <button type="button" class="group flex items-center justify-between w-full px-3 py-2.5 text-xs font-semibold rounded-xl transition-all duration-200 cursor-pointer menu-collapse-trigger {{ $isLoansActive ? 'text-emerald-600 dark:text-emerald-400 font-bold bg-slate-200/60 dark:bg-slate-700/30' : 'text-slate-600 dark:text-slate-400 hover:bg-emerald-500/10 dark:hover:bg-slate-700/60 hover:text-emerald-700 dark:hover:text-emerald-300' }}" data-target="mobile-loans-submenu">
                            <div class="flex items-center gap-3">
                                <i class="fa-solid fa-hand-holding-dollar w-5 text-center flex-shrink-0 text-sm {{ $isLoansActive ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-400 group-hover:text-emerald-600 dark:text-slate-400 dark:group-hover:text-emerald-400' }}"></i>
                                <span>Credit &amp; Loans</span>
                            </div>
                            <div class="flex items-center gap-1.5">
                                @if($pendingLoanCount > 0)
                                    <span class="parent-badge flex h-4.5 w-4.5 items-center justify-center rounded-full bg-rose-500 text-[9px] font-black text-white {{ $isLoansActive ? 'hidden' : '' }}">
                                        {{ $pendingLoanCount }}
                                    </span>
                                @endif
                                <i class="fa-solid fa-chevron-down text-[10px] transition-transform duration-200 chevron-icon {{ $isLoansActive ? 'rotate-180' : '' }}"></i>
                            </div>
                        </button>

                        <div id="mobile-loans-submenu" class="pl-4 ml-4 border-l border-slate-200 dark:border-slate-700/80 space-y-1 mt-1 {{ $isLoansActive ? 'block' : 'hidden' }}">
                            <a href="{{ route('admin.loans') }}"
                                class="flex items-center gap-3 px-3 py-2 text-xs font-semibold rounded-xl transition-all duration-200 {{ request()->routeIs('admin.loans') ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/20 font-bold' : 'text-slate-600 dark:text-slate-400 hover:bg-emerald-500/10 dark:hover:bg-slate-700/60 hover:text-emerald-700 dark:hover:text-emerald-300' }}">
                                <i class="fa-solid fa-list-check w-5 text-center flex-shrink-0 text-sm {{ request()->routeIs('admin.loans') ? 'text-white' : 'text-slate-400' }}"></i>
                                <span>Loans Directory</span>
                            </a>
                            <a href="{{ route('admin.loans.approvals') }}"
                                class="flex items-center gap-3 px-3 py-2 text-xs font-semibold rounded-xl transition-all duration-200 {{ request()->routeIs('admin.loans.approvals') ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/20 font-bold' : 'text-slate-600 dark:text-slate-400 hover:bg-emerald-500/10 dark:hover:bg-slate-700/60 hover:text-emerald-700 dark:hover:text-emerald-300' }}">
                                <i class="fa-solid fa-signature w-5 text-center flex-shrink-0 text-sm {{ request()->routeIs('admin.loans.approvals') ? 'text-white' : 'text-slate-400' }}"></i>
                                <span>Loan Approvals</span>
                                @if($pendingLoanCount > 0)
                                    <span class="ml-auto flex h-4.5 w-4.5 items-center justify-center rounded-full bg-rose-500 text-[9px] font-black text-white">
                                        {{ $pendingLoanCount }}
                                    </span>
                                @endif
                            </a>
                            <a href="{{ route('admin.loans.management') }}"
                                class="flex items-center gap-3 px-3 py-2 text-xs font-semibold rounded-xl transition-all duration-200 {{ request()->routeIs('admin.loans.management') ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/20 font-bold' : 'text-slate-600 dark:text-slate-400 hover:bg-emerald-500/10 dark:hover:bg-slate-700/60 hover:text-emerald-700 dark:hover:text-emerald-300' }}">
                                <i class="fa-solid fa-sliders w-5 text-center flex-shrink-0 text-sm {{ request()->routeIs('admin.loans.management') ? 'text-white' : 'text-slate-400' }}"></i>
                                <span>Loans Management</span>
                            </a>
                        </div>
                    </div>

                    <!-- Group: Treasury (Collapsible) -->
                    <div class="space-y-1">
                        <p class="text-[10px] font-extrabold uppercase tracking-widest text-slate-400 dark:text-slate-500 px-2.5 pt-2 pb-1 select-none">
                            Treasury
                        </p>

                        <button type="button" class="group flex items-center justify-between w-full px-3 py-2.5 text-xs font-semibold rounded-xl transition-all duration-200 cursor-pointer menu-collapse-trigger {{ $isTreasuryActive ? 'text-emerald-600 dark:text-emerald-400 font-bold bg-slate-200/60 dark:bg-slate-700/30' : 'text-slate-600 dark:text-slate-400 hover:bg-emerald-500/10 dark:hover:bg-slate-700/60 hover:text-emerald-700 dark:hover:text-emerald-300' }}" data-target="mobile-treasury-submenu">
                            <div class="flex items-center gap-3">
                                <i class="fa-solid fa-vault w-5 text-center flex-shrink-0 text-sm {{ $isTreasuryActive ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-400 group-hover:text-emerald-600 dark:text-slate-400 dark:group-hover:text-emerald-400' }}"></i>
                                <span>Treasury</span>
                            </div>
                            <div class="flex items-center gap-1.5">
                                @if($pendingTreasuryCount > 0)
                                    <span class="parent-badge flex h-4.5 w-4.5 items-center justify-center rounded-full bg-rose-500 text-[9px] font-black text-white {{ $isTreasuryActive ? 'hidden' : '' }}">
                                        {{ $pendingTreasuryCount }}
                                    </span>
                                @endif
                                <i class="fa-solid fa-chevron-down text-[10px] transition-transform duration-200 chevron-icon {{ $isTreasuryActive ? 'rotate-180' : '' }}"></i>
                            </div>
                        </button>

                        <div id="mobile-treasury-submenu" class="pl-4 ml-4 border-l border-slate-200 dark:border-slate-700/80 space-y-1 mt-1 {{ $isTreasuryActive ? 'block' : 'hidden' }}">
                            <a href="{{ route('admin.withdrawals') }}"
                                class="flex items-center gap-3 px-3 py-2 text-xs font-semibold rounded-xl transition-all duration-200 {{ request()->routeIs('admin.withdrawals') ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/20 font-bold' : 'text-slate-600 dark:text-slate-400 hover:bg-emerald-500/10 dark:hover:bg-slate-700/60 hover:text-emerald-700 dark:hover:text-emerald-300' }}">
                                <i class="fa-solid fa-arrow-up-from-bracket w-5 text-center flex-shrink-0 text-sm {{ request()->routeIs('admin.withdrawals') ? 'text-white' : 'text-slate-400' }}"></i>
                                <span>Withdrawal Approvals</span>
                                @if($pendingWithdrawalCount > 0)
                                    <span class="ml-auto flex h-4.5 w-4.5 items-center justify-center rounded-full bg-rose-500 text-[9px] font-black text-white">
                                        {{ $pendingWithdrawalCount }}
                                    </span>
                                @endif
                            </a>
                            <a href="{{ route('admin.deductions') }}"
                                class="flex items-center gap-3 px-3 py-2 text-xs font-semibold rounded-xl transition-all duration-200 {{ request()->routeIs('admin.deductions') ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/20 font-bold' : 'text-slate-600 dark:text-slate-400 hover:bg-emerald-500/10 dark:hover:bg-slate-700/60 hover:text-emerald-700 dark:hover:text-emerald-300' }}">
                                <i class="fa-solid fa-receipt w-5 text-center flex-shrink-0 text-sm {{ request()->routeIs('admin.deductions') ? 'text-white' : 'text-slate-400' }}"></i>
                                <span>Deduction Approvals</span>
                                @if($pendingDeductionCount > 0)
                                    <span class="ml-auto flex h-4.5 w-4.5 items-center justify-center rounded-full bg-rose-500 text-[9px] font-black text-white">
                                        {{ $pendingDeductionCount }}
                                    </span>
                                @endif
                            </a>
                        </div>
                    </div>

                    <!-- Group: Governance -->
                    <div class="space-y-1">
                        <p class="text-[10px] font-extrabold uppercase tracking-widest text-slate-400 dark:text-slate-500 px-2.5 pt-2 pb-1 select-none">
                            Governance &amp; Elections
                        </p>
                        <a href="{{ route('admin.elections.index') }}"
                            class="group w-full flex items-center gap-3 px-3 py-2.5 text-xs font-semibold rounded-xl transition-all duration-200 text-left {{ request()->routeIs('admin.elections.*') ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/20 font-bold' : 'text-slate-600 dark:text-slate-400 hover:bg-emerald-500/10 dark:hover:bg-slate-700/60 hover:text-emerald-700 dark:hover:text-emerald-300' }}">
                            <i class="fa-solid fa-check-to-slot w-5 text-center flex-shrink-0 text-sm {{ request()->routeIs('admin.elections.*') ? 'text-white' : 'text-slate-400 group-hover:text-emerald-600 dark:text-slate-400 dark:group-hover:text-emerald-400' }}"></i>
                            <span>Elections</span>
                        </a>
                    </div>

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
                <div class="flex items-center gap-2 flex-shrink-0">
                    <!-- Profile Dropdown Wrapper -->
                    <div class="relative" id="profile-dropdown-wrapper">
                        <!-- Profile Dropdown Trigger -->
                        <button id="profile-dropdown-trigger" type="button"
                            class="group flex items-center gap-2 rounded-xl p-1.5 transition-colors hover:bg-slate-100 dark:hover:bg-slate-800/60 focus:outline-none focus:ring-2 focus:ring-emerald-500/20">

                            <!-- Avatar -->
                            <div
                                class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-xl bg-emerald-600 font-bold text-sm text-white shadow-xs transition-transform duration-200 group-hover:scale-105">
                                {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}
                            </div>

                            <!-- User Info -->
                            <div class="hidden text-left sm:block">
                                <p
                                    class="text-xs font-bold tracking-tight text-slate-900 dark:text-slate-100 leading-snug">
                                    {{ Auth::user()->name ?? 'Admin Executive' }}
                                </p>
                                <p class="text-[10px] font-semibold text-slate-500 dark:text-slate-400">
                                    {{ Auth::user()->role === 'super_admin' ? 'Super Administrator' : 'Administrator' }}
                                </p>
                            </div>

                            <!-- Chevron Icon -->
                            <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 transition-transform duration-200 group-hover:text-slate-600 dark:text-slate-400 dark:group-hover:text-slate-200"></i>
                        </button>

                        <!-- Dropdown Menu -->
                        <div id="profile-dropdown-menu"
                            class="hidden absolute right-0 z-50 mt-2 w-56 sm:w-60 transform origin-top-right rounded-2xl border border-slate-200/80 bg-white p-1.5 shadow-xl transition-all duration-150 dark:border-slate-700/80 dark:bg-slate-800 dark:shadow-2xl">

                            <!-- User Info Header -->
                            <div class="border-b border-slate-100 dark:border-slate-700/60 px-3.5 py-2.5">
                                <p class="text-xs font-bold text-slate-900 dark:text-slate-100 truncate">
                                    {{ Auth::user()->name ?? 'Admin Executive' }}
                                </p>
                                <div class="flex items-center justify-between gap-2 mt-0.5">
                                    <p class="text-[10px] font-semibold text-slate-500 dark:text-slate-400">
                                        {{ Auth::user()->role === 'super_admin' ? 'Super Administrator' : 'Administrator' }}
                                    </p>
                                    @if(Auth::check() && Auth::user()->company_id)
                                        <span class="text-[9px] font-mono font-bold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/50 px-1.5 py-0.5 rounded border border-emerald-200/50 dark:border-emerald-800/40">
                                            {{ Auth::user()->company_id }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <!-- Navigation Links & Settings -->
                            <div class="py-1">
                                <!-- Settings -->
                                <a href="{{ route('member.settings') }}"
                                    class="group flex items-center gap-3 px-3.5 py-2 rounded-xl text-xs font-semibold text-slate-700 transition-colors hover:bg-slate-50 dark:text-slate-200 dark:hover:bg-slate-700/50">
                                    <i class="fa-solid fa-gear fa-fw text-sm text-slate-400 transition-colors group-hover:text-slate-600 dark:text-slate-400 dark:group-hover:text-slate-200"></i>
                                    <span>Settings</span>
                                </a>

                                @if(Auth::check() && in_array(Auth::user()->role, ['admin', 'super_admin']))
                                    <!-- Switch to Member Portal -->
                                    <a href="{{ route('member.savings') }}"
                                        class="group flex items-center gap-3 px-3.5 py-2 rounded-xl text-xs font-semibold text-slate-700 transition-colors hover:bg-slate-50 dark:text-slate-200 dark:hover:bg-slate-700/50">
                                        <i class="fa-solid fa-user-tag fa-fw text-sm text-slate-400 transition-colors group-hover:text-slate-600 dark:text-slate-400 dark:group-hover:text-slate-200"></i>
                                        <span>Member Portal</span>
                                    </a>
                                @endif

                                <!-- Theme Switcher Row -->
                                <div id="theme-toggle-row"
                                    class="group flex items-center justify-between px-3.5 py-2 rounded-xl text-xs font-semibold text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors cursor-pointer select-none">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-6 h-6 rounded-lg bg-slate-100 dark:bg-slate-700/70 flex items-center justify-center text-slate-500 dark:text-amber-400">
                                            <!-- Moon icon for light mode, Sun for dark mode -->
                                            <svg class="w-3.5 h-3.5 block dark:hidden text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                                            </svg>
                                            <svg class="w-3.5 h-3.5 hidden dark:block text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                                            </svg>
                                        </div>
                                        <span>Dark Mode</span>
                                    </div>

                                    <!-- Switch Toggle Pill -->
                                    <button id="theme-toggle" type="button"
                                        class="relative inline-flex h-5 w-9 flex-shrink-0 cursor-pointer items-center rounded-full border border-transparent bg-slate-300 dark:bg-emerald-600 transition-colors duration-200 ease-in-out focus:outline-none"
                                        role="switch" aria-checked="false" title="Toggle Theme">
                                        <span class="sr-only">Toggle Theme</span>
                                        <span class="pointer-events-none inline-block h-3.5 w-3.5 transform rounded-full bg-white shadow-xs transition duration-200 ease-in-out translate-x-0.5 dark:translate-x-4.5"></span>
                                    </button>
                                </div>
                            </div>

                            <div class="my-1 border-t border-slate-100 dark:border-slate-700/60"></div>

                            <!-- Logout Form -->
                            <form action="{{ route('logout') }}" method="POST" class="m-0">
                                @csrf
                                <button type="submit"
                                    class="group flex w-full items-center gap-3 px-3.5 py-2 rounded-xl text-left text-xs font-semibold text-rose-600 transition-colors hover:bg-rose-50/80 focus:outline-none dark:text-rose-400 dark:hover:bg-rose-950/30">
                                    <i class="fa-solid fa-right-from-bracket fa-fw text-sm text-rose-500 transition-colors group-hover:text-rose-600 dark:text-rose-400 dark:group-hover:text-rose-300"></i>
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
            const themeToggleRow = document.getElementById('theme-toggle-row');

            function toggleTheme() {
                if (document.documentElement.classList.contains('dark')) {
                    document.documentElement.classList.remove('dark');
                    localStorage.setItem('theme', 'light');
                    if (themeToggleBtn) themeToggleBtn.setAttribute('aria-checked', 'false');
                } else {
                    document.documentElement.classList.add('dark');
                    localStorage.setItem('theme', 'dark');
                    if (themeToggleBtn) themeToggleBtn.setAttribute('aria-checked', 'true');
                }
            }

            if (themeToggleBtn) {
                themeToggleBtn.setAttribute('aria-checked', document.documentElement.classList.contains('dark') ? 'true' : 'false');
                themeToggleBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    toggleTheme();
                });
            }

            if (themeToggleRow) {
                themeToggleRow.addEventListener('click', function(e) {
                    if (e.target.closest('#theme-toggle')) return;
                    toggleTheme();
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

                // Do not close dropdown on theme toggle click; close on links or submit
                dropdownMenu.addEventListener("click", function(e) {
                    if (e.target.closest('a') || e.target.closest('button[type="submit"]')) {
                        dropdownMenu.classList.add("hidden");
                    } else {
                        e.stopPropagation();
                    }
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