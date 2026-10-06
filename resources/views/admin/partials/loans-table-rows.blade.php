@forelse($loanProducts as $product)
    @php
        $categoryKey = strtolower($product->category);
        $categoryConfig = match($categoryKey) {
            'regular' => [
                'badge' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300 border-emerald-200/60 dark:border-emerald-800/60',
                'icon_bg' => 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 border-emerald-200/50 dark:border-emerald-800/50',
                'icon' => 'fa-solid fa-building-columns',
            ],
            'commodity' => [
                'badge' => 'bg-blue-50 text-blue-700 dark:bg-blue-950/40 dark:text-blue-300 border-blue-200/60 dark:border-blue-800/60',
                'icon_bg' => 'bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400 border-blue-200/50 dark:border-blue-800/50',
                'icon' => 'fa-solid fa-cart-shopping',
            ],
            'special', 'seasonal' => [
                'badge' => 'bg-amber-50 text-amber-700 dark:bg-amber-950/40 dark:text-amber-300 border-amber-200/60 dark:border-amber-800/60',
                'icon_bg' => 'bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400 border-amber-200/50 dark:border-amber-800/50',
                'icon' => 'fa-solid fa-star',
            ],
            'bonus_buyout' => [
                'badge' => 'bg-purple-50 text-purple-700 dark:bg-purple-950/40 dark:text-purple-300 border-purple-200/60 dark:border-purple-800/60',
                'icon_bg' => 'bg-purple-50 dark:bg-purple-950/40 text-purple-600 dark:text-purple-400 border-purple-200/50 dark:border-purple-800/50',
                'icon' => 'fa-solid fa-gift',
            ],
            'emergency', 'health' => [
                'badge' => 'bg-rose-50 text-rose-700 dark:bg-rose-950/40 dark:text-rose-300 border-rose-200/60 dark:border-rose-800/60',
                'icon_bg' => 'bg-rose-50 dark:bg-rose-950/40 text-rose-600 dark:text-rose-400 border-rose-200/50 dark:border-rose-800/50',
                'icon' => 'fa-solid fa-kit-medical',
            ],
            default => [
                'badge' => 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300 border-slate-200 dark:border-slate-700',
                'icon_bg' => 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 border-slate-200 dark:border-slate-700',
                'icon' => 'fa-solid fa-folder',
            ],
        };
    @endphp

    <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition-colors duration-150 {{ !$product->is_active ? 'opacity-65 bg-slate-50/40 dark:bg-slate-950/30' : '' }}">
        <!-- Product Name & Identity -->
        <td class="px-6 py-4.5">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl {{ $categoryConfig['icon_bg'] }} border flex items-center justify-center font-bold text-sm shadow-2xs flex-shrink-0">
                    <i class="{{ $categoryConfig['icon'] }}"></i>
                </div>
                <div class="min-w-0">
                    <div class="flex items-center gap-2">
                        <h4 class="font-bold text-slate-900 dark:text-white text-xs sm:text-sm tracking-tight truncate">{{ $product->name }}</h4>
                        @if($product->is_active)
                            <span class="inline-flex items-center gap-1 text-[9px] font-bold text-emerald-700 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/40 px-2 py-0.5 rounded-full border border-emerald-200/60 dark:border-emerald-800/60">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                <span>Active</span>
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 text-[9px] font-bold text-slate-500 dark:text-slate-400 bg-slate-100 dark:bg-slate-800 px-2 py-0.5 rounded-full border border-slate-200 dark:border-slate-700">
                                Inactive
                            </span>
                        @endif
                    </div>
                    
                    <div class="flex items-center gap-1.5 mt-1 flex-wrap">
                        <span class="text-[9px] px-2 py-0.5 rounded-md font-extrabold uppercase tracking-wider border {{ $categoryConfig['badge'] }}">
                            {{ str_replace('_', ' ', $product->category) }}
                        </span>

                        @if($product->partner)
                            <span class="text-[9px] px-2 py-0.5 rounded-md font-semibold text-slate-600 dark:text-slate-400 bg-slate-100 dark:bg-slate-800 border border-slate-200/80 dark:border-slate-700">
                                Partner: {{ $product->partner }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </td>

        <!-- Borrowing Limit -->
        <td class="px-6 py-4.5">
            <div class="flex flex-col">
                <span class="font-mono font-black text-slate-900 dark:text-white text-xs">
                    {{ is_numeric($product->loanable_amount) ? '₱' . number_format((float)$product->loanable_amount, 2) : ($product->loanable_amount ?: 'Open') }}
                </span>
                <span class="text-[10px] text-slate-400 dark:text-slate-500 font-medium">Borrowing Limit</span>
            </div>
        </td>

        <!-- Interest Rate Model -->
        <td class="px-6 py-4.5">
            @if($product->hasCustomTerms())
                @php $sortedTerms = $product->getSortedTerms(); @endphp
                <div class="flex flex-col">
                    <span class="inline-flex items-center gap-1 font-bold font-mono text-emerald-700 dark:text-emerald-400 text-xs">
                        <i class="fa-solid fa-bolt text-emerald-500 text-[10px]"></i>
                        <span>Tiered Matrix</span>
                    </span>
                    <span class="text-[10px] text-slate-500 dark:text-slate-400 font-mono mt-0.5">
                        {{ number_format($sortedTerms[0]['interest_rate'], 2) }}% – {{ number_format(end($sortedTerms)['interest_rate'], 2) }}% APR
                    </span>
                </div>
            @else
                <div class="flex flex-col">
                    <span class="font-mono font-black text-slate-900 dark:text-white text-xs">
                        {{ number_format($product->interest_rate, 2) }}%
                    </span>
                    <span class="text-[10px] text-slate-400 dark:text-slate-500 font-medium">Flat Rate APR</span>
                </div>
            @endif
        </td>

        <!-- Required Share Capital -->
        <td class="px-6 py-4.5">
            <div class="flex flex-col">
                <span class="font-mono font-bold text-slate-800 dark:text-slate-200 text-xs">
                    ₱{{ number_format($product->fixed_deposit, 2) }}
                </span>
                <span class="text-[10px] text-slate-400 dark:text-slate-500 font-medium">Share Capital</span>
            </div>
        </td>

        <!-- Tenure Options -->
        <td class="px-6 py-4.5">
            @if($product->hasCustomTerms())
                @php $sortedTerms = $product->getSortedTerms(); @endphp
                <div class="flex flex-col">
                    <span class="font-bold text-slate-900 dark:text-white text-xs">{{ count($sortedTerms) }} Tenures</span>
                    <span class="text-[10px] text-slate-500 dark:text-slate-400 font-mono mt-0.5 truncate max-w-[140px]" title="{{ implode(', ', array_map(fn($t) => $t['months'] . ' mos', $sortedTerms)) }}">
                        {{ implode(' · ', array_map(fn($t) => $t['months'] . 'm', $sortedTerms)) }}
                    </span>
                </div>
            @else
                <div class="flex flex-col">
                    <span class="font-bold text-slate-900 dark:text-white text-xs">
                        {{ $product->max_term_months ? $product->max_term_months . ' Months' : 'Open Tenure' }}
                    </span>
                    <span class="text-[10px] text-slate-400 dark:text-slate-500 font-medium">Max Horizon</span>
                </div>
            @endif
        </td>

        <!-- Governance & Approval Chain -->
        <td class="px-6 py-4.5">
            <div class="flex flex-wrap gap-1.5 items-center">
                @if($product->hrmd_approval)
                    <span class="inline-flex items-center gap-1 text-[9px] px-2 py-0.5 rounded-md bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-400 font-bold border border-amber-200/60 dark:border-amber-800/60 uppercase">
                        <span>HRMD</span>
                    </span>
                @endif
                @if($product->comakers)
                    <span class="inline-flex items-center gap-1 text-[9px] px-2 py-0.5 rounded-md bg-indigo-50 dark:bg-indigo-950/40 text-indigo-700 dark:text-indigo-400 font-bold border border-indigo-200/60 dark:border-indigo-800/60 uppercase">
                        <span>Co-Makers: {{ is_array($product->comakers) ? 'Matrix' : $product->comakers }}</span>
                    </span>
                @endif
                @if(!$product->hrmd_approval && !$product->comakers)
                    <span class="text-[10px] text-slate-400 dark:text-slate-500 italic">Direct SAKO</span>
                @endif
            </div>
        </td>

        <!-- Row Actions -->
        <td class="px-6 py-4.5 text-right">
            <div class="inline-flex items-center justify-end gap-1.5">
                <button type="button" 
                    class="btn-edit-loan-product w-8 h-8 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400 hover:text-emerald-600 hover:border-emerald-300 dark:hover:border-emerald-700 hover:bg-emerald-50 dark:hover:bg-emerald-950/30 transition-all cursor-pointer shadow-2xs hover:scale-105 active:scale-95 flex items-center justify-center" 
                    data-product="{{ json_encode($product) }}" 
                    title="Edit Loan Product">
                    <i class="fa-solid fa-pen-to-square text-xs"></i>
                </button>
                <button type="button" 
                    class="btn-delete-loan-product w-8 h-8 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-400 dark:text-slate-500 hover:text-rose-600 dark:hover:text-rose-400 hover:border-rose-300 dark:hover:border-rose-800 hover:bg-rose-50 dark:hover:bg-rose-950/30 transition-all cursor-pointer shadow-2xs hover:scale-105 active:scale-95 flex items-center justify-center"
                    data-id="{{ $product->id }}"
                    data-name="{{ $product->name }}"
                    data-category="{{ ucfirst(str_replace('_', ' ', $product->category)) }}"
                    data-rate="{{ $product->hasCustomTerms() ? 'Tiered Rates' : number_format($product->interest_rate, 2) . '%' }}"
                    data-term="{{ $product->hasCustomTerms() ? count($product->getSortedTerms()) . ' Tenures' : ($product->max_term_months ? $product->max_term_months . ' Mos' : 'N/A') }}"
                    title="Delete Loan Product">
                    <i class="fa-solid fa-trash-can text-xs"></i>
                </button>
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="7" class="px-6 py-16 text-center">
            <div class="flex flex-col items-center justify-center max-w-sm mx-auto space-y-3">
                <div class="w-14 h-14 rounded-2xl bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex items-center justify-center text-xl text-slate-400 dark:text-slate-500 shadow-2xs">
                    <i class="fa-solid fa-building-columns"></i>
                </div>
                <h4 class="text-sm font-bold text-slate-800 dark:text-slate-200">No Loan Products Found</h4>
                <p class="text-xs text-slate-500 dark:text-slate-400 text-center leading-relaxed">
                    No loan products matched the current search query or active filter criteria.
                </p>
                <button type="button" class="mt-2 text-xs font-bold text-emerald-600 dark:text-emerald-400 hover:underline cursor-pointer inline-flex items-center gap-1.5" onclick="document.getElementById('btn-reset-filters')?.click()">
                    <i class="fa-solid fa-arrows-rotate text-[10px]"></i>
                    <span>Clear all active filters</span>
                </button>
            </div>
        </td>
    </tr>
@endforelse
