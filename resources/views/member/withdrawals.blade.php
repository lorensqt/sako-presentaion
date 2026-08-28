@extends('layouts.user')

@section('title', 'My Withdrawals - ML Sako')

@section('header')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight serif-font">My Withdrawals</h1>
        <p class="text-xs text-slate-700 dark:text-slate-300 mt-1 font-bold">Initiate savings payout requests or track your pending withdrawal disbursements.</p>
    </div>
</div>
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8 animate-fade-in">
    <!-- Left Column: Smaller Compact Form -->
    <div class="lg:col-span-1 bg-white dark:bg-slate-800 rounded-3xl border border-slate-100 dark:border-slate-700 shadow-sm p-6 space-y-6 h-fit">
        <div>
            <h3 class="text-base font-extrabold text-slate-900 dark:text-white serif-font">File Request</h3>
            <p class="text-[11px] text-slate-600 dark:text-slate-400 mt-0.5 leading-relaxed font-bold">Submit a secure payout request from your savings deposit balance.</p>
        </div>

        <form id="withdrawal-request-form" action="{{ route('member.withdrawals.store') }}" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="pin" id="withdrawal-pin-input">
            
            <div class="space-y-1.5">
                <label class="text-[10px] font-extrabold text-slate-700 dark:text-slate-200 uppercase tracking-wider block">Amount to Withdraw</label>
                <div class="relative">
                    <span class="absolute left-3.5 top-3 text-sm font-bold text-slate-500 dark:text-slate-400">₱</span>
                    <input type="number" name="amount" min="100" max="{{ $withdrawableAmount }}" value="500" required class="w-full pl-7 pr-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-xs font-bold text-slate-950 dark:text-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all">
                </div>
                <div class="flex justify-between items-center text-[10px] text-slate-700 dark:text-slate-300 font-extrabold pt-1">
                    <span>Min: ₱100.00</span>
                    <span>Max: ₱{{ number_format($withdrawableAmount, 2) }}</span>
                </div>
            </div>

            <div class="space-y-1.5">
                <label class="text-[10px] font-extrabold text-slate-700 dark:text-slate-200 uppercase tracking-wider block">Disbursement Channel</label>
                <select name="channel" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 text-xs font-bold focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all bg-white dark:bg-slate-900 text-slate-950 dark:text-white">
                    <option value="M Lhuillier Branch Cash-Out">M Lhuillier Branch Cash-Out</option>
                    <option value="MCash E-Wallet">MCash E-Wallet</option>
                </select>
            </div>

            <div class="space-y-1.5">
                <label class="text-[10px] font-extrabold text-slate-700 dark:text-slate-200 uppercase tracking-wider block">Reason for Payout <span class="text-rose-500 font-bold">*</span></label>
                <textarea name="reason" rows="3" placeholder="State purpose (e.g. Tuition, medical, emergency)" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-xs font-bold text-slate-950 dark:text-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all placeholder-slate-500 dark:placeholder-slate-400"></textarea>
            </div>

            <!-- Maintenance & Rules Card -->
            <div class="p-3.5 bg-emerald-50/60 dark:bg-emerald-950/20 rounded-2xl border border-emerald-200/80 dark:border-emerald-900/50 flex gap-2.5 text-emerald-950 dark:text-emerald-300 text-2xs leading-relaxed font-bold">
                <svg class="w-4 h-4 text-emerald-700 dark:text-emerald-400 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <div class="space-y-0.5">
                    <p class="font-bold text-[9px] uppercase tracking-widest text-emerald-950 dark:text-emerald-300">Maintaining Reserve</p>
                    <p class="text-slate-800 dark:text-slate-200 text-[10px] font-bold">A minimum of <strong>₱500.00</strong> must remain in your savings balance. No fees apply.</p>
                </div>
            </div>

            <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 dark:bg-emerald-600 dark:hover:bg-emerald-500 text-white font-extrabold text-[11px] uppercase tracking-widest py-3 rounded-xl transition-all duration-200 shadow-md shadow-emerald-600/10">
                Submit Request
            </button>
        </form>
    </div>

    <!-- Right Column: Beautiful Table List History -->
    <div class="lg:col-span-2 bg-white dark:bg-slate-800 rounded-3xl border border-slate-100 dark:border-slate-700 shadow-sm p-6 sm:p-8 space-y-6 flex flex-col">
        <div>
            <h3 class="text-base font-extrabold text-slate-900 dark:text-white serif-font">Withdrawal Log</h3>
            <p class="text-[11px] text-slate-600 dark:text-slate-400 mt-0.5 leading-relaxed font-bold">Complete history of your savings payouts and real-time verification progress.</p>
        </div>

        <!-- Desktop Table (Visible on larger screens) -->
        <div class="hidden md:block overflow-x-auto flex-grow">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-900/60 border-b border-slate-150 dark:border-slate-700 text-[10px] font-extrabold text-slate-700 dark:text-slate-300 uppercase tracking-widest">
                        <th class="px-5 py-4">Reference No.</th>
                        <th class="px-5 py-4">Date Filed</th>
                        <th class="px-5 py-4">Disbursement Channel</th>
                        <th class="px-5 py-4">Amount Requested</th>
                        <th class="px-5 py-4">Verification Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700/60 text-slate-800 dark:text-slate-200 font-semibold">
                    @forelse($withdrawals as $w)
                        <tr class="hover:bg-slate-50/40 dark:hover:bg-slate-900/30 transition-colors">
                            <td class="px-5 py-4.5 font-black font-mono text-slate-800 dark:text-slate-200">
                                #WD-{{ str_pad($w->id, 5, '0', STR_PAD_LEFT) }}
                            </td>
                            <td class="px-5 py-4.5 text-slate-700 dark:text-slate-300 font-bold font-mono">
                                {{ $w->created_at->format('M d, Y h:i A') }}
                            </td>
                            <td class="px-5 py-4.5">
                                <span class="text-slate-900 dark:text-slate-100 font-bold block">{{ $w->channel }}</span>
                                @if($w->reason)
                                    <span class="text-[10px] text-slate-600 dark:text-slate-400 italic font-bold block truncate max-w-[200px] mt-0.5" title="{{ $w->reason }}">
                                        "{{ $w->reason }}"
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-4.5 font-black font-mono text-slate-950 dark:text-white text-sm">
                                ₱{{ number_format($w->amount, 2) }}
                            </td>
                            <td class="px-5 py-4.5">
                                <div class="flex items-center gap-3">
                                    @if($w->status === 'pending')
                                        <span class="px-2.5 py-0.5 rounded-full text-[9px] font-extrabold bg-amber-50 dark:bg-amber-950/20 text-amber-600 dark:text-amber-400 border border-amber-100/50 dark:border-amber-900/30 uppercase tracking-wider animate-pulse">Pending</span>
                                        <form action="{{ route('member.withdrawals.cancel', $w->id) }}" method="POST" class="inline cancel-withdrawal-form m-0">
                                            @csrf
                                            <button type="submit" class="text-[10px] font-extrabold text-rose-600 hover:text-rose-800 hover:underline outline-none focus:outline-none transition-colors">
                                                Cancel
                                            </button>
                                        </form>
                                    @elseif($w->status === 'processing')
                                        <span class="px-2.5 py-0.5 rounded-full text-[9px] font-extrabold bg-blue-50 dark:bg-blue-950/20 text-blue-600 dark:text-blue-400 border border-blue-100/50 dark:border-blue-900/30 uppercase tracking-wider">Processing</span>
                                    @elseif($w->status === 'released')
                                        <span class="px-2.5 py-0.5 rounded-full text-[9px] font-extrabold bg-emerald-50 dark:bg-emerald-950/20 text-emerald-600 dark:text-emerald-400 border border-emerald-100/50 dark:border-emerald-900/30 uppercase tracking-wider">Released</span>
                                    @elseif($w->status === 'cancelled')
                                        <span class="px-2.5 py-0.5 rounded-full text-[9px] font-extrabold bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-600 uppercase tracking-wider">Cancelled</span>
                                    @else
                                        <span class="px-2.5 py-0.5 rounded-full text-[9px] font-extrabold bg-rose-50 dark:bg-rose-950/20 text-rose-700 dark:text-rose-400 border border-rose-100/50 dark:border-rose-900/30 uppercase tracking-wider">{{ strtoupper($w->status) }}</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-12 text-center text-slate-500 dark:text-slate-400 font-extrabold italic text-xs">
                                <div class="w-10 h-10 bg-slate-50 dark:bg-slate-900/40 rounded-xl flex items-center justify-center text-slate-400 dark:text-slate-500 mx-auto border border-slate-200 dark:border-slate-700 mb-3">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                </div>
                                No past or pending withdrawal requests recorded.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Withdrawal Log List (Visible on mobile viewports) -->
        <div class="block md:hidden space-y-3.5">
            @forelse($withdrawals as $w)
                <div class="bg-slate-50/40 dark:bg-slate-900/40 border-2 border-slate-100 dark:border-slate-700/60 p-4 rounded-2xl space-y-4 transition-all duration-200">
                    
                    <!-- Reference ID & Status -->
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <span class="font-mono font-black text-slate-800 dark:text-white block text-sm leading-none">
                                #WD-{{ str_pad($w->id, 5, '0', STR_PAD_LEFT) }}
                            </span>
                            <span class="text-[10px] text-slate-400 dark:text-slate-500 font-bold block mt-1.5 leading-none">
                                Filed: {{ $w->created_at->format('M d, Y h:i A') }}
                            </span>
                        </div>
                        
                        <!-- Status Badge -->
                        <div class="flex items-center gap-2">
                            @if($w->status === 'pending')
                                <span class="px-2 py-0.5 rounded-full text-[9px] font-extrabold bg-amber-50 dark:bg-amber-950/20 text-amber-600 dark:text-amber-400 border border-amber-100/50 dark:border-amber-900/30 uppercase tracking-wider animate-pulse">Pending</span>
                                <form action="{{ route('member.withdrawals.cancel', $w->id) }}" method="POST" class="inline cancel-withdrawal-form m-0">
                                    @csrf
                                    <button type="submit" class="text-[10px] font-extrabold text-rose-600 hover:text-rose-800 hover:underline outline-none focus:outline-none transition-colors cursor-pointer">
                                        Cancel
                                    </button>
                                </form>
                            @elseif($w->status === 'processing')
                                <span class="px-2 py-0.5 rounded-full text-[9px] font-extrabold bg-blue-50 dark:bg-blue-950/20 text-blue-600 dark:text-blue-400 border border-blue-100/50 dark:border-blue-900/30 uppercase tracking-wider">Processing</span>
                            @elseif($w->status === 'released')
                                <span class="px-2 py-0.5 rounded-full text-[9px] font-extrabold bg-emerald-50 dark:bg-emerald-950/20 text-emerald-600 dark:text-emerald-400 border border-emerald-100/50 dark:border-emerald-900/30 uppercase tracking-wider">Released</span>
                            @elseif($w->status === 'cancelled')
                                <span class="px-2 py-0.5 rounded-full text-[9px] font-extrabold bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-600 uppercase tracking-wider">Cancelled</span>
                            @else
                                <span class="px-2 py-0.5 rounded-full text-[9px] font-extrabold bg-rose-50 dark:bg-rose-950/20 text-rose-700 dark:text-rose-400 border border-rose-100/50 dark:border-rose-900/30 uppercase tracking-wider">{{ strtoupper($w->status) }}</span>
                            @endif
                        </div>
                    </div>

                    <!-- Inner specs grid: Requested Amount & Channel -->
                    <div class="grid grid-cols-2 gap-4 p-3 bg-white dark:bg-slate-900/60 rounded-xl border border-slate-100 dark:border-slate-800/80">
                        <div>
                            <span class="text-[9px] font-extrabold text-slate-400 dark:text-slate-500 uppercase tracking-widest block leading-none">Requested Amount</span>
                            <span class="text-sm font-extrabold text-slate-900 dark:text-white font-mono mt-1 block leading-none">₱{{ number_format($w->amount, 2) }}</span>
                        </div>
                        <div class="border-l border-slate-150 dark:border-slate-800 pl-4">
                            <span class="text-[9px] font-extrabold text-slate-400 dark:text-slate-500 uppercase tracking-widest block leading-none">Disbursement Channel</span>
                            <span class="text-xs font-bold text-slate-700 dark:text-slate-300 mt-1 block leading-tight">{{ $w->channel }}</span>
                        </div>
                    </div>

                    <!-- Reason note Block -->
                    @if($w->reason)
                        <div class="border-t border-slate-100 dark:border-slate-800/80 pt-2.5">
                            <span class="text-[9px] font-extrabold text-slate-400 dark:text-slate-500 uppercase tracking-widest block leading-none">Purpose Description</span>
                            <p class="text-xs text-slate-600 dark:text-slate-400 italic mt-1.5 pl-3 border-l-2 border-emerald-500/40 leading-relaxed bg-slate-100/40 dark:bg-slate-900/40 p-2.5 rounded-r-lg">
                                "{{ $w->reason }}"
                            </p>
                        </div>
                    @endif

                </div>
            @empty
                <div class="p-8 text-center text-slate-400 dark:text-slate-500 font-semibold italic flex flex-col items-center justify-center">
                    <div class="w-9 h-9 bg-slate-100 dark:bg-slate-900/40 rounded-xl flex items-center justify-center text-slate-400 dark:text-slate-500 border border-slate-200 dark:border-slate-700 mb-2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    </div>
                    No past or pending withdrawal requests recorded.
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const form = document.getElementById("withdrawal-request-form");
        if (form) {
            form.addEventListener("submit", function(e) {
                if (form.dataset.confirmed === "true") {
                    return;
                }
                e.preventDefault();
                
                const amount = parseFloat(form.amount.value) || 0;
                const channel = form.channel.value;
                const reason = form.reason.value;

                if (!reason.trim()) {
                    if (window.MLSAKOAlert) {
                        MLSAKOAlert.fire({
                            icon: 'warning',
                            title: 'Validation Failed',
                            text: 'Please provide a reason for the payout request.',
                            confirmButtonText: 'Acknowledge'
                        });
                    } else {
                        alert('Please provide a reason for the payout request.');
                    }
                    return;
                }

                if (window.MLSAKOAlert) {
                    MLSAKOAlert.fire({
                        icon: 'question',
                        title: 'Confirm Withdrawal',
                        html: `
                            <div class="space-y-4 text-center">
                                <p class="text-xs font-semibold text-slate-600 dark:text-slate-400">
                                    Are you sure you want to submit this savings withdrawal request?
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
                            document.getElementById('withdrawal-pin-input').value = result.value;
                            form.dataset.confirmed = "true";
                            form.submit();
                        }
                    });
                } else {
                    const pinPrompt = prompt('Are you sure you want to request a payout of ₱' + amount.toLocaleString() + '? Please enter your 6-digit PIN to confirm:');
                    if (pinPrompt) {
                        if (pinPrompt.length === 6 && !isNaN(pinPrompt)) {
                            document.getElementById('withdrawal-pin-input').value = pinPrompt;
                            form.dataset.confirmed = "true";
                            form.submit();
                        } else {
                            alert('Invalid PIN format. Submission cancelled.');
                        }
                    }
                }
            });
        }

        document.querySelectorAll(".cancel-withdrawal-form").forEach(cancelForm => {
            cancelForm.addEventListener("submit", function(e) {
                if (cancelForm.dataset.confirmed === "true") {
                    return;
                }
                e.preventDefault();

                if (window.MLSAKOAlert) {
                    MLSAKOAlert.fire({
                        icon: 'warning',
                        title: 'Cancel Withdrawal Request',
                        text: 'Are you sure you want to cancel this pending withdrawal request? This action cannot be undone.',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, Cancel Request',
                        cancelButtonText: 'No, Keep It',
                        confirmButtonColor: '#e11d48',
                        iconColor: '#e11d48'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            cancelForm.dataset.confirmed = "true";
                            cancelForm.submit();
                        }
                    });
                } else {
                    if (confirm('Are you sure you want to cancel this pending withdrawal request?')) {
                        cancelForm.dataset.confirmed = "true";
                        cancelForm.submit();
                    }
                }
            });
        });
    });
</script>
@endpush
