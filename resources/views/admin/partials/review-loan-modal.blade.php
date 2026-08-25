<!-- MODAL: STUNNING FULL-SCREEN COCKPIT LOAN REVIEW -->
<style>
    /* Premium custom scrollbar for modal elements */
    .modal-container .overflow-y-auto {
        scrollbar-width: thin;
        scrollbar-color: rgba(148, 163, 184, 0.2) transparent;
    }
    .modal-container .overflow-y-auto::-webkit-scrollbar {
        width: 6px;
        height: 6px;
    }
    .modal-container .overflow-y-auto::-webkit-scrollbar-track {
        background: transparent;
    }
    .modal-container .overflow-y-auto::-webkit-scrollbar-thumb {
        background: rgba(148, 163, 184, 0.25);
        border-radius: 9999px;
        transition: all 0.2s ease;
    }
    .modal-container .overflow-y-auto:hover::-webkit-scrollbar-thumb {
        background: rgba(148, 163, 184, 0.45);
    }
    .dark .modal-container .overflow-y-auto {
        scrollbar-color: rgba(51, 65, 85, 0.45) transparent;
    }
    .dark .modal-container .overflow-y-auto::-webkit-scrollbar-thumb {
        background: rgba(51, 65, 85, 0.5);
    }
    .dark .modal-container .overflow-y-auto:hover::-webkit-scrollbar-thumb {
        background: rgba(71, 85, 105, 0.7);
    }
</style>

