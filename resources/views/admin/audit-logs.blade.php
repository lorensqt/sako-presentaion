@extends('layouts.admin')

@section('title', 'System Audit & Security Logs - Sako Cooperative')

@section('header')
<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div>
        <p class="text-xs text-emerald-600 dark:text-emerald-400 font-extrabold tracking-wider uppercase">Compliance Ledger</p>
        <h1 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight mt-0.5">System Audit & Security Logs</h1>
    </div>
</div>
@endsection

@section('content')
<div class="space-y-6">

    <!-- KPI Metrics Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Metric 1: Total Events -->
        <div class="bg-white dark:bg-slate-800 p-5 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 dark:bg-emerald-950/30 flex items-center justify-center shadow-inner">
                <!-- Swap src attribute below with your preferred Icons8 image link -->
                <img src="https://img.icons8.com/?size=100&id=eghpqErHSTMn&format=png&color=000000" alt="Total Events" class="w-7 h-7 object-contain">
            </div>
            <div>
                <p class="text-[10px] text-slate-400 dark:text-slate-500 font-extrabold uppercase tracking-wider">Total Audited Events</p>
                <h3 class="text-xl font-black text-slate-800 dark:text-white mt-0.5">{{ number_format($totalCount) }}</h3>
            </div>
        </div>

        <!-- Metric 2: Authentications -->
        <div class="bg-white dark:bg-slate-800 p-5 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-sky-50 dark:bg-sky-950/30 flex items-center justify-center shadow-inner">
                <!-- Swap src attribute below with your preferred Icons8 image link -->
                <img src="https://img.icons8.com/fluency/48/fingerprint.png" alt="Authentications" class="w-7 h-7 object-contain">
            </div>
            <div>
                <p class="text-[10px] text-slate-400 dark:text-slate-500 font-extrabold uppercase tracking-wider">Authentication Trails</p>
                <h3 class="text-xl font-black text-slate-800 dark:text-white mt-0.5">{{ number_format($authCount) }}</h3>
            </div>
        </div>

        <!-- Metric 3: Security Warnings -->
        <div class="bg-white dark:bg-slate-800 p-5 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-amber-50 dark:bg-amber-950/30 flex items-center justify-center shadow-inner">
                <!-- Swap src attribute below with your preferred Icons8 image link -->
                <img src="https://img.icons8.com/fluency/48/alarm.png" alt="Warnings" class="w-7 h-7 object-contain">
            </div>
            <div>
                <p class="text-[10px] text-slate-400 dark:text-slate-500 font-extrabold uppercase tracking-wider">Security Warnings</p>
                <h3 class="text-xl font-black text-slate-800 dark:text-white mt-0.5">{{ number_format($warningCount) }}</h3>
            </div>
        </div>

        <!-- Metric 4: Critical Lockouts -->
        <div class="bg-white dark:bg-slate-800 p-5 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-rose-50 dark:bg-rose-950/30 flex items-center justify-center shadow-inner">
                <!-- Swap src attribute below with your preferred Icons8 image link -->
                <img src="https://img.icons8.com/?size=100&id=U12vJQsF1INo&format=png&color=000000" alt="Critical Danger" class="w-7 h-7 object-contain">
            </div>
            <div>
                <p class="text-[10px] text-slate-400 dark:text-slate-500 font-extrabold uppercase tracking-wider">Critical Lockouts / Danger</p>
                <h3 class="text-xl font-black text-slate-800 dark:text-white mt-0.5">{{ number_format($dangerCount) }}</h3>
            </div>
        </div>
    </div>

    <!-- Live Filters Panel Card -->
    <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-200/80 dark:border-slate-700/80 p-5 shadow-sm">
        <form id="audit-filters-form" action="{{ route('admin.audit-logs') }}" method="GET" class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                <!-- Live Search -->
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400 pointer-events-none">
                        <svg class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search User, Email, IP, Action..." class="w-full text-xs font-bold pl-9.5 pr-4 py-3 bg-slate-100 dark:bg-slate-900 border-none rounded-xl text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-emerald-500/20">
                </div>

                <!-- Severity Filter -->
                <div>
                    <select name="severity" class="w-full text-xs font-bold px-4 py-3 bg-slate-100 dark:bg-slate-900 border-none rounded-xl text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-emerald-500/20">
                        <option value="">All Severities</option>
                        <option value="info" {{ request('severity') === 'info' ? 'selected' : '' }}>Info (Green)</option>
                        <option value="warning" {{ request('severity') === 'warning' ? 'selected' : '' }}>Warning (Amber)</option>
                        <option value="danger" {{ request('severity') === 'danger' ? 'selected' : '' }}>Danger (Red)</option>
                    </select>
                </div>

                <!-- Action Type Filter -->
                <div>
                    <select name="action_type" class="w-full text-xs font-bold px-4 py-3 bg-slate-100 dark:bg-slate-900 border-none rounded-xl text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-emerald-500/20">
                        <option value="">All Categories</option>
                        <option value="auth" {{ request('action_type') === 'auth' ? 'selected' : '' }}>Authentication & Security</option>
                        <option value="member" {{ request('action_type') === 'member' ? 'selected' : '' }}>Member Registry Profile</option>
                        <option value="withdrawal" {{ request('action_type') === 'withdrawal' ? 'selected' : '' }}>Savings Withdrawals</option>
                        <option value="loan" {{ request('action_type') === 'loan' ? 'selected' : '' }}>Loans & Workflows</option>
                        <option value="deduction" {{ request('action_type') === 'deduction' ? 'selected' : '' }}>Payroll Deductions</option>
                        <option value="compliance" {{ request('action_type') === 'compliance' ? 'selected' : '' }}>Compliance Reports Export</option>
                    </select>
                </div>

                <!-- Start Date -->
                <div>
                    <input type="date" name="start_date" value="{{ request('start_date') }}" title="Start Date" class="w-full text-xs font-bold px-4 py-3 bg-slate-100 dark:bg-slate-900 border-none rounded-xl text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-emerald-500/20">
                </div>

                <!-- End Date -->
                <div>
                    <input type="date" name="end_date" value="{{ request('end_date') }}" title="End Date" class="w-full text-xs font-bold px-4 py-3 bg-slate-100 dark:bg-slate-900 border-none rounded-xl text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-emerald-500/20">
                </div>
            </div>

            <!-- Filter Buttons -->
            <div class="flex items-center justify-end gap-2 pt-2">
                <a href="{{ route('admin.audit-logs') }}" class="px-4 py-2.5 text-xs font-bold text-slate-600 dark:text-slate-400 bg-slate-100 dark:bg-slate-900 hover:bg-slate-200 dark:hover:bg-slate-800 rounded-xl transition-all">Clear Filters</a>
                <button type="submit" class="px-5 py-2.5 text-xs font-black text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl transition-all shadow-md shadow-emerald-600/10">Filter Ledger</button>
            </div>
        </form>
    </div>

    <!-- Ledger Table Panel -->
    <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-200/80 dark:border-slate-700/80 overflow-hidden shadow-sm relative">
        
        <!-- Premium Loading Spinner Overlay -->
        <div id="table-loading-overlay" class="absolute inset-0 bg-white/70 dark:bg-slate-800/70 z-20 flex items-center justify-center backdrop-blur-[1px] opacity-0 pointer-events-none transition-all duration-200">
            <div class="flex flex-col items-center gap-2">
                <svg class="animate-spin h-8 w-8 text-emerald-600" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span class="text-[10px] font-extrabold text-emerald-600 uppercase tracking-widest animate-pulse">Syncing Ledger...</span>
            </div>
        </div>

        <div id="audit-logs-container">
            @include('admin.partials.audit-logs-table')
        </div>
    </div>

