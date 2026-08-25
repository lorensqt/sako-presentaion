@extends('layouts.admin')

@section('title', 'Election Audit & Results - ML Sako')

@section('header')
<div class="flex items-center gap-3">
    <a href="{{ route('admin.elections.show', $election) }}" class="inline-flex items-center justify-center p-2 rounded-xl text-slate-500 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition-all">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
    </a>
    <div>
        <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 text-[10px] font-black uppercase tracking-wider mb-1">
            Voting Tally Dashboard
        </span>
        <h1 class="text-2xl font-black tracking-tight text-slate-900 dark:text-white">{{ $election->name }} Results</h1>
    </div>
</div>
@endsection

@section('content')
<div class="space-y-8">
    <!-- Quick Statistics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
        <!-- Voted Count -->
        <div class="bg-white dark:bg-slate-800 border border-slate-200/80 dark:border-slate-700/80 rounded-[2rem] p-6 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 text-emerald-600 flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            </div>
            <div>
                <span class="text-[10px] font-extrabold text-slate-400 dark:text-slate-500 uppercase tracking-wider block">Members Voted</span>
                <span class="text-xl font-black text-slate-900 dark:text-white mt-0.5 block">{{ $votedCount }}</span>
                <span class="text-[10px] font-semibold text-slate-500">out of {{ $totalVotersCount }} eligible</span>
            </div>
        </div>

        <!-- Turnout Percentage -->
        <div class="bg-white dark:bg-slate-800 border border-slate-200/80 dark:border-slate-700/80 rounded-[2rem] p-6 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-sky-500/10 text-sky-600 flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/></svg>
            </div>
            <div>
                <span class="text-[10px] font-extrabold text-slate-400 dark:text-slate-500 uppercase tracking-wider block">Turnout Rate</span>
                <span class="text-xl font-black text-slate-900 dark:text-white mt-0.5 block">{{ $turnoutPercentage }}%</span>
                <div class="w-24 h-1.5 bg-slate-100 dark:bg-slate-700 rounded-full mt-1.5 overflow-hidden">
                    <div class="bg-sky-500 h-full rounded-full" style="width: {{ min($turnoutPercentage, 100) }}%"></div>
                </div>
            </div>
        </div>

        <!-- Election Status -->
        <div class="bg-white dark:bg-slate-800 border border-slate-200/80 dark:border-slate-700/80 rounded-[2rem] p-6 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-amber-500/10 text-amber-600 flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <span class="text-[10px] font-extrabold text-slate-400 dark:text-slate-500 uppercase tracking-wider block">Period Status</span>
                <span class="text-xs font-black text-slate-900 dark:text-white uppercase mt-1 inline-flex items-center gap-1">
                    @php $status = $election->computed_status; @endphp
                    @if($status === 'active')
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span> Ongoing
                    @elseif($status === 'upcoming')
                    <span class="w-2 h-2 rounded-full bg-amber-500"></span> Scheduled
                    @else
                    <span class="w-2 h-2 rounded-full bg-slate-500"></span> Concluded
                    @endif
                </span>
                <span class="text-[10px] font-semibold text-slate-500 block mt-1">End: {{ $election->end_time->format('M d, Y h:i A') }}</span>
            </div>
        </div>
    </div>

    <!-- Tally by Position -->
    <div class="space-y-6">
        <h2 class="text-lg font-black text-slate-900 dark:text-white">Positional Tallies & Standing</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @foreach($election->positions as $position)
            <div class="bg-white dark:bg-slate-800 border border-slate-200/80 dark:border-slate-700/80 rounded-[2rem] overflow-hidden shadow-sm">
                <!-- Position Title -->
                <div class="px-6 py-4 bg-slate-50/50 dark:bg-slate-900/50 border-b border-slate-100 dark:border-slate-700/80 flex items-center justify-between">
                    <h3 class="text-xs font-extrabold text-slate-800 dark:text-white uppercase tracking-wider">{{ $position->name }}</h3>
                    <span class="px-2 py-0.5 bg-emerald-500/10 text-emerald-600 text-[10px] font-bold rounded-lg">
                        {{ $position->candidates->sum('votes_count') }} total votes
                    </span>
                </div>

                <!-- Candidates progress metrics -->
                <div class="p-6 space-y-5">
                    @php 
                        $maxVotes = $position->candidates->max('votes_count') ?: 1; 
                        $totalPosVotes = $position->candidates->sum('votes_count') ?: 1;
                    @endphp
                    @foreach($position->candidates->sortByDesc('votes_count') as $candidate)
                    @php 
                        $percentageOfTotal = round(($candidate->votes_count / $totalPosVotes) * 100, 1);
                        $isLeader = $candidate->votes_count > 0 && $candidate->votes_count === $position->candidates->max('votes_count');
                    @endphp
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-9 h-9 rounded-xl bg-slate-100 dark:bg-slate-900 overflow-hidden flex-shrink-0 flex items-center justify-center border border-slate-200/40 shadow-sm">
                                    @if($candidate->photo_path)
                                    <img src="{{ asset('storage/' . $candidate->photo_path) }}" alt="{{ $candidate->name }}" class="w-full h-full object-cover">
                                    @else
                                    <span class="text-[10px] font-black text-slate-500">{{ strtoupper(substr($candidate->name, 0, 2)) }}</span>
                                    @endif
                                </div>
                                <div class="min-w-0">
                                    <div class="flex items-center gap-1.5">
                                        <span class="text-xs font-bold text-slate-900 dark:text-white truncate">{{ $candidate->name }}</span>
                                        @if($isLeader)
                                        <span class="inline-flex items-center p-0.5 rounded-full bg-amber-500/10 text-amber-500" title="Leader / Winner">
                                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                        </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="text-xs font-extrabold text-slate-900 dark:text-white">{{ $candidate->votes_count }}</span>
                                <span class="text-[10px] text-slate-500 font-semibold">({{ $percentageOfTotal }}%)</span>
                            </div>
                        </div>

                        <!-- Progress indicator bar -->
                        <div class="relative w-full h-2.5 bg-slate-50 dark:bg-slate-900 rounded-full overflow-hidden border border-slate-100 dark:border-slate-800">
                            <div class="h-full rounded-full transition-all duration-500 {{ $isLeader ? 'bg-emerald-500' : 'bg-slate-400 dark:bg-slate-600' }}" style="width: {{ $percentageOfTotal }}%"></div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Auditable Tracked Ballots Log -->
    <div class="bg-white dark:bg-slate-800 border border-slate-200/80 dark:border-slate-700/80 rounded-[2rem] overflow-hidden shadow-sm">
        <div class="px-6 py-5 border-b border-slate-100 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-900/50">
            <h3 class="text-xs font-extrabold text-slate-800 dark:text-white uppercase tracking-wider">Historical Tracked Ballots (Audit Trail)</h3>
            <p class="text-[10px] text-slate-500 mt-0.5">As per configuration, individual vote selections are tracked. Admins can audit each member's positional choice.</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-100 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-900/50">
                        <th class="px-6 py-3.5 text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Voter / Member</th>
                        <th class="px-6 py-3.5 text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Position</th>
                        <th class="px-6 py-3.5 text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Candidate Selected</th>
                        <th class="px-6 py-3.5 text-[10px] font-extrabold text-slate-400 uppercase tracking-wider text-right">Cast Time</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                    @forelse($votes as $vote)
                    <tr class="hover:bg-slate-50/30 dark:hover:bg-slate-900/10 transition-colors">
                        <td class="px-6 py-3.5">
                            <div class="flex items-center gap-2.5">
                                <div class="w-7 h-7 rounded-lg bg-slate-100 dark:bg-slate-950 text-slate-600 dark:text-slate-400 flex items-center justify-center font-bold text-xs shadow-sm">
                                    {{ strtoupper(substr($vote->user->name ?? 'M', 0, 1)) }}
                                </div>
                                <div class="text-xs font-bold text-slate-800 dark:text-white">{{ $vote->user->name ?? 'Deleted Member' }}</div>
                            </div>
                        </td>
                        <td class="px-6 py-3.5 text-xs font-semibold text-slate-600 dark:text-slate-300">
                            {{ $vote->position->name ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-3.5">
                            <span class="inline-flex items-center px-2 py-1 rounded-lg bg-sky-50 dark:bg-sky-950/20 text-sky-600 dark:text-sky-400 text-xs font-bold border border-sky-100 dark:border-sky-900/10">
                                {{ $vote->candidate->name ?? 'N/A' }}
                            </span>
                        </td>
                        <td class="px-6 py-3.5 text-xs font-semibold text-slate-500 text-right">
                            {{ $vote->created_at->format('M d, Y h:i A') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-8 text-center text-xs text-slate-400">No votes cast yet in this election.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($votes->hasPages())
        <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-700">
            {{ $votes->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
