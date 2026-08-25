@extends('layouts.user')

@section('title', 'Payroll Deductions - ML Sako')

@section('header')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h1 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight serif-font">Payroll Deductions</h1>
        <p class="text-xs text-slate-550 dark:text-slate-400 mt-1 font-semibold">Monitor auto-deducted cooperative contributions and submit voluntary adjustment requests.</p>
    </div>
    <div class="flex items-center">
        <button id="btn-trigger-adjustment" type="button" class="bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-[10px] uppercase tracking-widest px-5 py-3.5 rounded-2xl shadow-sm hover:shadow-xl transition-all cursor-pointer flex items-center gap-2">
            <svg class="w-4 h-4 stroke-[2.5]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
            </svg>
            Request Deduction Change
        </button>
    </div>
</div>
@endsection

@section('content')
@php
    $upcomingPaydays = [];
    $currentDate = now();
    $year = $currentDate->year;
    $month = $currentDate->month;
    
    while (count($upcomingPaydays) < 6) {
        $p15 = Carbon\Carbon::create($year, $month, 15, 0, 0, 0);
        if ($p15->isAfter($currentDate) || $p15->isSameDay($currentDate)) {
            $upcomingPaydays[] = $p15->copy();
        }
        
        if (count($upcomingPaydays) >= 6) break;
        
        $day30 = ($month == 2) ? ($year % 4 == 0 && ($year % 100 != 0 || $year % 400 == 0) ? 29 : 28) : 30;
        $p30 = Carbon\Carbon::create($year, $month, $day30, 0, 0, 0);
        
        if ($p30->isAfter($currentDate) || $p30->isSameDay($currentDate)) {
            $upcomingPaydays[] = $p30->copy();
        }
        
        $month++;
        if ($month > 12) {
            $month = 1;
            $year++;
        }
    }

    // Dynamic Active Contribution Summary based on last approved request
    $activeSavings = 2500.00;
    $activeFixed = 1000.00;
    
    $lastApproved = $deductionRequests->where('status', 'approved')->first();
    if ($lastApproved) {
        $activeSavings = (float) $lastApproved->savings_amount;
        $activeFixed = (float) $lastApproved->fixed_amount;
    }
    
    $aggregateSavings = $activeSavings + $activeFixed;
