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
        #sidebar.collapsed .sidebar-collapsed-affiliations {
            display: flex !important;
        }
        #sidebar.collapsed .sidebar-collapsed-affiliations:hover .sidebar-tooltip {
            opacity: 1;
            transform: translateX(0.25rem);
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

    @php
        $authUser = auth()->user();
        $canOverview = $authUser ? $authUser->canAccessAdminPage('dashboard') : false;
        $canMembers = $authUser ? $authUser->canAccessAdminPage('members') : false;
        $canLoansDir = $authUser ? $authUser->canAccessAdminPage('loans') : false;
        $canLoanApprovals = $authUser ? $authUser->canAccessAdminPage('loan_approvals') : false;
        $canLoansMgmt = $authUser ? $authUser->canAccessAdminPage('loans_management') : false;
        $canLoansGroup = $canLoansDir || $canLoanApprovals || $canLoansMgmt;
        $canWithdrawals = $authUser ? $authUser->canAccessAdminPage('withdrawals') : false;
        $canDeductions = $authUser ? $authUser->canAccessAdminPage('deductions') : false;
        $canTreasuryGroup = $canWithdrawals || $canDeductions;
        $canElections = $authUser ? $authUser->canAccessAdminPage('elections') : false;
        $canAuditLogs = $authUser ? $authUser->canAccessAdminPage('audit_logs') : false;
        $firstAdminRoute = $authUser ? $authUser->firstAccessibleAdminRoute() : 'admin.dashboard';
    @endphp

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
                <a href="{{ route($firstAdminRoute) }}" class="flex items-center gap-2.5 group">
                    <img src="{{ asset('img/sako-logo-nobg.png') }}" alt="ML Sako Logo" class="h-9 w-auto object-contain transition-transform duration-200 group-hover:scale-105 flex-shrink-0">
                    <span class="text-base font-black tracking-widest text-slate-900 dark:text-white uppercase sidebar-text">
                        ML<span class="text-emerald-600 font-black">Sako</span>
                    </span>
                    <span class="inline-flex items-center px-1.5 py-0.5 rounded bg-emerald-500/10 text-emerald-600 text-[9px] font-black tracking-wider uppercase border border-emerald-500/20 sidebar-text">{{ $authUser && $authUser->role === 'super_admin' ? 'Super Admin' : 'Admin' }}</span>
                </a>
            </div>

            <!-- Affiliations Card (Desktop Sidebar) -->
            <div class="px-3.5 py-3 border-b border-slate-200/80 dark:border-slate-700/80 bg-slate-50/50 dark:bg-slate-800/50">
                <!-- Expanded State -->
                <div class="sidebar-text space-y-1.5 p-2.5 rounded-xl bg-white dark:bg-slate-900/60 border border-slate-200/80 dark:border-slate-700/70 shadow-2xs">
                    <div class="flex items-center justify-between">
                        <span class="text-[9.5px] font-extrabold uppercase tracking-wider text-slate-400 dark:text-slate-500 flex items-center gap-1.5">
                            <i class="fa-solid fa-users-gear text-emerald-600 text-[10px]"></i>
                            <span>My Affiliations</span>
                        </span>
                        <span class="text-[9px] font-bold text-slate-400 dark:text-slate-500">({{ count($userRoles) }})</span>
                    </div>
                    <div class="flex flex-wrap gap-1">
                        @forelse($userRoles as $slug)
                            <span class="text-emerald-700 dark:text-emerald-300 font-extrabold bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-200/60 dark:border-emerald-800/40 px-2 py-0.5 rounded-md text-[9px] uppercase tracking-wider transition-all hover:bg-emerald-100 dark:hover:bg-emerald-950/70" title="Committee: {{ ucwords(str_replace('_', ' ', $slug)) }}">
                                {{ str_replace('_', ' ', $slug) }}
                            </span>
                        @empty
                            <span class="text-slate-400 dark:text-slate-500 font-semibold italic text-[9px]">Auditor</span>
                        @endforelse
                    </div>
                </div>

                <!-- Collapsed State: Icon with Tooltip -->
                <div class="sidebar-collapsed-affiliations hidden group relative justify-center py-0.5">
                    <div class="w-9 h-9 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 flex items-center justify-center text-emerald-600 dark:text-emerald-400 shadow-2xs cursor-pointer">
                        <i class="fa-solid fa-users-gear text-xs"></i>
                    </div>
                    <div class="sidebar-tooltip">
                        <span class="font-extrabold block text-[10px] text-emerald-400 mb-0.5 uppercase tracking-wider">My Affiliations</span>
                        <span class="text-xs">{{ implode(', ', array_map(fn($s) => ucwords(str_replace('_', ' ', $s)), $userRoles)) ?: 'Auditor' }}</span>
                    </div>
                </div>
            </div>

            <!-- Sidebar Navigation -->
            <nav class="flex-1 px-4 py-4 space-y-4 overflow-y-auto overflow-x-hidden sidebar-nav">
                @if($canOverview)
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
                @endif

                @if($canMembers)
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
                @endif

                @if($canLoansGroup)
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
                            @if($pendingLoanCount > 0 && $canLoanApprovals)
                                <span class="parent-badge flex h-4.5 w-4.5 items-center justify-center rounded-full bg-rose-500 text-[9px] font-black text-white animate-pulse {{ $isLoansActive ? 'hidden' : '' }}">
                                    {{ $pendingLoanCount }}
                                </span>
                            @endif
                            <i class="fa-solid fa-chevron-down text-[10px] transition-transform duration-200 chevron-icon {{ $isLoansActive ? 'rotate-180' : '' }}"></i>
                        </div>
                    </button>

                    <!-- Submenu items -->
                    <div id="loans-submenu" class="pl-4 ml-4 border-l border-slate-200 dark:border-slate-700/80 space-y-1 mt-1 transition-all duration-300 {{ $isLoansActive ? 'block' : 'hidden' }} submenu-container">
                        @if($canLoansDir)
                        <a href="{{ route('admin.loans') }}"
                            class="group relative flex items-center gap-3 px-3 py-2 text-xs font-semibold rounded-xl transition-all duration-200 {{ request()->routeIs('admin.loans') ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/20 font-bold' : 'text-slate-600 dark:text-slate-400 hover:bg-emerald-500/10 dark:hover:bg-slate-700/60 hover:text-emerald-700 dark:hover:text-emerald-300' }}">
                            <i class="fa-solid fa-list-check w-5 text-center flex-shrink-0 text-sm transition-transform duration-200 group-hover:scale-110 {{ request()->routeIs('admin.loans') ? 'text-white' : 'text-slate-400 group-hover:text-emerald-600 dark:text-slate-400 dark:group-hover:text-emerald-400' }}"></i>
                            <span class="sidebar-text flex-1 truncate">Loans Directory</span>
                            <span class="sidebar-tooltip hidden lg:block">Loans Directory</span>
                        </a>
                        @endif

                        @if($canLoanApprovals)
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
                        @endif

                        @if($canLoansMgmt)
                        <a href="{{ route('admin.loans.management') }}"
                            class="group relative flex items-center gap-3 px-3 py-2 text-xs font-semibold rounded-xl transition-all duration-200 {{ request()->routeIs('admin.loans.management') ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/20 font-bold' : 'text-slate-600 dark:text-slate-400 hover:bg-emerald-500/10 dark:hover:bg-slate-700/60 hover:text-emerald-700 dark:hover:text-emerald-300' }}">
                            <i class="fa-solid fa-sliders w-5 text-center flex-shrink-0 text-sm transition-transform duration-200 group-hover:scale-110 {{ request()->routeIs('admin.loans.management') ? 'text-white' : 'text-slate-400 group-hover:text-emerald-600 dark:text-slate-400 dark:group-hover:text-emerald-400' }}"></i>
                            <span class="sidebar-text flex-1 truncate">Loans Management</span>
                            <span class="sidebar-tooltip hidden lg:block">Loans Management</span>
                        </a>
                        @endif
                    </div>
                </div>
                @endif

                @if($canTreasuryGroup)
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
                        @if($canWithdrawals)
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
                        @endif

                        @if($canDeductions)
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
                        @endif
                    </div>
                </div>
                @endif

                @if($canElections)
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
                @endif

                @if($canAuditLogs || ($authUser && $authUser->role === 'super_admin'))
                <!-- Group: Security -->
                <div class="space-y-1">
                    <p class="text-[10px] font-extrabold uppercase tracking-widest text-slate-400 dark:text-slate-500 sidebar-text px-2.5 pt-2 pb-1 select-none">
                        System Security
                    </p>
                    @if($authUser && $authUser->role === 'super_admin')
                    <a href="{{ route('admin.administrators') }}"
                        class="group relative w-full flex items-center gap-3 px-3 py-2 text-xs font-semibold rounded-xl transition-all duration-200 text-left {{ request()->routeIs('admin.administrators*') ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/20 font-bold' : 'text-slate-600 dark:text-slate-400 hover:bg-emerald-500/10 dark:hover:bg-slate-700/60 hover:text-emerald-700 dark:hover:text-emerald-300' }}">
                        <i class="fa-solid fa-user-shield w-5 text-center flex-shrink-0 text-sm transition-transform duration-200 group-hover:scale-110 group-hover:translate-x-0.5 {{ request()->routeIs('admin.administrators*') ? 'text-white' : 'text-slate-400 group-hover:text-emerald-600 dark:text-slate-400 dark:group-hover:text-emerald-400' }}"></i>
                        <span class="sidebar-text flex-1 truncate">Administrators</span>
                        <span class="sidebar-tooltip hidden lg:block">System Administrators</span>
                    </a>
                    @endif

                    @if($canAuditLogs)
                    <a href="{{ route('admin.audit-logs') }}"
                        class="group relative w-full flex items-center gap-3 px-3 py-2 text-xs font-semibold rounded-xl transition-all duration-200 text-left {{ request()->routeIs('admin.audit-logs') ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/20 font-bold' : 'text-slate-600 dark:text-slate-400 hover:bg-emerald-500/10 dark:hover:bg-slate-700/60 hover:text-emerald-700 dark:hover:text-emerald-300' }}">
                        <i class="fa-solid fa-shield-halved w-5 text-center flex-shrink-0 text-sm transition-transform duration-200 group-hover:scale-110 group-hover:translate-x-0.5 {{ request()->routeIs('admin.audit-logs') ? 'text-white' : 'text-slate-400 group-hover:text-emerald-600 dark:text-slate-400 dark:group-hover:text-emerald-400' }}"></i>
                        <span class="sidebar-text flex-1 truncate">Audit &amp; Security Logs</span>
                        <span class="sidebar-tooltip hidden lg:block">Audit &amp; Security Logs</span>
                    </a>
                    @endif
                </div>
                @endif
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

                <!-- Affiliations Card (Mobile Sidebar) -->
                <div class="px-4 py-3 border-b border-slate-200/80 dark:border-slate-700/80 bg-slate-50/50 dark:bg-slate-800/50">
                    <div class="space-y-1.5 p-2.5 rounded-xl bg-white dark:bg-slate-900/60 border border-slate-200/80 dark:border-slate-700/70 shadow-2xs">
                        <div class="flex items-center justify-between">
                            <span class="text-[9.5px] font-extrabold uppercase tracking-wider text-slate-400 dark:text-slate-500 flex items-center gap-1.5">
                                <i class="fa-solid fa-users-gear text-emerald-600 text-[10px]"></i>
                                <span>My Affiliations</span>
                            </span>
                            <span class="text-[9px] font-bold text-slate-400 dark:text-slate-500">({{ count($userRoles) }})</span>
                        </div>
                        <div class="flex flex-wrap gap-1">
                            @forelse($userRoles as $slug)
                                <span class="text-emerald-700 dark:text-emerald-300 font-extrabold bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-200/60 dark:border-emerald-800/40 px-2 py-0.5 rounded-md text-[9px] uppercase tracking-wider">
                                    {{ str_replace('_', ' ', $slug) }}
                                </span>
                            @empty
                                <span class="text-slate-400 dark:text-slate-500 font-semibold italic text-[9px]">Auditor</span>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="flex-1 px-4 py-4 space-y-4 overflow-y-auto sidebar-nav">
                    @if($canOverview)
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
                    @endif

                    @if($canMembers)
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
                    @endif

                    @if($canLoansGroup)
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
                                @if($pendingLoanCount > 0 && $canLoanApprovals)
                                    <span class="parent-badge flex h-4.5 w-4.5 items-center justify-center rounded-full bg-rose-500 text-[9px] font-black text-white {{ $isLoansActive ? 'hidden' : '' }}">
                                        {{ $pendingLoanCount }}
                                    </span>
                                @endif
                                <i class="fa-solid fa-chevron-down text-[10px] transition-transform duration-200 chevron-icon {{ $isLoansActive ? 'rotate-180' : '' }}"></i>
                            </div>
                        </button>

                        <div id="mobile-loans-submenu" class="pl-4 ml-4 border-l border-slate-200 dark:border-slate-700/80 space-y-1 mt-1 {{ $isLoansActive ? 'block' : 'hidden' }}">
                            @if($canLoansDir)
                            <a href="{{ route('admin.loans') }}"
                                class="flex items-center gap-3 px-3 py-2 text-xs font-semibold rounded-xl transition-all duration-200 {{ request()->routeIs('admin.loans') ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/20 font-bold' : 'text-slate-600 dark:text-slate-400 hover:bg-emerald-500/10 dark:hover:bg-slate-700/60 hover:text-emerald-700 dark:hover:text-emerald-300' }}">
                                <i class="fa-solid fa-list-check w-5 text-center flex-shrink-0 text-sm {{ request()->routeIs('admin.loans') ? 'text-white' : 'text-slate-400' }}"></i>
                                <span>Loans Directory</span>
                            </a>
                            @endif

                            @if($canLoanApprovals)
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
                            @endif

                            @if($canLoansMgmt)
                            <a href="{{ route('admin.loans.management') }}"
                                class="flex items-center gap-3 px-3 py-2 text-xs font-semibold rounded-xl transition-all duration-200 {{ request()->routeIs('admin.loans.management') ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/20 font-bold' : 'text-slate-600 dark:text-slate-400 hover:bg-emerald-500/10 dark:hover:bg-slate-700/60 hover:text-emerald-700 dark:hover:text-emerald-300' }}">
                                <i class="fa-solid fa-sliders w-5 text-center flex-shrink-0 text-sm {{ request()->routeIs('admin.loans.management') ? 'text-white' : 'text-slate-400' }}"></i>
                                <span>Loans Management</span>
                            </a>
                            @endif
                        </div>
                    </div>
                    @endif

                    @if($canTreasuryGroup)
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
                            @if($canWithdrawals)
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
                            @endif

                            @if($canDeductions)
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
                            @endif
                        </div>
                    </div>
                    @endif

                    @if($canElections)
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
                    @endif

                    @if($canAuditLogs || ($authUser && $authUser->role === 'super_admin'))
                    <!-- Group: Security -->
                    <div class="space-y-1">
                        <p class="text-[10px] font-extrabold uppercase tracking-widest text-slate-400 dark:text-slate-500 px-2.5 pt-2 pb-1 select-none">
                            System Security
                        </p>
                        @if($authUser && $authUser->role === 'super_admin')
                        <a href="{{ route('admin.administrators') }}"
                            class="group w-full flex items-center gap-3 px-3 py-2.5 text-xs font-semibold rounded-xl transition-all duration-200 text-left {{ request()->routeIs('admin.administrators*') ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/20 font-bold' : 'text-slate-600 dark:text-slate-400 hover:bg-emerald-500/10 dark:hover:bg-slate-700/60 hover:text-emerald-700 dark:hover:text-emerald-300' }}">
                            <i class="fa-solid fa-user-shield w-5 text-center flex-shrink-0 text-sm {{ request()->routeIs('admin.administrators*') ? 'text-white' : 'text-slate-400 group-hover:text-emerald-600 dark:text-slate-400 dark:group-hover:text-emerald-400' }}"></i>
                            <span>Administrators</span>
                        </a>
                        @endif

                        @if($canAuditLogs)
                        <a href="{{ route('admin.audit-logs') }}"
                            class="group w-full flex items-center gap-3 px-3 py-2.5 text-xs font-semibold rounded-xl transition-all duration-200 text-left {{ request()->routeIs('admin.audit-logs') ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/20 font-bold' : 'text-slate-600 dark:text-slate-400 hover:bg-emerald-500/10 dark:hover:bg-slate-700/60 hover:text-emerald-700 dark:hover:text-emerald-300' }}">
                            <i class="fa-solid fa-shield-halved w-5 text-center flex-shrink-0 text-sm {{ request()->routeIs('admin.audit-logs') ? 'text-white' : 'text-slate-400 group-hover:text-emerald-600 dark:text-slate-400 dark:group-hover:text-emerald-400' }}"></i>
                            <span>Audit &amp; Security Logs</span>
                        </a>
                        @endif
                    </div>
                    @endif
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

                    <!-- Page Title & Subtitle in Topbar Header -->
                    <div class="flex items-center gap-3 border-l border-slate-200/80 dark:border-slate-700/80 pl-3 min-w-0">
                        <div class="min-w-0">
                            @hasSection('page_title')
                                <h1 class="text-sm sm:text-base font-extrabold text-slate-900 dark:text-white tracking-tight truncate leading-tight">
                                    @yield('page_title')
                                </h1>
                                @hasSection('page_subtitle')
                                    <p class="text-[11px] text-slate-400 dark:text-slate-500 font-medium truncate hidden md:block max-w-md lg:max-w-xl">
                                        @yield('page_subtitle')
                                    </p>
                                @endif
                            @else
                                <h1 class="text-sm sm:text-base font-extrabold text-slate-900 dark:text-white tracking-tight truncate leading-tight">
                                    {{ explode(' - ', trim($__env->yieldContent('title', 'Admin Console')))[0] }}
                                </h1>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Right Toolbar -->
                <div class="flex items-center gap-2 sm:gap-3 flex-shrink-0">
                    <!-- Page Header Actions Slot -->
                    @yield('header_actions')

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
                                <!-- My Profile & E-Signature Trigger -->
                                <button type="button" id="btn-open-admin-profile"
                                    class="w-full text-left group flex items-center gap-3 px-3.5 py-2 rounded-xl text-xs font-semibold text-slate-700 transition-colors hover:bg-slate-50 dark:text-slate-200 dark:hover:bg-slate-700/50 cursor-pointer">
                                    <i class="fa-solid fa-file-signature fa-fw text-sm text-slate-400 transition-colors group-hover:text-slate-600 dark:text-slate-400 dark:group-hover:text-slate-200"></i>
                                    <span>My E-Signature &amp; Profile</span>
                                </button>

                                @if(Auth::check() && Auth::user()->role === 'super_admin')
                                    <!-- Switch to Member Portal (Super Admin Testing Only) -->
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
                <!-- Backward-compatible Page Title Header (only if page still defines @section('header')) -->
                @hasSection('header')
                    <div class="mb-6">
                        @yield('header')
                    </div>
                @endif

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
                    popup: 'rounded-[2rem] border border-slate-200 dark:border-slate-800 shadow-2xl p-6 sm:p-8 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 font-sans',
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

    @if(Auth::check())
    <!-- MODAL: ADMIN PROFILE & E-SIGNATURE -->
    <div id="modal-admin-profile" class="fixed inset-0 z-50 hidden">
        <div class="fixed inset-0 bg-slate-950/40 dark:bg-slate-950/60 backdrop-blur-md opacity-0 transition-opacity duration-300 pointer-events-none modal-overlay"></div>
        
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-2xl overflow-hidden h-[calc(100vh-2rem)] w-[calc(100vw-2rem)] sm:w-full sm:max-w-lg fixed right-4 top-4 bottom-4 z-50 transform translate-x-[calc(100%+2rem)] transition-transform duration-300 modal-container p-5 sm:p-6 flex flex-col">
            <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                <div>
                    <h3 class="text-base font-bold text-slate-900 dark:text-slate-100 flex items-center gap-2">
                        <i class="fa-solid fa-file-signature text-emerald-600 text-sm"></i>
                        <span>My Official E-Signature &amp; Profile</span>
                    </h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Manage your administrative credential and sign-off signature</p>
                </div>
                <button type="button" class="admin-profile-modal-close p-1.5 rounded-xl text-slate-400 hover:text-slate-700 dark:text-slate-500 dark:hover:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors cursor-pointer">
                    <i class="fa-solid fa-xmark text-base"></i>
                </button>
            </div>

            <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data" class="flex-1 flex flex-col overflow-hidden mt-4">
                @csrf
                
                <div class="flex-1 overflow-y-auto pr-1 space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div class="space-y-1">
                            <label class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider block">Operator Name</label>
                            <input type="text" name="name" value="{{ Auth::user()->name }}" required class="w-full px-3 py-2 text-xs font-semibold border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 rounded-xl outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500/20 transition-all">
                        </div>
                        <div class="space-y-1">
                            <label class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider block">Login Identifier</label>
                            <input type="text" value="{{ Auth::user()->company_id }}" disabled class="w-full px-3 py-2 text-xs font-mono font-bold border border-slate-200 dark:border-slate-700 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 rounded-xl cursor-not-allowed">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div class="space-y-1">
                            <label class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider block">Notification Email</label>
                            <input type="email" name="email" value="{{ Auth::user()->email }}" placeholder="staff@coop.internal" class="w-full px-3 py-2 text-xs font-semibold border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 rounded-xl outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500/20 transition-all">
                        </div>
                        <div class="space-y-1">
                            <label class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider flex items-center justify-between">
                                <span>Change Password</span>
                                <span class="text-[9px] text-slate-400 normal-case">(Leave blank to keep)</span>
                            </label>
                            <input type="password" name="password" placeholder="••••••••" class="w-full px-3 py-2 text-xs font-semibold border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 rounded-xl outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500/20 transition-all">
                        </div>
                    </div>

                    <!-- E-Signature Section -->
                    <div class="space-y-2 border-t border-slate-100 dark:border-slate-800 pt-3">
                        <div class="flex items-center justify-between">
                            <label class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider block">Registered Official E-Signature</label>
                            <span class="text-[9px] text-slate-400 font-semibold">(Recorded in approvals &amp; PDF contracts)</span>
                        </div>

                        <!-- Current E-Sign preview -->
                        <div class="p-3 bg-slate-50 dark:bg-slate-950/40 border border-slate-200/60 dark:border-slate-800 rounded-xl flex items-center justify-between">
                            @if(Auth::user()->signature_url)
                                <div class="flex items-center gap-3">
                                    <img id="my-profile-signature-img" src="{{ Auth::user()->signature_url }}" alt="My E-Sign" class="max-h-16 w-auto object-contain bg-white dark:bg-slate-900 p-2 rounded-lg border border-slate-200 dark:border-slate-700">
                                    <span class="text-[10px] text-slate-400 font-medium">Currently active signature</span>
                                </div>
                                <button type="button" id="btn-remove-my-profile-sig" class="px-2.5 py-1 rounded-lg text-[10px] font-bold text-rose-600 dark:text-rose-400 bg-rose-50 dark:bg-rose-950/40 hover:bg-rose-100 border border-rose-200 dark:border-rose-800 transition-colors flex items-center gap-1 cursor-pointer">
                                    <i class="fa-solid fa-trash-can text-[9px]"></i>
                                    <span>Clear</span>
                                </button>
                            @else
                                <div class="text-center py-2 w-full">
                                    <i class="fa-solid fa-signature text-slate-300 dark:text-slate-600 text-2xl mb-1 block"></i>
                                    <span class="text-[11px] text-slate-400 dark:text-slate-500 font-semibold">No signature currently registered on file.</span>
                                </div>
                            @endif
                        </div>
                        <input type="hidden" name="remove_signature" id="my-profile-remove-signature-flag" value="0">

                        <div class="space-y-1">
                            <label class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider block">Upload New Signature</label>
                            <input type="file" name="signature" accept="image/png, image/jpeg, image/jpg, image/svg+xml" class="w-full px-3 py-1.5 text-xs font-semibold border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 rounded-xl outline-none focus:border-emerald-500 file:mr-3 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-[10px] file:font-bold file:bg-emerald-50 file:text-emerald-700 dark:file:bg-emerald-950/40 dark:file:text-emerald-300 hover:file:bg-emerald-100 cursor-pointer">
                        </div>

                        <p class="text-[10px] text-slate-400 dark:text-slate-500 leading-tight">
                            Recommended: transparent PNG or SVG image. You can draw and save a free transparent signature at 
                            <a href="https://www.signwell.com/online-signature/" target="_blank" class="text-emerald-600 dark:text-emerald-400 font-bold hover:underline">SignWell</a>.
                        </p>
                    </div>
                </div>

                <div class="pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-end gap-2 mt-3">
                    <button type="button" class="admin-profile-modal-close px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 text-xs font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors cursor-pointer">Cancel</button>
                    <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs px-4 py-2 rounded-xl shadow-xs shadow-emerald-600/10 transition-all cursor-pointer">Save Profile &amp; Signature</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const profileModal = document.getElementById("modal-admin-profile");
            const btnOpenProfile = document.getElementById("btn-open-admin-profile");
            
            function openProfileModal() {
                if (!profileModal) return;
                const overlay = profileModal.querySelector(".modal-overlay");
                const container = profileModal.querySelector(".modal-container");
                profileModal.classList.remove("hidden");
                setTimeout(() => {
                    if (overlay) {
                        overlay.classList.remove("opacity-0", "pointer-events-none");
                        overlay.classList.add("opacity-100", "pointer-events-auto");
                    }
                    if (container) {
                        container.classList.remove("translate-x-[calc(100%+2rem)]");
                        container.classList.add("translate-x-0");
                    }
                }, 30);
            }

            function closeProfileModal() {
                if (!profileModal) return;
                const overlay = profileModal.querySelector(".modal-overlay");
                const container = profileModal.querySelector(".modal-container");
                if (overlay) {
                    overlay.classList.add("opacity-0", "pointer-events-none");
                    overlay.classList.remove("opacity-100", "pointer-events-auto");
                }
                if (container) {
                    container.classList.add("translate-x-[calc(100%+2rem)]");
                    container.classList.remove("translate-x-0");
                }
                setTimeout(() => profileModal.classList.add("hidden"), 250);
            }

            if (btnOpenProfile) {
                btnOpenProfile.addEventListener("click", function(e) {
                    e.stopPropagation();
                    const dropdown = document.getElementById("profile-dropdown-menu");
                    if (dropdown) dropdown.classList.add("hidden");
                    openProfileModal();
                });
            }

            if (profileModal) {
                profileModal.querySelectorAll(".admin-profile-modal-close, .modal-overlay").forEach(btn => {
                    btn.addEventListener("click", closeProfileModal);
                });

                const btnRemoveMySig = document.getElementById("btn-remove-my-profile-sig");
                if (btnRemoveMySig) {
                    btnRemoveMySig.addEventListener("click", function() {
                        const removeFlag = document.getElementById("my-profile-remove-signature-flag");
                        const sigImg = document.getElementById("my-profile-signature-img");
                        if (removeFlag) removeFlag.value = "1";
                        if (sigImg) {
                            const parentFrame = sigImg.closest('.p-3');
                            if (parentFrame) {
                                parentFrame.innerHTML = '<div class="text-center py-2 w-full"><i class="fa-solid fa-signature text-slate-300 dark:text-slate-600 text-2xl mb-1 block"></i><span class="text-[11px] text-amber-600 dark:text-amber-400 font-bold uppercase tracking-wider">Signature marked for removal upon saving</span></div>';
                            }
                        }
                    });
                }
            }
        });
    </script>
    @endif
    @stack('scripts')
</body>
</html>