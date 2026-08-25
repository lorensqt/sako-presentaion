@extends('layouts.admin')

@section('title', 'Deduction Adjustments - Sako Cooperative')

@section('header')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h1 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight serif-font">Deduction Adjustments</h1>
        <p class="text-xs text-slate-550 dark:text-slate-400 mt-1 font-semibold">Review, approve, or reject cooperative member payroll contribution adjustment filings.</p>
    </div>
</div>
@endsection

@section('content')
<div class="space-y-8 animate-fade-in">

    <!-- KPI METRIC CARDS -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
        <!-- Pending Card -->
        <div class="bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700/80 rounded-3xl p-6 shadow-sm flex items-center justify-between transition-all hover:shadow-md">
            <div class="space-y-1">
                <span class="text-[10px] font-extrabold text-amber-600 dark:text-amber-400 uppercase tracking-widest block">Pending Reviews</span>
                <p class="text-3xl font-black text-slate-900 dark:text-white font-mono leading-none">{{ $pendingCount }}</p>
                <p class="text-[10px] text-slate-400 dark:text-slate-500 font-semibold">Awaiting administrative decision</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-amber-50 dark:bg-amber-950/20 text-amber-600 dark:text-amber-400 border border-amber-100/30 flex items-center justify-center text-lg">
                <svg class="w-6 h-6 {{ $pendingCount > 0 ? 'animate-pulse' : '' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>

        <!-- Approved Card -->
        <div class="bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700/80 rounded-3xl p-6 shadow-sm flex items-center justify-between transition-all hover:shadow-md">
            <div class="space-y-1">
                <span class="text-[10px] font-extrabold text-emerald-600 dark:text-emerald-400 uppercase tracking-widest block">Approved Changes</span>
                <p class="text-3xl font-black text-slate-900 dark:text-white font-mono leading-none">{{ $approvedCount }}</p>
                <p class="text-[10px] text-slate-400 dark:text-slate-500 font-semibold">Successfully applied adjustments</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 dark:bg-emerald-950/20 text-emerald-600 dark:text-emerald-400 border border-emerald-100/30 flex items-center justify-center text-lg">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>

        <!-- Rejected Card -->
        <div class="bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700/80 rounded-3xl p-6 shadow-sm flex items-center justify-between transition-all hover:shadow-md">
            <div class="space-y-1">
                <span class="text-[10px] font-extrabold text-rose-600 dark:text-rose-450 uppercase tracking-widest block">Rejected Filings</span>
                <p class="text-3xl font-black text-slate-900 dark:text-white font-mono leading-none">{{ $rejectedCount }}</p>
                <p class="text-[10px] text-slate-400 dark:text-slate-500 font-semibold">Denied adjustment requests</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-rose-50 dark:bg-rose-950/20 text-rose-600 dark:text-rose-400 border border-rose-100/30 flex items-center justify-center text-lg">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
    </div>

    <!-- MAIN DECISION BOARD -->
    <div class="bg-white dark:bg-slate-800 rounded-[2rem] border border-slate-100 dark:border-slate-700/80 shadow-sm overflow-hidden">
        <div class="p-6 sm:p-8 border-b border-slate-50 dark:border-slate-700/80 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="space-y-0.5">
                <h3 class="text-base font-extrabold text-slate-950 dark:text-white serif-font tracking-tight">Active Adjustment Requests Queue</h3>
                <p class="text-[11px] font-semibold text-slate-450 dark:text-slate-500">Live operational ledger of member adjustment forms filed on payday schedules.</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-900/50 border-b border-slate-100 dark:border-slate-700/80 text-[10px] font-extrabold text-slate-400 dark:text-slate-500 uppercase tracking-widest">
                        <th class="px-6 py-4.5">Member details</th>
                        <th class="px-6 py-4.5">Proposed Savings</th>
                        <th class="px-6 py-4.5">Proposed Fixed</th>
                        <th class="px-6 py-4.5">Requested Cycle</th>
                        <th class="px-6 py-4.5">Remarks / Reason</th>
                        <th class="px-6 py-4.5">Status</th>
                        <th class="px-6 py-4.5 text-right">Decision Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 dark:divide-slate-700 text-slate-700 dark:text-slate-350 font-medium">
                    @forelse($deductions as $req)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-900/10 transition-colors">
                            <!-- User Details -->
                            <td class="px-6 py-4 flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full bg-slate-100 dark:bg-slate-900 flex items-center justify-center font-bold text-slate-600 dark:text-slate-450 text-xs border border-slate-200/40 dark:border-slate-700/65 flex-shrink-0">
                                    {{ strtoupper(substr($req->user->name, 0, 1)) }}
                                </div>
                                <div>
                                    <h4 class="font-bold text-slate-950 dark:text-white">{{ $req->user->name }}</h4>
                                    <p class="text-[10px] text-slate-400 dark:text-slate-500 font-semibold font-mono">{{ $req->user->email }} (ID: {{ $req->user->company_id ?: 'N/A' }})</p>
                                </div>
                            </td>

                            <!-- Proposed Savings -->
                            <td class="px-6 py-4 font-extrabold text-slate-950 dark:text-white font-mono text-sm">
                                ₱{{ number_format($req->savings_amount, 2) }}
                            </td>

                            <!-- Proposed Fixed -->
                            <td class="px-6 py-4 font-extrabold text-slate-950 dark:text-white font-mono text-sm">
                                ₱{{ number_format($req->fixed_amount, 2) }}
                            </td>

                            <!-- Requested Cycle -->
                            <td class="px-6 py-4 text-slate-500 dark:text-slate-400 font-mono font-semibold">
                                {{ $req->effectivity_date->format('M d, Y') }}
                            </td>

                            <!-- Remarks -->
                            <td class="px-6 py-4 text-slate-450 dark:text-slate-500 italic max-w-xs truncate" title="{{ $req->remarks ?: 'No remarks submitted' }}">
                                {{ $req->remarks ? '"' . $req->remarks . '"' : '—' }}
                            </td>

                            <!-- Status -->
                            <td class="px-6 py-4">
                                @if($req->status === 'pending')
                                    <span class="px-2.5 py-1 rounded-full text-[9px] font-extrabold bg-amber-50 dark:bg-amber-950/20 text-amber-600 dark:text-amber-400 border border-amber-100 dark:border-amber-900/20 uppercase tracking-wider animate-pulse">Pending</span>
                                @elseif($req->status === 'approved')
                                    <span class="px-2.5 py-1 rounded-full text-[9px] font-extrabold bg-emerald-50 dark:bg-emerald-950/20 text-emerald-600 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-900/20 uppercase tracking-wider">Approved</span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[9px] font-extrabold bg-rose-50 dark:bg-rose-950/20 text-rose-600 dark:text-rose-450 border border-rose-100 dark:border-rose-900/20 uppercase tracking-wider">Rejected</span>
                                @endif
                            </td>

                            <!-- Actions -->
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2.5">
                                    @if($req->status === 'pending')
                                        <!-- Approve form -->
                                        <form action="{{ route('admin.deductions.status', $req) }}" method="POST" class="inline m-0 deduction-approve-form">
                                            @csrf
                                            <input type="hidden" name="action" value="approve">
                                            <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-[10px] uppercase tracking-wider px-3.5 py-2 rounded-xl shadow-xs transition-all cursor-pointer">
                                                Approve
                                            </button>
                                        </form>

                                        <!-- Reject form -->
                                        <form action="{{ route('admin.deductions.status', $req) }}" method="POST" class="inline m-0 deduction-reject-form">
                                            @csrf
                                            <input type="hidden" name="action" value="reject">
                                            <button type="submit" class="bg-rose-600 hover:bg-rose-700 text-white font-extrabold text-[10px] uppercase tracking-wider px-3.5 py-2 rounded-xl shadow-xs transition-all cursor-pointer">
                                                Reject
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-slate-400 dark:text-slate-500 font-extrabold text-[10px] uppercase tracking-wide italic mr-1">No actions pending</span>
                                    @endif

                                    <!-- PDF Generation Button -->
                                    <a href="{{ route('admin.deductions.pdf', $req) }}" target="_blank" class="bg-slate-100 hover:bg-slate-200 text-slate-700 dark:bg-slate-700/60 dark:hover:bg-slate-700 dark:text-slate-200 font-extrabold text-[10px] uppercase tracking-wider px-3 py-2 rounded-xl transition-all flex items-center gap-1.5 cursor-pointer" title="Preview Form PDF">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        PDF
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-400 dark:text-slate-500 font-extrabold italic text-xs">
                                No payroll deduction adjustment requests recorded in the pipeline.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($deductions->hasPages())
            <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-700">
                {{ $deductions->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const approveForms = document.querySelectorAll('.deduction-approve-form');
        const rejectForms = document.querySelectorAll('.deduction-reject-form');
        const alertInstance = window.MLSAKOAlert || Swal;

        approveForms.forEach(form => {
            form.addEventListener('submit', function (e) {
                e.preventDefault();
                alertInstance.fire({
                    icon: 'question',
                    title: 'Approve Adjustment?',
                    text: 'Are you sure you want to approve this payroll deduction adjustment request?',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, Approve',
                    cancelButtonText: 'Cancel',
                    iconColor: '#10b981'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });

        rejectForms.forEach(form => {
            form.addEventListener('submit', function (e) {
                e.preventDefault();
                alertInstance.fire({
                    icon: 'warning',
                    title: 'Reject Adjustment?',
                    text: 'Are you sure you want to reject this payroll deduction adjustment request?',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, Reject',
                    cancelButtonText: 'Cancel',
                    iconColor: '#ef4444'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    });
</script>
@endpush
