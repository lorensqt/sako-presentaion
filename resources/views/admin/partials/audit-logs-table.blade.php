<div class="overflow-x-auto">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="bg-slate-50 dark:bg-slate-900/50 border-b border-slate-100 dark:border-slate-700/60">
                <th class="px-6 py-4 text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Timestamp</th>
                <th class="px-6 py-4 text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Security Level</th>
                <th class="px-6 py-4 text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Operator/Actor</th>
                <th class="px-6 py-4 text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Action Code</th>
                <th class="px-6 py-4 text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Activity Summary</th>
                <th class="px-6 py-4 text-[10px] font-extrabold uppercase tracking-wider text-slate-400">IP & Network</th>
                <th class="px-6 py-4 text-[10px] font-extrabold uppercase tracking-wider text-slate-400 text-center">Inspect</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 dark:divide-slate-700/50">
            @forelse($logs as $log)
                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-700/20 transition-all duration-150">
                    <!-- Timestamp -->
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="text-xs font-black text-slate-800 dark:text-slate-200 block">
                            {{ $log->created_at->format('M d, Y h:i:s A') }}
                        </span>
                        <span class="text-[10px] font-semibold text-slate-400 dark:text-slate-500 mt-0.5 block">
                            {{ $log->created_at->diffForHumans() }}
                        </span>
                    </td>

                    <!-- Severity Pill -->
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($log->severity === 'danger')
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-rose-50 dark:bg-rose-950/20 text-rose-700 dark:text-rose-400 text-[10px] font-black border border-rose-100/40 dark:border-rose-900/20">
                                <span class="w-1.5 h-1.5 rounded-full bg-rose-600 animate-pulse"></span>
                                DANGER
                            </span>
                        @elseif($log->severity === 'warning')
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-amber-50 dark:bg-amber-950/20 text-amber-700 dark:text-amber-400 text-[10px] font-black border border-amber-100/40 dark:border-amber-900/20">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                WARNING
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-emerald-50 dark:bg-emerald-950/20 text-emerald-700 dark:text-emerald-400 text-[10px] font-black border border-emerald-100/40 dark:border-emerald-900/20">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                INFO
                            </span>
                        @endif
                    </td>

                    <!-- Actor -->
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($log->actor)
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-emerald-600 flex items-center justify-center text-white font-bold text-xs shadow-sm">
                                    {{ strtoupper(substr($log->actor->name, 0, 1)) }}
                                </div>
                                <div class="min-w-0">
                                    <span class="text-xs font-bold text-slate-800 dark:text-slate-200 block truncate max-w-[150px]">{{ $log->actor->name }}</span>
                                    <span class="text-[10px] font-semibold text-slate-400 dark:text-slate-500 block truncate max-w-[150px]">{{ $log->actor->email }}</span>
                                </div>
                            </div>
                        @else
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-slate-200 dark:bg-slate-700 flex items-center justify-center text-slate-500 dark:text-slate-400 font-bold text-xs">
                                    G
                                </div>
                                <div>
                                    <span class="text-xs font-bold text-slate-400 dark:text-slate-500 block">Guest Session</span>
                                    <span class="text-[10px] font-semibold text-slate-400/80 dark:text-slate-600 block">Unauthenticated</span>
                                </div>
                            </div>
                        @endif
                    </td>

                    <!-- Action Code -->
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="font-mono text-[11px] font-extrabold text-slate-700 dark:text-slate-300 px-2 py-1 rounded bg-slate-100 dark:bg-slate-900">
                            {{ $log->action }}
                        </span>
                    </td>

                    <!-- Summary Description -->
                    <td class="px-6 py-4">
                        <p class="text-xs font-semibold text-slate-600 dark:text-slate-300 leading-normal max-w-sm sm:max-w-md lg:max-w-lg truncate" title="{{ $log->description }}">
                            {{ $log->description }}
                        </p>
                    </td>

                    <!-- IP Address -->
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="text-xs font-mono font-bold text-slate-800 dark:text-slate-200 block">
                            {{ $log->ip_address ?? '0.0.0.0' }}
                        </span>
                        <span class="text-[9px] font-semibold text-slate-400 dark:text-slate-500 truncate max-w-[120px] block" title="{{ $log->user_agent }}">
                            {{ $log->user_agent ?? 'Unknown Agent' }}
                        </span>
                    </td>

                    <!-- Inspect -->
                    <td class="px-6 py-4 whitespace-nowrap text-center">
                        <button type="button" 
                            class="inspect-btn p-2 hover:bg-slate-100 dark:hover:bg-slate-700/50 hover:text-emerald-600 dark:hover:text-emerald-400 rounded-xl transition-all"
                            data-timestamp="{{ $log->created_at->format('M d, Y h:i:s A') }}"
                            data-action="{{ $log->action }}"
                            data-severity="{{ $log->severity }}"
                            data-actor="{{ $log->actor ? $log->actor->name . ' (' . $log->actor->email . ')' : 'Guest Session' }}"
                            data-description="{{ $log->description }}"
                            data-ip="{{ $log->ip_address ?? '0.0.0.0' }}"
                            data-agent="{{ $log->user_agent ?? 'Unknown Agent' }}"
                            data-old="{{ json_encode($log->old_values) }}"
                            data-new="{{ json_encode($log->new_values) }}">
                            <!-- Inspect Icon -->
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c3.79 0 7.5 3 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center justify-center space-y-3">
                            <div class="w-12 h-12 rounded-xl bg-slate-100 dark:bg-slate-900 flex items-center justify-center text-slate-400">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <div>
                                <p class="text-xs font-extrabold text-slate-800 dark:text-white">No Audited Transactions Found</p>
                                <p class="text-[10px] font-semibold text-slate-400 dark:text-slate-500 mt-1">Try tweaking your search parameters or date filters.</p>
                            </div>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Pagination Footer -->
@if($logs->hasPages())
    <div class="px-6 py-4 bg-slate-50 dark:bg-slate-900/50 border-t border-slate-100 dark:border-slate-700/60 pagination-container">
        {{ $logs->links() }}
    </div>
@endif