@endphp
<div class="space-y-8 animate-fade-in">

    <!-- Active contribution summary alert if validation errors occurred -->
    @if($errors->any())
        <div class="bg-rose-50 dark:bg-rose-950/20 border border-rose-100 dark:border-rose-900/30 rounded-2xl p-4 text-xs text-rose-600 dark:text-rose-400 space-y-1">
            <p class="font-extrabold text-[10px] uppercase tracking-wider">Please fix the following validation errors in your request:</p>
            <ul class="list-disc pl-4 font-semibold">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- TOP SECTION: ACTIVE CONTRIBUTION SUMMARY (Full-Width Card) -->
    <div class="bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700/80 rounded-[2rem] shadow-sm p-6 sm:p-8 space-y-6">
        <div class="space-y-0.5">
            <h3 class="text-base font-extrabold text-slate-950 dark:text-white serif-font tracking-tight">Active Monthly Contributions Schedule</h3>
            <p class="text-[11px] font-semibold text-slate-450 dark:text-slate-500">Current automated deductions applied directly to your salary cycle.</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
            <!-- Savings Deposit -->
            <div class="bg-emerald-50/40 dark:bg-emerald-950/15 p-5 rounded-2xl border border-emerald-100/50 dark:border-emerald-900/10 flex items-center justify-between">
                <div class="space-y-0.5">
                    <span class="text-[9px] font-extrabold text-emerald-800 dark:text-emerald-400 uppercase tracking-widest block">Savings Deposit</span>
                    <p class="text-xs text-slate-400 dark:text-slate-500 font-semibold">Credited to Savings Pool</p>
                </div>
                <div class="text-right">
                    <p class="text-xl font-black text-emerald-950 dark:text-emerald-300 font-mono">₱{{ number_format($activeSavings, 2) }}</p>
                    <span class="text-[9px] font-bold text-emerald-600/70 dark:text-emerald-450/70 uppercase tracking-widest">Active Contribution</span>
                </div>
            </div>

            <!-- Fixed Deposit -->
            <div class="bg-blue-50/40 dark:bg-blue-950/15 p-5 rounded-2xl border border-blue-100/50 dark:border-blue-900/10 flex items-center justify-between">
                <div class="space-y-0.5">
                    <span class="text-[9px] font-extrabold text-blue-800 dark:text-blue-400 uppercase tracking-widest block">Fixed Deposit</span>
                    <p class="text-xs text-slate-400 dark:text-slate-500 font-semibold">Credited to Share Capital</p>
                </div>
                <div class="text-right">
                    <p class="text-xl font-black text-blue-950 dark:text-blue-300 font-mono">₱{{ number_format($activeFixed, 2) }}</p>
                    <span class="text-[9px] font-bold text-blue-600/70 dark:text-blue-450/70 uppercase tracking-widest">Active Contribution</span>
                </div>
            </div>

            <!-- Aggregate Contribution -->
            <div class="bg-slate-50 dark:bg-slate-900 p-5 rounded-2xl border border-slate-100/70 dark:border-slate-800 flex items-center justify-between">
                <div class="space-y-0.5">
                    <span class="text-[9px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-widest block">Aggregate Deduction</span>
                    <p class="text-xs text-slate-400 dark:text-slate-500 font-semibold">Total combined per month</p>
                </div>
                <div class="text-right">
                    <p class="text-xl font-black text-slate-900 dark:text-white font-mono">₱{{ number_format($aggregateSavings, 2) }}</p>
                    <span class="text-[9px] font-bold text-slate-400 dark:text-slate-550 uppercase tracking-widest">Monthly Sum</span>
                </div>
            </div>
        </div>
    </div>

    <!-- BOTTOM SECTION: DEDUCTION ADJUSTMENT REQUEST HISTORY (Full-Width Card) -->
    <div class="bg-white dark:bg-slate-800 rounded-[2rem] border border-slate-100 dark:border-slate-700/80 shadow-sm p-6 sm:p-8 space-y-6">
        <div class="space-y-0.5">
            <h3 class="text-base font-extrabold text-slate-950 dark:text-white serif-font tracking-tight">Deduction Adjustment History Logs</h3>
            <p class="text-[11px] font-semibold text-slate-450 dark:text-slate-500">Historical records and live status updates of your voluntary payroll deduction adjustment filings.</p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="text-slate-400 dark:text-slate-500 font-extrabold border-b border-slate-100 dark:border-slate-700 uppercase tracking-wider text-[10.5px]">
                        <th class="pb-3 px-4">Reference ID</th>
                        <th class="pb-3 px-4">Savings Amount</th>
                        <th class="pb-3 px-4">Fixed Amount</th>
                        <th class="pb-3 px-4">Effectivity Date</th>
                        <th class="pb-3 px-4">Status</th>
                        <th class="pb-3 px-4">Remarks</th>
                        <th class="pb-3 px-4 text-right">Filed Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 dark:divide-slate-700/80 font-semibold text-slate-700 dark:text-slate-350">
                    @forelse($deductionRequests as $req)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-900/20 transition-colors">
                            <td class="py-4 px-4 font-bold text-slate-900 dark:text-white font-mono">
                                #ADJ-{{ str_pad($req->id, 5, '0', STR_PAD_LEFT) }}
                            </td>
                            <td class="py-4 px-4 font-extrabold text-slate-900 dark:text-white font-mono text-sm">
                                ₱{{ number_format($req->savings_amount, 2) }}
                            </td>
                            <td class="py-4 px-4 font-extrabold text-slate-900 dark:text-white font-mono text-sm">
                                ₱{{ number_format($req->fixed_amount, 2) }}
                            </td>
                            <td class="py-4 px-4 text-slate-500 dark:text-slate-400 font-mono">
                                {{ $req->effectivity_date->format('M d, Y') }}
                            </td>
                            <td class="py-4 px-4">
                                @if($req->status === 'pending')
                                    <span class="px-2.5 py-1 rounded-full text-[8.5px] font-extrabold bg-amber-50 dark:bg-amber-950/20 text-amber-600 dark:text-amber-400 border border-amber-100/30 dark:border-amber-900/20 uppercase tracking-wider animate-pulse">Pending</span>
                                @elseif($req->status === 'approved')
                                    <span class="px-2.5 py-1 rounded-full text-[8.5px] font-extrabold bg-emerald-50 dark:bg-emerald-950/20 text-emerald-600 dark:text-emerald-400 border border-emerald-100/30 dark:border-emerald-900/20 uppercase tracking-wider">Approved</span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[8.5px] font-extrabold bg-rose-50 dark:bg-rose-950/20 text-rose-600 dark:text-rose-450 border border-rose-100/30 dark:border-rose-900/20 uppercase tracking-wider">Rejected</span>
                                @endif
                            </td>
                            <td class="py-4 px-4 text-slate-500 dark:text-slate-450 italic font-medium max-w-xs truncate" title="{{ $req->remarks ?: 'No remarks' }}">
                                {{ $req->remarks ? '"' . $req->remarks . '"' : '—' }}
                            </td>
                            <td class="py-4 px-4 text-right text-slate-400 dark:text-slate-500 font-mono font-medium">
                                {{ $req->created_at->format('M d, Y h:i A') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-400 dark:text-slate-500 font-bold italic text-xs">
                                No adjustment requests submitted yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- MODAL: REQUEST ADJUSTMENT FORM -->
<div id="modal-adjustment-form" class="fixed inset-0 z-50 hidden overflow-y-auto flex items-center justify-center p-4">
    <!-- Backdrop -->
    <div id="modal-adj-backdrop" class="fixed inset-0 bg-slate-950/40 dark:bg-slate-950/60 backdrop-blur-md opacity-0 transition-opacity duration-300 ease-out cursor-pointer"></div>

    <!-- Modal Box -->
    <div id="modal-adj-box" class="bg-white dark:bg-slate-900 rounded-[2.5rem] border border-slate-100 dark:border-slate-800 shadow-2xl overflow-hidden w-full max-w-lg relative z-10 p-6 sm:p-8 space-y-6 transform scale-95 opacity-0 transition-all duration-300 ease-out">
        <div class="flex items-center justify-between">
            <div class="space-y-0.5">
                <h3 class="text-base font-black text-slate-950 dark:text-white serif-font tracking-tight">Request Deduction Change</h3>
                <p class="text-[10px] text-slate-400 dark:text-slate-500 font-extrabold uppercase tracking-widest">Cooperative Treasury Services</p>
            </div>
            <button type="button" id="btn-close-adj-modal" class="p-1.5 rounded-xl text-slate-400 hover:text-slate-700 dark:text-slate-500 dark:hover:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800/80 transition-all duration-200 outline-none cursor-pointer">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <form action="{{ route('member.deductions.store') }}" method="POST" id="deduction-request-form" class="space-y-4.5 m-0">
            @csrf
            <input type="hidden" name="pin" id="deduction-pin-input">

            <!-- Input Fields Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Savings Deposit Amount -->
                <div class="space-y-1.5">
                    <label for="savings_amount" class="text-[10px] font-extrabold text-slate-400 dark:text-slate-500 uppercase tracking-widest block">Savings Deposit Amount</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-450 font-extrabold font-mono text-sm">₱</span>
                        <input type="number" step="0.01" min="250" id="savings_amount" name="savings_amount" required value="{{ old('savings_amount') }}" placeholder="Min 250" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-2xl pl-8 pr-4 py-3.5 text-xs font-bold text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all font-mono">
                    </div>
                </div>

                <!-- Fixed Deposit Amount -->
                <div class="space-y-1.5">
                    <label for="fixed_amount" class="text-[10px] font-extrabold text-slate-400 dark:text-slate-500 uppercase tracking-widest block">Fixed Deposit Amount</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-450 font-extrabold font-mono text-sm">₱</span>
                        <input type="number" step="0.01" min="250" id="fixed_amount" name="fixed_amount" required value="{{ old('fixed_amount') }}" placeholder="Min 250" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-2xl pl-8 pr-4 py-3.5 text-xs font-bold text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all font-mono">
                    </div>
                </div>
            </div>

            <!-- Effectivity Date -->
            <div class="space-y-1.5">
                <label for="effectivity_date" class="text-[10px] font-extrabold text-slate-400 dark:text-slate-500 uppercase tracking-widest block">Effectivity Date (Upcoming Paydays)</label>
                <select id="effectivity_date" name="effectivity_date" required class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-2xl px-4 py-3.5 text-xs font-bold text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all font-mono cursor-pointer">
                    @foreach($upcomingPaydays as $date)
                        <option value="{{ $date->format('Y-m-d') }}" {{ old('effectivity_date') == $date->format('Y-m-d') ? 'selected' : '' }}>
                            {{ $date->format('F d, Y') }} (Payday)
                        </option>
                    @endforeach
                </select>
                <p class="text-[9px] text-slate-400 dark:text-slate-550 font-semibold leading-relaxed">Paydays are scheduled every 15th and 30th of the month. Select your desired payroll adjustment cycle.</p>
            </div>

            <!-- Remarks -->
            <div class="space-y-1.5">
                <label for="remarks" class="text-[10px] font-extrabold text-slate-400 dark:text-slate-500 uppercase tracking-widest block">Remarks / Notes</label>
                <textarea id="remarks" name="remarks" rows="2" placeholder="Optional adjustment reason..." class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-2xl px-4 py-3 text-xs font-semibold text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all">{{ old('remarks') }}</textarea>
            </div>

            <!-- Terms and Conditions -->
            <div class="space-y-1.5">
                <span class="text-[10px] font-extrabold text-slate-400 dark:text-slate-500 uppercase tracking-widest block">Terms & Conditions</span>
                <div class="bg-slate-50 dark:bg-slate-950 border border-slate-100 dark:border-slate-800 rounded-2xl p-4 text-[10px] text-slate-550 dark:text-slate-400 overflow-y-auto max-h-24 space-y-1.5 font-medium leading-relaxed">
                    <p class="font-extrabold text-slate-700 dark:text-slate-300">1. Voluntary Deduction Adjustment</p>
                    <p>I voluntarily authorize ML Sako Cooperative to adjust my payroll deductions for Savings and Fixed Deposit (Share Capital) starting from the effectivity date specified, subject to verification.</p>
                    <p class="font-extrabold text-slate-700 dark:text-slate-300">2. Deadlines</p>
                    <p>Submissions within 5 business days of the payroll run will be processed in the subsequent salary cycle.</p>
                </div>
            </div>

            <!-- Checkbox -->
            <div class="flex items-start gap-3 select-none">
                <input type="checkbox" id="terms_agreed" name="terms_agreed" value="1" required class="rounded border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-emerald-600 focus:ring-emerald-500 dark:focus:ring-emerald-600 w-4 h-4 cursor-pointer mt-0.5">
                <label for="terms_agreed" class="text-[10px] font-semibold text-slate-500 dark:text-slate-400 leading-snug cursor-pointer select-none">
                    I authorize this deduction adjustment and agree to the Terms.
                </label>
            </div>

            <!-- Actions -->
            <div class="flex items-center gap-3 pt-2">
                <button type="button" id="btn-cancel-adj-modal" class="flex-1 px-4 py-3 rounded-2xl border border-slate-200 dark:border-slate-800 text-xs font-bold text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-800 dark:hover:text-slate-100 transition-colors cursor-pointer">
                    Cancel
                </button>
                <button type="submit" class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs uppercase tracking-wider py-3.5 rounded-2xl shadow-xs hover:shadow-md transition-all cursor-pointer">
                    Submit Request
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const triggerBtn = document.getElementById("btn-trigger-adjustment");
    const adjModal = document.getElementById("modal-adjustment-form");
    const adjModalBackdrop = document.getElementById("modal-adj-backdrop");
    const adjModalBox = document.getElementById("modal-adj-box");
    const closeBtns = document.querySelectorAll("#btn-close-adj-modal, #btn-cancel-adj-modal");

    function openModal() {
        adjModal.classList.remove("hidden");
        setTimeout(() => {
            adjModalBackdrop.classList.remove("opacity-0");
            adjModalBackdrop.classList.add("opacity-100");
            adjModalBox.classList.remove("scale-95", "opacity-0");
            adjModalBox.classList.add("scale-100", "opacity-100");
        }, 10);
    }

    function closeModal() {
        adjModalBox.classList.remove("scale-100", "opacity-100");
        adjModalBox.classList.add("scale-95", "opacity-0");
        adjModalBackdrop.classList.remove("opacity-100");
        adjModalBackdrop.classList.add("opacity-0");
        setTimeout(() => {
            adjModal.classList.add("hidden");
        }, 300);
    }

    if (triggerBtn) {
        triggerBtn.addEventListener("click", openModal);
    }

    closeBtns.forEach(btn => {
        btn.addEventListener("click", closeModal);
    });

    if (adjModalBackdrop) {
        adjModalBackdrop.addEventListener("click", closeModal);
    }

    // Intercept form submission to prompt for PIN
    const form = document.getElementById("deduction-request-form");
    if (form) {
        form.addEventListener("submit", function(e) {
            if (form.dataset.confirmed === "true") {
                return;
            }
            e.preventDefault();

            if (window.MLSAKOAlert) {
                MLSAKOAlert.fire({
                    icon: 'question',
                    title: 'Confirm Deduction Change',
                    html: `
                        <div class="space-y-4 text-center">
                            <p class="text-xs font-semibold text-slate-600 dark:text-slate-400">
                                Are you sure you want to submit this deduction change request?
                            </p>
                            <div class="space-y-1.5 pt-2 border-t border-slate-100 dark:border-slate-800">
                                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">
                                    Enter 6-Digit Security PIN
                                </p>
                                <div class="flex justify-center gap-1.5" id="swal-pin-inputs-container">
                                    <input type="password" maxlength="1" pattern="[0-9]" inputmode="numeric" class="swal-pin-digit-input w-9 h-11 text-center text-lg font-bold bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-lg focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500 transition-all duration-150" required>
                                    <input type="password" maxlength="1" pattern="[0-9]" inputmode="numeric" class="swal-pin-digit-input w-9 h-11 text-center text-lg font-bold bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-lg focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500 transition-all duration-150" required>
                                    <input type="password" maxlength="1" pattern="[0-9]" inputmode="numeric" class="swal-pin-digit-input w-9 h-11 text-center text-lg font-bold bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-lg focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500 transition-all duration-150" required>
                                    <input type="password" maxlength="1" pattern="[0-9]" inputmode="numeric" class="swal-pin-digit-input w-9 h-11 text-center text-lg font-bold bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-lg focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500 transition-all duration-150" required>
                                    <input type="password" maxlength="1" pattern="[0-9]" inputmode="numeric" class="swal-pin-digit-input w-9 h-11 text-center text-lg font-bold bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-lg focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500 transition-all duration-150" required>
                                    <input type="password" maxlength="1" pattern="[0-9]" inputmode="numeric" class="swal-pin-digit-input w-9 h-11 text-center text-lg font-bold bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-lg focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500 transition-all duration-150" required>
                                </div>
                                <input type="hidden" id="swal-hidden-pin">
                            </div>
                        </div>
                    `,
                    showCancelButton: true,
                    confirmButtonText: 'Authorize Request',
                    cancelButtonText: 'Cancel',
                    iconColor: '#10b981',
                    didOpen: () => {
                        const container = document.getElementById('swal-pin-inputs-container');
                        if (container) {
                            const inputs = container.querySelectorAll('input');
                            const hidden = document.getElementById('swal-hidden-pin');
                            
                            inputs.forEach((input, index) => {
                                input.addEventListener('input', () => {
                                    input.value = input.value.replace(/[^0-9]/g, '');
                                    if (input.value.length === 1 && index < inputs.length - 1) {
                                        inputs[index + 1].focus();
                                    }
                                    updateVal();
                                });

                                input.addEventListener('keydown', (e) => {
                                    if (e.key === 'Backspace') {
                                        if (input.value.length === 0 && index > 0) {
                                            inputs[index - 1].value = '';
                                            inputs[index - 1].focus();
                                            e.preventDefault();
                                        } else {
                                            input.value = '';
                                        }
                                        updateVal();
                                    }
                                });

                                input.addEventListener('paste', (e) => {
                                    e.preventDefault();
                                    const pasteData = (e.clipboardData || window.clipboardData).getData('text').replace(/[^0-9]/g, '').slice(0, inputs.length);
                                    if (pasteData) {
                                        for (let i = 0; i < pasteData.length; i++) {
                                            if (inputs[i]) {
                                                inputs[i].value = pasteData[i];
                                            }
                                        }
                                        const nextFocus = Math.min(pasteData.length, inputs.length - 1);
                                        inputs[nextFocus].focus();
                                        updateVal();
                                    }
                                });
                            });

                            const updateVal = () => {
                                let fullVal = '';
                                inputs.forEach(inp => fullVal += inp.value);
                                hidden.value = fullVal;
                            };

                            setTimeout(() => {
                                if (inputs[0]) inputs[0].focus();
                            }, 150);
                        }
                    },
                    preConfirm: () => {
                        const pinVal = document.getElementById('swal-hidden-pin').value;
                        if (pinVal.length !== 6) {
                            Swal.showValidationMessage('Please enter your 6-digit security PIN.');
                            return false;
                        }
                        return pinVal;
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('deduction-pin-input').value = result.value;
                        form.dataset.confirmed = "true";
                        form.submit();
                    }
                });
            } else {
                const pinPrompt = prompt('Are you sure you want to adjust your deductions? Please enter your 6-digit security PIN to authorize:');
                if (pinPrompt) {
                    if (pinPrompt.length === 6 && !isNaN(pinPrompt)) {
                        document.getElementById('deduction-pin-input').value = pinPrompt;
                        form.dataset.confirmed = "true";
                        form.submit();
                    } else {
                        alert('Invalid PIN format. Submission cancelled.');
                    }
                }
            }
        });
    }
});
</script>
@endsection
