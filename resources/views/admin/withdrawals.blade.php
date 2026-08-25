@extends('layouts.admin')

@section('title', 'Withdrawals Decision Board - Sako Cooperative')

@section('header')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight serif-font">Withdrawals Decision Board</h1>
        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 font-semibold">Review savings payout requests, acknowledge processing tasks, and release completed disbursements.</p>
    </div>
</div>
@endsection

@section('content')
<div class="space-y-6 animate-fade-in">

    <!-- Withdrawal KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white dark:bg-slate-800 p-5 rounded-3xl border border-slate-100 dark:border-slate-700/50 shadow-sm space-y-1.5 flex items-center justify-between">
            <div class="space-y-1">
                <span class="text-[10px] font-bold text-amber-600 dark:text-amber-400 uppercase tracking-widest block">Pending Requests</span>
                <p class="text-3xl font-black text-slate-800 dark:text-white">{{ $metrics['pending'] }}</p>
                <span class="text-2xs text-slate-400 dark:text-slate-500 font-bold uppercase tracking-wider">Awaiting Verification</span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-amber-50 dark:bg-amber-950/20 text-amber-600 dark:text-amber-400 border border-amber-100 dark:border-amber-900/30 flex items-center justify-center">
                <svg class="w-6 h-6 animate-pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-800 p-5 rounded-3xl border border-slate-100 dark:border-slate-700/50 shadow-sm space-y-1.5 flex items-center justify-between">
            <div class="space-y-1">
                <span class="text-[10px] font-bold text-blue-600 dark:text-blue-400 uppercase tracking-widest block">In Processing</span>
                <p class="text-3xl font-black text-slate-800 dark:text-white">{{ $metrics['processing'] }}</p>
                <span class="text-2xs text-slate-400 dark:text-slate-500 font-bold uppercase tracking-wider">Disbursements ongoing</span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-blue-50 dark:bg-blue-950/20 text-blue-600 dark:text-blue-400 border border-blue-100 dark:border-blue-900/30 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-800 p-5 rounded-3xl border border-slate-100 dark:border-slate-700/50 shadow-sm space-y-1.5 flex items-center justify-between">
            <div class="space-y-1">
                <span class="text-[10px] font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-widest block">Completed Payouts</span>
                <p class="text-3xl font-black text-slate-800 dark:text-white">{{ $metrics['released'] }}</p>
                <span class="text-2xs text-slate-400 dark:text-slate-500 font-bold uppercase tracking-wider">Funds safely released</span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 dark:bg-emerald-950/20 text-emerald-600 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-900/30 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>
    </div>

    <!-- Main List Container -->
    <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-100 dark:border-slate-700/50 shadow-sm overflow-hidden flex flex-col">
        <div class="p-5 border-b border-slate-100 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-900/50 flex items-center justify-between">
            <h3 class="text-sm font-extrabold text-slate-800 dark:text-white uppercase tracking-widest">Withdrawal Pipeline Queue</h3>
            <span class="text-2xs font-extrabold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Real-time status stream</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-900/50 border-b border-slate-100 dark:border-slate-700/80 text-[10px] font-extrabold text-slate-400 dark:text-slate-500 uppercase tracking-widest">
                        <th class="px-6 py-4.5" style="width: 45px;">
                            <input type="checkbox" id="select-all-withdrawals" class="rounded border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-emerald-600 focus:ring-emerald-500 dark:focus:ring-emerald-600 w-4 h-4 cursor-pointer">
                        </th>
                        <th class="px-6 py-4.5">Member Profile</th>
                        <th class="px-6 py-4.5">Requested Amount</th>
                        <th class="px-6 py-4.5">Disbursement Channel</th>
                        <th class="px-6 py-4.5">Date Filed</th>
                        <th class="px-6 py-4.5">Status</th>
                        <th class="px-6 py-4.5 text-right">Lifecycle Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 dark:divide-slate-700 text-slate-700 dark:text-slate-350 font-medium">
                    @forelse($withdrawals as $w)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-900/10 transition-colors">
                            <td class="px-6 py-4" style="width: 45px;">
                                <input type="checkbox" name="withdrawal_ids[]" value="{{ $w->id }}" class="withdrawal-checkbox rounded border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-emerald-600 focus:ring-emerald-500 dark:focus:ring-emerald-600 w-4 h-4 cursor-pointer">
                            </td>
                            <td class="px-6 py-4 flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full bg-slate-100 dark:bg-slate-900 flex items-center justify-center font-bold text-slate-600 dark:text-slate-450 text-xs border border-slate-200/40 dark:border-slate-700/65">
                                    {{ strtoupper(substr($w->user->name, 0, 1)) }}
                                </div>
                                <div>
                                    <h4 class="font-bold text-slate-950 dark:text-white">{{ $w->user->name }}</h4>
                                    <p class="text-[10px] text-slate-400 dark:text-slate-500 font-semibold font-mono">{{ $w->user->email }} (ID: {{ $w->user->company_id ?: 'N/A' }})</p>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-black font-mono text-slate-900 dark:text-white text-sm">₱{{ number_format($w->amount, 2) }}</span>
                                @if($w->reason)
                                    <span class="text-[10px] text-slate-400 dark:text-slate-500 italic block truncate max-w-xs mt-0.5" title="{{ $w->reason }}">Reason: "{{ $w->reason }}"</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-slate-600 dark:text-slate-400 font-semibold">
                                {{ $w->channel }}
                                @if($w->transaction_id)
                                    <span class="text-[10px] text-blue-600 dark:text-blue-400 font-bold block font-mono mt-0.5 animate-fade-in" title="Transaction ID">TXN: {{ $w->transaction_id }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-slate-500 dark:text-slate-500 font-semibold font-mono">
                                {{ $w->created_at->format('M d, Y h:i A') }}
                            </td>
                            <td class="px-6 py-4">
                                @if($w->status === 'pending')
                                    <span class="px-2.5 py-1 rounded-full text-[9px] font-extrabold bg-amber-50 dark:bg-amber-950/20 text-amber-600 dark:text-amber-400 border border-amber-100 dark:border-amber-900/20 uppercase tracking-wider animate-pulse">Pending</span>
                                @elseif($w->status === 'processing')
                                    <span class="px-2.5 py-1 rounded-full text-[9px] font-extrabold bg-blue-50 dark:bg-blue-950/20 text-blue-600 dark:text-blue-400 border border-blue-100 dark:border-blue-900/20 uppercase tracking-wider">Processing</span>
                                @elseif($w->status === 'released')
                                    <span class="px-2.5 py-1 rounded-full text-[9px] font-extrabold bg-emerald-50 dark:bg-emerald-950/20 text-emerald-600 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-900/20 uppercase tracking-wider">Released</span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[9px] font-extrabold bg-rose-50 dark:bg-rose-950/20 text-rose-600 dark:text-rose-450 border border-rose-100 dark:border-rose-900/20 uppercase tracking-wider">{{ strtoupper($w->status) }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                @if($w->status === 'pending')
                                    <button type="button" 
                                            class="btn-trigger-acknowledge bg-blue-600 hover:bg-blue-700 text-white font-extrabold text-[10px] uppercase tracking-wider px-3.5 py-2 rounded-xl shadow-xs transition-all cursor-pointer"
                                            data-action="{{ route('admin.withdrawals.status', $w) }}"
                                            data-ref="REF #WD-{{ str_pad($w->id, 5, '0', STR_PAD_LEFT) }}"
                                            data-name="{{ $w->user->name }}"
                                            data-amount="₱{{ number_format($w->amount, 2) }}">
                                        Acknowledge Request
                                    </button>
                                @elseif($w->status === 'processing')
                                    <form action="{{ route('admin.withdrawals.status', $w) }}" method="POST" class="inline m-0">
                                        @csrf
                                        <input type="hidden" name="action" value="release">
                                        <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-[10px] uppercase tracking-wider px-3.5 py-2 rounded-xl shadow-xs transition-all cursor-pointer">
                                            Mark as Released
                                        </button>
                                    </form>
                                @else
                                    <span class="text-slate-400 dark:text-slate-500 font-extrabold text-[10px] uppercase tracking-wide italic">No actions pending</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-400 dark:text-slate-500 font-extrabold italic text-xs">
                                No withdrawal requests currently recorded in the pipeline.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($withdrawals->hasPages())
            <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-700">
                {{ $withdrawals->links() }}
            </div>
        @endif
    </div>
</div>

<!-- FLOATING ACTION BAR FOR BULK PDF PREVIEW -->
<div id="bulk-pdf-bar" class="fixed bottom-6 left-1/2 -translate-x-1/2 bg-slate-900/95 dark:bg-slate-950/95 backdrop-blur-md px-6 py-4 rounded-3xl shadow-2xl border border-slate-800 z-50 flex items-center gap-6 transition-all duration-300 transform translate-y-32 opacity-0">
    <div class="flex items-center gap-2">
        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
        <p class="text-xs text-slate-300 font-bold"><span id="selected-count" class="text-white font-black font-mono">0</span> items selected for export</p>
    </div>
    <div class="h-4 w-px bg-slate-700"></div>
    <div class="flex items-center gap-3">
        <button id="btn-export-pdf" type="button" class="bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-[10px] uppercase tracking-wider px-4 py-2 rounded-xl shadow-xs transition-all cursor-pointer">
            Preview Manifest PDF
        </button>
        <button id="btn-clear-selection" type="button" class="text-slate-400 hover:text-white font-bold text-[10px] uppercase tracking-wider transition-colors cursor-pointer">
            Clear
        </button>
    </div>
</div>

<!-- HIDDEN PDF EXPORT FORM -->
<form id="pdf-export-form" action="{{ route('admin.withdrawals.pdf') }}" method="GET" target="_blank" class="hidden">
</form>

<!-- MODAL: INPUT TRANSACTION ID FOR ACKNOWLEDGEMENT -->
<div id="modal-acknowledge-withdrawal" class="fixed inset-0 z-50 hidden overflow-y-auto flex items-center justify-center p-4">
    <!-- Backdrop -->
    <div id="modal-ack-backdrop" class="fixed inset-0 bg-slate-950/40 dark:bg-slate-950/60 backdrop-blur-md opacity-0 transition-opacity duration-300 ease-out cursor-pointer"></div>

    <!-- Modal Box -->
    <div id="modal-ack-box" class="bg-white dark:bg-slate-900 rounded-[2rem] border border-slate-100 dark:border-slate-800 shadow-2xl overflow-hidden w-full max-w-md relative z-10 p-6 sm:p-8 space-y-6 transform scale-95 opacity-0 transition-all duration-300 ease-out">
        <div class="flex items-center justify-between">
            <h3 class="text-base font-extrabold text-slate-950 dark:text-white serif-font tracking-tight">Acknowledge Savings Payout</h3>
            <button type="button" id="btn-close-ack-modal" class="p-1.5 rounded-xl text-slate-400 hover:text-slate-700 dark:text-slate-500 dark:hover:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800/80 transition-all duration-200 outline-none">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form id="ack-withdrawal-form" action="" method="POST" class="space-y-4 m-0">
            @csrf
            <input type="hidden" name="action" value="acknowledge">

            <!-- Context Info -->
            <div class="bg-slate-50 dark:bg-slate-950/40 border border-slate-100 dark:border-slate-800/60 rounded-2xl p-4 space-y-2 text-xs">
                <div class="flex justify-between">
                    <span class="text-slate-400 font-semibold uppercase tracking-wider text-[10px]">Reference</span>
                    <span id="ack-modal-ref" class="font-bold text-slate-800 dark:text-white font-mono">WD-00000</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-400 font-semibold uppercase tracking-wider text-[10px]">Member</span>
                    <span id="ack-modal-name" class="font-bold text-slate-800 dark:text-white">Jane Doe</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-400 font-semibold uppercase tracking-wider text-[10px]">Amount</span>
                    <span id="ack-modal-amount" class="font-bold text-emerald-600 font-mono">₱0.00</span>
                </div>
            </div>

            <!-- Input Field -->
            <div class="space-y-1.5">
                <label for="transaction_id" class="text-[10px] font-extrabold text-slate-400 dark:text-slate-500 uppercase tracking-widest block">Transaction ID / Ref Number</label>
                <input type="text" id="transaction_id_input" name="transaction_id" required placeholder="e.g. TXN-123456789" class="w-full bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-2xl px-4 py-3 text-sm text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all font-mono font-semibold">
                <p class="text-[10px] text-slate-400 dark:text-slate-500 font-medium">Please enter the actual GCash, Bank, or OTC Reference ID to transition this request into the processing status.</p>
            </div>

            <!-- Actions -->
            <div class="flex items-center gap-3 pt-2">
                <button type="button" id="btn-cancel-ack-modal" class="flex-1 px-4 py-3 rounded-2xl border border-slate-200 dark:border-slate-700 text-xs font-bold text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-800 dark:hover:text-slate-100 transition-colors cursor-pointer">
                    Cancel
                </button>
                <button type="submit" class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs uppercase tracking-wider py-3 rounded-2xl shadow-xs hover:shadow-md transition-all cursor-pointer">
                    Acknowledge
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const selectAllCheckbox = document.getElementById("select-all-withdrawals");
    const itemCheckboxes = document.querySelectorAll(".withdrawal-checkbox");
    const bulkPdfBar = document.getElementById("bulk-pdf-bar");
    const selectedCountSpan = document.getElementById("selected-count");
    const btnExportPdf = document.getElementById("btn-export-pdf");
    const btnClearSelection = document.getElementById("btn-clear-selection");
    const pdfExportForm = document.getElementById("pdf-export-form");

    function updateBulkBar() {
        const checkedBoxes = document.querySelectorAll(".withdrawal-checkbox:checked");
        const count = checkedBoxes.length;
        selectedCountSpan.textContent = count;

        if (count > 0) {
            bulkPdfBar.classList.remove("translate-y-32", "opacity-0");
            bulkPdfBar.classList.add("translate-y-0", "opacity-100");
        } else {
            bulkPdfBar.classList.remove("translate-y-0", "opacity-100");
            bulkPdfBar.classList.add("translate-y-32", "opacity-0");
        }
    }

    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener("change", function () {
            itemCheckboxes.forEach(cb => {
                cb.checked = selectAllCheckbox.checked;
            });
            updateBulkBar();
        });
    }

    itemCheckboxes.forEach(cb => {
        cb.addEventListener("change", function () {
            const allChecked = Array.from(itemCheckboxes).every(c => c.checked);
            const someChecked = Array.from(itemCheckboxes).some(c => c.checked);
            
            if (selectAllCheckbox) {
                selectAllCheckbox.checked = allChecked;
                selectAllCheckbox.indeterminate = someChecked && !allChecked;
            }
            updateBulkBar();
        });
    });

    if (btnClearSelection) {
        btnClearSelection.addEventListener("click", function () {
            if (selectAllCheckbox) {
                selectAllCheckbox.checked = false;
                selectAllCheckbox.indeterminate = false;
            }
            itemCheckboxes.forEach(cb => cb.checked = false);
            updateBulkBar();
        });
    }

    if (btnExportPdf) {
        btnExportPdf.addEventListener("click", function () {
            const checkedBoxes = document.querySelectorAll(".withdrawal-checkbox:checked");
            if (checkedBoxes.length === 0) return;

            // Clear previous inputs from form
            pdfExportForm.innerHTML = "";

            // Create inputs for each ID
            checkedBoxes.forEach(cb => {
                const hiddenInput = document.createElement("input");
                hiddenInput.type = "hidden";
                hiddenInput.name = "ids[]";
                hiddenInput.value = cb.value;
                pdfExportForm.appendChild(hiddenInput);
            });

            pdfExportForm.submit();
        });
    }

    // Modal Acknowledge logic
    const ackModal = document.getElementById("modal-acknowledge-withdrawal");
    const ackModalBackdrop = document.getElementById("modal-ack-backdrop");
    const ackModalBox = document.getElementById("modal-ack-box");
    const closeAckBtns = document.querySelectorAll("#btn-close-ack-modal, #btn-cancel-ack-modal");
    const ackForm = document.getElementById("ack-withdrawal-form");
    const triggerBtns = document.querySelectorAll(".btn-trigger-acknowledge");

    const modalRef = document.getElementById("ack-modal-ref");
    const modalName = document.getElementById("ack-modal-name");
    const modalAmount = document.getElementById("ack-modal-amount");
    const transactionIdInput = document.getElementById("transaction_id_input");

    function openAckModal(btn) {
        const actionRoute = btn.getAttribute("data-action");
        const ref = btn.getAttribute("data-ref");
        const name = btn.getAttribute("data-name");
        const amount = btn.getAttribute("data-amount");

        ackForm.action = actionRoute;
        modalRef.textContent = ref;
        modalName.textContent = name;
        modalAmount.textContent = amount;
        transactionIdInput.value = "";

        ackModal.classList.remove("hidden");
        // Animate in
        setTimeout(() => {
            ackModalBackdrop.classList.remove("opacity-0");
            ackModalBackdrop.classList.add("opacity-100");
            ackModalBox.classList.remove("scale-95", "opacity-0");
            ackModalBox.classList.add("scale-100", "opacity-100");
            transactionIdInput.focus();
        }, 10);
    }

    function closeAckModal() {
        ackModalBox.classList.remove("scale-100", "opacity-100");
        ackModalBox.classList.add("scale-95", "opacity-0");
        ackModalBackdrop.classList.remove("opacity-100", "opacity-0");
        ackModalBackdrop.classList.add("opacity-0");

        setTimeout(() => {
            ackModal.classList.add("hidden");
        }, 300);
    }

    triggerBtns.forEach(btn => {
        btn.addEventListener("click", function () {
            openAckModal(this);
        });
    });

    closeAckBtns.forEach(btn => {
        btn.addEventListener("click", closeAckModal);
    });

    if (ackModalBackdrop) {
        ackModalBackdrop.addEventListener("click", closeAckModal);
    }
});
</script>
@endsection
