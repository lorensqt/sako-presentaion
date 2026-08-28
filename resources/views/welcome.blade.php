@extends('layouts.guest')

@section('title', 'SAKO - Empowering Your Financial Future')

@push('styles')
    <style>
        /* Hide scrollbar for Chrome, Safari and Opera */
        .scrollbar-none::-webkit-scrollbar {
            display: none;
        }
        /* Hide scrollbar for IE, Edge and Firefox */
        .scrollbar-none {
            -ms-overflow-style: none;  /* IE and Edge */
            scrollbar-width: none;  /* Firefox */
        }
    </style>
@endpush

@section('content')
    <!-- Hero Sectionsss -->
    <section class="relative min-h-[85vh] lg:min-h-[90vh] flex items-center justify-center overflow-hidden pt-8 pb-16 lg:pt-16 lg:pb-24 bg-[#FAF9F6]">
        <!-- Warm alabaster background -->
        <!-- Background Gradient Grids -->
        <div class="absolute inset-0 z-0">
            <div class="absolute top-0 right-0 w-[50rem] h-[50rem] bg-emerald-100/40 rounded-full blur-3xl -mr-48 -mt-48">
            </div>
            <div class="absolute bottom-0 left-0 w-[45rem] h-[45rem] bg-teal-100/30 rounded-full blur-3xl -ml-48 -mb-48">
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 w-full">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-16 items-center">
                <!-- Text Content -->
                <div class="lg:col-span-7 space-y-6 sm:space-y-8 text-center lg:text-left">
                    <span
                        class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-emerald-50 text-emerald-800 text-[10px] sm:text-xs font-bold tracking-wide uppercase border border-emerald-100/50 shadow-sm">
                        <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full"></span>
                        Your Trusted Financial Partner Since 2001
                    </span>

                    <h1
                        class="text-3xl xs:text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight text-slate-900 leading-[1.1] serif-font">
                        Empowering Your <span
                            class="bg-gradient-to-r from-emerald-600 to-teal-700 bg-clip-text text-transparent">Financial
                            Future</span>, Together.
                    </h1>

                    <p class="text-xs xs:text-sm sm:text-base lg:text-lg text-slate-600 max-w-2xl mx-auto lg:mx-0 leading-relaxed font-medium">
                        Join Sako Cooperative today. Experience secure savings, flexible loan options, and a
                        community-driven ecosystem dedicated to sustainable financial progress for everyone.
                    </p>

                    <!-- Actions -->
                    <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4 pt-2">
                        <a href="#membership"
                            class="w-full sm:w-auto inline-flex items-center justify-center bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm sm:text-base px-6 py-3.5 sm:px-8 sm:py-4 rounded-xl sm:rounded-2xl shadow-lg shadow-emerald-600/20 hover:shadow-emerald-600/35 hover:-translate-y-0.5 transition-all duration-200">
                            Become a Member Today
                            <svg class="w-5 h-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                        </a>
                        <a href="#services"
                            class="w-full sm:w-auto inline-flex items-center justify-center bg-white hover:bg-slate-50 text-slate-800 font-bold text-sm sm:text-base px-6 py-3.5 sm:px-8 sm:py-4 rounded-xl sm:rounded-2xl border border-slate-200 shadow-sm hover:border-slate-300 hover:-translate-y-0.5 transition-all duration-200">
                            Explore Our Services
                        </a>
                    </div>

                    <!-- Live Milestones Grid -->
                    <div class="grid grid-cols-3 gap-3 xs:gap-6 pt-6 sm:pt-8 border-t border-slate-200/60 max-w-lg mx-auto lg:mx-0">
                        <div class="text-center lg:text-left">
                            <p class="text-xl xs:text-2xl sm:text-3xl font-black text-slate-900 serif-font">6,000+</p>
                            <p class="text-[9px] xs:text-xs font-bold text-slate-500 uppercase tracking-widest mt-1 leading-tight">Active Members</p>
                        </div>

                        <div class="text-center lg:text-left border-x border-slate-200/60 px-2 xs:px-4">
                            <p class="text-xl xs:text-2xl sm:text-3xl font-black text-slate-900 serif-font">25+</p>
                            <p class="text-[9px] xs:text-xs font-bold text-slate-500 uppercase tracking-widest mt-1 leading-tight">Years Service</p>
                        </div>

                        <div class="text-center lg:text-left">
                            <p class="text-xl xs:text-2xl sm:text-3xl font-black text-slate-900 serif-font">₱250M+</p>
                            <p class="text-[9px] xs:text-xs font-bold text-slate-500 uppercase tracking-widest mt-1 leading-tight">Assets Managed</p>
                        </div>
                    </div>
                </div>

                <!-- Visual Graphics Side -->
                <div class="lg:col-span-5 relative w-full">

                    <!-- Main Glassmorphic Panel (Now Login/Profile Card) -->
                    <div
                        class="relative bg-gradient-to-tr from-emerald-800 to-teal-950 rounded-3xl sm:rounded-[2.5rem] p-5 xs:p-6 sm:p-10 text-white shadow-2xl overflow-hidden border border-emerald-800/20 group">
                        <!-- Overlay Grid Graphic -->
                        <div
                            class="absolute inset-0 opacity-10 bg-[linear-gradient(to_right,#808080_1px,transparent_1px),linear-gradient(to_bottom,#808080_1px,transparent_1px)] bg-[size:24px_24px]">
                        </div>

                        <div class="relative z-10 space-y-5 sm:space-y-6">
                            @auth
                                <!-- Logged In Profile View -->
                                <div class="flex items-center justify-between">
                                    <span class="text-[10px] sm:text-xs font-bold uppercase tracking-widest text-emerald-300">Active Member Session</span>
                                    <div class="flex gap-1.5">
                                        <span class="w-2.5 h-2.5 bg-emerald-500 rounded-full animate-ping"></span>
                                        <span class="w-2.5 h-2.5 bg-emerald-500 rounded-full"></span>
                                    </div>
                                </div>

                                <div class="text-center py-4 sm:py-6 space-y-4">
                                    <div class="w-16 h-16 sm:w-20 sm:h-20 bg-emerald-500/20 border border-emerald-400/30 rounded-full flex items-center justify-center mx-auto shadow-inner">
                                        <svg class="w-8 h-8 sm:w-10 sm:h-10 text-emerald-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                    </div>
                                    <div class="space-y-1">
                                        <h3 class="text-lg sm:text-xl font-bold serif-font">{{ Auth::user()->name }}</h3>
                                        <p class="text-[10px] sm:text-xs text-emerald-300 font-semibold tracking-wider uppercase">{{ strtoupper(Auth::user()->role) }}</p>
                                    </div>

                                    <div class="bg-emerald-900/40 border border-emerald-800/30 p-3 sm:p-4 rounded-2xl text-left text-xs space-y-2">
                                        <p class="text-emerald-200"><span class="font-bold text-white">Company ID:</span> {{ Auth::user()->company_id ?: 'N/A' }}</p>
                                        <p class="text-emerald-200"><span class="font-bold text-white">Email:</span> {{ Auth::user()->email }}</p>
                                        <p class="text-emerald-200"><span class="font-bold text-white">Contact:</span> {{ Auth::user()->contact_number ?: 'N/A' }}</p>
                                    </div>
                                </div>

                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="w-full inline-flex items-center justify-center bg-rose-600 hover:bg-rose-700 text-white font-bold text-sm px-6 py-3 sm:py-3.5 rounded-xl transition-all duration-200 shadow-md shadow-rose-900/30">
                                        Log Out Account
                                    </button>
                                </form>
                            @else
                                <!-- Login Card View -->
                                <div class="flex items-center justify-between">
                                    <span class="text-[10px] sm:text-xs font-bold uppercase tracking-widest text-emerald-300">Member Gateway</span>
                                    <div class="flex gap-1.5">
                                        <span class="w-2.5 h-2.5 bg-rose-500 rounded-full"></span>
                                        <span class="w-2.5 h-2.5 bg-amber-500 rounded-full"></span>
                                        <span class="w-2.5 h-2.5 bg-emerald-500 rounded-full"></span>
                                    </div>
                                </div>

                                <div class="space-y-1">
                                    <h3 class="text-xl sm:text-2xl font-bold serif-font">Welcome Back</h3>
                                    <p class="text-[10px] sm:text-xs text-emerald-200 font-medium">Please sign in to access your dashboard.</p>
                                </div>

                                <!-- Session Alert Feedback -->
                                @if(session('success'))
                                    <div class="p-3.5 bg-emerald-500/20 border border-emerald-500/30 text-emerald-200 text-xs rounded-xl font-semibold">
                                        {{ session('success') }}
                                    </div>
                                @endif

                                @if($errors->has('login_identifier'))
                                    <div class="p-3.5 bg-rose-500/20 border border-rose-500/30 text-rose-200 text-xs rounded-xl font-semibold">
                                        {{ $errors->first('login_identifier') }}
                                    </div>
                                @endif

                                <form action="{{ route('login.post') }}" method="POST" class="space-y-4">
                                    @csrf
                                    <div class="space-y-1.5">
                                        <label class="text-[10px] font-bold text-emerald-300 uppercase tracking-widest">Company ID</label>
                                        <input type="text" name="login_identifier" required value="{{ old('login_identifier') }}"
                                            placeholder="e.g. 20248216"
                                            class="w-full bg-emerald-950/50 border border-emerald-800/60 rounded-xl px-4 py-3 sm:py-3.5 text-sm text-white placeholder-emerald-600/70 focus:outline-none focus:border-emerald-400 focus:ring-4 focus:ring-emerald-500/10 transition-all">
                                    </div>

                                    <div class="space-y-1.5">
                                        <div class="flex justify-between items-center">
                                            <label class="text-[10px] font-bold text-emerald-300 uppercase tracking-widest">Password</label>
                                        </div>
                                        <input type="password" name="password" placeholder="••••••••"
                                            class="w-full bg-emerald-950/50 border border-emerald-800/60 rounded-xl px-4 py-3 sm:py-3.5 text-sm text-white placeholder-emerald-600/70 focus:outline-none focus:border-emerald-400 focus:ring-4 focus:ring-emerald-500/10 transition-all">
                                    </div>

                                    <div class="flex items-center">
                                        <input type="checkbox" name="remember" id="remember" class="rounded bg-emerald-950 border-emerald-800 text-emerald-600 focus:ring-emerald-500/30">
                                        <label for="remember" class="ml-2 text-xs text-emerald-200 font-medium">Remember me</label>
                                    </div>

                                    <div class="pt-1 space-y-3">
                                        <button type="submit" class="w-full inline-flex items-center justify-center bg-emerald-500 hover:bg-emerald-600 text-white font-bold text-sm px-6 py-3 sm:py-3.5 rounded-xl transition-all duration-200 shadow-lg shadow-emerald-950/50 hover:-translate-y-0.5">
                                            Sign In Account
                                        </button>

                                        <div class="relative flex py-1 items-center">
                                            <div class="flex-grow border-t border-emerald-800/40"></div>
                                            <span class="flex-shrink mx-4 text-[10px] text-emerald-500 font-bold uppercase tracking-widest">or</span>
                                            <div class="flex-grow border-t border-emerald-800/40"></div>
                                        </div>

                                        <a href="{{ route('auth.google') }}" class="w-full inline-flex items-center justify-center bg-white hover:bg-slate-50 text-slate-800 font-bold text-sm px-6 py-3 sm:py-3.5 rounded-xl transition-all duration-200 border border-slate-200 shadow-sm hover:-translate-y-0.5">
                                            <svg class="w-4 h-4 mr-2" viewBox="0 0 24 24">
                                                <path fill="#EA4335" d="M12 5.04c1.5 0 2.85.51 3.91 1.53l2.92-2.92C17.07 1.95 14.73 1 12 1 7.35 1 3.4 3.65 1.51 7.5l3.52 2.73C5.87 6.82 8.69 5.04 12 5.04z"/>
                                                <path fill="#4285F4" d="M23.49 12.27c0-.81-.07-1.59-.2-2.36H12v4.51h6.46c-.28 1.48-1.12 2.73-2.38 3.58l3.69 2.85c2.16-1.99 3.72-4.92 3.72-8.58z"/>
                                                <path fill="#FBBC05" d="M5.03 10.23c-.23-.68-.36-1.41-.36-2.23s.13-1.55.36-2.23L1.51 3.04C.55 4.96 0 7.12 0 9.42c0 2.3.55 4.46 1.51 6.38l3.52-2.73c-.23-.68-.36-1.41-.36-2.23z"/>
                                                <path fill="#34A853" d="M12 23c3.24 0 5.97-1.07 7.96-2.92l-3.69-2.85c-1.02.68-2.33 1.09-3.91 1.09-3.31 0-6.13-1.78-7.13-4.19l-3.52 2.73C3.4 20.35 7.35 23 12 23z"/>
                                            </svg>
                                            Sign in with Google
                                        </a>
                                    </div>
                                </form>

                                <div class="text-center pt-1 text-xs text-emerald-300">
                                    Don't have an account? <button type="button" class="trigger-pmes-modal font-bold text-white hover:underline focus:outline-none">Apply for Membership</button>
                                </div>
                            @endauth
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="py-24 bg-white relative reveal">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Tab Selectors for Slideshow -->
            <div class="flex items-center justify-center gap-3 mb-16">
                <button id="about-tab-1"
                    class="px-6 py-3 rounded-xl text-sm font-bold shadow-md shadow-emerald-600/10 transition-all duration-300 bg-emerald-600 text-white border border-emerald-600">
                    Who We Are
                </button>
                <button id="about-tab-2"
                    class="px-6 py-3 rounded-xl text-sm font-bold hover:shadow-md hover:bg-slate-50 transition-all duration-300 bg-white text-slate-600 border border-slate-200">
                    Sako Ng MLhuillier
                </button>
            </div>

            <!-- Slide 1 Content Container (Who We Are) -->
            <div id="about-slide-1"
                class="grid grid-cols-1 lg:grid-cols-12 gap-16 items-center transition-all duration-500 ease-in-out opacity-100 transform translate-x-0">
                <!-- Left Side: Core Mission -->
                <div class="lg:col-span-6 space-y-8">
                    <span
                        class="text-xs font-extrabold uppercase tracking-widest text-emerald-600 bg-emerald-50 px-3 py-1.5 rounded-full">Vision
                        & Philosophy</span>
                    <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight serif-font">
                        Co-Owned, Democratic, and Built to Last.
                    </h2>
                    <p class="text-slate-600 text-sm sm:text-base leading-relaxed font-medium">
                        We are a member-owned cooperative dedicated to inclusive finance, livelihood, and community welfare.
                        Our services are built on transparency, responsible lending, and sustainable growth.
                    </p>

                    <!-- 3 Pillars list -->
                    <div class="space-y-6 pt-4 border-t border-slate-100">
                        <ul class="space-y-5">
                            <li class="flex items-start gap-4">
                                <span
                                    class="w-6 h-6 rounded-full bg-emerald-100 text-emerald-800 flex items-center justify-center text-xs font-bold mt-0.5">&check;</span>
                                <div>
                                    <h5 class="text-sm font-bold text-slate-900">Inclusive Finance</h5>
                                    <p class="text-xs text-slate-500 mt-1 leading-relaxed">Lowering entry barriers to
                                        savings plans, high dividend distributions, and absolute transparent credit lines
                                        for all co-owners.</p>
                                </div>
                            </li>
                            <li class="flex items-start gap-4">
                                <span
                                    class="w-6 h-6 rounded-full bg-emerald-100 text-emerald-800 flex items-center justify-center text-xs font-bold mt-0.5">&check;</span>
                                <div>
                                    <h5 class="text-sm font-bold text-slate-900">Sustainable Livelihood</h5>
                                    <p class="text-xs text-slate-500 mt-1 leading-relaxed">Providing high-end credit
                                        options and SME enterprise micro-loans to help employees fund and launch successful
                                        local ventures.</p>
                                </div>
                            </li>
                            <li class="flex items-start gap-4">
                                <span
                                    class="w-6 h-6 rounded-full bg-emerald-100 text-emerald-800 flex items-center justify-center text-xs font-bold mt-0.5">&check;</span>
                                <div>
                                    <h5 class="text-sm font-bold text-slate-900">Community Welfare</h5>
                                    <p class="text-xs text-slate-500 mt-1 leading-relaxed">Directing ten percent of Sako's
                                        annual margins into community scholarship assistance, health coverage packages, and
                                        crisis funds.</p>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Right Side: Graphic Representation -->
                <div class="lg:col-span-6 relative">
                    <div class="grid grid-cols-2 gap-4 relative">
                        <!-- Image 1 -->
                        <div
                            class="rounded-3xl overflow-hidden shadow-md h-56 hover:scale-105 transition-transform duration-300">
                            <img src="https://plus.unsplash.com/premium_photo-1679923813998-6603ee2466c5?q=80&w=1170&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D"
                                alt="Cooperative unity" class="w-full h-full object-cover">
                        </div>
                        <!-- Image 2 -->
                        <div
                            class="rounded-3xl overflow-hidden shadow-md h-56 mt-8 hover:scale-105 transition-transform duration-300">
                            <img src="https://images.unsplash.com/photo-1582213782179-e0d53f98f2ca?q=80&w=1170&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D"
                                alt="Financial progress" class="w-full h-full object-cover">
                        </div>
                        <!-- Image 3 -->
                        <div
                            class="rounded-3xl overflow-hidden shadow-md h-56 -mt-8 hover:scale-105 transition-transform duration-300">
                            <img src="https://images.unsplash.com/photo-1461532257246-777de18cd58b?q=80&w=1176&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D"
                                alt="Local enterprise" class="w-full h-full object-cover">
                        </div>
                        <!-- Image 4 -->
                        <div
                            class="rounded-3xl overflow-hidden shadow-md h-56 hover:scale-105 transition-transform duration-300">
                            <img src="https://images.unsplash.com/photo-1579621970588-a35d0e7ab9b6?q=80&w=1170&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D"
                                alt="Community support" class="w-full h-full object-cover">
                        </div>
                    </div>

                    <!-- Circular seal -->
                    <div
                        class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 bg-white text-emerald-800 p-4 rounded-full shadow-2xl border-4 border-emerald-50 flex flex-col items-center justify-center w-28 h-28 text-center">
                        <span class="text-xs font-black tracking-widest uppercase">Since</span>
                        <span class="text-2xl font-black serif-font">1998</span>
                    </div>
                </div>
            </div>

            <!-- Slide 2 Content Container (Sako Ng MLhuillier History) -->
            <div id="about-slide-2"
                class="hidden grid grid-cols-1 lg:grid-cols-12 gap-16 items-center transition-all duration-500 ease-in-out opacity-0 transform translate-x-8">
                <!-- Left Side: Sako Ng MLhuillier Legacy Story -->
                <div class="lg:col-span-7 space-y-8">
                    <span
                        class="text-xs font-extrabold uppercase tracking-widest text-emerald-600 bg-emerald-50 px-3 py-1.5 rounded-full">Our
                        Pioneering Roots</span>
                    <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight serif-font">
                        Sako Ng MLhuillier
                    </h2>
                    <p class="text-slate-600 text-sm sm:text-base leading-relaxed font-medium">
                        The Samahang Kooperatiba ng M.Lhuillier Pawnshops' Employees (SAKO), as it is commonly known to its
                        members, was conceptualized in September 1997 and conducted its first ownership meeting with VICTO
                        in December 1997. It was organized and registered with CDA in September 1998.
                    </p>
                    <p class="text-slate-600 text-sm leading-relaxed">
                        The founder, <strong class="text-slate-900">Mrs. Judith Jardeloza Peralta, CPA</strong>, the former
                        ML VisMin General Manager, together with the twenty-five (25) original and founding members, raised
                        a total contribution for the Share Capital amounting to <strong class="text-emerald-700">Php
                            26,000.00</strong> at one thousand pesos each. Upon its registration, the total Capital Share
                        generated was <strong class="text-slate-900">Php 71,500.00</strong>, and it began its credit
                        services in 1999.
                    </p>

                    <!-- Historic timeline summary list -->
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 pt-4 border-t border-slate-100 text-center">
                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                            <p class="text-xs text-slate-400 font-extrabold uppercase tracking-wider">Sep 1997</p>
                            <p class="text-xs font-bold text-slate-900 mt-1">Conceived</p>
                        </div>
                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                            <p class="text-xs text-slate-400 font-extrabold uppercase tracking-wider">Dec 1997</p>
                            <p class="text-xs font-bold text-slate-900 mt-1">VICTO Meet</p>
                        </div>
                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                            <p class="text-xs text-slate-400 font-extrabold uppercase tracking-wider">Sep 1998</p>
                            <p class="text-xs font-bold text-slate-900 mt-1">Registered</p>
                        </div>
                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                            <p class="text-xs text-slate-400 font-extrabold uppercase tracking-wider">Y2K (1999)</p>
                            <p class="text-xs font-bold text-slate-900 mt-1">Launched</p>
                        </div>
                    </div>
                </div>

                <!-- Right Side: Beautiful Statistics Box & Founder Card -->
                <div class="lg:col-span-5 space-y-6">
                    <!-- Mrs. Judith Jardeloza Peralta Card -->
                    <div
                        class="bg-gradient-to-tr from-emerald-800 to-teal-950 p-6 rounded-[2rem] text-white shadow-xl relative overflow-hidden">
                        <div class="absolute -top-12 -right-12 w-32 h-32 bg-emerald-500/10 rounded-full blur-2xl"></div>
                        <div class="relative z-10 flex gap-4 items-center">
                            <div
                                class="w-12 h-12 rounded-xl bg-emerald-500/10 flex items-center justify-center border border-emerald-500/20 font-bold text-emerald-400">
                                JP
                            </div>
                            <div>
                                <p class="text-[10px] text-emerald-400 font-extrabold tracking-widest uppercase">SAKO
                                    Founder</p>
                                <h4 class="text-base font-bold serif-font">Judith Jardeloza Peralta, CPA</h4>
                                <p class="text-xs text-emerald-100/80 leading-none mt-1">Former ML VisMin General Manager
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Ledger share capital values compartments -->
                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-[#FAF9F6] p-5 rounded-2xl border border-slate-150 flex flex-col justify-between">
                            <span class="text-[10px] font-extrabold text-slate-400 tracking-wider">FOUNDING POOL</span>
                            <p class="text-lg font-black text-slate-900 mt-2">Php 26,000.00</p>
                            <span class="text-[10px] text-slate-400 leading-none mt-1 font-semibold">25 pioneer
                                members</span>
                        </div>

                        <div class="bg-[#FAF9F6] p-5 rounded-2xl border border-slate-150 flex flex-col justify-between">
                            <span class="text-[10px] font-extrabold text-slate-400 tracking-wider">TOTAL
                                REGISTRATION</span>
                            <p class="text-lg font-black text-slate-900 mt-2">Php 71,500.00</p>
                            <span class="text-[10px] text-slate-400 leading-none mt-1 font-semibold">Registered with
                                CDA</span>
                        </div>
                    </div>

                    <!-- Graphic link of MLhuillier pawnshop employees concept -->
                    <div class="rounded-3xl overflow-hidden h-40 border border-slate-200 relative shadow-inner">
                        <img src="https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?auto=format&fit=crop&w=600&q=80"
                            alt="Office collaboration legacy" class="w-full h-full object-cover">
                    </div>
                </div>
            </div>

        </div>
    </section>

    <!-- Services Section -->
    @php
    // Fetch active loans from the database
    $loans = [];
    try {
        $dbLoans = \App\Models\Loan::where('is_active', true)->get();
        foreach ($dbLoans as $dbLoan) {
            $category = $dbLoan->category;
            
            // Map categories to landing page categories
            if ($category === 'regular') {
                $type = $dbLoan->type_key;
                if (in_array($type, ['instant', 'petty_cash', 'sako_care', 'emergency'])) {
                    $category = 'cash';
                } elseif (in_array($type, ['maxi', 'preferential', 'retirement'])) {
                    $category = 'major';
                } else {
                    $category = 'celebration';
                }
            } elseif ($category === 'travel') {
                $category = 'major';
            }
            
            // Badge translation
            $badge = 'General';
            switch ($category) {
                case 'cash': $badge = 'Cash & Emergency'; break;
                case 'major': $badge = 'Major & Strategic'; break;
                case 'celebration': $badge = 'Festive & Buyouts'; break;
                case 'commodity': $badge = 'Commodity'; break;
            }

            // Loanable amount formatting
            $maxAmount = $dbLoan->loanable_amount;
            if (is_numeric($maxAmount)) {
                $maxAmount = '₱' . number_format((float)$maxAmount);
            } elseif (empty($maxAmount)) {
                $maxAmount = 'Varies';
            }

            // Interest rate formatting
            $rate = $dbLoan->interest_rate;
            $rateStr = $rate > 0 ? number_format((float)$rate, 1) . '%' : 'Varies';

            // Select an icon path
            $iconPath = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />';
            $type = strtolower($dbLoan->type_key);
            if (str_contains($type, 'instant') || str_contains($type, 'fast')) {
                $iconPath = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />';
            } elseif (str_contains($type, 'petty') || str_contains($type, 'cash')) {
                $iconPath = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />';
            } elseif (str_contains($type, 'birthday') || str_contains($type, 'lechon') || str_contains($type, 'celebration') || str_contains($type, 'star')) {
                $iconPath = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.381-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />';
            } elseif (str_contains($type, '13th') || str_contains($type, 'month') || str_contains($type, 'money') || str_contains($type, 'pay')) {
                $iconPath = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />';
            } elseif (str_contains($type, 'appliance') || str_contains($type, 'gadget') || str_contains($type, 'device') || str_contains($type, 'product') || str_contains($type, 'adtel')) {
                $iconPath = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />';
            } elseif (str_contains($type, 'jewelry') || str_contains($type, 'sparkle') || str_contains($type, 'gem')) {
                $iconPath = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707m0-12.728l.707.707m11.314 11.314l.707-.707M12 7a5 5 0 100 10 5 5 0 000-10z" />';
            } elseif (str_contains($type, 'seasonal') || str_contains($type, 'calendar') || str_contains($type, 'event')) {
                $iconPath = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2-2v12a2 2 0 002 2z" />';
            } elseif (str_contains($type, 'emergency') || str_contains($type, 'medical') || str_contains($type, 'care') || str_contains($type, 'health') || str_contains($type, 'hospital')) {
                if (str_contains($type, 'care')) {
                    $iconPath = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.318 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />';
                } else {
                    $iconPath = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />';
                }
            } elseif (str_contains($type, 'preferential') || str_contains($type, 'special') || str_contains($type, 'star-four')) {
                $iconPath = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />';
            }

            $termStr = $dbLoan->max_term_months ? $dbLoan->max_term_months . ' Mos' : 'Flexible';
            
            // Description extraction
            $description = $dbLoan->name . ' assistance program tailored for active members.';
            if (isset($dbLoan->metadata['desc'])) {
                $description = $dbLoan->metadata['desc'];
            } elseif (isset($dbLoan->metadata['name'])) {
                $description = $dbLoan->name . ' product.';
            }

            $loans[] = [
                'category' => $category,
                'title' => $dbLoan->name,
                'desc' => $description,
                'rate' => $rateStr,
                'max' => $maxAmount,
                'term' => $termStr,
                'icon' => $iconPath,
                'badge' => $badge
            ];
        }
    } catch (\Exception $e) {
        $loans = [];
    }
    @endphp

    <!-- Services Section -->
    <section id="services" class="py-24 bg-gradient-to-b from-white to-[#F8FAFC] relative reveal">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Header -->
            <div class="text-center max-w-3xl mx-auto space-y-4 mb-16">
                <span
                    class="text-xs font-extrabold uppercase tracking-widest text-emerald-600 bg-emerald-50 px-3 py-1.5 rounded-full">Our
                    Products & Services</span>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight serif-font">
                    Cooperative Loan Programs Tailored for You
                </h2>
                <div class="w-16 h-1 bg-emerald-500 mx-auto rounded-full"></div>
                <p class="text-slate-600 text-sm leading-relaxed">
                    Discover our specialized financial assistance programs designed to meet your immediate cash
                    requirements, celebrations, major investments, and career transitions.
                </p>
            </div>

            <!-- Filter Controls -->
            <div class="flex flex-wrap items-center justify-center gap-2 mb-12">
                <button id="filter-all" data-filter="all"
                    class="loan-filter-btn px-5 py-2.5 rounded-xl text-xs font-bold transition-all duration-300 bg-emerald-600 text-white border border-emerald-600 shadow-md shadow-emerald-500/10">
                    All Programs ({{ count($loans) }})
                </button>
                <button id="filter-cash" data-filter="cash"
                    class="loan-filter-btn px-5 py-2.5 rounded-xl text-xs font-bold transition-all duration-300 bg-white text-slate-600 border border-slate-200 hover:shadow-md hover:bg-slate-50">
                    Cash & Emergency
                </button>
                <button id="filter-major" data-filter="major"
                    class="loan-filter-btn px-5 py-2.5 rounded-xl text-xs font-bold transition-all duration-300 bg-white text-slate-600 border border-slate-200 hover:shadow-md hover:bg-slate-50">
                    Major & Strategic
                </button>
                <button id="filter-celebration" data-filter="celebration"
                    class="loan-filter-btn px-5 py-2.5 rounded-xl text-xs font-bold transition-all duration-300 bg-white text-slate-600 border border-slate-200 hover:shadow-md hover:bg-slate-50">
                    Festive & Buyouts
                </button>
                <button id="filter-commodity" data-filter="commodity"
                    class="loan-filter-btn px-5 py-2.5 rounded-xl text-xs font-bold transition-all duration-300 bg-white text-slate-600 border border-slate-200 hover:shadow-md hover:bg-slate-50">
                    Commodity
                </button>
            </div>

            <!-- Services Grid -->
            <div id="services-grid" class="flex overflow-x-auto md:grid md:grid-cols-2 lg:grid-cols-3 gap-6 snap-x snap-mandatory scrollbar-none pb-6 md:pb-0 scroll-smooth transition-all duration-500">

                @foreach($loans as $loan)
                <div data-category="{{ $loan['category'] }}"
                    class="loan-card bg-white rounded-3xl p-6 border border-slate-100 shadow-sm hover-card-trigger relative overflow-hidden group transition-all duration-300 w-[85vw] sm:w-[380px] md:w-auto flex-shrink-0 snap-start">
                    <div
                        class="absolute top-0 right-0 w-24 h-24 bg-emerald-500/5 rounded-full blur-xl group-hover:bg-emerald-500/10 transition-colors">
                    </div>
                    <div
                        class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-lg mb-5 border border-emerald-100 transition-all duration-300 group-hover:bg-emerald-600 group-hover:text-white group-hover:scale-110 group-hover:rotate-3 shadow-sm">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            {!! $loan['icon'] !!}
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 serif-font mb-2 group-hover:text-emerald-700 transition-colors duration-200">
                        {{ $loan['title'] }}
                    </h3>
                    <p class="text-xs text-slate-500 leading-relaxed mb-5 min-h-[40px]">
                        {{ $loan['desc'] }}
                    </p>
                    
                    <!-- Embedded Financial Terms Grid -->
                    <div class="bg-slate-50/70 rounded-2xl p-4 mb-5 border border-slate-100/60 grid grid-cols-2 gap-3 text-left">
                        <div>
                            <span class="block text-[10px] font-extrabold uppercase tracking-widest text-slate-400">Interest Rate</span>
                            <span class="text-xs sm:text-sm font-bold text-slate-800">{{ $loan['rate'] }} <span class="text-[10px] text-slate-400 font-medium">/ mo</span></span>
                        </div>
                        <div>
                            <span class="block text-[10px] font-extrabold uppercase tracking-widest text-slate-400">Max Amount</span>
                            <span class="text-xs sm:text-sm font-bold text-emerald-600">{{ $loan['max'] }}</span>
                        </div>
                        <div class="col-span-2 pt-2 border-t border-slate-100/60">
                            <div class="flex justify-between items-center text-[11px] font-semibold text-slate-500">
                                <span>Max Repayment Term:</span>
                                <span class="text-slate-800 font-bold">{{ $loan['term'] }}</span>
                            </div>
                        </div>
                    </div>

                    <div
                        class="flex items-center justify-between pt-4 border-t border-slate-50 text-xs font-bold text-slate-400 transition-colors">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-slate-100 text-slate-600 font-extrabold text-[9px] tracking-wide uppercase">
                            {{ $loan['badge'] }}
                        </span>
                        <span class="text-slate-500 group-hover:text-emerald-600 flex items-center gap-1 transition-colors">
                            Apply Now <span class="group-hover:translate-x-1.5 transition-transform duration-200">➔</span>
                        </span>
                    </div>
                </div>
                @endforeach

            </div>

            <!-- Swipe Indicator Hint (Mobile Only) -->
            <div class="flex md:hidden items-center justify-center gap-2 mt-6 text-xs font-bold text-slate-400 select-none animate-pulse">
                <span>Swipe left or right to explore programs</span>
                <svg class="w-4 h-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                </svg>
            </div>
        </div>
    </section>

    <!-- Membership Section -->
    <section id="membership" class="py-24 bg-[#FAF9F6] relative reveal">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-16 items-start">
                <!-- Left Side: Interactive Membership Steps & Pricing -->
                <div class="lg:col-span-7 space-y-12">
                    <div>
                        <span
                            class="text-xs font-extrabold uppercase tracking-widest text-emerald-600 bg-emerald-50 px-3 py-1.5 rounded-full">Membership
                            Enrollment</span>
                        <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight serif-font mt-4">
                            Why Join MLSAKO?
                        </h2>
                        <p class="text-slate-600 text-sm mt-3 max-w-lg leading-relaxed font-medium">
                            MLSAKO is dedicated to your financial well-being. By joining, you gain immediate access to
                            exclusive patronage dividends, fair financing solutions, and modern digital channels.
                        </p>
                    </div>

                    <!-- 2x2 Grid for "Why Join MLSAKO" benefits -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-8 pt-2">
                        <!-- Benefit 1 -->
                        <div class="flex gap-4 items-start">
                            <div
                                class="w-10 h-10 rounded-xl bg-emerald-500/10 flex items-center justify-center text-emerald-600 flex-shrink-0 border border-emerald-500/20">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-slate-900 leading-snug">Patronage Refunds & Dividends
                                </h3>
                                <p class="text-xs text-slate-500 mt-1.5 leading-relaxed">Watch your money grow. Benefit
                                    from direct patronage refunds and premium dividend opportunities on your share capital
                                    investments.</p>
                            </div>
                        </div>

                        <!-- Benefit 2 -->
                        <div class="flex gap-4 items-start">
                            <div
                                class="w-10 h-10 rounded-xl bg-emerald-500/10 flex items-center justify-center text-emerald-600 flex-shrink-0 border border-emerald-500/20">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-slate-900 leading-snug">Fair Financing & Emergency Loans
                                </h3>
                                <p class="text-xs text-slate-500 mt-1.5 leading-relaxed">Access immediate, fair financial
                                    relief when you need it most with low interest rates, flexible schedules, and emergency
                                    credit approvals.</p>
                            </div>
                        </div>

                        <!-- Benefit 3 -->
                        <div class="flex gap-4 items-start">
                            <div
                                class="w-10 h-10 rounded-xl bg-emerald-500/10 flex items-center justify-center text-emerald-600 flex-shrink-0 border border-emerald-500/20">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-slate-900 leading-snug">Financial Education & Programs
                                </h3>
                                <p class="text-xs text-slate-500 mt-1.5 leading-relaxed">Enhance your financial literacy
                                    with high-quality budget masterclasses, business workshops, and proactive community
                                    programs.</p>
                            </div>
                        </div>

                        <!-- Benefit 4 -->
                        <div class="flex gap-4 items-start">
                            <div
                                class="w-10 h-10 rounded-xl bg-emerald-500/10 flex items-center justify-center text-emerald-600 flex-shrink-0 border border-emerald-500/20">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-slate-900 leading-snug">Digital Channels for Convenience
                                </h3>
                                <p class="text-xs text-slate-500 mt-1.5 leading-relaxed">Transact securely anytime,
                                    anywhere. Monitor your ledger, track loan applications, and enjoy safe virtual portals
                                    with ease.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Membership Requirements Section -->
                    <div class="pt-8 border-t border-slate-200/60 space-y-6">
                        <div class="flex items-center justify-between flex-wrap gap-4">
                            <h3 class="text-xl font-bold text-slate-900 serif-font">Membership Requirements</h3>
                            <span
                                class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-500/10 text-emerald-700 border border-emerald-500/20">
                                <svg class="w-3.5 h-3.5 animate-pulse" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Processing: 1–3 business days
                            </span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                            <!-- Requirement 1 -->
                            <div
                                class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex flex-col justify-between hover:border-emerald-200 transition-colors duration-200 group">
                                <span
                                    class="text-[10px] font-extrabold text-slate-400 group-hover:text-emerald-500 transition-colors tracking-widest">ACTIVE
                                    ELIGIBILITY</span>
                                <p class="text-xs font-semibold text-slate-700 mt-3 leading-relaxed">Must be an MLhuillier
                                    employee with active employment status.</p>
                            </div>

                            <!-- Requirement 2 -->
                            <div
                                class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex flex-col justify-between hover:border-emerald-200 transition-colors duration-200 group">
                                <span
                                    class="text-[10px] font-extrabold text-slate-400 group-hover:text-emerald-500 transition-colors tracking-widest">TENURE
                                    CRITERIA</span>
                                <p class="text-xs font-semibold text-slate-700 mt-3 leading-relaxed">Must have already
                                    received at least one salary payment.</p>
                            </div>

                            <!-- Requirement 3 -->
                            <div
                                class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex flex-col justify-between hover:border-emerald-200 transition-colors duration-200 group">
                                <span
                                    class="text-[10px] font-extrabold text-slate-400 group-hover:text-emerald-500 transition-colors tracking-widest">INITIAL
                                    ACCOUNT DEPOSIT</span>
                                <p class="text-xs font-semibold text-slate-700 mt-3 leading-relaxed">Requires an initial
                                    share capital deposit to configure your ledger.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Side: Beautiful Membership Application CTA -->
                <div class="lg:col-span-5">
                    <div
                        class="bg-gradient-to-tr from-emerald-800 to-teal-950 text-white rounded-[2rem] p-8 sm:p-10 shadow-2xl relative overflow-hidden group border border-emerald-800/20">
                        <div class="absolute -top-12 -right-12 w-32 h-32 bg-emerald-500/10 rounded-full blur-2xl"></div>
                        <div class="absolute inset-0 opacity-10 bg-[linear-gradient(to_right,#808080_1px,transparent_1px),linear-gradient(to_bottom,#808080_1px,transparent_1px)] bg-[size:24px_24px]"></div>

                        <div class="relative z-10 space-y-8">
                            <div class="space-y-3">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-extrabold bg-emerald-400/20 text-emerald-300 border border-emerald-400/20">
                                    <span class="w-1.5 h-1.5 bg-emerald-400 rounded-full animate-pulse"></span>
                                    ONLINE APPLICATION ACTIVE
                                </span>
                                <h3 class="text-2xl sm:text-3xl font-bold serif-font text-white">Join the Cooperative Online</h3>
                                <p class="text-xs text-emerald-200 leading-relaxed">
                                    Skip the long queues and Seminars. Fill out our simplified online registration form and set up your dynamic ledger account in just under 5 minutes.
                                </p>
                            </div>

                            <div class="space-y-4 pt-4 border-t border-emerald-800/40">
                                <h4 class="text-xs font-extrabold text-emerald-300 uppercase tracking-widest">Enrollment Benefits</h4>
                                <ul class="space-y-3 text-xs">
                                    <li class="flex items-center gap-2.5">
                                        <svg class="w-4 h-4 text-emerald-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                        <span>Instant ledger creation</span>
                                    </li>
                                    <li class="flex items-center gap-2.5">
                                        <svg class="w-4 h-4 text-emerald-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                        <span>Paperless document uploads</span>
                                    </li>
                                    <li class="flex items-center gap-2.5">
                                        <svg class="w-4 h-4 text-emerald-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                        <span>Direct payroll-integration eligibility</span>
                                    </li>
                                </ul>
                            </div>

                            @auth
                                <div class="pt-4 text-center">
                                    <p class="text-xs font-semibold text-emerald-300">You are logged in as an active member.</p>
                                    <p class="text-[10px] text-emerald-400/80 mt-1">Explore your portal details or apply for savings & loans from your dashboard.</p>
                                </div>
                            @else
                                <div class="pt-4">
                                    <button type="button"
                                        class="trigger-pmes-modal w-full inline-flex items-center justify-center bg-emerald-500 hover:bg-emerald-600 text-white font-extrabold text-sm py-4 rounded-xl shadow-lg shadow-emerald-950/40 transition-all duration-200 transform hover:-translate-y-0.5 focus:outline-none">
                                        Apply for Membership Now &rarr;
                                    </button>
                                </div>
                            @endauth
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>

    <!-- News & Events Section -->
    <section id="news" class="py-24 bg-white relative reveal">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Header -->
            <div class="flex flex-col md:flex-row items-start md:items-end justify-between gap-6 mb-16">
                <div class="space-y-4 max-w-xl">
                    <span
                        class="text-xs font-extrabold uppercase tracking-widest text-emerald-600 bg-emerald-50 px-3 py-1.5 rounded-full">Community
                        Hub</span>
                    <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight serif-font">
                        Latest News, Assembly Advisories & Events
                    </h2>
                    <div class="w-16 h-1 bg-emerald-500 rounded-full"></div>
                </div>
                <div>
                    <a href="#"
                        class="inline-flex items-center text-sm font-bold text-emerald-600 hover:text-emerald-700 hover:underline">
                        View Complete Newsroom &rarr;
                    </a>
                </div>
            </div>

            <!-- News Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">

                <!-- Article 1 -->
                <div
                    class="bg-white rounded-3xl border border-slate-100 overflow-hidden shadow-sm hover-card-trigger group flex flex-col h-full">
                    <div class="h-48 overflow-hidden relative">
                        <img src="https://images.unsplash.com/photo-1540575467063-178a50c2df87?auto=format&fit=crop&w=500&q=80"
                            alt="General Assembly"
                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        <span
                            class="absolute top-4 left-4 bg-emerald-600 text-white text-[10px] font-extrabold tracking-widest uppercase px-2.5 py-1 rounded-md">Advisory</span>
                    </div>
                    <div class="p-6 flex-1 flex flex-col justify-between space-y-4">
                        <div class="space-y-2">
                            <p class="text-[10px] font-semibold text-slate-400">Published: July 28, 2026</p>
                            <h3
                                class="text-lg font-bold text-slate-900 leading-snug group-hover:text-emerald-600 transition-colors serif-font">
                                25th Annual General Assembly Scheduled on August 15, 2026
                            </h3>
                            <p class="text-xs text-slate-500 leading-relaxed">
                                All legitimate regular members are requested to attend our upcoming assembly. Voting
                                protocols regarding the new dividend tier reform will be initiated.
                            </p>
                        </div>
                        <span class="text-xs font-bold text-emerald-600 group-hover:underline">Read Story &rarr;</span>
                    </div>
                </div>

                <!-- Article 2 -->
                <div
                    class="bg-white rounded-3xl border border-slate-100 overflow-hidden shadow-sm hover-card-trigger group flex flex-col h-full">
                    <div class="h-48 overflow-hidden relative">
                        <img src="https://images.unsplash.com/photo-1517048676732-d65bc937f952?auto=format&fit=crop&w=500&q=80"
                            alt="Financial seminar"
                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        <span
                            class="absolute top-4 left-4 bg-teal-700 text-white text-[10px] font-extrabold tracking-widest uppercase px-2.5 py-1 rounded-md">Workshop</span>
                    </div>
                    <div class="p-6 flex-1 flex flex-col justify-between space-y-4">
                        <div class="space-y-2">
                            <p class="text-[10px] font-semibold text-slate-400">Published: July 20, 2026</p>
                            <h3
                                class="text-lg font-bold text-slate-900 leading-snug group-hover:text-teal-600 transition-colors serif-font">
                                Empowering Agri-businesses: Free Accounting & Seed Funding Seminars
                            </h3>
                            <p class="text-xs text-slate-500 leading-relaxed">
                                Join us this Wednesday to discover modern financial methodologies, low-overhead inventory
                                management, and exclusive agricultural cooperative credit lines.
                            </p>
                        </div>
                        <span class="text-xs font-bold text-teal-600 group-hover:underline">Read Story &rarr;</span>
                    </div>
                </div>

                <!-- Article 3 -->
                <div
                    class="bg-white rounded-3xl border border-slate-100 overflow-hidden shadow-sm hover-card-trigger group flex flex-col h-full">
                    <div class="h-48 overflow-hidden relative">
                        <img src="https://images.unsplash.com/photo-1488521787991-ed7bbaae773c?auto=format&fit=crop&w=500&q=80"
                            alt="Scholarship distribution"
                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        <span
                            class="absolute top-4 left-4 bg-violet-700 text-white text-[10px] font-extrabold tracking-widest uppercase px-2.5 py-1 rounded-md">Community</span>
                    </div>
                    <div class="p-6 flex-1 flex flex-col justify-between space-y-4">
                        <div class="space-y-2">
                            <p class="text-[10px] font-semibold text-slate-400">Published: June 15, 2026</p>
                            <h3
                                class="text-lg font-bold text-slate-900 leading-snug group-hover:text-violet-600 transition-colors serif-font">
                                EduCare Grant Program Welcomes 50 New Student Scholars
                            </h3>
                            <p class="text-xs text-slate-500 leading-relaxed">
                                Sako Cooperative's primary social impact trust releases scholarship support grants covering
                                secondary and college tuition fees for exemplary underprivileged youth.
                            </p>
                        </div>
                        <span class="text-xs font-bold text-violet-600 group-hover:underline">Read Story &rarr;</span>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="py-24 bg-gradient-to-b from-white to-[#F8FAFC] relative reveal">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-16">
                <!-- Left Side: Information Cards & Office Map -->
                <div class="lg:col-span-5 space-y-8">
                    <div>
                        <span
                            class="text-xs font-extrabold uppercase tracking-widest text-emerald-600 bg-emerald-50 px-3 py-1.5 rounded-full">Contact
                            Details</span>
                        <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight serif-font mt-4">
                            We're Here to Listen & Support.
                        </h2>
                        <p class="text-slate-600 text-sm mt-3 leading-relaxed">
                            Have queries about regular membership, micro-financing loans, or dividend payouts? Reach our
                            central support desk. Or visit our head office.
                        </p>
                    </div>

                    <div class="space-y-4">
                        <!-- Head Office -->
                        <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex gap-4 items-start">
                            <div
                                class="w-10 h-10 rounded-xl bg-emerald-500/10 flex items-center justify-center text-emerald-600 mt-1 flex-shrink-0 border border-emerald-500/10">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-slate-900">Cooperative Headquarters</h4>
                                <p class="text-xs text-slate-500 mt-1">Room 201, ML Borromeo Bldg. Borromeo St. Pahina
                                    Central, Cebu City, 6000</p>
                                <span class="inline-block text-[10px] text-slate-400 mt-1 font-semibold">Open: Mon to Sat
                                    (8:00 AM - 5:00 PM)</span>
                            </div>
                        </div>

                        <!-- Direct Contacts -->
                        <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex gap-4 items-start">
                            <div
                                class="w-10 h-10 rounded-xl bg-teal-500/10 flex items-center justify-center text-teal-600 mt-1 flex-shrink-0 border border-teal-500/10">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 5a2 2 0 012-2h3.28a1 1 0 01.94.725l.548 2.2a1 1 0 01-.321.988l-1.305.98a10.582 10.582 0 004.872 4.872l.98-1.305a1 1 0 01.988-.321l2.2.548a1 1 0 01.725.94V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-slate-900">Direct Support Hotline</h4>
                                <p class="text-xs text-slate-500 mt-1">Phone: 09682010246 / 09479992492</p>
                                <p class="text-xs text-slate-500">Email: support@mlsako.com</p>
                            </div>
                        </div>
                    </div>

                    <!-- Google Map Mockup Frame -->
                    <div class="rounded-3xl overflow-hidden shadow-inner h-52 border border-slate-150 relative">
                        <img src="https://images.unsplash.com/photo-1524661135-423995f22d0b?auto=format&fit=crop&w=600&q=80"
                            alt="Map mockup placeholder" class="w-full h-full object-cover brightness-95 saturate-50">
                        <div class="absolute inset-0 bg-slate-900/10 flex items-center justify-center">
                            <div
                                class="bg-white/95 backdrop-blur px-4 py-2.5 rounded-2xl shadow border border-slate-100 text-center space-y-1">
                                <p class="text-xs font-bold text-slate-900">Interactive Location Map</p>
                                <a href="https://www.google.com/maps/search/Room+201,+ML+Borromeo+Bldg.,+Borromeo+St.,+Pahina+Central,+Cebu+City,+6000/@10.295597,123.8961094,51m/data=!3m1!1e3?hl=en-US&entry=ttu&g_ep=EgoyMDI2MDcyNy4wIKXMDSoASAFQAw%3D%3D"
                                    target="_blank"
                                    class="text-[10px] text-emerald-600 font-extrabold hover:underline">Open in Google Maps
                                    &rarr;</a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Side: Clean Modern Feedback Contact Form -->
                <div class="lg:col-span-7">
                    <div class="bg-white rounded-3xl sm:rounded-[2.5rem] p-6 sm:p-10 md:p-12 shadow-md border border-slate-100">
                        <h3 class="text-xl sm:text-2xl font-bold text-slate-900 serif-font mb-2">Send a Secure Message</h3>
                        <p class="text-xs text-slate-500 mb-8">Fill in your inquiry details below. All submitted
                            communication remains private under absolute administrative encryption.</p>

                        <form
                            onsubmit="event.preventDefault(); alert('This is a prototype form layout. Functionality is not operational yet.');"
                            class="grid grid-cols-1 sm:grid-cols-2 gap-6">

                            <div class="space-y-1.5 col-span-1 sm:col-span-1">
                                <label class="text-xs font-bold text-slate-600 uppercase tracking-wider">Your Full
                                    Name</label>
                                <input type="text" placeholder="John Laurence Castillo"
                                    class="w-full bg-slate-50 border border-slate-200 focus:border-emerald-500 focus:bg-white px-4 py-3 rounded-xl text-sm font-medium outline-none transition-all duration-200"
                                    required>
                            </div>

                            <div class="space-y-1.5 col-span-1 sm:col-span-1">
                                <label class="text-xs font-bold text-slate-600 uppercase tracking-wider">Email
                                    Address</label>
                                <input type="email" placeholder="john@example.com"
                                    class="w-full bg-slate-50 border border-slate-200 focus:border-emerald-500 focus:bg-white px-4 py-3 rounded-xl text-sm font-medium outline-none transition-all duration-200"
                                    required>
                            </div>

                            <div class="space-y-1.5 col-span-1 sm:col-span-1">
                                <label class="text-xs font-bold text-slate-600 uppercase tracking-wider">Phone
                                    Number</label>
                                <input type="tel" placeholder="+63 912 345 6789"
                                    class="w-full bg-slate-50 border border-slate-200 focus:border-emerald-500 focus:bg-white px-4 py-3 rounded-xl text-sm font-medium outline-none transition-all duration-200"
                                    required>
                            </div>

                            <div class="space-y-1.5 col-span-1 sm:col-span-1">
                                <label class="text-xs font-bold text-slate-600 uppercase tracking-wider">Subject
                                    Matter</label>
                                <select
                                    class="w-full bg-slate-50 border border-slate-200 focus:border-emerald-500 focus:bg-white px-4 py-3 rounded-xl text-sm font-medium outline-none transition-all duration-200"
                                    required>
                                    <option value="savings">Savings Inquiry</option>
                                    <option value="loans">Loan Application Request</option>
                                    <option value="membership">Membership Registration Help</option>
                                    <option value="other">General Community Query</option>
                                </select>
                            </div>

                            <div class="space-y-1.5 col-span-1 sm:col-span-2">
                                <label class="text-xs font-bold text-slate-600 uppercase tracking-wider">Inquiry
                                    Message</label>
                                <textarea rows="5" placeholder="Write down your queries or specifications here..."
                                    class="w-full bg-slate-50 border border-slate-200 focus:border-emerald-500 focus:bg-white px-4 py-3 rounded-xl text-sm font-medium outline-none transition-all duration-200 resize-none"
                                    required></textarea>
                            </div>

                            <div class="col-span-1 sm:col-span-2 pt-2">
                                <button type="submit"
                                    class="w-full sm:w-auto inline-flex items-center justify-center bg-slate-900 hover:bg-slate-800 text-white font-extrabold text-sm px-8 py-4 rounded-xl shadow-lg transition-all duration-200">
                                    Send Secure Message
                                    <svg class="w-4 h-4 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                            d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                    </svg>
                                </button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>

        </div>
    </section>

    <!-- PMES Membership Info Modal -->
    <div id="pmes-modal" class="fixed inset-0 z-[100] hidden flex items-center justify-center p-4">
        <!-- Backdrop -->
        <div id="pmes-modal-backdrop" class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity duration-300"></div>

        <!-- Modal Box -->
        <div id="pmes-modal-box" class="relative bg-white rounded-[2.5rem] w-full max-w-2xl overflow-hidden shadow-2xl border border-slate-100 transform scale-95 opacity-0 transition-all duration-300 z-10 max-h-[90vh] flex flex-col">
            <!-- Header -->
            <div class="p-8 sm:p-10 border-b border-slate-100 flex justify-between items-start flex-shrink-0">
                <div class="space-y-2">
                    <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-50 text-emerald-800 text-[10px] font-bold tracking-wide uppercase border border-emerald-100/50">
                        <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse"></span>
                        Pre-Membership Education Seminar (PMES)
                    </span>
                    <h3 class="text-2xl sm:text-3xl font-bold text-slate-900 serif-font">Membership Enrollment Steps</h3>
                </div>
                <button type="button" id="close-pmes-modal" class="text-slate-400 hover:text-slate-600 p-2 hover:bg-slate-50 rounded-xl transition-all outline-none">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Content -->
            <div class="p-8 sm:p-10 overflow-y-auto space-y-8 flex-grow">
                <p class="text-sm text-slate-600 leading-relaxed font-medium">
                    Welcome to MLSAKO Cooperative! To align with regulations set by the Cooperative Development Authority (CDA) and ensure strong financial integration, all new memberships are completed through our secure 3-step offline onboarding process.
                </p>

                <!-- Steps Timeline -->
                <div class="relative pl-8 border-l border-emerald-100 space-y-8">
                    <!-- Step 1 -->
                    <div class="relative">
                        <!-- Dot -->
                        <div class="absolute -left-[41px] top-0.5 w-6 h-6 rounded-full bg-emerald-50 border-4 border-emerald-500 flex items-center justify-center font-bold text-emerald-700 text-xs shadow-sm">1</div>
                        <div class="space-y-1">
                            <h4 class="text-sm font-bold text-slate-900">Attend the PMES Seminar</h4>
                            <p class="text-xs text-slate-500 leading-relaxed">
                                Join our 30-minute Pre-Membership Education Seminar, held weekly at our Cebu City main office or online via MS Teams. This briefing covers your rights, duties, patronage dividend payouts, and cooperative bylaws.
                            </p>
                        </div>
                    </div>

                    <!-- Step 2 -->
                    <div class="relative">
                        <!-- Dot -->
                        <div class="absolute -left-[41px] top-0.5 w-6 h-6 rounded-full bg-emerald-50 border-4 border-emerald-500 flex items-center justify-center font-bold text-emerald-700 text-xs shadow-sm">2</div>
                        <div class="space-y-1">
                            <h4 class="text-sm font-bold text-slate-900">Submit Your Physical Form & Share Capital Contribution</h4>
                            <p class="text-xs text-slate-500 leading-relaxed">
                                Secure your printed Membership Form from our office or download it digitally. Submit the completed form along with your initial share capital contribution to configure your personal cooperative pool.
                            </p>
                        </div>
                    </div>

                    <!-- Step 3 -->
                    <div class="relative">
                        <!-- Dot -->
                        <div class="absolute -left-[41px] top-0.5 w-6 h-6 rounded-full bg-emerald-50 border-4 border-emerald-500 flex items-center justify-center font-bold text-emerald-700 text-xs shadow-sm">3</div>
                        <div class="space-y-1">
                            <h4 class="text-sm font-bold text-slate-900">Account Encoding & Online Activation</h4>
                            <p class="text-xs text-slate-500 leading-relaxed">
                                Once received, our administration encodes your official profile details and unique Company ID. After encoding, you can log in instantly on our Portal to manage your savings, deposits, and loans!
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Office Hours info box -->
                <div class="bg-slate-50 border border-slate-100 p-5 rounded-2xl flex gap-4 items-start">
                    <div class="w-8 h-8 rounded-lg bg-emerald-500/10 flex items-center justify-center text-emerald-600 flex-shrink-0">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div class="space-y-1">
                        <h5 class="text-xs font-bold text-slate-900">Questions about the next seminar schedule?</h5>
                        <p class="text-[11px] text-slate-500 leading-normal">
                            Reach our Cebu City Central Desk at **09682010246 / 09479992492** or email **support@mlsako.com** to book your PMES slot.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="p-6 bg-slate-50 border-t border-slate-100 text-center flex-shrink-0 flex items-center justify-end gap-3 rounded-b-[2.5rem]">
                <button type="button" id="close-pmes-modal-btn" class="px-5 py-3 rounded-xl border border-slate-200 text-slate-700 font-bold text-xs hover:bg-slate-100 transition-all outline-none">
                    Close Guide
                </button>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                // PMES Modal open/close logic
                const pmesModal = document.getElementById("pmes-modal");
                const pmesModalBackdrop = document.getElementById("pmes-modal-backdrop");
                const pmesModalBox = document.getElementById("pmes-modal-box");
                const closePmesModal = document.getElementById("close-pmes-modal");
                const closePmesModalBtn = document.getElementById("close-pmes-modal-btn");
                const triggerButtons = document.querySelectorAll(".trigger-pmes-modal");

                function openModal() {
                    pmesModal.classList.remove("hidden");
                    setTimeout(() => {
                        pmesModalBox.classList.remove("scale-95", "opacity-0");
                        pmesModalBox.classList.add("scale-100", "opacity-100");
                    }, 50);
                }

                function closeModal() {
                    pmesModalBox.classList.remove("scale-100", "opacity-100");
                    pmesModalBox.classList.add("scale-95", "opacity-0");
                    setTimeout(() => {
                        pmesModal.classList.add("hidden");
                    }, 300);
                }

                triggerButtons.forEach(btn => {
                    btn.addEventListener("click", openModal);
                });

                if (closePmesModal) closePmesModal.addEventListener("click", closeModal);
                if (closePmesModalBtn) closePmesModalBtn.addEventListener("click", closeModal);
                if (pmesModalBackdrop) pmesModalBackdrop.addEventListener("click", closeModal);

                // About Us slideshow tabs
                const tab1 = document.getElementById("about-tab-1");
                const tab2 = document.getElementById("about-tab-2");
                const slide1 = document.getElementById("about-slide-1");
                const slide2 = document.getElementById("about-slide-2");

                function activateTab1() {
                    tab1.className =
                        "px-6 py-3 rounded-xl text-sm font-bold shadow-md shadow-emerald-600/10 transition-all duration-300 bg-emerald-600 text-white border border-emerald-600";
                    tab2.className =
                        "px-6 py-3 rounded-xl text-sm font-bold hover:shadow-md hover:bg-slate-50 transition-all duration-300 bg-white text-slate-600 border border-slate-200";

                    slide2.classList.add("hidden");
                    slide2.classList.remove("opacity-100", "translate-x-0");
                    slide2.classList.add("opacity-0", "translate-x-8");

                    slide1.classList.remove("hidden");
                    setTimeout(() => {
                        slide1.classList.remove("opacity-0", "translate-x-8");
                        slide1.classList.add("opacity-100", "translate-x-0");
                    }, 50);
                }

                function activateTab2() {
                    tab2.className =
                        "px-6 py-3 rounded-xl text-sm font-bold shadow-md shadow-emerald-600/10 transition-all duration-300 bg-emerald-600 text-white border border-emerald-600";
                    tab1.className =
                        "px-6 py-3 rounded-xl text-sm font-bold hover:shadow-md hover:bg-slate-50 transition-all duration-300 bg-white text-slate-600 border border-slate-200";

                    slide1.classList.add("hidden");
                    slide1.classList.remove("opacity-100", "translate-x-0");
                    slide1.classList.add("opacity-0", "translate-x-8");

                    slide2.classList.remove("hidden");
                    setTimeout(() => {
                        slide2.classList.remove("opacity-0", "translate-x-8");
                        slide2.classList.add("opacity-100", "translate-x-0");
                    }, 50);
                }

                if (tab1 && tab2) {
                    tab1.addEventListener("click", activateTab1);
                    tab2.addEventListener("click", activateTab2);
                }

                // Loan Programs filtering system
                const filterBtns = document.querySelectorAll(".loan-filter-btn");
                const loanCards = document.querySelectorAll(".loan-card");
                const servicesGrid = document.getElementById("services-grid");

                filterBtns.forEach(btn => {
                    btn.addEventListener("click", () => {
                        // Style clicked button as active
                        filterBtns.forEach(b => {
                            b.className =
                                "loan-filter-btn px-5 py-2.5 rounded-xl text-xs font-bold transition-all duration-300 bg-white text-slate-600 border border-slate-200 hover:shadow-md hover:bg-slate-50";
                        });
                        btn.className =
                            "loan-filter-btn px-5 py-2.5 rounded-xl text-xs font-bold transition-all duration-300 bg-emerald-600 text-white border border-emerald-600 shadow-md shadow-emerald-500/10";

                        // Scroll back to start on mobile carousel when filter changes
                        if (servicesGrid) {
                            servicesGrid.scroll({ left: 0, behavior: 'smooth' });
                        }

                        const category = btn.getAttribute("data-filter");

                        loanCards.forEach(card => {
                            // Smooth scaling and opacity fade out
                            card.style.opacity = "0";
                            card.style.transform = "scale(0.95)";

                            setTimeout(() => {
                                if (category === "all" || card.getAttribute(
                                        "data-category") === category) {
                                    card.classList.remove("hidden");
                                    setTimeout(() => {
                                        card.style.opacity = "1";
                                        card.style.transform = "scale(1)";
                                    }, 50);
                                } else {
                                    card.classList.add("hidden");
                                }
                            }, 200);
                        });
                    });
                });
            });
        </script>
    @endpush
@endsection
