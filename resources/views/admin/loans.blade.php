@extends('layouts.admin')

@section('title', 'Loans Archives & Directory - Sako Cooperative')
@section('page_title', 'Loans Directory & Archives')
@section('page_subtitle', 'Exhaustive ledger of all member loan applications, legal contracts, and historical records.')

@section('content')
<div class="space-y-6 animate-fade-in">

    <!-- KPI / Metrics Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- KPI 1: Total Portfolio -->
        <div class="bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700/80 rounded-2xl p-5 flex items-center gap-4 shadow-3xs">
            <div class="w-12 h-12 rounded-xl bg-slate-100 dark:bg-slate-900 flex items-center justify-center text-slate-500 dark:text-slate-400 flex-shrink-0 text-xl shadow-2xs">
                <i class="fa-solid fa-folder-open"></i>
            </div>
            <div>
                <span class="text-[10px] font-black uppercase text-slate-400 tracking-wider">Total Application Portfolio</span>
                <h3 class="text-xl font-black text-slate-800 dark:text-white mt-0.5">{{ $metrics['total'] }}</h3>
            </div>
        </div>

        <!-- KPI 2: Active Pipeline -->
        <div class="bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700/80 rounded-2xl p-5 flex items-center gap-4 shadow-3xs">
            <div class="w-12 h-12 rounded-xl bg-blue-50 dark:bg-blue-950/30 flex items-center justify-center text-blue-600 dark:text-blue-400 flex-shrink-0 text-xl shadow-2xs">
                <i class="fa-solid fa-clock-rotate-left"></i>
            </div>
            <div>
                <span class="text-[10px] font-black uppercase text-slate-400 tracking-wider">Active Decision Pipeline</span>
                <h3 class="text-xl font-black text-blue-600 dark:text-blue-400 mt-0.5">{{ $metrics['pending'] }}</h3>
            </div>
        </div>

        <!-- KPI 3: Approved & Released -->
        <div class="bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700/80 rounded-2xl p-5 flex items-center gap-4 shadow-3xs">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 dark:bg-emerald-950/30 flex items-center justify-center text-emerald-600 dark:text-emerald-400 flex-shrink-0 text-xl shadow-2xs">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <div>
                <span class="text-[10px] font-black uppercase text-slate-400 tracking-wider">Fully Approved & Released</span>
                <h3 class="text-xl font-black text-emerald-600 dark:text-emerald-400 mt-0.5">{{ $metrics['approved'] }}</h3>
            </div>
        </div>

        <!-- KPI 4: Rejected / Archived -->
        <div class="bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700/80 rounded-2xl p-5 flex items-center gap-4 shadow-3xs">
            <div class="w-12 h-12 rounded-xl bg-rose-50 dark:bg-rose-950/30 flex items-center justify-center text-rose-600 dark:text-rose-400 flex-shrink-0 text-xl shadow-2xs">
                <i class="fa-solid fa-ban"></i>
            </div>
            <div>
                <span class="text-[10px] font-black uppercase text-slate-400 tracking-wider">Rejected / Archived</span>
                <h3 class="text-xl font-black text-rose-600 dark:text-rose-400 mt-0.5">{{ $metrics['rejected'] }}</h3>
            </div>
        </div>
    </div>

    <!-- Interactive Filter & Command Bar -->
    <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-100 dark:border-slate-700/80 shadow-sm p-4 sm:p-5 space-y-4">
        <!-- Top Row: Interactive Search and Customized Smooth Dropdowns -->
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3">
            
            <!-- Smooth Search Bar with Live Spinner and Instant Clear -->
            <div class="relative flex-1 min-w-[260px] max-w-md">
                <i id="search-icon" class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 dark:text-slate-500 text-xs transition-colors duration-200"></i>
                <input type="text" id="ajax-search" value="{{ $search }}" placeholder="Search borrower name, ID, or LN-XXXXX..." class="w-full pl-9 pr-14 py-2.5 text-xs font-semibold border border-slate-200 dark:border-slate-700/80 bg-slate-50/60 dark:bg-slate-900 text-slate-900 dark:text-slate-100 rounded-xl focus:bg-white dark:focus:bg-slate-900 focus:border-emerald-500 dark:focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 dark:focus:ring-emerald-500/10 placeholder-slate-400 dark:placeholder-slate-500 transition-all outline-none">
                
                <div class="absolute right-3 top-1/2 -translate-y-1/2 flex items-center gap-1.5">
                    <i id="search-spinner" class="fa-solid fa-circle-notch fa-spin text-emerald-500 text-xs hidden"></i>
                    <button type="button" id="btn-clear-search" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 text-xs cursor-pointer p-0.5 {{ $search ? '' : 'hidden' }}" title="Clear search">
                        <i class="fa-solid fa-circle-xmark"></i>
                    </button>
                </div>
            </div>

            <!-- Customized Smooth Dropdowns -->
            <div class="flex flex-wrap items-center gap-2.5">
                
                <!-- Status Custom Dropdown -->
                <div class="relative custom-dropdown" id="dropdown-wrapper-status">
                    <input type="hidden" id="filter-status" value="{{ $status ?: 'all' }}">
                    <button type="button" id="dropdown-btn-status" class="inline-flex items-center gap-2 px-3.5 py-2.5 text-xs font-semibold border border-slate-200 dark:border-slate-700/80 bg-slate-50/70 dark:bg-slate-900 text-slate-800 dark:text-slate-200 rounded-xl hover:border-emerald-500/50 hover:bg-white dark:hover:bg-slate-800 transition-all cursor-pointer select-none">
                        <i class="fa-solid fa-toggle-on text-slate-400 text-xs"></i>
                        <span id="label-status" class="truncate max-w-[100px]">
                            {{ $status === 'pending' ? 'Pending' : ($status === 'approved' ? 'Approved' : ($status === 'released' ? 'Released' : ($status === 'rejected' ? 'Rejected' : 'All Statuses'))) }}
                        </span>
                        <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 transition-transform duration-200 dropdown-arrow"></i>
                    </button>
                    <!-- Floating Menu -->
                    <div id="dropdown-menu-status" class="absolute top-full left-0 mt-1.5 w-48 z-40 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl shadow-xl shadow-slate-900/10 dark:shadow-slate-950/40 p-1.5 space-y-0.5 transform opacity-0 scale-95 pointer-events-none transition-all duration-200 ease-out origin-top-left">
                        <div class="dropdown-item px-3 py-2 text-xs font-semibold rounded-xl text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700/70 cursor-pointer flex items-center justify-between transition-colors" data-value="all">
                            <span>All Statuses</span>
                            <i class="fa-solid fa-check text-emerald-600 dark:text-emerald-400 text-[11px] check-icon {{ (!$status || $status === 'all') ? '' : 'hidden' }}"></i>
                        </div>
                        <div class="dropdown-item px-3 py-2 text-xs font-semibold rounded-xl text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700/70 cursor-pointer flex items-center justify-between transition-colors" data-value="pending">
                            <span>Pending</span>
                            <i class="fa-solid fa-check text-emerald-600 dark:text-emerald-400 text-[11px] check-icon {{ $status === 'pending' ? '' : 'hidden' }}"></i>
                        </div>
                        <div class="dropdown-item px-3 py-2 text-xs font-semibold rounded-xl text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700/70 cursor-pointer flex items-center justify-between transition-colors" data-value="approved">
                            <span>Approved</span>
                            <i class="fa-solid fa-check text-emerald-600 dark:text-emerald-400 text-[11px] check-icon {{ $status === 'approved' ? '' : 'hidden' }}"></i>
                        </div>
                        <div class="dropdown-item px-3 py-2 text-xs font-semibold rounded-xl text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700/70 cursor-pointer flex items-center justify-between transition-colors" data-value="released">
                            <span>Released</span>
                            <i class="fa-solid fa-check text-emerald-600 dark:text-emerald-400 text-[11px] check-icon {{ $status === 'released' ? '' : 'hidden' }}"></i>
                        </div>
                        <div class="dropdown-item px-3 py-2 text-xs font-semibold rounded-xl text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700/70 cursor-pointer flex items-center justify-between transition-colors" data-value="rejected">
                            <span>Rejected</span>
                            <i class="fa-solid fa-check text-emerald-600 dark:text-emerald-400 text-[11px] check-icon {{ $status === 'rejected' ? '' : 'hidden' }}"></i>
                        </div>
                    </div>
                </div>

                <!-- Category Custom Dropdown -->
                <div class="relative custom-dropdown" id="dropdown-wrapper-category">
                    <input type="hidden" id="filter-category" value="{{ $category ?: 'all' }}">
                    <button type="button" id="dropdown-btn-category" class="inline-flex items-center gap-2 px-3.5 py-2.5 text-xs font-semibold border border-slate-200 dark:border-slate-700/80 bg-slate-50/70 dark:bg-slate-900 text-slate-800 dark:text-slate-200 rounded-xl hover:border-emerald-500/50 hover:bg-white dark:hover:bg-slate-800 transition-all cursor-pointer select-none">
                        <i class="fa-solid fa-layer-group text-slate-400 text-xs"></i>
                        <span id="label-category" class="truncate max-w-[110px]">
                            {{ $category && $category !== 'all' ? ucfirst(str_replace('_', ' ', $category)) : 'All Categories' }}
                        </span>
                        <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 transition-transform duration-200 dropdown-arrow"></i>
                    </button>
                    <!-- Floating Menu -->
                    <div id="dropdown-menu-category" class="absolute top-full left-0 mt-1.5 w-48 z-40 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl shadow-xl shadow-slate-900/10 dark:shadow-slate-950/40 p-1.5 space-y-0.5 transform opacity-0 scale-95 pointer-events-none transition-all duration-200 ease-out origin-top-left">
                        <div class="dropdown-item px-3 py-2 text-xs font-semibold rounded-xl text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700/70 cursor-pointer flex items-center justify-between transition-colors" data-value="all">
                            <span>All Categories</span>
                            <i class="fa-solid fa-check text-emerald-600 dark:text-emerald-400 text-[11px] check-icon {{ (!$category || $category === 'all') ? '' : 'hidden' }}"></i>
                        </div>
                        <div class="dropdown-item px-3 py-2 text-xs font-semibold rounded-xl text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700/70 cursor-pointer flex items-center justify-between transition-colors" data-value="regular">
                            <span>Regular</span>
                            <i class="fa-solid fa-check text-emerald-600 dark:text-emerald-400 text-[11px] check-icon {{ $category === 'regular' ? '' : 'hidden' }}"></i>
                        </div>
                        <div class="dropdown-item px-3 py-2 text-xs font-semibold rounded-xl text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700/70 cursor-pointer flex items-center justify-between transition-colors" data-value="commodity">
                            <span>Commodity</span>
                            <i class="fa-solid fa-check text-emerald-600 dark:text-emerald-400 text-[11px] check-icon {{ $category === 'commodity' ? '' : 'hidden' }}"></i>
                        </div>
                        <div class="dropdown-item px-3 py-2 text-xs font-semibold rounded-xl text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700/70 cursor-pointer flex items-center justify-between transition-colors" data-value="special">
                            <span>Special</span>
                            <i class="fa-solid fa-check text-emerald-600 dark:text-emerald-400 text-[11px] check-icon {{ $category === 'special' ? '' : 'hidden' }}"></i>
                        </div>
                        <div class="dropdown-item px-3 py-2 text-xs font-semibold rounded-xl text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700/70 cursor-pointer flex items-center justify-between transition-colors" data-value="seasonal">
                            <span>Seasonal</span>
                            <i class="fa-solid fa-check text-emerald-600 dark:text-emerald-400 text-[11px] check-icon {{ $category === 'seasonal' ? '' : 'hidden' }}"></i>
                        </div>
                        <div class="dropdown-item px-3 py-2 text-xs font-semibold rounded-xl text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700/70 cursor-pointer flex items-center justify-between transition-colors" data-value="bonus_buyout">
                            <span>Bonus Buyout</span>
                            <i class="fa-solid fa-check text-emerald-600 dark:text-emerald-400 text-[11px] check-icon {{ $category === 'bonus_buyout' ? '' : 'hidden' }}"></i>
                        </div>
                        <div class="dropdown-item px-3 py-2 text-xs font-semibold rounded-xl text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700/70 cursor-pointer flex items-center justify-between transition-colors" data-value="emergency">
                            <span>Emergency</span>
                            <i class="fa-solid fa-check text-emerald-600 dark:text-emerald-400 text-[11px] check-icon {{ $category === 'emergency' ? '' : 'hidden' }}"></i>
                        </div>
                    </div>
                </div>

                <!-- Stage Custom Dropdown -->
                <div class="relative custom-dropdown" id="dropdown-wrapper-stage">
                    <input type="hidden" id="filter-stage" value="{{ $stage ?: 'all' }}">
                    <button type="button" id="dropdown-btn-stage" class="inline-flex items-center gap-2 px-3.5 py-2.5 text-xs font-semibold border border-slate-200 dark:border-slate-700/80 bg-slate-50/70 dark:bg-slate-900 text-slate-800 dark:text-slate-200 rounded-xl hover:border-emerald-500/50 hover:bg-white dark:hover:bg-slate-800 transition-all cursor-pointer select-none">
                        <i class="fa-solid fa-bars-progress text-slate-400 text-xs"></i>
                        <span id="label-stage" class="truncate max-w-[120px]">
                            {{ $stage && $stage !== 'all' ? ucwords(str_replace('_', ' ', $stage)) : 'All Stages' }}
                        </span>
                        <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 transition-transform duration-200 dropdown-arrow"></i>
                    </button>
                    <!-- Floating Menu -->
                    <div id="dropdown-menu-stage" class="absolute top-full left-0 mt-1.5 w-52 z-40 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl shadow-xl shadow-slate-900/10 dark:shadow-slate-950/40 p-1.5 space-y-0.5 transform opacity-0 scale-95 pointer-events-none transition-all duration-200 ease-out origin-top-left">
                        <div class="dropdown-item px-3 py-2 text-xs font-semibold rounded-xl text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700/70 cursor-pointer flex items-center justify-between transition-colors" data-value="all">
                            <span>All Stages</span>
                            <i class="fa-solid fa-check text-emerald-600 dark:text-emerald-400 text-[11px] check-icon {{ (!$stage || $stage === 'all') ? '' : 'hidden' }}"></i>
                        </div>
                        <div class="dropdown-item px-3 py-2 text-xs font-semibold rounded-xl text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700/70 cursor-pointer flex items-center justify-between transition-colors" data-value="comakers">
                            <span>Co-Makers</span>
                            <i class="fa-solid fa-check text-emerald-600 dark:text-emerald-400 text-[11px] check-icon {{ $stage === 'comakers' ? '' : 'hidden' }}"></i>
                        </div>
                        <div class="dropdown-item px-3 py-2 text-xs font-semibold rounded-xl text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700/70 cursor-pointer flex items-center justify-between transition-colors" data-value="sako_staff">
                            <span>SAKO Staff Review</span>
                            <i class="fa-solid fa-check text-emerald-600 dark:text-emerald-400 text-[11px] check-icon {{ $stage === 'sako_staff' ? '' : 'hidden' }}"></i>
                        </div>
                        <div class="dropdown-item px-3 py-2 text-xs font-semibold rounded-xl text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700/70 cursor-pointer flex items-center justify-between transition-colors" data-value="hrmd_staff">
                            <span>HRMD Verification</span>
                            <i class="fa-solid fa-check text-emerald-600 dark:text-emerald-400 text-[11px] check-icon {{ $stage === 'hrmd_staff' ? '' : 'hidden' }}"></i>
                        </div>
                        <div class="dropdown-item px-3 py-2 text-xs font-semibold rounded-xl text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700/70 cursor-pointer flex items-center justify-between transition-colors" data-value="credit_committee">
                            <span>Credit Committee</span>
                            <i class="fa-solid fa-check text-emerald-600 dark:text-emerald-400 text-[11px] check-icon {{ $stage === 'credit_committee' ? '' : 'hidden' }}"></i>
                        </div>
                        <div class="dropdown-item px-3 py-2 text-xs font-semibold rounded-xl text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700/70 cursor-pointer flex items-center justify-between transition-colors" data-value="accounting">
                            <span>Accounting</span>
                            <i class="fa-solid fa-check text-emerald-600 dark:text-emerald-400 text-[11px] check-icon {{ $stage === 'accounting' ? '' : 'hidden' }}"></i>
                        </div>
                        <div class="dropdown-item px-3 py-2 text-xs font-semibold rounded-xl text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700/70 cursor-pointer flex items-center justify-between transition-colors" data-value="releasing_officer">
                            <span>Releasing Officer</span>
                            <i class="fa-solid fa-check text-emerald-600 dark:text-emerald-400 text-[11px] check-icon {{ $stage === 'releasing_officer' ? '' : 'hidden' }}"></i>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- Bottom Row: Quick Filter Status Chips, Active Counter, and Reset -->
        <div class="flex flex-wrap items-center justify-between gap-2 pt-3 border-t border-slate-100 dark:border-slate-700/80">
            <div class="flex flex-wrap items-center gap-1.5" id="quick-status-pills">
                <span class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mr-1">Quick Status:</span>
                <button type="button" data-status="all" class="status-chip px-2.5 py-1 rounded-lg text-[10px] font-bold border transition-all cursor-pointer bg-slate-900 text-white border-slate-900 dark:bg-white dark:text-slate-900 dark:border-white shadow-2xs">All</button>
                <button type="button" data-status="pending" class="status-chip px-2.5 py-1 rounded-lg text-[10px] font-bold border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 hover:border-blue-500 hover:text-blue-600 dark:hover:text-blue-400 transition-all cursor-pointer">Pending</button>
                <button type="button" data-status="approved" class="status-chip px-2.5 py-1 rounded-lg text-[10px] font-bold border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 hover:border-emerald-500 hover:text-emerald-600 dark:hover:text-emerald-400 transition-all cursor-pointer">Approved</button>
                <button type="button" data-status="released" class="status-chip px-2.5 py-1 rounded-lg text-[10px] font-bold border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 hover:border-teal-500 hover:text-teal-600 dark:hover:text-teal-400 transition-all cursor-pointer">Released</button>
                <button type="button" data-status="rejected" class="status-chip px-2.5 py-1 rounded-lg text-[10px] font-bold border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 hover:border-rose-500 hover:text-rose-600 dark:hover:text-rose-400 transition-all cursor-pointer">Rejected</button>
            </div>
            
            <div class="flex items-center gap-3">
                <span id="loans-count-badge" class="text-[11px] font-bold px-2.5 py-1 rounded-xl bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 border border-slate-200/80 dark:border-slate-600">
                    {{ $allLoans->total() }} Loans
                </span>
                <button type="button" id="btn-reset-filters" class="text-[11px] font-bold text-slate-500 hover:text-rose-600 dark:text-slate-400 dark:hover:text-rose-400 transition-colors flex items-center gap-1.5 cursor-pointer">
                    <i class="fa-solid fa-arrows-rotate text-[10px]"></i>
                    <span>Reset Filters</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Main Table Card -->
    <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-100 dark:border-slate-700/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-900/50 border-b border-slate-100 dark:border-slate-700/80 text-[10px] font-extrabold text-slate-400 dark:text-slate-500 uppercase tracking-widest">
                        <th class="px-6 py-4.5">Borrower Profile</th>
                        <th class="px-6 py-4.5">Loan Details</th>
                        <th class="px-6 py-4.5">Requested Principal</th>
                        <th class="px-6 py-4.5">Ledger</th>
                        <th class="px-6 py-4.5">Schedule</th>
                        <th class="px-6 py-4.5">Contract Status</th>
                        <th class="px-6 py-4.5">Filing Date</th>
                        <th class="px-6 py-4.5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody id="loans-directory-body" class="divide-y divide-slate-50 dark:divide-slate-700/50 text-xs font-semibold text-slate-700 dark:text-slate-300">
                    @include('admin.partials.loans-directory-rows')
                </tbody>
            </table>
        </div>

        <!-- Pagination Links -->
        <div id="loans-pagination">
            @if($allLoans->hasPages())
                <div class="px-6 py-4 bg-slate-50 dark:bg-slate-900/50 border-t border-slate-100 dark:border-slate-700/80">
                    {{ $allLoans->links() }}
                </div>
            @endif
        </div>
    </div>

