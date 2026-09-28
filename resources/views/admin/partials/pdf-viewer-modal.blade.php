<!-- MODAL: IN-APP PDF VIEWER PREVIEW -->
<div id="modal-pdf-viewer" class="fixed inset-0 z-[70] hidden items-center justify-center p-3 sm:p-6 transition-all duration-300">
    <!-- Backdrop overlay -->
    <div id="pdf-viewer-backdrop" class="modal-overlay absolute inset-0 bg-slate-950/80 backdrop-blur-md opacity-0 transition-opacity duration-300 pointer-events-none"></div>

    <!-- Modal Dialog Window -->
    <div class="modal-container relative w-full max-w-5xl h-[88vh] max-h-[920px] bg-slate-900 rounded-[2rem] border border-slate-800 shadow-2xl flex flex-col overflow-hidden transform scale-95 opacity-0 transition-all duration-300 z-10">
        
        <!-- Header Bar -->
        <div class="h-16 px-5 sm:px-6 bg-slate-900 border-b border-slate-800 flex items-center justify-between flex-shrink-0">
            <div class="flex items-center gap-3 min-w-0 pr-3">
                <div class="w-9 h-9 rounded-xl bg-rose-500/15 text-rose-400 flex items-center justify-center flex-shrink-0 text-xs font-black ring-1 ring-rose-500/30">
                    PDF
                </div>
                <div class="min-w-0 truncate">
                    <h3 id="pdf-viewer-title" class="text-xs sm:text-sm font-bold text-white truncate leading-tight">
                        Compliance Document
                    </h3>
                    <p id="pdf-viewer-meta" class="text-[10px] sm:text-[11px] text-slate-400 font-mono mt-0.5 truncate">
                        Loading document stream...
                    </p>
                </div>
            </div>

            <!-- Header Actions -->
            <div class="flex items-center gap-2 flex-shrink-0">
                <!-- External Tab Fallback Link -->
                <a id="pdf-viewer-external-link" href="#" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-800 hover:bg-slate-750 text-slate-300 hover:text-white rounded-xl text-xs font-bold transition-all border border-slate-700/60" title="Open in dedicated tab">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                    </svg>
                    <span class="hidden sm:inline">New Tab</span>
                </a>

                <!-- Close Button -->
                <button type="button" id="btn-close-pdf-viewer" class="p-2 text-slate-400 hover:text-white hover:bg-rose-500/20 hover:text-rose-300 rounded-xl transition-all cursor-pointer" title="Close Preview (Esc)">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Document Viewer Body -->
        <div class="relative flex-1 w-full bg-slate-950 overflow-hidden">
            <!-- Loading Indicator while iframe buffers -->
            <div id="pdf-viewer-loader" class="absolute inset-0 z-10 flex flex-col items-center justify-center bg-slate-950 text-white space-y-3 transition-opacity duration-300">
                <div class="w-10 h-10 border-3 border-emerald-500/20 border-t-emerald-500 rounded-full animate-spin"></div>
                <p class="text-xs font-semibold text-slate-400">Loading document preview...</p>
            </div>

            <!-- Native PDF Stream Iframe -->
            <iframe id="pdf-viewer-frame" class="w-full h-full border-0 bg-slate-950" src="about:blank"></iframe>
        </div>
    </div>
</div>