<div id="modal-review" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 md:p-6 lg:p-8 overflow-hidden">
    <!-- Backdrop with enhanced blur and depth -->
    <div class="fixed inset-0 bg-slate-950/50 dark:bg-slate-900/80 backdrop-blur-xl opacity-0 transition-opacity duration-300 ease-[cubic-bezier(0.16,1,0.3,1)] pointer-events-none modal-overlay"></div>

    <!-- Premium Workspace Canvas -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 shadow-2xl shadow-slate-950/20 dark:shadow-slate-950/50 overflow-hidden h-full w-full max-w-7xl z-50 transform scale-95 opacity-0 transition-all duration-300 ease-[cubic-bezier(0.16,1,0.3,1)] modal-container flex flex-col">

        <!-- Header -->
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800/80 px-6 sm:px-8 py-5 flex-shrink-0 bg-gradient-to-r from-slate-50/50 to-white dark:from-slate-900/60 dark:to-slate-900/30">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-950/35 border border-emerald-100/50 dark:border-emerald-900/30 flex items-center justify-center text-emerald-600 dark:text-emerald-400">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-xl font-extrabold text-slate-900 dark:text-slate-100 tracking-tight serif-font">Review Loan Facility</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 font-semibold mt-1">Verification, Pipeline Visualizer & Decision Canvas</p>
                </div>
            </div>
            <button class="modal-close p-2 rounded-xl text-slate-450 hover:text-slate-700 dark:text-slate-500 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition-all duration-200 cursor-pointer">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- Scrollable Three-Column Split Workspace Content Area -->
        <div class="flex-grow flex flex-col lg:flex-row overflow-hidden min-h-0">

            <!-- COLUMN 1: Borrower & Loan Details Panel (Scrollable, Sticky context) -->
            <div class="w-full lg:w-[300px] xl:w-[340px] flex-shrink-0 bg-slate-50 dark:bg-slate-900/10 border-r border-slate-200/80 dark:border-slate-800/60 p-6 overflow-y-auto flex flex-col gap-6">

                <!-- Borrower Profile Block -->
                <div class="space-y-3">
                    <h4 class="text-[10px] font-extrabold uppercase tracking-widest text-slate-400 dark:text-slate-500 flex items-center gap-2">
                        <svg class="w-3.5 h-3.5 text-slate-400 dark:text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        Borrower Dossier
                    </h4>
                    <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800/80 p-5 rounded-2xl text-xs space-y-4 shadow-sm hover:shadow transition-all duration-300">
                        <div class="flex items-center gap-3">
                            <div class="w-11 h-11 rounded-full bg-emerald-50 text-emerald-600 dark:bg-emerald-950/45 dark:text-emerald-400 flex items-center justify-center font-extrabold text-sm border-2 border-emerald-100/50 dark:border-emerald-900/30 shadow-sm" id="view-borrower-avatar">
                                --
                            </div>
                            <div class="min-w-0">
                                <p class="font-extrabold text-slate-900 dark:text-slate-100 text-sm truncate" id="view-borrower-name"></p>
                                <p class="text-slate-450 dark:text-slate-500 font-semibold text-[10.5px] truncate max-w-[210px]" id="view-borrower-email"></p>
                            </div>
                        </div>
                        <div class="pt-3.5 border-t border-slate-100 dark:border-slate-800/60 space-y-2.5 text-[11px] font-semibold text-slate-500 dark:text-slate-400">
                            <div class="flex justify-between items-center bg-slate-50/50 dark:bg-slate-950/40 p-2 rounded-lg border border-slate-100 dark:border-slate-800/50">
                                <span>Company ID:</span>
                                <span class="font-extrabold text-slate-800 dark:text-slate-200 font-mono" id="view-company-id"></span>
                            </div>
                            <div class="space-y-1.5">
                                <span class="block">Permanent Address:</span>
                                <span class="font-bold text-slate-850 dark:text-slate-300 block bg-slate-50/50 dark:bg-slate-950/40 border border-slate-100 dark:border-slate-800/50 p-2.5 rounded-xl text-[10px] leading-relaxed shadow-3xs" id="view-address"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Loan Details Block -->
                <div class="space-y-3">
                    <h4 class="text-[10px] font-extrabold uppercase tracking-widest text-slate-400 dark:text-slate-500 flex items-center gap-2">
                        <svg class="w-3.5 h-3.5 text-slate-400 dark:text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Loan Facility Details
                    </h4>
                    <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800/80 p-5 rounded-2xl text-xs space-y-4 shadow-sm hover:shadow transition-all duration-300">
                        <div class="flex justify-between items-start border-b border-slate-100 dark:border-slate-800/60 pb-3.5">
                            <div>
                                <span class="font-black text-slate-900 dark:text-slate-100 text-lg block tracking-tight" id="view-amount"></span>
                                <span class="text-[9.5px] text-emerald-600 dark:text-emerald-400 font-extrabold uppercase tracking-wider block mt-0.5" id="view-type-name"></span>
                            </div>
                            <div class="text-right">
                                <span class="font-extrabold text-slate-800 dark:text-slate-100 text-sm block" id="view-term"></span>
                                <span class="text-[9.5px] text-slate-400 dark:text-slate-500 font-bold block mt-0.5">Chosen Term</span>
                            </div>
                        </div>

                        <!-- Member Request remarks -->
                        <div class="space-y-1.5">
                            <p class="text-[10px] text-slate-400 dark:text-slate-500 font-extrabold uppercase tracking-widest flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-slate-400 dark:text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                                </svg>
                                Member Remarks / Notes
                            </p>
                            <p class="text-[11px] text-slate-600 dark:text-slate-355 italic bg-amber-50/20 dark:bg-amber-950/10 p-3.5 rounded-xl border border-amber-200/30 dark:border-amber-900/20 border-l-4 border-l-amber-500/85 dark:border-l-amber-500/50 leading-relaxed font-sans shadow-3xs" id="view-member-remarks"></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- COLUMN 2: Interactive Vertical Flowline (Middle Column) -->
            <div class="flex-grow p-6 overflow-hidden flex flex-col gap-4 border-r border-slate-100 dark:border-slate-800/60 min-w-0">
                <div class="flex justify-between items-center flex-shrink-0">
                    <h4 class="text-[10px] font-extrabold uppercase tracking-widest text-slate-400 dark:text-slate-500 flex items-center gap-2">
                        <svg class="w-4 h-4 text-slate-400 dark:text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 1121.21 8H17m0 0V3m0 4h.01"/>
                        </svg>
                        Complete Sequential Approval Timeline
                    </h4>
                </div>

                <!-- VERTICAL TIMELINE TRACK CONTAINER -->
                <div class="relative flex-grow overflow-y-auto pr-2 pl-1 py-2 scrollbar-thin scrollbar-thumb-slate-200 dark:scrollbar-thumb-slate-800" id="view-history-timeline-wrapper">
                    <!-- Connecting Line: Centered precisely behind the 36px (w-9) status circles -->
                    <div class="absolute left-[22px] top-6 bottom-6 w-0.5 bg-slate-200 dark:bg-slate-850 pointer-events-none"></div>

                    <div class="relative flex flex-col gap-5" id="view-history-timeline">
                        <!-- Dynamic step cards generated via JS -->
                    </div>
                </div>
            </div>

            <!-- COLUMN 3: Signatory Action Desk & Info Canvas (Right Column) -->
            <div class="w-full lg:w-[320px] xl:w-[360px] flex-shrink-0 bg-slate-50 dark:bg-slate-900/10 p-6 overflow-y-auto flex flex-col gap-6">
                <h4 class="text-[10px] font-extrabold uppercase tracking-widest text-slate-400 dark:text-slate-500 flex items-center gap-2">
                    <svg class="w-3.5 h-3.5 text-slate-400 dark:text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                    Review Desk
                </h4>

                <!-- Signatory Decision Box -->
                <div id="review-action-panel" class="hidden space-y-4 p-5 bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800/80 rounded-2xl shadow-sm hover:shadow transition-all duration-300 flex-shrink-0">
                    <h4 class="text-[10px] font-extrabold uppercase tracking-widest text-emerald-600 dark:text-emerald-450 flex items-center gap-1.5 mb-2">
                        <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                        </svg>
                        Signatory Decision Desk
                    </h4>
                    <form id="form-action" method="POST" class="space-y-4">
                        @csrf
                        <div class="space-y-2">
                            <label class="text-[10px] font-extrabold uppercase text-slate-500 dark:text-slate-400 tracking-wider flex items-center justify-between">
                                <span>Evaluation Remarks / Audit Details</span>
                                <span class="text-[9px] bg-slate-100 dark:bg-slate-800 px-1.5 py-0.5 rounded text-slate-450 dark:text-slate-500">Required</span>
                            </label>
                            <textarea name="remarks" id="action-remarks" required rows="4" placeholder="Provide audit reasoning, payslip validation codes, or comaker verification context..." class="w-full px-4 py-3 text-xs font-medium border border-slate-200 dark:border-slate-700/85 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 rounded-xl outline-none focus:border-emerald-500 dark:focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 dark:focus:ring-emerald-500/5 placeholder-slate-400 dark:placeholder-slate-600 transition-all resize-none duration-200 leading-relaxed"></textarea>
                        </div>

                        <div class="flex flex-col gap-2.5 pt-1">
                            <button type="submit" id="btn-action-approve" class="w-full bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-550 hover:to-teal-550 text-white font-extrabold text-xs px-6 py-3.5 rounded-xl shadow-lg shadow-emerald-600/10 hover:shadow-xl hover:shadow-emerald-600/20 dark:shadow-emerald-950/45 border border-emerald-500/10 transition-all duration-300 active:scale-[0.98] cursor-pointer flex items-center justify-center gap-2 group">
                                <svg class="w-4 h-4 text-emerald-100 group-hover:scale-110 transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Sign & Approve
                            </button>
                            <button type="submit" id="btn-action-reject" class="w-full bg-rose-50/55 hover:bg-rose-100/75 dark:bg-rose-950/15 text-rose-700 dark:text-rose-400 border border-rose-200/50 dark:border-rose-900/30 font-extrabold text-xs px-5 py-3 rounded-xl transition-all duration-300 active:scale-[0.98] cursor-pointer shadow-3xs hover:shadow flex items-center justify-center gap-2 group">
                                <svg class="w-4 h-4 text-rose-500 group-hover:scale-115 transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Reject Application
                            </button>
                            <button type="submit" id="btn-action-return" class="w-full bg-amber-50/55 hover:bg-amber-100/75 dark:bg-amber-950/15 text-amber-700 dark:text-amber-400 border border-amber-200/50 dark:border-amber-900/30 font-extrabold text-xs px-5 py-3 rounded-xl transition-all duration-300 active:scale-[0.98] cursor-pointer shadow-3xs hover:shadow hidden flex items-center justify-center gap-2 group">
                                <svg class="w-4 h-4 text-amber-500 group-hover:rotate-45 transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                                </svg>
                                Return Application
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Read-Only Information / Guidance Panel -->
                <div id="review-info-panel" class="space-y-4 p-5 bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800/80 rounded-2xl shadow-sm hover:shadow transition-all duration-300 flex flex-col gap-3">
                    <h4 class="text-[10px] font-extrabold uppercase tracking-widest text-slate-400 dark:text-slate-500 flex items-center gap-2">
                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Application Insights
                    </h4>
                    <div class="text-[11px] text-slate-500 dark:text-slate-400 leading-relaxed space-y-3 font-medium">
                        <p>This loan application is currently running through the automated workflow. All signatory reviews are logged sequentially.</p>
                        <div class="p-3 bg-sky-50/35 dark:bg-sky-950/10 border border-sky-100 dark:border-sky-900/20 rounded-xl flex items-center justify-between">
                            <span class="font-bold text-slate-700 dark:text-slate-355 text-[10.5px]">Current Active Stage:</span>
                            <span class="text-[10px] font-extrabold uppercase px-2 py-0.5 rounded bg-sky-100/60 dark:bg-sky-950/60 text-sky-750 dark:text-sky-400 border border-sky-200/40 dark:border-sky-800/50 shadow-3xs" id="info-current-stage">N/A</span>
                        </div>
                    </div>
                    <div class="pt-3 border-t border-slate-100 dark:border-slate-800/60 flex flex-col gap-2.5">
                        <a id="btn-export-pdf" href="#" target="_blank" class="w-full bg-white dark:bg-slate-900 hover:bg-slate-50 dark:hover:bg-slate-800/50 text-slate-700 dark:text-slate-300 font-extrabold text-xs px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-800 transition-all duration-300 shadow-3xs hover:shadow flex items-center justify-center gap-2 group hover:-translate-y-0.5">
                            <svg class="w-4 h-4 text-slate-400 group-hover:text-emerald-550 transition-colors duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Export Official PDF
                        </a>
                        <button class="modal-close w-full bg-slate-100/75 hover:bg-slate-200/85 dark:bg-slate-800 dark:hover:bg-slate-750 text-slate-600 dark:text-slate-400 font-extrabold text-xs px-4 py-3.5 rounded-xl transition-all duration-300 text-center cursor-pointer active:scale-[0.98] shadow-3xs">
                            Close Workspace
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