</div>

@include('admin.partials.pdf-viewer-modal')

@endsection

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function () {
        // --- PDF PREVIEW MODAL LOGIC ---
        const modalPdf = document.getElementById("modal-pdf-viewer");
        const pdfIframe = document.getElementById("pdf-viewer-frame");
        const pdfLoader = document.getElementById("pdf-viewer-loader");
        const pdfTitle = document.getElementById("pdf-viewer-title");
        const pdfMeta = document.getElementById("pdf-viewer-meta");
        const pdfExternalLink = document.getElementById("pdf-viewer-external-link");
        const btnClosePdf = document.getElementById("btn-close-pdf-viewer");
        const backdropPdf = document.getElementById("pdf-viewer-backdrop");

        function openModal(modalId) {
            const modal = document.getElementById(modalId);
            if (!modal) return;
            const overlay = modal.querySelector(".modal-overlay");
            const container = modal.querySelector(".modal-container");
            modal.classList.remove("hidden");
            modal.classList.add("flex");
            setTimeout(() => {
                if (overlay) {
                    overlay.classList.remove("opacity-0", "pointer-events-none");
                    overlay.classList.add("opacity-100", "pointer-events-auto");
                }
                if (container) {
                    container.classList.remove("scale-95", "opacity-0");
                    container.classList.add("scale-100", "opacity-100");
                }
            }, 50);
        }

        function closeModal(modalId) {
            const modal = document.getElementById(modalId);
            if (!modal) return;
            const overlay = modal.querySelector(".modal-overlay");
            const container = modal.querySelector(".modal-container");
            if (overlay) {
                overlay.classList.add("opacity-0", "pointer-events-none");
                overlay.classList.remove("opacity-100", "pointer-events-auto");
            }
            if (container) {
                container.classList.add("scale-95", "opacity-0");
                container.classList.remove("scale-100", "opacity-100");
            }
            setTimeout(() => {
                modal.classList.add("hidden");
                modal.classList.remove("flex");
            }, 300);
        }

        function openPdfPreview(url, filename, filesize) {
            if (!modalPdf) return;
            if (pdfTitle) pdfTitle.textContent = filename || 'Compliance Document';
            if (pdfMeta) pdfMeta.textContent = (filesize ? filesize + ' • ' : '') + 'Verified PDF Stream';

            // Normalize URL scheme to match page protocol to prevent mixed content
            let targetUrl = url;
            if (window.location.protocol === 'https:' && targetUrl.startsWith('http://')) {
                targetUrl = targetUrl.replace('http://', 'https://');
            }

            if (pdfExternalLink) pdfExternalLink.href = targetUrl;

            if (pdfLoader) pdfLoader.classList.remove("opacity-0", "pointer-events-none");
            if (pdfIframe) {
                pdfIframe.src = targetUrl + '#toolbar=1&navpanes=0';
                pdfIframe.onload = function() {
                    setTimeout(() => {
                        if (pdfLoader) pdfLoader.classList.add("opacity-0", "pointer-events-none");
                    }, 250);
                };
            }
            openModal("modal-pdf-viewer");
        }

        function closePdfPreview() {
            closeModal("modal-pdf-viewer");
            setTimeout(() => {
                if (pdfIframe) pdfIframe.src = "about:blank";
            }, 300);
        }

        if (btnClosePdf) btnClosePdf.addEventListener("click", closePdfPreview);
        if (backdropPdf) backdropPdf.addEventListener("click", closePdfPreview);

        // Escape Key Dismissal
        document.addEventListener("keydown", function(e) {
            if (e.key === "Escape") {
                // Close custom dropdowns
                document.querySelectorAll("[id^='dropdown-menu']").forEach(menu => {
                    menu.classList.add("opacity-0", "scale-95", "pointer-events-none");
                    menu.classList.remove("opacity-100", "scale-100", "pointer-events-auto");
                    const arrow = menu.closest(".custom-dropdown")?.querySelector(".dropdown-arrow");
                    if (arrow) arrow.classList.remove("rotate-180");
                });

                if (modalPdf && !modalPdf.classList.contains("hidden")) {
                    closePdfPreview();
                }
            }
        });

        // Event Delegation for PDF Preview Triggers
        document.addEventListener("click", function(e) {
            const pdfBtn = e.target.closest(".btn-preview-pdf");
            if (pdfBtn) {
                e.preventDefault();
                openPdfPreview(pdfBtn.dataset.url, pdfBtn.dataset.name, pdfBtn.dataset.size);
            }
        });

        // Event Delegation for Delete Confirmation
        document.addEventListener("submit", function(e) {
            const form = e.target.closest(".delete-loan-form");
            if (!form) return;

            e.preventDefault();
            const alertInstance = window.MLSAKOAlert || Swal;

            alertInstance.fire({
                title: 'Delete Loan Application?',
                text: "You are about to completely delete and archive this member's loan application record. This contract cannot be recovered once purged.",
                icon: 'warning',
                iconColor: '#f43f5e',
                showCancelButton: true,
                confirmButtonText: 'Yes, Purge Contract',
                cancelButtonText: 'Cancel',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });

        // ==========================================
        // CUSTOM SMOOTH DROPDOWNS CONTROLLER
        // ==========================================
        document.querySelectorAll(".custom-dropdown").forEach(wrapper => {
            const btn = wrapper.querySelector("button");
            const menu = wrapper.querySelector("[id^='dropdown-menu']");
            const input = wrapper.querySelector("input[type='hidden']");
            const label = wrapper.querySelector("[id^='label-']");
            const arrow = wrapper.querySelector(".dropdown-arrow");

            if (!btn || !menu) return;

            function toggleDropdown(forceState) {
                const isOpen = !menu.classList.contains("pointer-events-none");
                const shouldOpen = forceState !== undefined ? forceState : !isOpen;

                // Close other open dropdowns
                document.querySelectorAll("[id^='dropdown-menu']").forEach(otherMenu => {
                    if (otherMenu !== menu) {
                        otherMenu.classList.add("opacity-0", "scale-95", "pointer-events-none");
                        otherMenu.classList.remove("opacity-100", "scale-100", "pointer-events-auto");
                        const otherArrow = otherMenu.closest(".custom-dropdown")?.querySelector(".dropdown-arrow");
                        if (otherArrow) otherArrow.classList.remove("rotate-180");
                    }
                });

                if (shouldOpen) {
                    menu.classList.remove("opacity-0", "scale-95", "pointer-events-none");
                    menu.classList.add("opacity-100", "scale-100", "pointer-events-auto");
                    if (arrow) arrow.classList.add("rotate-180");
                } else {
                    menu.classList.add("opacity-0", "scale-95", "pointer-events-none");
                    menu.classList.remove("opacity-100", "scale-100", "pointer-events-auto");
                    if (arrow) arrow.classList.remove("rotate-180");
                }
            }

            btn.addEventListener("click", function(e) {
                e.stopPropagation();
                toggleDropdown();
            });

            menu.querySelectorAll(".dropdown-item").forEach(item => {
                item.addEventListener("click", function(e) {
                    e.stopPropagation();
                    const val = this.getAttribute("data-value");
                    const itemText = this.querySelector("span").textContent;

                    if (input) input.value = val;
                    if (label) label.textContent = itemText;

                    // Update checkmarks
                    menu.querySelectorAll(".dropdown-item").forEach(i => {
                        const check = i.querySelector(".check-icon");
                        if (check) {
                            check.classList.toggle("hidden", i !== item);
                        }
                    });

                    toggleDropdown(false);

                    // Sync status chips if status was selected
                    if (input && input.id === "filter-status") {
                        updateStatusChipStyles(val);
                    }

                    fetchLoansData();
                });
            });
        });

        // Click outside closes any active dropdown
        document.addEventListener("click", function(e) {
            if (!e.target.closest(".custom-dropdown")) {
                document.querySelectorAll("[id^='dropdown-menu']").forEach(menu => {
                    menu.classList.add("opacity-0", "scale-95", "pointer-events-none");
                    menu.classList.remove("opacity-100", "scale-100", "pointer-events-auto");
                    const arrow = menu.closest(".custom-dropdown")?.querySelector(".dropdown-arrow");
                    if (arrow) arrow.classList.remove("rotate-180");
                });
            }
        });

        // ==========================================
        // REAL-TIME SEARCH & FILTER ENGINE
        // ==========================================
        const searchInput = document.getElementById("ajax-search");
        const btnClearSearch = document.getElementById("btn-clear-search");
        const statusFilter = document.getElementById("filter-status");
        const categoryFilter = document.getElementById("filter-category");
        const stageFilter = document.getElementById("filter-stage");
        const loansTableBody = document.getElementById("loans-directory-body");
        const loansPagination = document.getElementById("loans-pagination");
        const loansCountBadge = document.getElementById("loans-count-badge");
        const statusChips = document.querySelectorAll(".status-chip");
        const btnResetFilters = document.getElementById("btn-reset-filters");
        const searchSpinner = document.getElementById("search-spinner");
        const searchIcon = document.getElementById("search-icon");

        let debounceTimer = null;
        let searchAbort = null;

        function updateStatusChipStyles(activeStatus) {
            statusChips.forEach(chip => {
                if (chip.getAttribute("data-status") === activeStatus) {
                    chip.className = "status-chip px-2.5 py-1 rounded-lg text-[10px] font-bold border transition-all cursor-pointer bg-slate-900 text-white border-slate-900 dark:bg-white dark:text-slate-900 dark:border-white shadow-2xs";
                } else {
                    chip.className = "status-chip px-2.5 py-1 rounded-lg text-[10px] font-bold border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 hover:border-emerald-500 hover:text-emerald-600 dark:hover:text-emerald-400 transition-all cursor-pointer";
                }
            });
        }

        function fetchLoansData() {
            const search = searchInput ? searchInput.value.trim() : "";
            const status = statusFilter ? statusFilter.value : "all";
            const category = categoryFilter ? categoryFilter.value : "all";
            const stage = stageFilter ? stageFilter.value : "all";

            // Toggle clear button
            if (btnClearSearch) {
                btnClearSearch.classList.toggle("hidden", search.length === 0);
            }

            // Visual feedback
            if (searchSpinner) searchSpinner.classList.remove("hidden");
            if (searchIcon) searchIcon.classList.add("text-emerald-500");

            if (loansTableBody) loansTableBody.classList.add("opacity-40", "transition-opacity", "duration-200");

            if (searchAbort) searchAbort.abort();
            searchAbort = new AbortController();

            const url = `{{ route('admin.loans') }}?search=${encodeURIComponent(search)}&status=${status}&category=${category}&stage=${stage}`;

            fetch(url, {
                signal: searchAbort.signal,
                headers: {
                    "X-Requested-With": "XMLHttpRequest"
                }
            })
            .then(res => res.json())
            .then(data => {
                if (loansTableBody) loansTableBody.innerHTML = data.loans_html;
                if (loansPagination) loansPagination.innerHTML = data.pagination_html;
                if (loansCountBadge) loansCountBadge.textContent = `${data.total_count} Loan${data.total_count === 1 ? '' : 's'}`;
            })
            .catch(err => {
                if (err.name !== "AbortError") {
                    console.error("Filter error:", err);
                }
            })
            .finally(() => {
                if (searchSpinner) searchSpinner.classList.add("hidden");
                if (searchIcon && search.length === 0) searchIcon.classList.remove("text-emerald-500");
                if (loansTableBody) loansTableBody.classList.remove("opacity-40");
            });
        }

        // Live search with debounce
        if (searchInput) {
            searchInput.addEventListener("input", function() {
                if (searchSpinner) searchSpinner.classList.remove("hidden");
                if (searchIcon) searchIcon.classList.add("text-emerald-500");

                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(fetchLoansData, 220);
            });

            searchInput.addEventListener("focus", function() {
                if (searchIcon) searchIcon.classList.add("text-emerald-500");
            });

            searchInput.addEventListener("blur", function() {
                if (this.value.trim().length === 0 && searchIcon) {
                    searchIcon.classList.remove("text-emerald-500");
                }
            });
        }

        // Clear search input
        if (btnClearSearch) {
            btnClearSearch.addEventListener("click", function() {
                if (searchInput) {
                    searchInput.value = "";
                    searchInput.focus();
                }
                btnClearSearch.classList.add("hidden");
                fetchLoansData();
            });
        }

        // Quick Status Chip Clicks
        statusChips.forEach(chip => {
            chip.addEventListener("click", function() {
                const selectedStatus = this.getAttribute("data-status");
                const sInput = document.getElementById("filter-status");
                const sLabel = document.getElementById("label-status");

                if (sInput) sInput.value = selectedStatus;
                if (sLabel) {
                    const matchItem = document.querySelector(`#dropdown-menu-status .dropdown-item[data-value="${selectedStatus}"]`);
                    sLabel.textContent = matchItem ? matchItem.querySelector("span").textContent : "All Statuses";
                }

                document.querySelectorAll("#dropdown-menu-status .dropdown-item").forEach(i => {
                    const check = i.querySelector(".check-icon");
                    if (check) check.classList.toggle("hidden", i.getAttribute("data-value") !== selectedStatus);
                });

                updateStatusChipStyles(selectedStatus);
                fetchLoansData();
            });
        });

        // Reset Filters Button
        if (btnResetFilters) {
            btnResetFilters.addEventListener("click", function() {
                if (searchInput) searchInput.value = "";
                if (btnClearSearch) btnClearSearch.classList.add("hidden");

                // Reset Status
                const stInput = document.getElementById("filter-status");
                const stLabel = document.getElementById("label-status");
                if (stInput) stInput.value = "all";
                if (stLabel) stLabel.textContent = "All Statuses";
                document.querySelectorAll("#dropdown-menu-status .dropdown-item").forEach(i => {
                    const check = i.querySelector(".check-icon");
                    if (check) check.classList.toggle("hidden", i.getAttribute("data-value") !== "all");
                });

                // Reset Category
                const cInput = document.getElementById("filter-category");
                const cLabel = document.getElementById("label-category");
                if (cInput) cInput.value = "all";
                if (cLabel) cLabel.textContent = "All Categories";
                document.querySelectorAll("#dropdown-menu-category .dropdown-item").forEach(i => {
                    const check = i.querySelector(".check-icon");
                    if (check) check.classList.toggle("hidden", i.getAttribute("data-value") !== "all");
                });

                // Reset Stage
                const sInput = document.getElementById("filter-stage");
                const sLabel = document.getElementById("label-stage");
                if (sInput) sInput.value = "all";
                if (sLabel) sLabel.textContent = "All Stages";
                document.querySelectorAll("#dropdown-menu-stage .dropdown-item").forEach(i => {
                    const check = i.querySelector(".check-icon");
                    if (check) check.classList.toggle("hidden", i.getAttribute("data-value") !== "all");
                });

                updateStatusChipStyles("all");
                fetchLoansData();
            });
        }
    });
</script>
@endpush
