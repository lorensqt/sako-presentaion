@forelse($withdrawals as $w)
    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-900/20 transition-colors">
        <!-- Select Checkbox -->
        <td class="px-6 py-4" style="width: 45px;">
            <input type="checkbox" name="withdrawal_ids[]" value="{{ $w->id }}" class="withdrawal-checkbox rounded border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-emerald-600 focus:ring-emerald-500 dark:focus:ring-emerald-600 w-4 h-4 cursor-pointer">
        </td>

        <!-- Member Profile -->
        <td class="px-6 py-4">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-full bg-slate-100 dark:bg-slate-900 flex items-center justify-center font-bold text-slate-700 dark:text-slate-300 text-xs border border-slate-200/50 dark:border-slate-700/60 shadow-2xs">
                    {{ strtoupper(substr($w->user->name, 0, 1)) }}
                </div>
                <div>
                    <h4 class="font-bold text-slate-950 dark:text-white leading-snug">{{ $w->user->name }}</h4>
                    <p class="text-[10px] text-slate-400 dark:text-slate-500 font-semibold font-mono">
                        {{ $w->user->email }} @if($w->user->company_id)(ID: {{ $w->user->company_id }})@endif
                    </p>
                </div>
            </div>
        </td>

        <!-- Requested Amount -->
        <td class="px-6 py-4">
            <span class="font-black font-mono text-slate-900 dark:text-white text-sm">₱{{ number_format($w->amount, 2) }}</span>
            @if($w->reason)
                <span class="text-[10px] text-slate-400 dark:text-slate-500 italic block truncate max-w-xs mt-0.5" title="{{ $w->reason }}">
                    <i class="fa-solid fa-quote-left text-[9px] text-slate-300 dark:text-slate-600 mr-1"></i>{{ $w->reason }}
                </span>
            @endif
        </td>

        <!-- Disbursement Channel -->
        <td class="px-6 py-4">
            <div class="flex items-center gap-1.5 text-slate-700 dark:text-slate-300 font-semibold text-xs">
                @if(str_contains(strtolower($w->channel), 'mcash'))
                    <i class="fa-solid fa-mobile-screen text-blue-500 text-xs"></i>
                @else
                    <i class="fa-solid fa-money-check-dollar text-emerald-600 dark:text-emerald-400 text-xs"></i>
                @endif
                <span>{{ $w->channel }}</span>
            </div>
        </td>

        <!-- Admin Remarks -->
        <td class="px-6 py-4 max-w-xs">
            @if($w->remarks || $w->transaction_id)
                <span class="text-xs text-slate-700 dark:text-slate-300 font-medium block truncate" title="{{ $w->remarks ?: $w->transaction_id }}">
                    <i class="fa-solid fa-comment-dots text-slate-400 dark:text-slate-500 text-[10px] mr-1"></i>{{ $w->remarks ?: $w->transaction_id }}
                </span>
            @else
                <span class="text-slate-400 dark:text-slate-500 text-xs italic">—</span>
            @endif
        </td>

        <!-- Date Filed -->
        <td class="px-6 py-4 text-slate-500 dark:text-slate-400 font-semibold font-mono text-xs whitespace-nowrap">
            <i class="fa-regular fa-clock text-[10px] text-slate-400 mr-1"></i>{{ $w->created_at->format('M d, Y h:i A') }}
        </td>

        <!-- Status Badge -->
        <td class="px-6 py-4 whitespace-nowrap">
            @if($w->status === 'pending')
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[9px] font-extrabold bg-amber-50 dark:bg-amber-950/20 text-amber-600 dark:text-amber-400 border border-amber-100 dark:border-amber-900/20 uppercase tracking-wider animate-pulse">
                    <i class="fa-solid fa-hourglass-half text-[9px]"></i> Pending
                </span>
            @elseif($w->status === 'processing')
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[9px] font-extrabold bg-blue-50 dark:bg-blue-950/20 text-blue-600 dark:text-blue-400 border border-blue-100 dark:border-blue-900/20 uppercase tracking-wider">
                    <i class="fa-solid fa-arrows-rotate fa-spin text-[9px]"></i> Processing
                </span>
            @elseif($w->status === 'released')
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[9px] font-extrabold bg-emerald-50 dark:bg-emerald-950/20 text-emerald-600 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-900/20 uppercase tracking-wider">
                    <i class="fa-solid fa-circle-check text-[9px]"></i> Released
                </span>
            @else
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[9px] font-extrabold bg-rose-50 dark:bg-rose-950/20 text-rose-600 dark:text-rose-400 border border-rose-100 dark:border-rose-900/20 uppercase tracking-wider">
                    <i class="fa-solid fa-circle-xmark text-[9px]"></i> {{ strtoupper($w->status) }}
                </span>
            @endif
        </td>

        <!-- Lifecycle Actions -->
        <td class="px-6 py-4 text-right whitespace-nowrap">
            @if($w->status === 'pending')
                <div class="inline-flex items-center gap-2">
                    <form action="{{ route('admin.withdrawals.status', $w) }}" method="POST" class="inline m-0 form-ack-withdrawal">
                        @csrf
                        <input type="hidden" name="action" value="acknowledge">
                        <button type="submit" class="inline-flex items-center gap-1.5 bg-blue-600 hover:bg-blue-700 text-white font-extrabold text-[10px] uppercase tracking-wider px-3.5 py-2 rounded-xl shadow-xs transition-all cursor-pointer">
                            <i class="fa-solid fa-check-to-slot text-[10px]"></i>
                            <span>Acknowledge</span>
                        </button>
                    </form>
                    <button type="button" 
                            class="btn-trigger-reject inline-flex items-center gap-1.5 bg-rose-50 hover:bg-rose-100 dark:bg-rose-950/30 text-rose-600 dark:text-rose-400 border border-rose-200 dark:border-rose-800/60 font-extrabold text-[10px] uppercase tracking-wider px-3 py-2 rounded-xl shadow-xs transition-all cursor-pointer"
                            data-action="{{ route('admin.withdrawals.status', $w) }}"
                            data-ref="REF #WD-{{ str_pad($w->id, 5, '0', STR_PAD_LEFT) }}"
                            data-name="{{ $w->user->name }}"
                            data-amount="₱{{ number_format($w->amount, 2) }}">
                        <i class="fa-solid fa-ban text-[10px]"></i>
                        <span>Reject</span>
                    </button>
                </div>
            @elseif($w->status === 'processing')
                <div class="inline-flex items-center gap-2">
                    <button type="button" 
                            class="btn-trigger-release inline-flex items-center gap-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-[10px] uppercase tracking-wider px-3.5 py-2 rounded-xl shadow-xs transition-all cursor-pointer"
                            data-action="{{ route('admin.withdrawals.status', $w) }}"
                            data-ref="REF #WD-{{ str_pad($w->id, 5, '0', STR_PAD_LEFT) }}"
                            data-name="{{ $w->user->name }}"
                            data-amount="₱{{ number_format($w->amount, 2) }}">
                        <i class="fa-solid fa-hand-holding-dollar text-[10px]"></i>
                        <span>Release Funds</span>
                    </button>
                    <button type="button" 
                            class="btn-trigger-reject inline-flex items-center gap-1.5 bg-rose-50 hover:bg-rose-100 dark:bg-rose-950/30 text-rose-600 dark:text-rose-400 border border-rose-200 dark:border-rose-800/60 font-extrabold text-[10px] uppercase tracking-wider px-3 py-2 rounded-xl shadow-xs transition-all cursor-pointer"
                            data-action="{{ route('admin.withdrawals.status', $w) }}"
                            data-ref="REF #WD-{{ str_pad($w->id, 5, '0', STR_PAD_LEFT) }}"
                            data-name="{{ $w->user->name }}"
                            data-amount="₱{{ number_format($w->amount, 2) }}">
                        <i class="fa-solid fa-ban text-[10px]"></i>
                        <span>Reject</span>
                    </button>
                </div>
            @elseif($w->status === 'released')
                <span class="text-emerald-600 dark:text-emerald-400 font-extrabold text-[10px] uppercase tracking-wider inline-flex items-center gap-1.5">
                    <i class="fa-solid fa-circle-check text-xs"></i> Completed
                </span>
            @elseif($w->status === 'rejected')
                <span class="text-rose-500 dark:text-rose-400 font-extrabold text-[10px] uppercase tracking-wider inline-flex items-center gap-1.5">
                    <i class="fa-solid fa-circle-xmark text-xs"></i> Rejected
                </span>
            @else
                <span class="text-slate-400 dark:text-slate-500 font-extrabold text-[10px] uppercase tracking-wide italic">No actions pending</span>
            @endif
        </td>
    </tr>
@empty
    <tr>
        <td colspan="8" class="px-6 py-12 text-center text-slate-400 dark:text-slate-500 font-medium italic text-xs">
            <div class="w-12 h-12 rounded-2xl bg-slate-50 dark:bg-slate-900/60 border border-slate-200/60 dark:border-slate-800 flex items-center justify-center mx-auto text-slate-400 mb-3 text-base">
                <i class="fa-solid fa-box-open"></i>
            </div>
            No withdrawal requests found matching your filter criteria.
        </td>
    </tr>
@endforelse