</div>

<!-- Inspector Side-Over Slide Drawer -->
<div id="inspector-drawer" class="fixed inset-0 z-50 hidden">
    <!-- Backdrop -->
    <div id="drawer-backdrop" class="fixed inset-0 bg-slate-900/40 dark:bg-slate-950/60 backdrop-blur-sm transition-opacity duration-300"></div>
    
    <!-- Panel -->
    <div id="drawer-panel" class="fixed top-0 bottom-0 right-0 w-full max-w-xl bg-white dark:bg-slate-800 border-l border-slate-200 dark:border-slate-700 flex flex-col z-50 transform translate-x-full transition-transform duration-300 ease-in-out shadow-2xl">
        <!-- Header -->
        <div class="h-16 flex items-center justify-between px-6 border-b border-slate-200/80 dark:border-slate-700/80">
            <div class="flex items-center gap-2">
                <span class="text-emerald-600 dark:text-emerald-400">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                </span>
                <span id="drawer-title-code" class="font-mono text-xs font-black text-slate-800 dark:text-white uppercase tracking-wider">Inspect Trail Event</span>
            </div>
            <button id="drawer-close-btn" class="p-1.5 rounded-lg text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-slate-100 hover:bg-slate-100 dark:hover:bg-slate-700/50 transition-all">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <!-- Content -->
        <div class="flex-1 overflow-y-auto p-6 space-y-6">
            <!-- Event Overview Details -->
            <div class="grid grid-cols-2 gap-4 bg-slate-50 dark:bg-slate-900/40 border border-slate-100 dark:border-slate-700/40 p-4 rounded-2xl">
                <div>
                    <span class="text-[9px] font-extrabold uppercase tracking-wider text-slate-400">Timestamp</span>
                    <span id="drawer-time" class="text-xs font-bold text-slate-700 dark:text-slate-200 block mt-0.5">Aug 11, 2026 12:00:00 AM</span>
                </div>
                <div>
                    <span class="text-[9px] font-extrabold uppercase tracking-wider text-slate-400">Severity</span>
                    <span id="drawer-severity" class="inline-flex mt-0.5">INFO</span>
                </div>
                <div>
                    <span class="text-[9px] font-extrabold uppercase tracking-wider text-slate-400">Operator/Actor</span>
                    <span id="drawer-actor" class="text-xs font-bold text-slate-700 dark:text-slate-200 block mt-0.5 truncate" title="">Jane Doe</span>
                </div>
                <div>
                    <span class="text-[9px] font-extrabold uppercase tracking-wider text-slate-400">IP Origin</span>
                    <span id="drawer-ip" class="text-xs font-mono font-bold text-slate-700 dark:text-slate-200 block mt-0.5">127.0.0.1</span>
                </div>
                <div class="col-span-2 border-t border-slate-100 dark:border-slate-700/40 pt-3">
                    <span class="text-[9px] font-extrabold uppercase tracking-wider text-slate-400">User Agent (Browser Origin)</span>
                    <span id="drawer-agent" class="text-[10px] font-semibold text-slate-600 dark:text-slate-300 block mt-0.5 leading-normal select-all">Mozilla/5.0...</span>
                </div>
            </div>

            <!-- Activity Summary block -->
            <div class="space-y-1.5">
                <span class="text-[9px] font-extrabold uppercase tracking-wider text-slate-400 block">Activity Narrative</span>
                <div class="bg-emerald-50/10 dark:bg-emerald-950/5 border border-emerald-500/10 rounded-2xl p-4">
                    <p id="drawer-description" class="text-xs font-semibold text-slate-700 dark:text-slate-200 leading-relaxed select-all">
                        Something occurred in the system.
                    </p>
                </div>
            </div>

            <!-- State comparative Diffs (Before / After values) -->
            <div id="diffs-container" class="space-y-4">
                <span class="text-[9px] font-extrabold uppercase tracking-wider text-slate-400 block">Data Payload Inspector (JSON State Diff)</span>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Old values (Before) -->
                    <div class="space-y-1.5">
                        <span class="text-[9px] font-black text-rose-500 uppercase tracking-widest block flex items-center gap-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                            BEFORE STATE (- OLD VALUES)
                        </span>
                        <pre class="bg-slate-900 text-rose-400 font-mono text-[10px] p-4 rounded-2xl overflow-x-auto border border-rose-500/10 max-h-[220px] select-all scrollbar-thin"><code id="drawer-old-values">{}</code></pre>
                    </div>

                    <!-- New values (After) -->
                    <div class="space-y-1.5">
                        <span class="text-[9px] font-black text-emerald-500 uppercase tracking-widest block flex items-center gap-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            AFTER STATE (+ NEW VALUES)
                        </span>
                        <pre class="bg-slate-900 text-emerald-400 font-mono text-[10px] p-4 rounded-2xl overflow-x-auto border border-emerald-500/10 max-h-[220px] select-all scrollbar-thin"><code id="drawer-new-values">{}</code></pre>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="p-4 border-t border-slate-200/80 dark:border-slate-700/80 flex items-center justify-end bg-slate-50 dark:bg-slate-900/40">
            <button id="drawer-ok-btn" class="px-6 py-2.5 text-xs font-black text-white bg-slate-800 hover:bg-slate-900 dark:bg-slate-700 dark:hover:bg-slate-600 rounded-xl transition-all shadow-md">Acknowledge</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const form = document.getElementById("audit-filters-form");
        const container = document.getElementById("audit-logs-container");
        const loader = document.getElementById("table-loading-overlay");

        const drawer = document.getElementById("inspector-drawer");
        const panel = document.getElementById("drawer-panel");
        const backdrop = document.getElementById("drawer-backdrop");
        const closeBtn = document.getElementById("drawer-close-btn");
        const okBtn = document.getElementById("drawer-ok-btn");

        // Elements to populate in drawer
        const tCode = document.getElementById("drawer-title-code");
        const tTime = document.getElementById("drawer-time");
        const tSeverity = document.getElementById("drawer-severity");
        const tActor = document.getElementById("drawer-actor");
        const tIp = document.getElementById("drawer-ip");
        const tAgent = document.getElementById("drawer-agent");
        const tDesc = document.getElementById("drawer-description");
        const codeOld = document.getElementById("drawer-old-values");
        const codeNew = document.getElementById("drawer-new-values");
        const diffsContainer = document.getElementById("diffs-container");

        function openDrawer() {
            drawer.classList.remove("hidden");
            setTimeout(() => {
                panel.classList.remove("translate-x-full");
            }, 10);
        }

        function closeDrawer() {
            panel.classList.add("translate-x-full");
            setTimeout(() => {
                drawer.classList.add("hidden");
            }, 300);
        }

        closeBtn.addEventListener("click", closeDrawer);
        okBtn.addEventListener("click", closeDrawer);
        backdrop.addEventListener("click", closeDrawer);

        // EVENT DELEGATION: Listen to table container clicks (survives AJAX DOM swaps)
        container.addEventListener("click", function (e) {
            
            // 1. INSPECT EVENT HOOK
            const inspectBtn = e.target.closest(".inspect-btn");
            if (inspectBtn) {
                // Read properties
                const action = inspectBtn.getAttribute("data-action");
                const time = inspectBtn.getAttribute("data-timestamp");
                const severity = inspectBtn.getAttribute("data-severity");
                const actor = inspectBtn.getAttribute("data-actor");
                const description = inspectBtn.getAttribute("data-description");
                const ip = inspectBtn.getAttribute("data-ip");
                const agent = inspectBtn.getAttribute("data-agent");
                
                let oldValStr = inspectBtn.getAttribute("data-old");
                let newValStr = inspectBtn.getAttribute("data-new");

                // Populate drawer fields
                tCode.textContent = "Inspect: " + action;
                tTime.textContent = time;
                tIp.textContent = ip;
                tActor.textContent = actor;
                tActor.setAttribute("title", actor);
                tAgent.textContent = agent;
                tAgent.setAttribute("title", agent);
                tDesc.textContent = description;

                // Severity Badge Formatting
                let sevBadgeHtml = "";
                if (severity === 'danger') {
                    sevBadgeHtml = `<span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded bg-rose-500/10 text-rose-600 dark:text-rose-400 text-[10px] font-black border border-rose-500/20 uppercase tracking-widest animate-pulse">Critical Danger</span>`;
                } else if (severity === 'warning') {
                    sevBadgeHtml = `<span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded bg-amber-500/10 text-amber-600 dark:text-amber-400 text-[10px] font-black border border-amber-500/20 uppercase tracking-widest">Warning Alerts</span>`;
                } else {
                    sevBadgeHtml = `<span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 text-[10px] font-black border border-emerald-500/20 uppercase tracking-widest">Normal Audit</span>`;
                }
                tSeverity.innerHTML = sevBadgeHtml;

                // Pretty format comparative JSON states
                let parsedOld = null;
                let parsedNew = null;

                try {
                    parsedOld = JSON.parse(oldValStr);
                } catch(err) { parsedOld = null; }

                try {
                    parsedNew = JSON.parse(newValStr);
                } catch(err) { parsedNew = null; }

                if (!parsedOld && !parsedNew) {
                    diffsContainer.classList.add("hidden");
                } else {
                    diffsContainer.classList.remove("hidden");
                    codeOld.textContent = parsedOld ? JSON.stringify(parsedOld, null, 4) : "No original state recorded.\n(Null initial state)";
                    codeNew.textContent = parsedNew ? JSON.stringify(parsedNew, null, 4) : "No changes made.\n(Null terminal state)";
                }

                openDrawer();
            }

            // 2. PAGINATION AJAX HOOK
            const paginationLink = e.target.closest(".pagination-container a, .pagination a");
            if (paginationLink) {
                e.preventDefault();
                const url = paginationLink.getAttribute("href");
                if (url) {
                    fetchLogs(url);
                }
            }
        });

        // AJAX Query function
        function fetchLogs(url) {
            // Show Loading Overlay
            loader.classList.remove("pointer-events-none", "opacity-0");
            loader.classList.add("opacity-100");

            // Perform fetch
            fetch(url, {
                headers: {
                    "X-Requested-With": "XMLHttpRequest"
                }
            })
            .then(res => res.text())
            .then(html => {
                container.innerHTML = html;
                // Update history url state
                window.history.pushState(null, "", url);
            })
            .catch(err => {
                console.error("AJAX Audit log fetch failed: ", err);
            })
            .finally(() => {
                // Hide Loading Overlay
                loader.classList.add("pointer-events-none", "opacity-0");
                loader.classList.remove("opacity-100");
            });
        }

        // Build full URL based on current inputs
        function submitFiltersForm() {
            const formData = new FormData(form);
            const params = new URLSearchParams(formData);
            const baseUrl = form.getAttribute("action");
            const url = `${baseUrl}?${params.toString()}`;
            fetchLogs(url);
        }

        // Search Keystroke Debouncer
        let searchTimeout = null;
        const searchInput = form.querySelector('input[name="search"]');
        if (searchInput) {
            searchInput.addEventListener("input", function () {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    submitFiltersForm();
                }, 350); // 350ms of typing silence before fetching
            });
        }

        // Dropdown triggers and Date limits
        form.querySelectorAll("select, input[type='date']").forEach(input => {
            input.addEventListener("change", function () {
                submitFiltersForm();
            });
        });

        // Hijack general submit (e.g. hitting Enter or clicking Filter button)
        form.addEventListener("submit", function (e) {
            e.preventDefault();
            submitFiltersForm();
        });
    });
</script>
@endpush
