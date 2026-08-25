<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Sako Coop - Empowering Communities')</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('img/sako-logo-nobg.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400;1,500;1,600&family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&display=swap" rel="stylesheet">

    <!-- Styles / Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Custom CSS for Smooth Scrolling and Animations -->
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #F8FAFC; /* Light slate neutral, gentle on eyes */
        }
        .serif-font {
            font-family: 'Playfair Display', serif;
        }
        
        /* Smooth Fade In Animation */
        .reveal {
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.8s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .reveal.active {
            opacity: 1;
            transform: translateY(0);
        }
        
        /* Custom dynamic glow effects */
        .gradient-glow {
            background: radial-gradient(circle at 50% 50%, rgba(16, 185, 129, 0.08) 0%, rgba(248, 250, 252, 0) 70%);
        }
        
        /* Hover shadow transition */
        .hover-card-trigger {
            transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .hover-card-trigger:hover {
            transform: translateY(-6px);
            box-shadow: 0 20px 40px -15px rgba(15, 23, 42, 0.08);
        }
    </style>
    @stack('styles')
</head>
<body class="text-slate-800 antialiased overflow-x-hidden selection:bg-emerald-500 selection:text-white">
    <!-- Announcement Bar -->
    <div class="bg-gradient-to-r from-emerald-800 to-teal-950 text-emerald-100 text-xs py-2 px-4 text-center font-medium tracking-wide">
        <span class="inline-flex items-center gap-1.5">
            <span class="w-1.5 h-1.5 bg-emerald-400 rounded-full animate-ping"></span>
            Annual General Assembly Scheduled: August 15, 2026. <a href="#news" class="underline hover:text-white transition-colors duration-200">Learn More &rarr;</a>
        </span>
    </div>

    <!-- Sticky Navigation Bar -->
    <header id="main-header" class="sticky top-0 z-50 w-full bg-white/80 backdrop-blur-md border-b border-slate-100 transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-24">
                <!-- Logo -->
                <div class="flex-shrink-0 flex items-center">
                    <a href="#" class="flex items-center group">
                        <img src="{{ asset('img/sako-logo-nobg.png') }}" alt="ML Sako Logo" class="h-14 w-auto object-contain transition-transform duration-200 group-hover:scale-105">
                    </a>
                </div>

                <!-- Desktop Navigation -->
                <nav class="hidden md:flex items-center gap-8 font-medium text-sm text-slate-600">
                    <a href="#about" class="hover:text-emerald-600 transition-colors duration-200 py-2">About Us</a>
                    <a href="#services" class="hover:text-emerald-600 transition-colors duration-200 py-2">Services</a>
                    <a href="#membership" class="hover:text-emerald-600 transition-colors duration-200 py-2">Membership</a>
                    <a href="#news" class="hover:text-emerald-600 transition-colors duration-200 py-2">News & Events</a>
                    <a href="#contact" class="hover:text-emerald-600 transition-colors duration-200 py-2">Contact</a>
                </nav>

                <!-- Navigation Action Buttons -->
                <div class="hidden md:flex items-center gap-4">
                    <a href="{{ route('member.savings') }}" class="text-slate-600 hover:text-emerald-700 font-medium text-sm px-4 py-2 transition-colors duration-200">
                        Member Portal
                    </a>
                    <a href="#membership" class="inline-flex items-center justify-center bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-sm px-5 py-2.5 rounded-xl shadow-md shadow-emerald-600/15 hover:shadow-emerald-600/25 transition-all duration-200 transform hover:-translate-y-0.5">
                        Join Sako
                    </a>
                </div>

                <!-- Mobile Menu Button -->
                <div class="md:hidden">
                    <button id="mobile-menu-toggle" type="button" class="inline-flex items-center justify-center p-2 rounded-lg text-slate-500 hover:text-emerald-600 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-all duration-200" aria-controls="mobile-menu" aria-expanded="false">
                        <span class="sr-only">Open main menu</span>
                        <svg id="menu-icon" class="block h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                        </svg>
                        <svg id="close-icon" class="hidden h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div id="mobile-menu" class="hidden md:hidden bg-white/95 backdrop-blur-md border-b border-slate-100 transition-all duration-300">
            <div class="px-4 pt-2 pb-6 space-y-3 font-medium text-base text-slate-700">
                <a href="#about" class="block px-3 py-2.5 rounded-lg hover:bg-emerald-50 hover:text-emerald-700 transition-all duration-200">About Us</a>
                <a href="#services" class="block px-3 py-2.5 rounded-lg hover:bg-emerald-50 hover:text-emerald-700 transition-all duration-200">Services</a>
                <a href="#membership" class="block px-3 py-2.5 rounded-lg hover:bg-emerald-50 hover:text-emerald-700 transition-all duration-200">Membership</a>
                <a href="#news" class="block px-3 py-2.5 rounded-lg hover:bg-emerald-50 hover:text-emerald-700 transition-all duration-200">News & Events</a>
                <a href="#contact" class="block px-3 py-2.5 rounded-lg hover:bg-emerald-50 hover:text-emerald-700 transition-all duration-200">Contact Us</a>
                
                <div class="pt-4 border-t border-slate-100 flex flex-col gap-3">
                    <a href="{{ route('member.savings') }}" class="flex items-center justify-center px-4 py-2.5 rounded-lg border border-slate-200 text-slate-600 font-semibold hover:bg-slate-50 transition-all duration-200">
                        Member Portal
                    </a>
                    <a href="#membership" class="flex items-center justify-center px-4 py-2.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-semibold shadow-md shadow-emerald-600/10 transition-all duration-200">
                        Join Sako
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content Slot -->
    <main class="min-h-[calc(100vh-20rem)]">
        @yield('content')
    </main>

    <!-- Premium Footer Section -->
    <footer class="bg-gradient-to-b from-slate-900 to-teal-950 text-slate-400 pt-16 pb-8 border-t border-emerald-950">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-12 mb-16">
                <!-- Branding and Mission -->
                <div class="lg:col-span-2">
                    <div class="flex items-center mb-6">
                        <img src="{{ asset('img/sako-logo-nobg.png') }}" alt="ML Sako Logo" class="h-12 w-auto object-contain brightness-0 invert">
                    </div>
                    <p class="text-slate-300 text-sm mb-6 leading-relaxed max-w-sm">
                        Empowering members and transforming communities through high-yield savings programs, tailored financial solutions, and mutual cooperative support.
                    </p>
                    <div class="flex items-center gap-3">
                        <a href="#" class="w-9 h-9 rounded-lg bg-slate-800 hover:bg-emerald-600 hover:text-white flex items-center justify-center transition-all duration-200" title="Facebook">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z"/></svg>
                        </a>
                        <a href="#" class="w-9 h-9 rounded-lg bg-slate-800 hover:bg-emerald-600 hover:text-white flex items-center justify-center transition-all duration-200" title="Twitter/X">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                        </a>
                        <a href="#" class="w-9 h-9 rounded-lg bg-slate-800 hover:bg-emerald-600 hover:text-white flex items-center justify-center transition-all duration-200" title="LinkedIn">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/></svg>
                        </a>
                    </div>
                </div>

                <!-- Quick Links -->
                <div>
                    <h3 class="text-white font-bold text-sm mb-6 tracking-wider uppercase">Sitemap</h3>
                    <ul class="space-y-4 text-sm font-medium">
                        <li><a href="#about" class="hover:text-emerald-400 transition-colors duration-200">About Our Coop</a></li>
                        <li><a href="#services" class="hover:text-emerald-400 transition-colors duration-200">Financial Services</a></li>
                        <li><a href="#membership" class="hover:text-emerald-400 transition-colors duration-200">How to Join</a></li>
                        <li><a href="#news" class="hover:text-emerald-400 transition-colors duration-200">News & Events</a></li>
                        <li><a href="#contact" class="hover:text-emerald-400 transition-colors duration-200">Get in Touch</a></li>
                    </ul>
                </div>

                <!-- Products -->
                <div>
                    <h3 class="text-white font-bold text-sm mb-6 tracking-wider uppercase">Products</h3>
                    <ul class="space-y-4 text-sm">
                        <li><a href="#services" class="hover:text-emerald-400 transition-colors duration-200">Regular Savings</a></li>
                        <li><a href="#services" class="hover:text-emerald-400 transition-colors duration-200">High-Yield Time Deposit</a></li>
                        <li><a href="#services" class="hover:text-emerald-400 transition-colors duration-200">Personal & Business Loans</a></li>
                        <li><a href="#services" class="hover:text-emerald-400 transition-colors duration-200">Emergency & Micro Loans</a></li>
                    </ul>
                </div>

                <!-- Contact Info / Regulators -->
                <div>
                    <h3 class="text-white font-bold text-sm mb-6 tracking-wider uppercase">Office</h3>
                    <ul class="space-y-4 text-sm leading-relaxed text-slate-300">
                        <li class="flex items-start gap-2">
                            <svg class="w-5 h-5 text-emerald-400 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <span>Room 201, ML Borromeo Bldg. Borromeo St. Pahina Central, Cebu City, 6000</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-emerald-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.94.725l.548 2.2a1 1 0 01-.321.988l-1.305.98a10.582 10.582 0 004.872 4.872l.98-1.305a1 1 0 01.988-.321l2.2.548a1 1 0 01.725.94V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            <span>09682010246 / 09479992492 / 09479992492</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-emerald-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            <span>support@mlsako.com</span>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Footer Divider & Bottom Section -->
            <div class="border-t border-slate-800/80 pt-8 flex flex-col md:flex-row items-center justify-between gap-4 text-xs font-medium text-slate-500">
                <p>&copy; 2026 Sako Cooperative. All rights reserved. Regulated by the Cooperative Development Authority (CDA).</p>
                <div class="flex gap-6">
                    <a href="#" class="hover:text-emerald-400 transition-colors duration-200">Privacy Policy</a>
                    <a href="#" class="hover:text-emerald-400 transition-colors duration-200">Terms of Service</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Vanilla Javascript for UI enhancements -->
    <script>
        // Smooth scroll reveal logic
        document.addEventListener("DOMContentLoaded", function () {
            const reveals = document.querySelectorAll(".reveal");

            function checkReveal() {
                const windowHeight = window.innerHeight;
                reveals.forEach((reveal) => {
                    const elementTop = reveal.getBoundingClientRect().top;
                    const elementVisible = 100; // Trigger threshold
                    if (elementTop < windowHeight - elementVisible) {
                        reveal.classList.add("active");
                    }
                });
            }

            window.addEventListener("scroll", checkReveal);
            checkReveal(); // Trigger once on load in case elements are already visible

            // Navbar shadow & shrinking effect on scroll
            const header = document.getElementById("main-header");
            const navWrapper = document.querySelector("#main-header .flex.items-center.justify-between");
            window.addEventListener("scroll", function () {
                if (window.scrollY > 10) {
                    header.classList.add("shadow-md", "bg-white/95");
                    header.classList.remove("bg-white/80");
                    if (navWrapper) {
                        navWrapper.classList.add("h-20");
                        navWrapper.classList.remove("h-24");
                    }
                } else {
                    header.classList.remove("shadow-md", "bg-white/95");
                    header.classList.add("bg-white/80");
                    if (navWrapper) {
                        navWrapper.classList.add("h-24");
                        navWrapper.classList.remove("h-20");
                    }
                }
            });

            // Mobile menu toggle
            const mobileMenuToggle = document.getElementById("mobile-menu-toggle");
            const mobileMenu = document.getElementById("mobile-menu");
            const menuIcon = document.getElementById("menu-icon");
            const closeIcon = document.getElementById("close-icon");

            mobileMenuToggle.addEventListener("click", function () {
                const isExpanded = mobileMenuToggle.getAttribute("aria-expanded") === "true";
                mobileMenuToggle.setAttribute("aria-expanded", !isExpanded);
                mobileMenu.classList.toggle("hidden");
                menuIcon.classList.toggle("hidden");
                closeIcon.classList.toggle("hidden");
            });

            // Close mobile menu when a link is clicked
            const mobileLinks = mobileMenu.querySelectorAll("a");
            mobileLinks.forEach((link) => {
                link.addEventListener("click", () => {
                    mobileMenu.classList.add("hidden");
                    menuIcon.classList.remove("hidden");
                    closeIcon.classList.add("hidden");
                    mobileMenuToggle.setAttribute("aria-expanded", "false");
                });
            });
        });
    </script>
    @stack('scripts')
</body>
</html>
