@forelse($allLoans as $loan)
    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-900/30 transition-colors">
        <!-- Borrower Profile -->
        <td class="px-6 py-4 flex items-center gap-3">
            <div class="w-9 h-9 rounded-full bg-slate-100 dark:bg-slate-900 flex items-center justify-center font-bold text-slate-600 dark:text-slate-300 text-xs border border-slate-200/40 dark:border-slate-700/40 flex-shrink-0">
                {{ strtoupper(substr($loan->borrower->name, 0, 1)) }}
            </div>
            <div class="min-w-0">
                <h4 class="font-bold text-slate-950 dark:text-slate-100 truncate">{{ $loan->borrower->name }}</h4>
                <p class="text-[10px] text-slate-400 dark:text-slate-500 font-semibold mt-0.5">
                    <span>ID: {{ $loan->borrower->company_id ?: 'N/A' }}</span>
                    <span class="mx-1">•</span>
                    <span class="font-mono text-emerald-600 dark:text-emerald-400">LN-{{ str_pad($loan->id, 5, '0', STR_PAD_LEFT) }}</span>
                </p>
            </div>
        </td>

        <!-- Loan Details -->
        <td class="px-6 py-4">
            <span class="font-bold text-slate-800 dark:text-slate-200 block truncate max-w-[170px]">
                {{ $loan->loan ? $loan->loan->name : ucwords(str_replace('_', ' ', $loan->loan_type)) }}
            </span>
            <span class="text-[9px] font-extrabold uppercase tracking-wider text-emerald-600 dark:text-emerald-400 block mt-0.5">
                {{ $loan->loan_category }} Loan
            </span>
        </td>

        <!-- Requested Principal -->
        <td class="px-6 py-4">
            <span class="font-bold font-mono text-slate-800 dark:text-slate-100 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 px-2.5 py-1 rounded-xl shadow-2xs text-xs">
                ₱{{ number_format($loan->requested_amount, 2) }}
            </span>
        </td>

        <!-- Ledger Column -->
        <td class="px-6 py-4">
            @if($loan->ledger_path)
                <button type="button" class="btn-preview-pdf inline-flex items-center gap-1.5 px-2.5 py-1 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 dark:bg-emerald-950/40 dark:hover:bg-emerald-900/50 dark:text-emerald-400 rounded-lg text-[10px] font-extrabold cursor-pointer border border-emerald-200/50 dark:border-emerald-800/40 shadow-3xs transition-all"
                    data-url="{{ $loan->ledger_url }}"
                    data-name="Loan_Ledger_LN-{{ str_pad($loan->id, 5, '0', STR_PAD_LEFT) }}.pdf"
                    data-size="PDF">
                    <i class="fa-solid fa-file-pdf text-xs"></i>
                    <span>Ledger</span>
                </button>
            @else
                <span class="text-[10px] text-slate-400 dark:text-slate-500 font-semibold italic">—</span>
            @endif
        </td>

        <!-- Schedule Column -->
        <td class="px-6 py-4">
            @if($loan->schedule_path)
                <button type="button" class="btn-preview-pdf inline-flex items-center gap-1.5 px-2.5 py-1 bg-blue-50 hover:bg-blue-100 text-blue-700 dark:bg-blue-950/40 dark:hover:bg-blue-900/50 dark:text-blue-400 rounded-lg text-[10px] font-extrabold cursor-pointer border border-blue-200/50 dark:border-blue-800/40 shadow-3xs transition-all"
                    data-url="{{ $loan->schedule_url }}"
                    data-name="Amortization_Schedule_LN-{{ str_pad($loan->id, 5, '0', STR_PAD_LEFT) }}.pdf"
                    data-size="PDF">
                    <i class="fa-solid fa-calendar-days text-xs"></i>
                    <span>Schedule</span>
                </button>
            @else
                <span class="text-[10px] text-slate-400 dark:text-slate-500 font-semibold italic">—</span>
            @endif
        </td>

        <!-- Contract Status -->
        <td class="px-6 py-4">
            @if($loan->status === 'approved' || $loan->status === 'released')
                <span class="inline-flex items-center gap-1 text-[9px] font-bold text-emerald-700 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/40 px-2.5 py-1 rounded-full border border-emerald-200/60 dark:border-emerald-800/60 uppercase tracking-wider">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>Released</span>
                </span>
            @elseif($loan->status === 'rejected')
                <span class="inline-flex items-center gap-1 text-[9px] font-bold text-rose-700 dark:text-rose-400 bg-rose-50 dark:bg-rose-950/40 px-2.5 py-1 rounded-full border border-rose-200/60 dark:border-rose-900/40 uppercase tracking-wider">
                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                    <span>Rejected</span>
                </span>
            @else
                <span class="inline-flex items-center gap-1 text-[9px] font-bold text-blue-700 dark:text-blue-400 bg-blue-50 dark:bg-blue-950/40 px-2.5 py-1 rounded-full border border-blue-200/60 dark:border-blue-800/60 uppercase tracking-wider">
                    <span>Pending</span>
                    <span class="text-[8px] font-semibold text-slate-400">({{ ucwords(str_replace('_', ' ', $loan->current_stage)) }})</span>
                </span>
            @endif
        </td>

        <!-- Filing Date -->
        <td class="px-6 py-4 font-semibold text-slate-500 dark:text-slate-400 whitespace-nowrap">
            {{ $loan->created_at->format('M d, Y') }}
        </td>

        <!-- Actions -->
        <td class="px-6 py-4 text-right">
            <div class="inline-flex items-center justify-end gap-1.5">
                <!-- Download Contract PDF -->
                <a href="{{ route('admin.loans.pdf', $loan->id) }}" target="_blank" class="w-8 h-8 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400 hover:text-emerald-600 dark:hover:text-emerald-400 hover:border-emerald-300 dark:hover:border-emerald-800/80 hover:bg-emerald-50 dark:hover:bg-emerald-950/30 transition-all shadow-2xs hover:scale-105 active:scale-95 flex items-center justify-center cursor-pointer" title="Download Official Contract PDF">
                    <i class="fa-solid fa-file-arrow-down text-xs"></i>
                </a>

                <!-- Delete Loan Application -->
                <form action="{{ route('admin.loans.destroy_application', $loan->id) }}" method="POST" class="inline delete-loan-form">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-8 h-8 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-400 dark:text-slate-500 hover:text-rose-600 dark:hover:text-rose-400 hover:border-rose-300 dark:hover:border-rose-800 hover:bg-rose-50 dark:hover:bg-rose-950/30 transition-all shadow-2xs hover:scale-105 active:scale-95 flex items-center justify-center cursor-pointer" title="Delete Loan Application">
                        <i class="fa-solid fa-trash-can text-xs"></i>
                    </button>
                </form>
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="8" class="px-6 py-16 text-center text-slate-400 dark:text-slate-500">
            <div class="w-14 h-14 rounded-2xl bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex items-center justify-center mx-auto mb-3 text-xl text-slate-400 dark:text-slate-500 shadow-2xs">
                <i class="fa-solid fa-box-open"></i>
            </div>
            <h4 class="text-xs font-bold text-slate-800 dark:text-slate-200">No loan records matched your criteria</h4>
            <p class="text-[11px] text-slate-400 dark:text-slate-500 mt-1 font-medium">Try broadening your keywords or resetting active status/category filters.</p>
            <button type="button" class="mt-2 text-xs font-bold text-emerald-600 dark:text-emerald-400 hover:underline cursor-pointer inline-flex items-center gap-1.5" onclick="document.getElementById('btn-reset-filters')?.click()">
                <i class="fa-solid fa-arrows-rotate text-[10px]"></i>
                <span>Clear all filters</span>
            </button>
        </td>
    </tr>
@endforelse
