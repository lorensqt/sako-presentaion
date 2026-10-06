<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50 dark:bg-slate-900">

<head>
    <!-- Dark Mode Init -->
    <script>
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia(
                '(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Member Portal - ML Sako')</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('img/sako-logo-nobg.png') }}">

    @include('partials.pwa-meta')

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Styles / Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @php
        $pendingComakerCount = 0;
        $activeUnvotedElectionsCount = 0;
        $rejectedComakersLoansCount = 0;
        if (auth()->check()) {
            $userId = auth()->id();
            $allPendingComakersLoans = \App\Models\LoanApplication::where('status', 'pending')
                ->where('current_stage', 'comakers')
                ->get();

            $pendingComakerCount = $allPendingComakersLoans
                ->filter(function ($app) use ($userId) {
                    $comakers = $app->form_data['comakers'] ?? [];
                    $isComaker = in_array($userId, $comakers) || in_array((string) $userId, $comakers);

                    if (!$isComaker) {
                        return false;
                    }

                    $hasActioned = $app
                        ->approvals()
                        ->where('stage_role_slug', 'comakers')
                        ->where('actioned_by_user_id', $userId)
                        ->exists();

                    return !$hasActioned;
                })
                ->count();

            // Calculate active unvoted elections
            $now = \Carbon\Carbon::now();
            $activeUnvotedElectionsCount = \App\Models\Election::where('start_time', '<=', $now)
                ->where('end_time', '>=', $now)
                ->get()
                ->filter(function($election) use ($userId) {
                    $hasVoted = \App\Models\Vote::where('election_id', $election->id)
                        ->where('user_id', $userId)
                        ->exists();
                    return !$hasVoted;
                })
                ->count();

            // Calculate active loans with rejected co-makers needing replacement
            $rejectedComakersLoansCount = \App\Models\LoanApplication::where('user_id', $userId)
                ->where('status', 'pending')
                ->where('current_stage', 'comakers')
                ->whereHas('comakers', function($q) {
                    $q->where('status', 'rejected');
                })
                ->count();
        }
    @endphp

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        @media print {
            .no-print {
                display: none !important;
            }

            body {
                background: white !important;
                color: black !important;
            }

            .print-container {
                display: block !important;
                width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
            }
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

        #sidebar.collapsed nav a,
        #sidebar.collapsed nav button {
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
            background: #1e293b;
            /* slate-800 */
            color: #f8fafc;
            /* slate-50 */
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
            border: 1px solid #334155;
            /* slate-700 */
        }

        #sidebar.collapsed nav a:hover .sidebar-tooltip,
        #sidebar.collapsed nav button:hover .sidebar-tooltip,
        #sidebar.collapsed .p-4 button:hover .sidebar-tooltip {
            opacity: 1;
            transform: translateX(0.25rem);
        }
    </style>
    @stack('styles')
</head>

