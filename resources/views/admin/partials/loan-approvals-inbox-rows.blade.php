@forelse($myInboxLoans as $loan)
    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-900/30 transition-colors">
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
        <td class="px-6 py-4">
            <span class="font-bold text-slate-800 dark:text-slate-200 block truncate max-w-[160px]">
                {{ $loan->loan ? $loan->loan->name : ucwords(str_replace('_', ' ', $loan->loan_type)) }}
            </span>
            <span class="text-[9px] font-extrabold uppercase tracking-wider text-emerald-600 dark:text-emerald-400 block mt-0.5">{{ $loan->loan_category }} Loan</span>
        </td>
        <td class="px-6 py-4">
            <span class="font-bold font-mono text-slate-800 dark:text-slate-100 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 px-2.5 py-1 rounded-xl text-xs shadow-2xs">
                ₱{{ number_format($loan->requested_amount, 2) }}
            </span>
        </td>
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
        <td class="px-6 py-4">
            @if($loan->current_stage === 'hrmd_staff')
                <div class="inline-flex flex-col items-start gap-1">
                    <span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-amber-50 dark:bg-amber-950/40 text-amber-800 dark:text-amber-300 border border-amber-300/80 dark:border-amber-800 uppercase tracking-wider shadow-2xs">
                        HRMD Seq #{{ $loan->current_hrmd_sequence ?? 1 }}
                    </span>
                    @if(auth()->user()->hasRole('hrmd_staff'))
                        @if((int) auth()->user()->hrmd_sequence === (int) ($loan->current_hrmd_sequence ?? 1) || auth()->user()->role === 'super_admin')
                            <span class="text-[9px] font-black text-emerald-600 dark:text-emerald-400 flex items-center gap-1">
                                <i class="fa-solid fa-circle-exclamation text-[10px]"></i>
                                <span>Your Turn to Review</span>
                            </span>
                        @endif
                    @endif
                </div>
            @else
                <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-blue-50 dark:bg-blue-950/30 text-blue-700 dark:text-blue-400 border border-blue-100/40 dark:border-blue-900/30 uppercase tracking-wider">
                    {{ ucwords(str_replace('_', ' ', $loan->current_stage)) }}
                </span>
            @endif
        </td>
        <td class="px-6 py-4 font-semibold text-slate-500 dark:text-slate-400 whitespace-nowrap">
            {{ $loan->created_at->format('M d, Y') }}
        </td>
        <td class="px-6 py-4 text-right">
            <div class="inline-flex items-center justify-end gap-2">
                <button class="btn-review-loan inline-flex items-center gap-1.5 bg-emerald-600 hover:bg-emerald-700 dark:bg-emerald-650 dark:hover:bg-emerald-600 text-white font-bold text-xs px-3.5 py-2 rounded-xl shadow-md shadow-emerald-600/10 hover:shadow-emerald-600/20 hover:-translate-y-0.5 transition-all duration-200 cursor-pointer" data-loan="{{ json_encode([
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
                    'current_hrmd_sequence' => $loan->current_hrmd_sequence,
                    'form_data' => $loan->form_data,
                    'workflow_steps' => $loan->workflow_steps,
                    'ledger_url' => $loan->ledger_url,
                    'schedule_url' => $loan->schedule_url,
                    'documents' => $loan->documents->map(fn($d) => [
                        'id' => $d->id,
                        'original_name' => $d->original_name,
                        'file_size' => $d->formatted_file_size,
                        'file_url' => $d->file_url,
                    ])->values()
                ]) }}">
                    <i class="fa-solid fa-file-signature text-xs"></i>
                    <span>Review Application</span>
                </button>
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="8" class="px-6 py-16 text-center text-slate-400 dark:text-slate-500">
            <div class="w-12 h-12 rounded-full bg-slate-50 dark:bg-slate-900 border border-slate-100 dark:border-slate-800 flex items-center justify-center mx-auto mb-3 text-slate-400 dark:text-slate-500 shadow-2xs">
                <i class="fa-solid fa-circle-check text-xl text-emerald-500"></i>
            </div>
            <p class="text-xs font-bold text-slate-800 dark:text-slate-200">No matching applications in your inbox</p>
            <p class="text-[11px] text-slate-400 dark:text-slate-500 mt-1 font-medium">No pending loans requiring your action matched the active filter criteria.</p>
        </td>
    </tr>
@endforelse
