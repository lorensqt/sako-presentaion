@extends('layouts.admin')

@section('title', 'Loan Approvals - Sako Cooperative')

@section('header')
<div>
    <h1 class="text-2xl font-extrabold text-slate-900 dark:text-slate-100 tracking-tight serif-font">Loan Approvals Board</h1>
    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 font-semibold">Oversee, inspect, and approve cooperative loan applications through sequential organizational review stages.</p>
</div>
@endsection

@section('content')
<div class="space-y-6 animate-fade-in">

    <!-- My Active Roles & Workloads Hub -->
    <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-100 dark:border-slate-700/80 shadow-sm p-6 space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 pb-3 border-b border-slate-100 dark:border-slate-700/60">
            <div>
                <h3 class="text-xs font-black text-slate-900 dark:text-white tracking-wider uppercase flex items-center gap-1.5">
                    My Active Decision Workspaces
                </h3>
                <p class="text-[10px] text-slate-505 dark:text-slate-400 font-semibold mt-0.5">Dynamic queues assigned to your profile roles</p>
            </div>
            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-emerald-50 dark:bg-emerald-950/20 text-emerald-800 dark:text-emerald-400 text-[10px] font-extrabold border border-emerald-100/40 dark:border-emerald-800/20">
                🔒 Cryptographically Signed Workstation
            </span>
        </div>

        @php
            $inboxGrouped = $myInboxLoans->groupBy('current_stage');
            $allStagesConfig = [
                'sako_staff' => [
                    'name' => 'SAKO Staff Review',
                    'icon_url' => 'https://img.icons8.com/?size=100&id=8NGo_ebaZB64&format=png&color=000000',
                    'color' => 'sky',
                ],
                'hrmd_staff' => [
                    'name' => 'HRMD Verification',
                    'icon_url' => 'https://img.icons8.com/?size=100&id=MkDL506zTrpE&format=png&color=000000',
                    'color' => 'rose',
                ],
                'credit_committee' => [
                    'name' => 'Credit Committee',
                    'icon_url' => 'https://img.icons8.com/?size=100&id=jyDP2XjBiXdD&format=png&color=000000',
                    'color' => 'indigo',
                ],
                'accounting' => [
                    'name' => 'Accounting Computations',
                    'icon_url' => 'https://img.icons8.com/?size=100&id=22462&format=png&color=000000',
                    'color' => 'amber',
                ],
                'releasing_officer' => [
                    'name' => 'Releasing Officer',
                    'icon_url' => 'https://img.icons8.com/?size=100&id=HYdHmi0wO7zZ&format=png&color=000000',
                    'color' => 'teal',
                ],
            ];
        @endphp

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3.5">
            @foreach($allStagesConfig as $slug => $config)
                @php
                    $isActiveUserRole = in_array($slug, $myGroupSlugs);
                    $pendingCount = isset($inboxGrouped[$slug]) ? $inboxGrouped[$slug]->count() : 0;
                @endphp

                @if($isActiveUserRole)
                    <!-- Active Role card -->
                    <div class="relative bg-emerald-500/[0.02] dark:bg-emerald-500/[0.04] border border-emerald-500/25 dark:border-emerald-500/15 rounded-2xl p-4 flex flex-col justify-between min-h-[105px] transition-all hover:scale-[1.01] shadow-sm">
                        <div class="flex items-start justify-between">
                            <img src="{{ $config['icon_url'] }}" alt="{{ $config['name'] }}" class="w-6.5 h-6.5 object-contain">
                            <span class="inline-flex items-center px-1.5 py-0.5 rounded-md bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 text-[8px] font-black tracking-wider uppercase border border-emerald-500/10">Active</span>
                        </div>
                        <div class="mt-2.5">
                            <span class="text-[10px] font-black text-slate-700 dark:text-slate-300 block truncate leading-tight">{{ $config['name'] }}</span>
                            @if($pendingCount > 0)
                                <span class="text-[10.5px] font-black text-rose-600 dark:text-rose-400 mt-1 block flex items-center gap-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500 animate-pulse"></span>
                                    {{ $pendingCount }} Pending Approval{{ $pendingCount > 1 ? 's' : '' }}
                                </span>
                            @else
                                <span class="text-[10.5px] font-black text-emerald-600 dark:text-emerald-400 mt-1 block">
                                    ✓ Queue Cleared
                                </span>
                            @endif
                        </div>
                    </div>
                @else
                    <!-- Inactive Role card -->
                    <div class="relative bg-slate-50/50 dark:bg-slate-900/30 border border-slate-100 dark:border-slate-800 rounded-2xl p-4 flex flex-col justify-between min-h-[105px] opacity-50">
                        <div class="flex items-start justify-between">
                            <img src="{{ $config['icon_url'] }}" alt="{{ $config['name'] }}" class="w-6.5 h-6.5 object-contain filter grayscale opacity-45">
                            <span class="inline-flex items-center px-1.5 py-0.5 rounded-md bg-slate-100 dark:bg-slate-800 text-slate-400 dark:text-slate-500 text-[8px] font-extrabold uppercase border border-slate-200/50 dark:border-slate-700/50">Restricted</span>
                        </div>
                        <div class="mt-2.5">
                            <span class="text-[10px] font-black text-slate-400 dark:text-slate-500 block truncate leading-tight">{{ $config['name'] }}</span>
                            <span class="text-[10.5px] font-semibold text-slate-400 dark:text-slate-500 mt-1 block">Not in My Roles</span>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    </div>

    <!-- Ledger Tabs Wrapper -->
    <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-100 dark:border-slate-700/80 shadow-sm overflow-hidden flex flex-col">
        
        <!-- Tab Headers (Segmented Control style) -->
        <div class="flex border-b border-slate-100 dark:border-slate-700/80 bg-slate-50 dark:bg-slate-900/40 p-2 gap-2 overflow-x-auto">
            <button id="tab-inbox" class="tab-btn active px-4 py-2.5 rounded-xl font-bold text-xs transition-all duration-200 text-emerald-800 dark:text-emerald-400 bg-white dark:bg-slate-700 shadow-sm border border-slate-100 dark:border-slate-700 whitespace-nowrap cursor-pointer flex items-center gap-1.5 hover:scale-[1.01]">
                <span>📥</span> Awaiting My Group's Action ({{ $myInboxLoans->count() }})
            </button>
            <button id="tab-all" class="tab-btn px-4 py-2.5 rounded-xl font-bold text-xs text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200 hover:bg-white/60 dark:hover:bg-slate-700/40 transition-all duration-200 whitespace-nowrap cursor-pointer flex items-center gap-1.5 hover:scale-[1.01]">
                <span>🌐</span> All Cooperative Pipelines ({{ $allLoans->total() }})
            </button>
        </div>

        <!-- TAB CONTENT: MY GROUP INBOX -->
        <div id="content-inbox" class="tab-panel transition-opacity duration-300 animate-fade-in">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-slate-900/50 border-b border-slate-100 dark:border-slate-700/80 text-[10px] font-extrabold text-slate-400 dark:text-slate-500 uppercase tracking-widest">
                            <th class="px-6 py-4.5">Borrower Profile</th>
                            <th class="px-6 py-4.5">Loan Type</th>
                            <th class="px-6 py-4.5">Requested Amount</th>
                            <th class="px-6 py-4.5">Awaiting Verification</th>
                            <th class="px-6 py-4.5">Submitted Date</th>
                            <th class="px-6 py-4.5 text-right">Evaluation</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50 dark:divide-slate-700/50 text-xs font-medium text-slate-700 dark:text-slate-300">
                        @forelse($myInboxLoans as $loan)
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-900/30 transition-colors">
                                <td class="px-6 py-4 flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full bg-slate-100 dark:bg-slate-900 flex items-center justify-center font-bold text-slate-600 dark:text-slate-300 text-xs border border-slate-200/40 dark:border-slate-700/40">
                                        {{ strtoupper(substr($loan->borrower->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-slate-950 dark:text-slate-100">{{ $loan->borrower->name }}</h4>
                                        <p class="text-[10px] text-slate-400 dark:text-slate-500 font-semibold mt-0.5">ID: {{ $loan->borrower->company_id ?: 'N/A' }}</p>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="font-bold text-slate-800 dark:text-slate-200 block">
                                        {{ $loan->loan ? $loan->loan->name : ucwords(str_replace('_', ' ', $loan->loan_type)) }}
                                    </span>
                                    <span class="text-[9px] font-extrabold uppercase tracking-wider text-emerald-600 dark:text-emerald-450 block mt-0.5">{{ $loan->loan_category }} Loan</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="font-bold font-mono text-slate-800 dark:text-slate-100 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 px-2.5 py-1 rounded-xl text-xs shadow-sm">
                                        ₱{{ number_format($loan->requested_amount, 2) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-blue-50 dark:bg-blue-950/30 text-blue-700 dark:text-blue-400 border border-blue-100/40 dark:border-blue-900/30 uppercase tracking-wider">
                                        {{ ucwords(str_replace('_', ' ', $loan->current_stage)) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 font-semibold text-slate-500 dark:text-slate-400">
                                    {{ $loan->created_at->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="inline-flex items-center justify-end gap-2">
                                        <button class="btn-review-loan bg-emerald-600 hover:bg-emerald-700 dark:bg-emerald-650 dark:hover:bg-emerald-600 text-white font-bold text-xs px-3.5 py-2 rounded-xl shadow-md shadow-emerald-600/10 hover:shadow-emerald-600/20 hover:-translate-y-0.5 transition-all duration-200 cursor-pointer" data-loan="{{ json_encode([
                                            'id' => $loan->id,
                                            'borrower_name' => $loan->borrower->name,
                                            'borrower_email' => $loan->borrower->email,
                                            'borrower_company_id' => $loan->borrower->company_id,
                                            'borrower_address' => $loan->borrower->address,
                                            'category' => ucwords($loan->loan_category),
                                            'type_name' => $loan->loan ? $loan->loan->name : ucwords(str_replace('_', ' ', $loan->loan_type)),
                                            'amount' => '₱' . number_format($loan->requested_amount, 2),
                                            'term' => ($loan->form_data['term_months'] ?? $loan->term_months ?? 'N/A') . ' Months',
                                            'current_stage' => $loan->current_stage,
                                            'form_data' => $loan->form_data,
                                            'workflow_steps' => $loan->workflow_steps
                                        ]) }}">
                                            Review Application
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-16 text-center text-slate-400 dark:text-slate-500">
                                    <div class="w-12 h-12 rounded-full bg-slate-50 dark:bg-slate-900 border border-slate-100 dark:border-slate-800 flex items-center justify-center mx-auto mb-3 text-slate-400 dark:text-slate-500">
                                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                    <p class="text-xs font-bold">Your group inbox is completely clear!</p>
                                    <p class="text-2xs text-slate-400 dark:text-slate-500 mt-1 font-semibold">No pending cooperative loans currently require your signature.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- TAB CONTENT: ALL COOPERATIVE PIPELINES -->
        <div id="content-all" class="tab-panel hidden transition-opacity duration-300">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-slate-900/50 border-b border-slate-100 dark:border-slate-700/80 text-[10px] font-extrabold text-slate-400 dark:text-slate-500 uppercase tracking-widest">
                            <th class="px-6 py-4.5">Borrower Profile</th>
                            <th class="px-6 py-4.5">Loan Type</th>
                            <th class="px-6 py-4.5">Requested Amount</th>
                            <th class="px-6 py-4.5">Current Stage</th>
                            <th class="px-6 py-4.5">Status</th>
                            <th class="px-6 py-4.5">Submitted Date</th>
                            <th class="px-6 py-4.5 text-right">Details</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50 dark:divide-slate-700/50 text-xs font-medium text-slate-700 dark:text-slate-300">
                        @forelse($allLoans as $loan)
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-900/30 transition-colors">
                                <td class="px-6 py-4 flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full bg-slate-100 dark:bg-slate-900 flex items-center justify-center font-bold text-slate-600 dark:text-slate-300 text-xs border border-slate-200/40 dark:border-slate-700/40">
                                        {{ strtoupper(substr($loan->borrower->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-slate-955 dark:text-slate-100">{{ $loan->borrower->name }}</h4>
                                        <p class="text-[10px] text-slate-400 dark:text-slate-500 font-semibold mt-0.5">ID: {{ $loan->borrower->company_id ?: 'N/A' }}</p>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="font-bold text-slate-800 dark:text-slate-200 block">
                                        {{ $loan->loan ? $loan->loan->name : ucwords(str_replace('_', ' ', $loan->loan_type)) }}
                                    </span>
                                    <span class="text-[9px] font-extrabold uppercase tracking-wider text-emerald-600 dark:text-emerald-455 block mt-0.5">{{ $loan->loan_category }} Loan</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="font-bold font-mono text-slate-800 dark:text-slate-100 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 px-2.5 py-1 rounded-xl text-xs shadow-sm">
                                        ₱{{ number_format($loan->requested_amount, 2) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    @if($loan->status === 'approved' || $loan->status === 'released')
                                        <span class="text-emerald-600 dark:text-emerald-400 font-extrabold flex items-center gap-1.5 text-[10px] uppercase tracking-wider">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                            Released
                                        </span>
                                    @elseif($loan->status === 'rejected')
                                        <span class="text-rose-600 dark:text-rose-400 font-extrabold text-[10px] uppercase tracking-wider flex items-center gap-1.5">
                                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                            Rejected
                                        </span>
                                    @else
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-blue-50 dark:bg-blue-950/30 text-blue-700 dark:text-blue-400 border border-blue-100/40 dark:border-blue-900/30 uppercase tracking-wider">
                                            {{ ucwords(str_replace('_', ' ', $loan->current_stage)) }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @if($loan->status === 'approved' || $loan->status === 'released')
                                        <span class="px-2.5 py-1 rounded-full text-[9px] font-extrabold bg-emerald-50 dark:bg-emerald-950/30 text-emerald-700 dark:text-emerald-400 border border-emerald-100/40 dark:border-emerald-900/30 uppercase tracking-wider">Approved</span>
                                    @elseif($loan->status === 'rejected')
                                        <span class="px-2.5 py-1 rounded-full text-[9px] font-extrabold bg-rose-50 dark:bg-rose-950/30 text-rose-700 dark:text-rose-455 border border-rose-100/20 dark:border-rose-900/20 uppercase tracking-wider">Rejected</span>
                                    @else
                                        <span class="px-2.5 py-1 rounded-full text-[9px] font-extrabold bg-blue-50 dark:bg-blue-950/30 text-blue-700 dark:text-blue-455 border border-blue-100/60 dark:border-blue-900/20 uppercase tracking-wider">Pending</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 font-semibold text-slate-500 dark:text-slate-400">
                                    {{ $loan->created_at->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="inline-flex items-center justify-end gap-2">
                                        <button class="btn-review-loan bg-slate-50 dark:bg-slate-900 hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 hover:text-slate-955 dark:hover:text-white border border-slate-200 dark:border-slate-700 font-bold text-xs px-3.5 py-2 rounded-xl hover:shadow-sm transition-all duration-200 cursor-pointer" data-loan="{{ json_encode([
                                            'id' => $loan->id,
                                            'borrower_name' => $loan->borrower->name,
                                            'borrower_email' => $loan->borrower->email,
                                            'borrower_company_id' => $loan->borrower->company_id,
                                            'borrower_address' => $loan->borrower->address,
                                            'category' => ucwords($loan->loan_category),
                                            'type_name' => $loan->loan ? $loan->loan->name : ucwords(str_replace('_', ' ', $loan->loan_type)),
                                            'amount' => '₱' . number_format($loan->requested_amount, 2),
                                            'term' => ($loan->form_data['term_months'] ?? $loan->term_months ?? 'N/A') . ' Months',
                                            'current_stage' => $loan->current_stage,
                                            'form_data' => $loan->form_data,
                                            'workflow_steps' => $loan->workflow_steps
                                        ]) }}">
                                            View Full Ledger
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-16 text-center text-slate-400 dark:text-slate-500">
                                    <p class="text-xs font-bold">No applications found.</p>
                                    <p class="text-2xs text-slate-400 dark:text-slate-500 mt-1 font-semibold">No loan files exist in the cooperative database yet.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination Links -->
            @if($allLoans->hasPages())
                <div class="px-6 py-4 bg-slate-50 dark:bg-slate-900/50 border-t border-slate-100 dark:border-slate-700/80">
                    {{ $allLoans->links() }}
                </div>
            @endif
        </div>

    </div>
</div>

@include('admin.partials.review-loan-modal')

@endsection

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function () {
        
        // Parse active roles slugs
        const myGroupSlugs = @json($myGroupSlugs);

        // Tab Switching logic
        const tabInbox = document.getElementById("tab-inbox");
        const tabAll = document.getElementById("tab-all");
        const panelInbox = document.getElementById("content-inbox");
        const panelAll = document.getElementById("content-all");

        tabInbox.addEventListener("click", function() {
            setTabActive(this, panelInbox);
            setTabInactive(tabAll, panelAll);
        });

        tabAll.addEventListener("click", function() {
            setTabActive(this, panelAll);
            setTabInactive(tabInbox, panelInbox);
        });

        function setTabActive(btn, panel) {
            btn.classList.add("active", "text-emerald-800", "dark:text-emerald-400", "bg-white", "dark:bg-slate-700", "shadow-sm", "border", "border-slate-100", "dark:border-slate-700");
            btn.classList.remove("text-slate-500", "dark:text-slate-400", "hover:text-slate-900", "dark:hover:text-slate-200", "hover:bg-white/60", "dark:hover:bg-slate-700/40");
            panel.classList.remove("hidden");
            // Trigger animation
            setTimeout(() => { panel.classList.add("opacity-100"); }, 50);
        }

        function setTabInactive(btn, panel) {
            btn.classList.remove("active", "text-emerald-800", "dark:text-emerald-400", "bg-white", "dark:bg-slate-700", "shadow-sm", "border", "border-slate-100", "dark:border-slate-700");
            btn.classList.add("text-slate-500", "dark:text-slate-400", "hover:text-slate-900", "dark:hover:text-slate-200", "hover:bg-white/60", "dark:hover:bg-slate-700/40");
            panel.classList.add("hidden");
            panel.classList.remove("opacity-100");
        }

        function openModal(modalId) {
            const modal = document.getElementById(modalId);
            const overlay = modal.querySelector(".modal-overlay");
            const container = modal.querySelector(".modal-container");
            
            modal.classList.remove("hidden");
            modal.classList.add("flex");
            
            // Allow browser layout pass to register classes
            setTimeout(() => {
                if (overlay) {
                    overlay.classList.remove("opacity-0", "pointer-events-none");
                    overlay.classList.add("opacity-100", "pointer-events-auto");
                }
                
                if (container) {
                    // Slide fullscreen modal in beautifully (scale + opacity fade)
                    container.classList.remove("scale-95", "opacity-0");
                    container.classList.add("scale-100", "opacity-100");
                }
            }, 50);
        }

        function closeModal(modalId) {
            const modal = document.getElementById(modalId);
            const overlay = modal.querySelector(".modal-overlay");
            const container = modal.querySelector(".modal-container");
            
            if (overlay) {
                overlay.classList.add("opacity-0", "pointer-events-none");
                overlay.classList.remove("opacity-100", "pointer-events-auto");
            }
            
            if (container) {
                // Return to scale 95 and fade out
                container.classList.add("scale-95", "opacity-0");
                container.classList.remove("scale-100", "opacity-100");
            }
            
            // Hide the wrapper after animation completes
            setTimeout(() => {
                modal.classList.add("hidden");
                modal.classList.remove("flex");
            }, 300);
        }

        // Setup click listeners for cancel/close controls
        document.querySelectorAll(".modal-close, .modal-overlay").forEach(btn => {
            btn.addEventListener("click", function() {
                const modal = this.closest('[id^="modal-"]');
                if (modal) closeModal(modal.id);
            });
        });

        // Review Button Trigger
        document.querySelectorAll(".btn-review-loan").forEach(btn => {
            btn.addEventListener("click", function() {
                const loan = JSON.parse(this.getAttribute("data-loan"));
                
                // Populate Borrower dossier
                document.getElementById("view-borrower-name").textContent = loan.borrower_name;
                document.getElementById("view-borrower-email").textContent = loan.borrower_email;
                document.getElementById("view-company-id").textContent = loan.borrower_company_id || 'N/A';
                document.getElementById("view-address").textContent = loan.borrower_address || 'N/A';
                document.getElementById("view-address").title = loan.borrower_address || 'N/A';
                document.getElementById("view-borrower-avatar").textContent = (loan.borrower_name ? loan.borrower_name.substring(0, 1).toUpperCase() : '--');

                // Populate loan details
                document.getElementById("view-amount").textContent = loan.amount;
                document.getElementById("view-type-name").textContent = loan.type_name + " (" + loan.category + ")";
                document.getElementById("view-term").textContent = loan.term;
                document.getElementById("view-member-remarks").textContent = loan.form_data.member_remarks || 'No special remarks provided.';

                // Build Complete Dynamic Stepper Vertical Timeline
                const timelineContainer = document.getElementById("view-history-timeline");
                timelineContainer.innerHTML = "";

                if (!loan.workflow_steps || loan.workflow_steps.length === 0) {
                    timelineContainer.innerHTML = '<div class="text-center w-full py-12 text-slate-400 dark:text-slate-500 font-semibold italic text-xs">This application has no sequential workflow path.</div>';
                } else {
                    loan.workflow_steps.forEach((step, idx) => {
                        // Icon and BG based on status
                        let iconHtml = "";
                        let stepBgClass = "";
                        let stepBorderClass = "";
                        let labelColorClass = "";
                        let statusBadgeHtml = "";
                        let detailsHtml = "";

                        switch (step.status) {
                            case 'approved':
                            case 'completed':
                                iconHtml = `<svg class="w-4 h-4 text-emerald-600 dark:text-emerald-450" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>`;
                                stepBgClass = "bg-emerald-50 dark:bg-emerald-950/25 text-emerald-600 dark:text-emerald-450";
                                stepBorderClass = "border-emerald-100 dark:border-emerald-900/30";
                                labelColorClass = "text-slate-900 dark:text-slate-100 font-bold";
                                statusBadgeHtml = `<span class="text-[9px] font-extrabold uppercase tracking-wider px-2 py-0.5 rounded bg-emerald-50 dark:bg-emerald-950/45 text-emerald-700 dark:text-emerald-450 border border-emerald-100/60 dark:border-emerald-900/20 shadow-3xs">Approved</span>`;
                                detailsHtml = `
                                    <p class="text-[10px] text-slate-400 dark:text-slate-500 mt-0.5">By: <span class="font-bold text-slate-700 dark:text-slate-300">${step.actor || 'System/Staff'}</span></p>
                                    ${step.remarks ? `<p class="text-[10.5px] text-slate-600 dark:text-slate-350 italic mt-1.5 p-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl shadow-3xs font-sans leading-relaxed">"${step.remarks}"</p>` : ''}
                                    <p class="text-[9px] text-slate-400 dark:text-slate-500 mt-1.5 text-right font-semibold font-mono">${step.date}</p>
                                `;
                                break;
                            case 'rejected':
                                iconHtml = `<svg class="w-4 h-4 text-rose-600 dark:text-rose-455" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>`;
                                stepBgClass = "bg-rose-50 dark:bg-rose-950/25 text-rose-600 dark:text-rose-450 border border-rose-100/30 dark:border-rose-900/20";
                                stepBorderClass = "border-rose-100 dark:border-rose-900/30";
                                labelColorClass = "text-rose-750 dark:text-rose-400 font-bold";
                                statusBadgeHtml = `<span class="text-[9px] font-extrabold uppercase tracking-wider px-2 py-0.5 rounded bg-rose-50 dark:bg-rose-950/45 text-rose-700 dark:text-rose-400 border border-rose-100/60 dark:border-rose-900/20 shadow-3xs">Rejected</span>`;
                                detailsHtml = `
                                    <p class="text-[10px] text-slate-400 dark:text-slate-500 mt-0.5">By: <span class="font-bold text-slate-700 dark:text-slate-300">${step.actor || 'System/Staff'}</span></p>
                                    ${step.remarks ? `<p class="text-[10.5px] text-rose-600 dark:text-rose-400/80 italic mt-1.5 p-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl shadow-3xs font-sans leading-relaxed">"${step.remarks}"</p>` : ''}
                                    <p class="text-[9px] text-slate-400 dark:text-slate-500 mt-1.5 text-right font-semibold font-mono">${step.date}</p>
                                `;
                                break;
                            case 'skipped':
                                iconHtml = `<svg class="w-3.5 h-3.5 text-slate-400 dark:text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>`;
                                stepBgClass = "bg-slate-100 dark:bg-slate-800/40 text-slate-400 dark:text-slate-500";
                                stepBorderClass = "border-slate-200 dark:border-slate-800";
                                labelColorClass = "text-slate-400 dark:text-slate-500 line-through font-medium";
                                statusBadgeHtml = `<span class="text-[9px] font-bold uppercase tracking-wider px-2 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-550 dark:text-slate-400 border border-slate-200/40 dark:border-slate-700/50 shadow-3xs">Skipped</span>`;
                                detailsHtml = `<p class="text-[10px] text-slate-400 dark:text-slate-550 mt-1 italic leading-relaxed">Verification stage automatically skipped per rules.</p>`;
                                break;
                            case 'current':
                                iconHtml = `<span class="flex h-2.5 w-2.5 relative"><span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-sky-450 opacity-75"></span><span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-sky-500"></span></span>`;
                                stepBgClass = "bg-sky-50 dark:bg-sky-950/25 border-2 border-sky-400/80 text-sky-500";
                                stepBorderClass = "border-sky-200 dark:border-sky-850";
                                labelColorClass = "text-slate-900 dark:text-slate-100 font-extrabold";
                                statusBadgeHtml = `<span class="text-[9px] font-extrabold uppercase tracking-wider px-2 py-0.5 rounded bg-sky-50 dark:bg-sky-950/45 text-sky-700 dark:text-sky-400 border border-sky-100/60 dark:border-sky-900/20 shadow-3xs">Active Stage</span>`;
                                detailsHtml = `<p class="text-[10px] text-slate-505 dark:text-slate-400 mt-1 font-semibold leading-relaxed flex items-center gap-1"><span class="inline-block w-1.5 h-1.5 rounded-full bg-sky-500 animate-pulse"></span> Awaiting specialist sign-off.</p>`;
                                break;
                            case 'cancelled':
                                iconHtml = `<svg class="w-3.5 h-3.5 text-slate-400 dark:text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>`;
                                stepBgClass = "bg-rose-50/10 dark:bg-slate-900/40 text-rose-350 dark:text-slate-600";
                                stepBorderClass = "border-rose-100/30 dark:border-slate-800 border-dashed";
                                labelColorClass = "text-slate-400 dark:text-slate-505 font-medium";
                                statusBadgeHtml = `<span class="text-[9px] font-bold uppercase tracking-wider px-2 py-0.5 rounded bg-rose-50/15 dark:bg-slate-900 text-rose-955/40 dark:text-slate-600 border border-rose-100/20 dark:border-rose-800 shadow-3xs">Cancelled</span>`;
                                detailsHtml = `<p class="text-[10px] text-slate-400 dark:text-slate-500 mt-1 italic font-sans">Workflow terminated prior to this stage.</p>`;
                                break;
                            case 'pending':
                            default:
                                iconHtml = `<span class="w-2 h-2 rounded-full bg-slate-300 dark:bg-slate-600"></span>`;
                                stepBgClass = "bg-slate-50/30 dark:bg-slate-900/20 text-slate-400 dark:text-slate-505";
                                stepBorderClass = "border-slate-100 dark:border-slate-800 border-dashed";
                                labelColorClass = "text-slate-400 dark:text-slate-555 font-semibold";
                                statusBadgeHtml = `<span class="text-[9px] font-bold uppercase tracking-wider px-2 py-0.5 rounded bg-slate-50 dark:bg-slate-900/50 text-slate-400 dark:text-slate-505 border border-slate-100 dark:border-slate-800/60 shadow-3xs">Pending</span>`;
                                detailsHtml = `<p class="text-[10px] text-slate-400 dark:text-slate-505 mt-1 italic font-sans">Upcoming sequential stage.</p>`;
                                break;
                        }

                        // Create the step block container (Vertical Layout Row)
                        const stepBlock = document.createElement("div");
                        stepBlock.className = "relative flex items-start gap-4";
                        
                        stepBlock.innerHTML = `
                            <!-- Node Circle -->
                            <div class="w-9 h-9 rounded-full ${stepBgClass} flex items-center justify-center flex-shrink-0 border shadow-2xs z-10 bg-white dark:bg-slate-900">
                                ${iconHtml}
                            </div>
                            <!-- Card Panel -->
                            <div class="flex-grow bg-white dark:bg-slate-900 border ${stepBorderClass} p-4 rounded-2xl text-[11px] leading-relaxed transition-all hover:bg-slate-50/50 dark:hover:bg-slate-900/45 duration-200 shadow-sm flex flex-col gap-1.5">
                                <div class="flex items-center justify-between gap-3">
                                    <p class="${labelColorClass} text-xs tracking-tight truncate font-bold" title="${step.label}">${step.label}</p>
                                    <div class="flex-shrink-0">${statusBadgeHtml}</div>
                                </div>
                                <div class="pt-2 border-t border-slate-100 dark:border-slate-800/60">
                                    ${detailsHtml}
                                </div>
                            </div>
                        `;

                        timelineContainer.appendChild(stepBlock);
                    });
                }

                // Show action signatory box only if it's currently sitting at the user's role stage!
                const actionPanel = document.getElementById("review-action-panel");
                const infoPanel = document.getElementById("review-info-panel");
                const formAction = document.getElementById("form-action");
                const txtRemarks = document.getElementById("action-remarks");

                // Update dynamic PDF link in Info Panel
                const pdfBtn = document.getElementById("btn-export-pdf");
                if (pdfBtn) {
                    pdfBtn.href = `/admin/loans/${loan.id}/pdf`;
                }

                // Update active stage label in Info Panel
                let activeStageLabel = "Completed";
                if (loan.workflow_steps && loan.workflow_steps.length > 0) {
                    const activeStep = loan.workflow_steps.find(s => s.status === 'current');
                    if (activeStep) {
                        activeStageLabel = activeStep.label;
                    }
                }
                const infoCurrentStage = document.getElementById("info-current-stage");
                if (infoCurrentStage) {
                    infoCurrentStage.textContent = activeStageLabel;
                }

                if (myGroupSlugs.includes(loan.current_stage)) {
                    actionPanel.classList.remove("hidden");
                    if (infoPanel) infoPanel.classList.add("hidden");
                    txtRemarks.value = "";

                    // Assign routes dynamic URLs
                    const btnApprove = document.getElementById("btn-action-approve");
                    const btnReject = document.getElementById("btn-action-reject");
                    const btnReturn = document.getElementById("btn-action-return");

                    if (['sako_staff', 'hrmd_staff'].includes(loan.current_stage)) {
                        btnReturn.classList.remove("hidden");
                    } else {
                        btnReturn.classList.add("hidden");
                    }

                    btnApprove.onclick = function(e) {
                        e.preventDefault();
                        const alertInstance = window.MLSAKOAlert || Swal;

                        if (!txtRemarks.value.trim()) {
                            alertInstance.fire({
                                icon: 'warning',
                                title: 'Remarks Required',
                                text: 'Please enter evaluation remarks before signing off.',
                                iconColor: '#f59e0b',
                                confirmButtonText: 'Understood'
                            });
                            return;
                        }

                        alertInstance.fire({
                            icon: 'question',
                            title: 'Confirm Signature',
                            text: 'Are you sure you want to sign and approve this loan facility application?',
                            showCancelButton: true,
                            confirmButtonText: 'Yes, Sign & Approve',
                            cancelButtonText: 'Cancel',
                            iconColor: '#10b981'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                formAction.action = `/loans/${loan.id}/approve`;
                                formAction.submit();
                            }
                        });
                    };

                    btnReject.onclick = function(e) {
                        e.preventDefault();
                        const alertInstance = window.MLSAKOAlert || Swal;

                        if (!txtRemarks.value.trim()) {
                            alertInstance.fire({
                                icon: 'warning',
                                title: 'Remarks Required',
                                text: 'Please enter rejection remarks to record the decision.',
                                iconColor: '#f59e0b',
                                confirmButtonText: 'Understood'
                            });
                            return;
                        }

                        alertInstance.fire({
                            icon: 'warning',
                            title: 'Confirm Rejection',
                            text: 'Are you sure you want to decline and reject this loan facility application? This will terminate the workflow path.',
                            showCancelButton: true,
                            confirmButtonText: 'Yes, Decline & Reject',
                            cancelButtonText: 'Cancel',
                            iconColor: '#f43f5e'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                formAction.action = `/loans/${loan.id}/reject`;
                                formAction.submit();
                            }
                        });
                    };

                    btnReturn.onclick = function(e) {
                        e.preventDefault();
                        const alertInstance = window.MLSAKOAlert || Swal;

                        if (!txtRemarks.value.trim()) {
                            alertInstance.fire({
                                icon: 'warning',
                                title: 'Remarks Required',
                                text: 'Please enter remarks explaining which requirements are lacking.',
                                iconColor: '#f59e0b',
                                confirmButtonText: 'Understood'
                            });
                            return;
                        }

                        alertInstance.fire({
                            icon: 'warning',
                            title: 'Confirm Return',
                            text: 'Are you sure you want to return this loan application back to the member for requirement corrections?',
                            showCancelButton: true,
                            confirmButtonText: 'Yes, Return Application',
                            cancelButtonText: 'Cancel',
                            iconColor: '#f59e0b'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                formAction.action = `/loans/${loan.id}/return`;
                                formAction.submit();
                            }
                        });
                    };
                } else {
                    actionPanel.classList.add("hidden");
                    if (infoPanel) infoPanel.classList.remove("hidden");
                }

                openModal("modal-review");
            });
        });

    });
</script>
@endpush