<body class="h-full bg-slate-50 dark:bg-slate-900 text-slate-800 dark:text-slate-100 antialiased overflow-hidden">
    @include('components.pin-security-overlay')

    <div class="flex h-full overflow-hidden">

        <!-- Sidebar for Desktop -->
        <aside id="sidebar"
            class="hidden md:flex md:flex-col md:w-64 bg-slate-100 dark:bg-slate-800 border-r border-slate-200 dark:border-slate-700 flex-shrink-0 transition-all duration-300 no-print overflow-x-hidden">
            <script>
                if (localStorage.getItem("sidebar-collapsed") === "true") {
                    document.getElementById("sidebar").classList.add("collapsed");
                }
            </script>
            <!-- Sidebar Header / Branding -->
            <div class="h-16 flex items-center px-6 border-b border-slate-200/80 dark:border-slate-700/80">
                <a href="/" class="flex items-center gap-2.5 group">
                    <img src="{{ asset('img/sako-logo-nobg.png') }}" alt="ML Sako Logo"
                        class="h-9 w-auto object-contain transition-transform duration-200 group-hover:scale-105 flex-shrink-0">
                    <span
                        class="text-base font-black tracking-widest text-slate-900 dark:text-white uppercase sidebar-text">
                        ML<span class="text-emerald-600 font-black">Sako</span>
                    </span>
                </a>
            </div>

            {{-- <!-- Member Summary Mini-Card -->
            <div class="p-4 border-b border-slate-200/60 dark:border-slate-700/60">
                <div class="bg-white/60 dark:bg-slate-800/60 rounded-xl p-4 border border-slate-200/80 dark:border-slate-700/80 flex flex-col shadow-sm transition-all duration-200">
                    <p class="text-[10px] text-slate-400 dark:text-slate-500 font-semibold uppercase tracking-wider sidebar-text">Welcome back,</p>
                    <p class="text-sm text-slate-800 dark:text-slate-200 font-bold truncate max-w-[180px] mt-0.5 sidebar-text">{{ Auth::check() ? Auth::user()->name : 'Member' }}</p>
                </div>
            </div> --}}

            <!-- Sidebar Navigation -->
            <nav class="flex-1 px-3 py-4 space-y-4 overflow-y-auto overflow-x-hidden sidebar-nav">
                
                <!-- GROUP 1: ACCOUNTS & SAVINGS -->
                <div class="space-y-1">
                    <p class="text-[10px] font-extrabold uppercase tracking-widest text-slate-400 dark:text-slate-500 sidebar-text px-3 pb-1 select-none">
                        Accounts &amp; Savings
                    </p>

                    <!-- My Savings -->
                    <a href="{{ route('member.savings') }}"
                        class="group relative w-full flex items-center gap-3 px-3 py-2 text-xs font-semibold rounded-xl transition-all duration-200 text-left {{ request()->routeIs('member.savings') ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/20 font-bold' : 'text-slate-600 dark:text-slate-400 hover:bg-emerald-500/10 dark:hover:bg-slate-700/60 hover:text-emerald-700 dark:hover:text-emerald-300' }}">
                        <i class="fa-solid fa-wallet w-5 text-center flex-shrink-0 text-sm transition-transform duration-200 group-hover:scale-110 group-hover:translate-x-0.5 {{ request()->routeIs('member.savings') ? 'text-white' : 'text-slate-400 group-hover:text-emerald-600 dark:text-slate-400 dark:group-hover:text-emerald-400' }}"></i>
                        <span class="sidebar-text flex-1 truncate">My Savings</span>
                        <span class="sidebar-tooltip hidden lg:block">My Savings</span>
                    </a>

                    <!-- Payroll Deduction -->
                    <a href="{{ route('member.deductions') }}"
                        class="group relative w-full flex items-center gap-3 px-3 py-2 text-xs font-semibold rounded-xl transition-all duration-200 text-left {{ request()->routeIs('member.deductions') ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/20 font-bold' : 'text-slate-600 dark:text-slate-400 hover:bg-emerald-500/10 dark:hover:bg-slate-700/60 hover:text-emerald-700 dark:hover:text-emerald-300' }}">
                        <i class="fa-solid fa-sliders w-5 text-center flex-shrink-0 text-sm transition-transform duration-200 group-hover:scale-110 group-hover:translate-x-0.5 {{ request()->routeIs('member.deductions') ? 'text-white' : 'text-slate-400 group-hover:text-emerald-600 dark:text-slate-400 dark:group-hover:text-emerald-400' }}"></i>
                        <span class="sidebar-text flex-1 truncate">Payroll Deduction</span>
                        <span class="sidebar-tooltip hidden lg:block">Payroll Deduction</span>
                    </a>
                </div>

                <!-- GROUP 2: FINANCING & CREDIT -->
                <div class="space-y-1">
                    <p class="text-[10px] font-extrabold uppercase tracking-widest text-slate-400 dark:text-slate-500 sidebar-text px-3 pt-2 pb-1 select-none">
                        Credit &amp; Loans
                    </p>

                    <!-- My Loans -->
                    <a href="{{ route('member.loans') }}"
                        class="group relative w-full flex items-center gap-3 px-3 py-2 text-xs font-semibold rounded-xl transition-all duration-200 text-left {{ request()->routeIs('member.loans') ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/20 font-bold' : 'text-slate-600 dark:text-slate-400 hover:bg-emerald-500/10 dark:hover:bg-slate-700/60 hover:text-emerald-700 dark:hover:text-emerald-300' }}">
                        <i class="fa-solid fa-receipt w-5 text-center flex-shrink-0 text-sm transition-transform duration-200 group-hover:scale-110 group-hover:translate-x-0.5 {{ request()->routeIs('member.loans') ? 'text-white' : 'text-slate-400 group-hover:text-emerald-600 dark:text-slate-400 dark:group-hover:text-emerald-400' }}"></i>
                        <span class="sidebar-text flex-1 truncate">My Loans</span>
                        @if ($rejectedComakersLoansCount > 0)
                            <span class="sidebar-text bg-amber-500 text-white font-extrabold text-[10px] px-1.5 py-0.5 rounded-full animate-bounce shadow-xs">{{ $rejectedComakersLoansCount }}</span>
                        @endif
                        <span class="sidebar-tooltip hidden lg:block">My Loans</span>
                    </a>

                    <!-- Apply for Loan -->
                    <a href="{{ route('member.forms') }}"
                        class="group relative w-full flex items-center gap-3 px-3 py-2 text-xs font-semibold rounded-xl transition-all duration-200 text-left {{ request()->routeIs('member.forms') ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/20 font-bold' : 'text-slate-600 dark:text-slate-400 hover:bg-emerald-500/10 dark:hover:bg-slate-700/60 hover:text-emerald-700 dark:hover:text-emerald-300' }}">
                        <i class="fa-solid fa-circle-plus w-5 text-center flex-shrink-0 text-sm transition-transform duration-200 group-hover:scale-110 group-hover:translate-x-0.5 {{ request()->routeIs('member.forms') ? 'text-white' : 'text-slate-400 group-hover:text-emerald-600 dark:text-slate-400 dark:group-hover:text-emerald-400' }}"></i>
                        <span class="sidebar-text flex-1 truncate">Apply for Loan</span>
                        <span class="sidebar-tooltip hidden lg:block">Apply for Loan</span>
                    </a>

                    <!-- Co-Maker Requests -->
                    <a href="{{ route('member.comaker_requests') }}"
                        class="group relative w-full flex items-center gap-3 px-3 py-2 text-xs font-semibold rounded-xl transition-all duration-200 text-left {{ request()->routeIs('member.comaker_requests') ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/20 font-bold' : 'text-slate-600 dark:text-slate-400 hover:bg-emerald-500/10 dark:hover:bg-slate-700/60 hover:text-emerald-700 dark:hover:text-emerald-300' }}">
                        <i class="fa-solid fa-signature w-5 text-center flex-shrink-0 text-sm transition-transform duration-200 group-hover:scale-110 group-hover:translate-x-0.5 {{ request()->routeIs('member.comaker_requests') ? 'text-white' : 'text-slate-400 group-hover:text-emerald-600 dark:text-slate-400 dark:group-hover:text-emerald-400' }}"></i>
                        <span class="sidebar-text flex-1 truncate">Co-Maker Requests</span>
                        @if ($pendingComakerCount > 0)
                            <span class="sidebar-text bg-rose-500 text-white font-extrabold text-[10px] px-1.5 py-0.5 rounded-full animate-pulse shadow-xs">{{ $pendingComakerCount }}</span>
                        @endif
                        <span class="sidebar-tooltip hidden lg:block">Co-Maker Requests</span>
                    </a>

                    <!-- My Withdrawals -->
                    <a href="{{ route('member.withdrawals') }}"
                        class="group relative w-full flex items-center gap-3 px-3 py-2 text-xs font-semibold rounded-xl transition-all duration-200 text-left {{ request()->routeIs('member.withdrawals') ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/20 font-bold' : 'text-slate-600 dark:text-slate-400 hover:bg-emerald-500/10 dark:hover:bg-slate-700/60 hover:text-emerald-700 dark:hover:text-emerald-300' }}">
                        <i class="fa-solid fa-arrow-up-from-bracket w-5 text-center flex-shrink-0 text-sm transition-transform duration-200 group-hover:scale-110 group-hover:translate-x-0.5 {{ request()->routeIs('member.withdrawals') ? 'text-white' : 'text-slate-400 group-hover:text-emerald-600 dark:text-slate-400 dark:group-hover:text-emerald-400' }}"></i>
                        <span class="sidebar-text flex-1 truncate">My Withdrawals</span>
                        <span class="sidebar-tooltip hidden lg:block">My Withdrawals</span>
                    </a>
                </div>

                <!-- GROUP 3: GOVERNANCE -->
                <div class="space-y-1">
                    <p class="text-[10px] font-extrabold uppercase tracking-widest text-slate-400 dark:text-slate-500 sidebar-text px-3 pt-2 pb-1 select-none">
                        Cooperative Governance
                    </p>

                    <!-- Elections -->
                    <a href="{{ route('member.elections.index') }}"
                        class="group relative w-full flex items-center gap-3 px-3 py-2 text-xs font-semibold rounded-xl transition-all duration-200 text-left {{ request()->routeIs('member.elections.*') ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/20 font-bold' : 'text-slate-600 dark:text-slate-400 hover:bg-emerald-500/10 dark:hover:bg-slate-700/60 hover:text-emerald-700 dark:hover:text-emerald-300' }}">
                        <i class="fa-solid fa-check-to-slot w-5 text-center flex-shrink-0 text-sm transition-transform duration-200 group-hover:scale-110 group-hover:translate-x-0.5 {{ request()->routeIs('member.elections.*') ? 'text-white' : 'text-slate-400 group-hover:text-emerald-600 dark:text-slate-400 dark:group-hover:text-emerald-400' }}"></i>
                        <span class="sidebar-text flex-1 truncate">Elections &amp; Voting</span>
                        @if ($activeUnvotedElectionsCount > 0)
                            <span class="sidebar-text bg-rose-500 dark:bg-rose-600 text-white font-extrabold text-[9px] px-2 py-0.5 rounded-full shadow-xs animate-pulse">Vote Now!</span>
                        @endif
                        <span class="sidebar-tooltip hidden lg:block">Elections &amp; Voting</span>
                    </a>
                </div>
            </nav>

            <!-- Sidebar Footer -->
            <div class="hidden">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit"
                        class="relative w-full flex items-center gap-2.5 px-2.5 py-2 text-[13px] font-medium text-rose-600 rounded-xl hover:bg-rose-50 hover:text-rose-700 transition-all duration-200 text-left focus:outline-none">
                        <i class="fa-solid fa-right-from-bracket w-5 text-center"></i>
                        <span class="sidebar-text">Sign Out</span>
                        <span class="sidebar-tooltip hidden lg:block">Sign Out</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Mobile Sidebar / Off-canvas Menu Overlay -->
        <div id="mobile-sidebar" class="fixed inset-0 z-40 hidden md:hidden no-print">
            <div id="mobile-sidebar-backdrop"
                class="fixed inset-0 bg-slate-900/40 dark:bg-slate-950/60 backdrop-blur-sm"></div>
            <nav id="mobile-sidebar-panel"
                class="fixed top-0 bottom-0 left-0 w-64 bg-slate-100 dark:bg-slate-800 border-r border-slate-200 dark:border-slate-700 flex flex-col z-50 transform -translate-x-full transition-transform duration-300 ease-in-out">
                <div
                    class="h-16 flex items-center justify-between px-6 border-b border-slate-200/80 dark:border-slate-700/80">
                    <a href="/" class="flex items-center gap-2.5 group">
                        <img src="{{ asset('img/sako-logo-nobg.png') }}" alt="ML Sako Logo"
                            class="h-9 w-auto object-contain">
                        <span class="text-base font-black tracking-widest text-slate-900 dark:text-white uppercase">
                            ML<span class="text-emerald-600 font-black">Sako</span>
                        </span>
                    </a>
                    <button id="mobile-sidebar-close"
                        class="p-1.5 rounded-lg text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-slate-100 hover:bg-slate-200/60 dark:hover:bg-slate-700/50 transition-all">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>

                <div class="flex-1 px-3 py-4 space-y-4 overflow-y-auto sidebar-nav">
                    
                    <!-- GROUP 1: ACCOUNTS & SAVINGS -->
                    <div class="space-y-1">
                        <p class="text-[10px] font-extrabold uppercase tracking-widest text-slate-400 dark:text-slate-500 px-3 pb-1 select-none">
                            Accounts &amp; Savings
                        </p>

                        <!-- My Savings -->
                        <a href="{{ route('member.savings') }}"
                            class="group w-full flex items-center gap-3 px-3 py-2.5 text-xs font-semibold rounded-xl transition-all duration-200 text-left {{ request()->routeIs('member.savings') ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/20 font-bold' : 'text-slate-600 dark:text-slate-400 hover:bg-emerald-500/10 dark:hover:bg-slate-700/60 hover:text-emerald-700 dark:hover:text-emerald-300' }}">
                            <i class="fa-solid fa-wallet w-5 text-center flex-shrink-0 text-sm transition-transform duration-200 group-hover:scale-110 {{ request()->routeIs('member.savings') ? 'text-white' : 'text-slate-400 group-hover:text-emerald-600 dark:text-slate-400 dark:group-hover:text-emerald-400' }}"></i>
                            <span class="flex-1 truncate">My Savings</span>
                        </a>

                        <!-- Payroll Deduction -->
                        <a href="{{ route('member.deductions') }}"
                            class="group w-full flex items-center gap-3 px-3 py-2.5 text-xs font-semibold rounded-xl transition-all duration-200 text-left {{ request()->routeIs('member.deductions') ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/20 font-bold' : 'text-slate-600 dark:text-slate-400 hover:bg-emerald-500/10 dark:hover:bg-slate-700/60 hover:text-emerald-700 dark:hover:text-emerald-300' }}">
                            <i class="fa-solid fa-sliders w-5 text-center flex-shrink-0 text-sm transition-transform duration-200 group-hover:scale-110 {{ request()->routeIs('member.deductions') ? 'text-white' : 'text-slate-400 group-hover:text-emerald-600 dark:text-slate-400 dark:group-hover:text-emerald-400' }}"></i>
                            <span class="flex-1 truncate">Payroll Deduction</span>
                        </a>
                    </div>

                    <!-- GROUP 2: CREDIT & LOANS -->
                    <div class="space-y-1">
                        <p class="text-[10px] font-extrabold uppercase tracking-widest text-slate-400 dark:text-slate-500 px-3 pt-2 pb-1 select-none">
                            Credit &amp; Loans
                        </p>

                        <!-- My Loans -->
                        <a href="{{ route('member.loans') }}"
                            class="group w-full flex items-center gap-3 px-3 py-2.5 text-xs font-semibold rounded-xl transition-all duration-200 text-left {{ request()->routeIs('member.loans') ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/20 font-bold' : 'text-slate-600 dark:text-slate-400 hover:bg-emerald-500/10 dark:hover:bg-slate-700/60 hover:text-emerald-700 dark:hover:text-emerald-300' }}">
                            <i class="fa-solid fa-receipt w-5 text-center flex-shrink-0 text-sm transition-transform duration-200 group-hover:scale-110 {{ request()->routeIs('member.loans') ? 'text-white' : 'text-slate-400 group-hover:text-emerald-600 dark:text-slate-400 dark:group-hover:text-emerald-400' }}"></i>
                            <span class="flex-1 truncate">My Loans</span>
                            @if ($rejectedComakersLoansCount > 0)
                                <span class="bg-amber-500 text-white font-extrabold text-[10px] px-1.5 py-0.5 rounded-full animate-bounce shadow-xs">{{ $rejectedComakersLoansCount }}</span>
                            @endif
                        </a>

                        <!-- Apply for Loan -->
                        <a href="{{ route('member.forms') }}"
                            class="group w-full flex items-center gap-3 px-3 py-2.5 text-xs font-semibold rounded-xl transition-all duration-200 text-left {{ request()->routeIs('member.forms') ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/20 font-bold' : 'text-slate-600 dark:text-slate-400 hover:bg-emerald-500/10 dark:hover:bg-slate-700/60 hover:text-emerald-700 dark:hover:text-emerald-300' }}">
                            <i class="fa-solid fa-circle-plus w-5 text-center flex-shrink-0 text-sm transition-transform duration-200 group-hover:scale-110 {{ request()->routeIs('member.forms') ? 'text-white' : 'text-slate-400 group-hover:text-emerald-600 dark:text-slate-400 dark:group-hover:text-emerald-400' }}"></i>
                            <span class="flex-1 truncate">Apply for Loan</span>
                        </a>

                        <!-- Co-Maker Requests -->
                        <a href="{{ route('member.comaker_requests') }}"
                            class="group w-full flex items-center gap-3 px-3 py-2.5 text-xs font-semibold rounded-xl transition-all duration-200 text-left {{ request()->routeIs('member.comaker_requests') ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/20 font-bold' : 'text-slate-600 dark:text-slate-400 hover:bg-emerald-500/10 dark:hover:bg-slate-700/60 hover:text-emerald-700 dark:hover:text-emerald-300' }}">
                            <i class="fa-solid fa-signature w-5 text-center flex-shrink-0 text-sm transition-transform duration-200 group-hover:scale-110 {{ request()->routeIs('member.comaker_requests') ? 'text-white' : 'text-slate-400 group-hover:text-emerald-600 dark:text-slate-400 dark:group-hover:text-emerald-400' }}"></i>
                            <span class="flex-1 truncate">Co-Maker Requests</span>
                            @if ($pendingComakerCount > 0)
                                <span class="bg-rose-500 text-white font-extrabold text-[10px] px-1.5 py-0.5 rounded-full animate-pulse shadow-xs">{{ $pendingComakerCount }}</span>
                            @endif
                        </a>

                        <!-- My Withdrawals -->
                        <a href="{{ route('member.withdrawals') }}"
                            class="group w-full flex items-center gap-3 px-3 py-2.5 text-xs font-semibold rounded-xl transition-all duration-200 text-left {{ request()->routeIs('member.withdrawals') ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/20 font-bold' : 'text-slate-600 dark:text-slate-400 hover:bg-emerald-500/10 dark:hover:bg-slate-700/60 hover:text-emerald-700 dark:hover:text-emerald-300' }}">
                            <i class="fa-solid fa-arrow-up-from-bracket w-5 text-center flex-shrink-0 text-sm transition-transform duration-200 group-hover:scale-110 {{ request()->routeIs('member.withdrawals') ? 'text-white' : 'text-slate-400 group-hover:text-emerald-600 dark:text-slate-400 dark:group-hover:text-emerald-400' }}"></i>
                            <span class="flex-1 truncate">My Withdrawals</span>
                        </a>
                    </div>

                    <!-- GROUP 3: GOVERNANCE -->
                    <div class="space-y-1">
                        <p class="text-[10px] font-extrabold uppercase tracking-widest text-slate-400 dark:text-slate-500 px-3 pt-2 pb-1 select-none">
                            Cooperative Governance
                        </p>

                        <!-- Elections -->
                        <a href="{{ route('member.elections.index') }}"
                            class="group w-full flex items-center gap-3 px-3 py-2.5 text-xs font-semibold rounded-xl transition-all duration-200 text-left {{ request()->routeIs('member.elections.*') ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/20 font-bold' : 'text-slate-600 dark:text-slate-400 hover:bg-emerald-500/10 dark:hover:bg-slate-700/60 hover:text-emerald-700 dark:hover:text-emerald-300' }}">
                            <i class="fa-solid fa-check-to-slot w-5 text-center flex-shrink-0 text-sm transition-transform duration-200 group-hover:scale-110 {{ request()->routeIs('member.elections.*') ? 'text-white' : 'text-slate-400 group-hover:text-emerald-600 dark:text-slate-400 dark:group-hover:text-emerald-400' }}"></i>
                            <span class="flex-1 truncate">Elections &amp; Voting</span>
                            @if ($activeUnvotedElectionsCount > 0)
                                <span class="bg-rose-500 dark:bg-rose-600 text-white font-extrabold text-[9px] px-2 py-0.5 rounded-full shadow-xs animate-pulse">Vote Now!</span>
                            @endif
                        </a>
                    </div>

                </div>
            </nav>
        </div>

        <!-- Main Window Container -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Topbar Header -->
            <header
                class="h-16 bg-white dark:bg-slate-800 border-b border-slate-100 dark:border-slate-700 flex items-center justify-between gap-2 sm:gap-4 px-3 sm:px-6 flex-shrink-0 z-10 no-print">
                <div class="flex items-center gap-2 sm:gap-3 min-w-0 flex-1 sm:flex-initial">
                    <!-- Hamburger button for mobile -->
                    <button id="mobile-sidebar-toggle"
                        class="p-1.5 rounded-lg text-slate-500 dark:text-slate-400 hover:text-emerald-600 hover:bg-slate-50 dark:hover:bg-slate-700 md:hidden transition-all flex-shrink-0">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>

                    <!-- Sidebar collapse button for desktop -->
                    <button id="desktop-sidebar-toggle"
                        class="hidden md:flex p-1.5 rounded-xl text-slate-500 dark:text-slate-400 hover:text-emerald-600 hover:bg-slate-50 dark:hover:bg-slate-700 transition-all flex-shrink-0"
                        title="Toggle Sidebar">
                        <!-- Modern Panel Collapse Icon -->
                        <svg class="w-5.5 h-5.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 5a1 1 0 011-1h14a1 1 0 011 1v14a1 1 0 01-1 1H5a1 1 0 01-1-1V5z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 4v16" />
                        </svg>
                    </button>

                    @hasSection('navbar_title')
                        <div class="h-5 w-px bg-slate-200 dark:bg-slate-700 hidden sm:block flex-shrink-0"></div>
                        <div class="flex flex-col justify-center min-w-0">
                            <h1 class="text-base sm:text-lg font-bold sm:font-extrabold tracking-tight text-slate-900 dark:text-white leading-tight truncate">
                                @yield('navbar_title')
                            </h1>
                            @hasSection('navbar_subtitle')
                                <p class="text-[11px] sm:text-xs text-slate-500 dark:text-slate-400 font-medium hidden sm:block truncate max-w-xs md:max-w-md lg:max-w-xl">
                                    @yield('navbar_subtitle')
                                </p>
                            @endif
                        </div>
                    @endif
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
                                {{ Auth::check() ? strtoupper(substr(Auth::user()->name, 0, 2)) : 'ME' }}
                            </div>

                            <!-- User Info -->
                            <div class="hidden text-left sm:block">
                                <p
                                    class="text-xs font-bold tracking-tight text-slate-900 dark:text-slate-100 leading-snug">
                                    {{ Auth::check() ? Auth::user()->name : 'Member User' }}
                                </p>
                                <p class="text-[10px] font-semibold text-slate-500 dark:text-slate-400">
                                    {{ Auth::check() && Auth::user()->role === 'super_admin' ? 'Super Admin' : (Auth::check() && Auth::user()->role === 'admin' ? 'Coop Admin' : 'Regular Member') }}
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
                                    {{ Auth::check() ? Auth::user()->name : 'Member User' }}
                                </p>
                                <div class="flex items-center justify-between gap-2 mt-0.5">
                                    <p class="text-[10px] font-semibold text-slate-500 dark:text-slate-400">
                                        {{ Auth::check() && Auth::user()->role === 'super_admin' ? 'Super Admin' : (Auth::check() && Auth::user()->role === 'admin' ? 'Coop Admin' : 'Regular Member') }}
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
                                    <!-- Switch to Admin Console -->
                                    <a href="{{ route('admin.dashboard') }}"
                                        class="group flex items-center gap-3 px-3.5 py-2 rounded-xl text-xs font-semibold text-slate-700 transition-colors hover:bg-slate-50 dark:text-slate-200 dark:hover:bg-slate-700/50">
                                        <i class="fa-solid fa-shield-halved fa-fw text-sm text-slate-400 transition-colors group-hover:text-slate-600 dark:text-slate-400 dark:group-hover:text-slate-200"></i>
                                        <span>Admin Console</span>
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
                @hasSection('header')
                    <div class="mb-8">
                        @yield('header')
                    </div>
                @endif

                <!-- Main Content Slot -->
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Vanilla Javascript for Mobile Sidebar & Tab Swappings -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const toggleBtn = document.getElementById("mobile-sidebar-toggle");
            const closeBtn = document.getElementById("mobile-sidebar-close");
            const backdrop = document.getElementById("mobile-sidebar-backdrop");
            const sidebar = document.getElementById("mobile-sidebar");
            const panel = document.getElementById("mobile-sidebar-panel");

            function openSidebar() {
                sidebar.classList.remove("hidden");
                setTimeout(() => {
                    panel.classList.remove("-translate-x-full");
                }, 10);
            }

            function closeSidebar() {
                panel.classList.add("-translate-x-full");
                setTimeout(() => {
                    sidebar.classList.add("hidden");
                }, 300);
            }

            if (toggleBtn) toggleBtn.addEventListener("click", openSidebar);
            if (closeBtn) closeBtn.addEventListener("click", closeSidebar);
            if (backdrop) backdrop.addEventListener("click", closeSidebar);

            // Collapsible Sidebar handler
            const desktopToggleBtn = document.getElementById("desktop-sidebar-toggle");
            const desktopSidebar = document.getElementById("sidebar");

            if (desktopToggleBtn && desktopSidebar) {
                desktopToggleBtn.addEventListener("click", function() {
                    desktopSidebar.classList.toggle("collapsed");
                    const isCollapsed = desktopSidebar.classList.contains("collapsed");
                    localStorage.setItem("sidebar-collapsed", isCollapsed ? "true" : "false");
                });
            }

            // High-Level SPA Client Router
            const tabTriggers = document.querySelectorAll(".tab-trigger");
            const tabPanes = document.querySelectorAll(".tab-pane");

            function switchTab(tabId) {
                // Hide all panes
                tabPanes.forEach(pane => {
                    pane.classList.add("hidden");
                });

                // Show target pane
                const activePane = document.getElementById(`tab-${tabId}`);
                if (activePane) {
                    activePane.classList.remove("hidden");
                }

                // Update active tab buttons styling
                tabTriggers.forEach(trigger => {
                    const isTarget = trigger.getAttribute("data-tab") === tabId;

                    if (isTarget) {
                        trigger.className =
                            "tab-trigger w-full flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-xl bg-emerald-600 text-white shadow-lg shadow-emerald-600/15 transition-all duration-200 text-left";
                    } else {
                        trigger.className =
                            "tab-trigger w-full flex items-center gap-3 px-3 py-2.5 text-sm font-medium text-slate-600 rounded-xl hover:bg-slate-200/70 hover:text-slate-900 transition-all duration-200 text-left";
                    }
                });

                // Close mobile sidebar if open
                closeSidebar();
            }

            // Bind click events to triggers
            tabTriggers.forEach(trigger => {
                trigger.addEventListener("click", function() {
                    const tabId = this.getAttribute("data-tab");
                    switchTab(tabId);
                });
            });

            // Expose switchTab to global window for cross-tab navigation
            window.switchTab = switchTab;

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

                // Close dropdown only when a link or form submit is clicked inside the menu
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
        });
    </script>

    <!-- SweetAlert2 Integration -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Configure a custom SweetAlert2 instance styled for MLSAKO Cooperative
            const MLSAKOAlert = Swal.mixin({
                customClass: {
                    popup: 'rounded-[2rem] border border-slate-200 dark:border-slate-800 shadow-2xl p-6 sm:p-8 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 font-sans',
                    title: 'text-base font-extrabold text-slate-900 dark:text-white tracking-tight pt-2',
                    htmlContainer: 'text-xs font-semibold text-slate-600 dark:text-slate-400 leading-relaxed mt-2',
                    confirmButton: 'bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold px-6 py-3 rounded-xl transition-all shadow-md focus:ring-2 focus:ring-emerald-500/20 outline-none',
                    cancelButton: 'bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-bold px-6 py-3 rounded-xl transition-all border border-slate-200 dark:border-slate-700 outline-none'
                },
                buttonsStyling: false
            });

            // Expose globally
            window.MLSAKOAlert = MLSAKOAlert;

            // Check and trigger Laravel Session Alerts
            @if(session('success'))
                @if(str_contains(session('success'), 'loan application was successfully submitted'))
                    MLSAKOAlert.fire({
                        title: 'Finalizing Submission...',
                        html: `
                            <div class="flex flex-col items-center justify-center p-4 space-y-4">
                                <div class="relative w-14 h-14 flex items-center justify-center">
                                    <div class="w-14 h-14 rounded-full border-4 border-emerald-500/20 border-t-emerald-500 animate-spin"></div>
                                    <span class="absolute text-lg">📄</span>
                                </div>
                                <div class="space-y-1 text-center">
                                    <p class="text-xs font-bold text-slate-800 dark:text-slate-100">
                                        Synchronizing cooperative loan registry...
                                    </p>
                                    <p class="text-[10px] text-slate-500 dark:text-slate-400 font-medium">
                                        Updating pipeline queues and notifying co-makers.
                                    </p>
                                </div>
                            </div>
                        `,
                        showConfirmButton: false,
                        allowOutsideClick: false,
                        timer: 1300,
                        timerProgressBar: true
                    }).then(() => {
                        MLSAKOAlert.fire({
                            icon: 'success',
                            title: {!! json_encode(session('success_title') ?? 'Success') !!},
                            text: {!! json_encode(session('success')) !!},
                            iconColor: '#10b981',
                            confirmButtonText: 'Great, Thank You'
                        });
                    });
                @else
                    MLSAKOAlert.fire({
                        icon: 'success',
                        title: {!! json_encode(session('success_title') ?? 'Success') !!},
                        text: {!! json_encode(session('success')) !!},
                        iconColor: '#10b981',
                        confirmButtonText: 'Great, Thank You'
                    });
                @endif
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
    <x-pwa-install-prompt />
    @stack('scripts')
</body>

</html>
