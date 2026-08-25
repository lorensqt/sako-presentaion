@extends('layouts.admin')

@section('title', 'Loans Archives & Directory - Sako Cooperative')

@section('header')
<div>
    <h1 class="text-2xl font-extrabold text-slate-900 dark:text-slate-100 tracking-tight serif-font">Loans Directory & Archives</h1>
    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 font-semibold">Exhaustive ledger of all member loan applications, legal contracts, and historical records.</p>
</div>
@endsection

@section('content')
<div class="space-y-6 animate-fade-in">

    <!-- KPI / Metrics Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- KPI 1 -->
        <div class="bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700/80 rounded-2xl p-5 flex items-center gap-4 shadow-3xs">
            <div class="w-12 h-12 rounded-xl bg-slate-100 dark:bg-slate-900 flex items-center justify-center text-slate-500 dark:text-slate-400 flex-shrink-0">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                </svg>
            </div>
            <div>
                <span class="text-[10px] font-black uppercase text-slate-400 tracking-wider">Total Application Portfolio</span>
                <h3 class="text-xl font-black text-slate-800 dark:text-white mt-0.5">{{ $metrics['total'] }}</h3>
            </div>
        </div>

        <!-- KPI 2 -->
        <div class="bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700/80 rounded-2xl p-5 flex items-center gap-4 shadow-3xs">
            <div class="w-12 h-12 rounded-xl bg-blue-50 dark:bg-blue-950/20 flex items-center justify-center text-blue-600 dark:text-blue-400 flex-shrink-0">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <span class="text-[10px] font-black uppercase text-slate-400 tracking-wider">Active Decision Pipeline</span>
                <h3 class="text-xl font-black text-blue-600 dark:text-blue-400 mt-0.5">{{ $metrics['pending'] }}</h3>
            </div>
        </div>

        <!-- KPI 3 -->
        <div class="bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700/80 rounded-2xl p-5 flex items-center gap-4 shadow-3xs">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 dark:bg-emerald-950/20 flex items-center justify-center text-emerald-600 dark:text-emerald-450 flex-shrink-0">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <span class="text-[10px] font-black uppercase text-slate-400 tracking-wider">Fully Approved & Released</span>
                <h3 class="text-xl font-black text-emerald-600 dark:text-emerald-450 mt-0.5">{{ $metrics['approved'] }}</h3>
            </div>
        </div>

        <!-- KPI 4 -->
        <div class="bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700/80 rounded-2xl p-5 flex items-center gap-4 shadow-3xs">
            <div class="w-12 h-12 rounded-xl bg-rose-50 dark:bg-rose-950/20 flex items-center justify-center text-rose-600 dark:text-rose-455 flex-shrink-0">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <span class="text-[10px] font-black uppercase text-slate-400 tracking-wider">Rejected / Archived</span>
                <h3 class="text-xl font-black text-rose-600 dark:text-rose-450 mt-0.5">{{ $metrics['rejected'] }}</h3>
            </div>
        </div>
    </div>

    <!-- Search & Filters Container -->
    <div class="bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700/80 rounded-2xl p-5 flex flex-col md:flex-row gap-4 items-center justify-between shadow-3xs">
        <form method="GET" action="{{ route('admin.loans') }}" class="w-full flex flex-col md:flex-row gap-3">
            <!-- Search Bar -->
            <div class="relative flex-grow">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </span>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search borrower name, ID..." class="w-full pl-10 pr-4 py-2.5 text-xs font-bold border border-slate-200 dark:border-slate-700/80 bg-slate-50/50 dark:bg-slate-900 text-slate-900 dark:text-slate-100 rounded-xl outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all placeholder-slate-400 dark:placeholder-slate-500">
            </div>

            <!-- Status Filter -->
            <div class="w-full md:w-48 flex-shrink-0">
                <select name="status" onchange="this.form.submit()" class="w-full px-3 py-2.5 text-xs font-bold border border-slate-200 dark:border-slate-700/80 bg-slate-50/50 dark:bg-slate-900 text-slate-900 dark:text-slate-100 rounded-xl outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all cursor-pointer">
                    <option value="">All Statuses</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="released" {{ request('status') === 'released' ? 'selected' : '' }}>Released</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>

            <!-- Filter Submit & Reset -->
            <div class="flex gap-2">
                <button type="submit" class="bg-slate-900 hover:bg-slate-800 dark:bg-slate-700 dark:hover:bg-slate-600 text-white font-extrabold text-xs px-5 py-2.5 rounded-xl transition-all shadow-sm cursor-pointer">
                    Filter
                </button>
                @if(request('search') || request('status'))
                    <a href="{{ route('admin.loans') }}" class="bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-750 text-slate-600 dark:text-slate-300 font-extrabold text-xs px-4 py-2.5 rounded-xl transition-all flex items-center justify-center border border-slate-200 dark:border-slate-700">
                        Reset
                    </a>
                @endif
            </div>
        </form>
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
                        <th class="px-6 py-4.5">Contract Status</th>
                        <th class="px-6 py-4.5">Filing Date</th>
                        <th class="px-6 py-4.5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 dark:divide-slate-700/50 text-xs font-semibold text-slate-700 dark:text-slate-300">
                    @forelse($allLoans as $loan)
                        <tr class="hover:bg-slate-50/30 dark:hover:bg-slate-900/10 transition-colors">
                            <!-- Borrower -->
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full bg-emerald-50 text-emerald-600 dark:bg-emerald-950/45 dark:text-emerald-450 border border-emerald-100/40 dark:border-emerald-900/20 flex items-center justify-center font-bold text-xs">
                                        {{ strtoupper(substr($loan->borrower->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-slate-950 dark:text-slate-100">{{ $loan->borrower->name }}</h4>
                                        <p class="text-[10px] text-slate-400 dark:text-slate-500 mt-0.5">ID: {{ $loan->borrower->company_id ?: 'N/A' }}</p>
                                    </div>
                                </div>
                            </td>

                            <!-- Loan Type & Category -->
                            <td class="px-6 py-4">
                                <span class="font-bold text-slate-800 dark:text-slate-200 block">
                                    {{ $loan->loan ? $loan->loan->name : ucwords(str_replace('_', ' ', $loan->loan_type)) }}
                                </span>
                                <span class="text-[9px] font-extrabold uppercase tracking-wider text-emerald-600 dark:text-emerald-400 block mt-0.5">{{ $loan->loan_category }} Loan</span>
                            </td>

                            <!-- Amount -->
                            <td class="px-6 py-4">
                                <span class="font-bold font-mono text-slate-800 dark:text-slate-100 bg-slate-50 dark:bg-slate-950 border border-slate-200/50 dark:border-slate-800/80 px-2.5 py-1 rounded-xl shadow-3xs text-xs">
                                    ₱{{ number_format($loan->requested_amount, 2) }}
                                </span>
                            </td>

                            <!-- Status Badge -->
                            <td class="px-6 py-4">
                                @if($loan->status === 'approved' || $loan->status === 'released')
                                    <span class="px-2.5 py-1 rounded-full text-[9px] font-extrabold bg-emerald-50 dark:bg-emerald-950/30 text-emerald-700 dark:text-emerald-400 border border-emerald-100/40 dark:border-emerald-900/30 uppercase tracking-wider">Approved</span>
                                @elseif($loan->status === 'rejected')
                                    <span class="px-2.5 py-1 rounded-full text-[9px] font-extrabold bg-rose-50 dark:bg-rose-950/30 text-rose-700 dark:text-rose-455 border border-rose-100/20 dark:border-rose-900/20 uppercase tracking-wider">Rejected</span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[9px] font-extrabold bg-blue-50 dark:bg-blue-950/30 text-blue-700 dark:text-blue-455 border border-blue-100/60 dark:border-blue-900/20 uppercase tracking-wider">Pending ({{ ucwords(str_replace('_', ' ', $loan->current_stage)) }})</span>
                                @endif
                            </td>

                            <!-- Date -->
                            <td class="px-6 py-4 text-slate-500 dark:text-slate-400">
                                {{ $loan->created_at->format('M d, Y') }}
                            </td>

                            <!-- Actions (Download PDF & Delete Loan only) -->
                            <td class="px-6 py-4 text-right">
                                <div class="inline-flex items-center justify-end gap-2">
                                    <!-- Download PDF -->
                                    <a href="{{ route('admin.loans.pdf', $loan->id) }}" target="_blank" class="p-2 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400 hover:text-emerald-600 dark:hover:text-emerald-400 hover:border-emerald-300 dark:hover:border-emerald-800/80 transition-all hover:shadow-3xs active:scale-95 flex items-center justify-center cursor-pointer" title="Download Official Contract PDF">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                    </a>

                                    <!-- Delete Loan Application -->
                                    <form action="{{ route('admin.loans.destroy_application', $loan->id) }}" method="POST" class="inline delete-loan-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-455 hover:text-rose-600 dark:hover:text-rose-400 hover:border-rose-300 dark:hover:border-rose-800 transition-all hover:shadow-3xs active:scale-95 flex items-center justify-center cursor-pointer" title="Delete Loan application">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-4v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-16 text-center text-slate-400 dark:text-slate-500">
                                <div class="w-12 h-12 rounded-full bg-slate-50 dark:bg-slate-900 border border-slate-100 dark:border-slate-800 flex items-center justify-center mx-auto mb-3 text-slate-400 dark:text-slate-500">
                                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6M12 9v6m9-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <p class="text-xs font-bold">No loans matched your criteria.</p>
                                <p class="text-2xs text-slate-400 dark:text-slate-500 mt-1 font-semibold">Try modifying your filters or search keywords.</p>
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
@endsection

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Intercept delete forms to trigger a stunning SweetAlert confirm dialogue
        const deleteForms = document.querySelectorAll(".delete-loan-form");
        deleteForms.forEach(form => {
            form.addEventListener("submit", function (e) {
                e.preventDefault();
                
                window.MLSAKOAlert.fire({
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
        });
    });
</script>
@endpush
