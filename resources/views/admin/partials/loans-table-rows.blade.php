@forelse($loanProducts as $product)
    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-900/10 transition-colors {{ !$product->is_active ? 'opacity-60 bg-slate-50/30' : '' }}">
        <td class="px-6 py-4">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-slate-100 dark:bg-slate-900 flex items-center justify-center font-bold text-slate-600 text-xs border border-slate-200/40 dark:border-slate-700/55 flex-shrink-0">
                    <img src="https://img.icons8.com/?size=100&id=oWdlOvw3CmcM&format=png&color=000000" class="w-5 h-5 flex-shrink-0 dark:hidden" alt="Facility Icon">
                    <img src="https://img.icons8.com/?size=100&id=oWdlOvw3CmcM&format=png&color=FFFFFF" class="w-5 h-5 flex-shrink-0 hidden dark:block" alt="Facility Icon">
                </div>
                <div>
                    <h4 class="font-bold text-slate-955 dark:text-white text-xs">{{ $product->name }}</h4>
                    <div class="flex items-center gap-1.5 mt-0.5">
                        <span class="text-[9px] px-1.5 py-0.5 rounded bg-slate-100 dark:bg-slate-900 text-slate-500 dark:text-slate-400 font-bold uppercase tracking-wide border border-slate-250/30 dark:border-slate-850">{{ $product->category }}</span>
                        @if(!$product->is_active)
                            <span class="text-[9px] px-1.5 py-0.5 rounded bg-rose-50 dark:bg-rose-950/20 text-rose-600 dark:text-rose-400 font-bold border border-rose-100/30">Inactive</span>
                        @endif
                    </div>
                </div>
            </div>
        </td>
        <td class="px-6 py-4 font-bold text-slate-900 dark:text-white text-xs">
            {{ is_numeric($product->loanable_amount) ? '₱' . number_format($product->loanable_amount, 2) : ($product->loanable_amount ?: 'N/A') }}
        </td>
        <td class="px-6 py-4 font-black text-emerald-600 dark:text-emerald-400 font-mono text-xs">
            {{ number_format($product->interest_rate, 2) }}%
        </td>
        <td class="px-6 py-4 font-semibold text-slate-500 dark:text-slate-400 text-xs">
            ₱{{ number_format($product->fixed_deposit, 2) }}
        </td>
        <td class="px-6 py-4 text-slate-500 dark:text-slate-400 font-bold text-xs">
            {{ $product->max_term_months ? $product->max_term_months . ' Mos' : 'N/A' }}
        </td>
        <td class="px-6 py-4 text-xs">
            <div class="flex flex-wrap gap-1">
                @if($product->hrmd_approval)
                    <span class="text-[9px] px-1.5 py-0.5 rounded bg-amber-50 dark:bg-amber-950/20 text-amber-700 dark:text-amber-400 border border-amber-100/50 dark:border-amber-900/30 font-bold uppercase tracking-wider">HRMD Staff</span>
                @endif
                @if($product->comakers)
                    <span class="text-[9px] px-1.5 py-0.5 rounded bg-indigo-50 dark:bg-indigo-950/20 text-indigo-700 dark:text-indigo-400 border border-indigo-100/50 dark:border-indigo-900/30 font-bold uppercase tracking-wider">
                        Co-Makers ({{ is_array($product->comakers) ? 'Matrix' : $product->comakers }})
                    </span>
                @endif
            </div>
        </td>
        <td class="px-6 py-4 text-right">
            <div class="inline-flex items-center justify-end gap-2">
                <button type="button" class="btn-edit-loan-product p-1.5 rounded-lg border border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400 hover:text-indigo-600 hover:border-indigo-300 dark:hover:border-indigo-500 transition-colors cursor-pointer" data-product="{{ json_encode($product) }}">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                </button>
                <form action="{{ route('admin.loans.destroy', $product->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete/deactivate this loan product?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="p-1.5 rounded-lg border border-slate-200 dark:border-slate-700 text-slate-455 hover:text-rose-600 hover:border-rose-300 dark:hover:border-rose-500 transition-colors cursor-pointer">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-4v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                </form>
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="7" class="px-6 py-12 text-center text-slate-400 dark:text-slate-500 italic">
            No loan facilities matched the query.
        </td>
    </tr>
@endforelse
