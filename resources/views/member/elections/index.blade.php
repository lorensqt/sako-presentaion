@extends('layouts.user')

@section('title', 'Coop Elections & Polls - ML Sako')

@section('header')
<div>
    <h1 class="text-2xl font-black tracking-tight text-slate-900 dark:text-white sm:text-3xl">Cooperative Elections</h1>
    <p class="mt-1 text-xs font-semibold text-slate-500 dark:text-slate-400">Participate in cooperative decision-making. Cast your vote securely and view election results.</p>
</div>
@endsection

@section('content')
<div class="space-y-8">
    @if($elections->isEmpty())
    <div class="bg-white dark:bg-slate-800 border border-slate-200/80 dark:border-slate-700/80 rounded-3xl sm:rounded-[2rem] p-6 sm:p-12 text-center">
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-slate-50 dark:bg-slate-900 text-slate-400 mb-4 shadow-sm border border-slate-100 dark:border-slate-800">
            <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
        </div>
        <h3 class="text-base font-bold text-slate-900 dark:text-white">No Active Elections</h3>
        <p class="mt-1.5 text-xs text-slate-500 max-w-sm mx-auto">There are no upcoming, active, or concluded elections at this moment. You will be notified once a new election is scheduled by the administrators.</p>
    </div>
    @else
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @foreach($elections as $election)
        @php 
            $status = $election->computed_status; 
            $hasVoted = $election->has_voted;
        @endphp
        <div class="bg-white dark:bg-slate-800 border {{ $status === 'active' && !$hasVoted ? 'border-emerald-500/30 shadow-emerald-500/5' : 'border-slate-200/80 dark:border-slate-700/80 shadow-sm' }} rounded-3xl sm:rounded-[2rem] p-5 sm:p-6 flex flex-col justify-between hover:scale-[1.01] hover:shadow-md transition-all duration-300">
            <div>
                <!-- Status & Timing Row -->
                <div class="flex items-center justify-between gap-2 mb-4">
                    @if($status === 'active')
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 text-[10px] font-black uppercase tracking-wider">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            Ongoing
                        </span>
                    @elseif($status === 'upcoming')
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-amber-500/10 text-amber-600 dark:text-amber-400 text-[10px] font-black uppercase tracking-wider">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                            Scheduled
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-slate-100 dark:bg-slate-900 text-slate-500 dark:text-slate-400 text-[10px] font-black uppercase tracking-wider">
                            Concluded
                        </span>
                    @endif

                    @if($hasVoted)
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 text-[10px] font-black uppercase tracking-wider">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        Voted
                    </span>
                    @endif
                </div>

                <!-- Election Info -->
                <h3 class="text-base font-black text-slate-900 dark:text-white leading-tight">{{ $election->name }}</h3>
                @if($election->description)
                <p class="mt-2 text-xs text-slate-500 dark:text-slate-400 line-clamp-3 leading-relaxed">{{ $election->description }}</p>
                @endif

                <!-- Timing metadata -->
                <div class="mt-4 pt-4 border-t border-slate-100 dark:border-slate-700/50 space-y-1.5">
                    <div class="flex items-center gap-2 text-[11px] font-semibold text-slate-500">
                        <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <span>Starts: {{ $election->start_time->format('F d, Y h:i A') }}</span>
                    </div>
                    <div class="flex items-center gap-2 text-[11px] font-semibold text-slate-500">
                        <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Ends: {{ $election->end_time->format('F d, Y h:i A') }}</span>
                    </div>
                    <div class="flex items-center gap-2 text-[11px] font-semibold text-slate-500">
                        <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                        <span>Ballot structure: {{ $election->positions_count }} positions</span>
                    </div>
                </div>
            </div>

            <!-- Action Button -->
            <div class="mt-6 pt-4 border-t border-slate-100 dark:border-slate-700/50">
                @if($status === 'active')
                    @if($hasVoted)
                        <div class="w-full text-center py-2.5 rounded-xl bg-slate-100 dark:bg-slate-900 text-slate-500 text-xs font-bold uppercase tracking-wider">
                            Vote Cast Recorded
                        </div>
                    @else
                        <a href="{{ route('member.elections.show', $election) }}" class="w-full inline-flex items-center justify-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-extrabold px-4 py-3 rounded-xl shadow-md shadow-emerald-600/10 transition-all duration-200">
                            Cast Your Vote
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </a>
                    @endif
                @elseif($status === 'upcoming')
                    <button disabled class="w-full text-center py-2.5 rounded-xl bg-slate-100 dark:bg-slate-900 text-slate-400 text-xs font-bold uppercase tracking-wider cursor-not-allowed">
                        Awaiting Start Time
                    </button>
                @else
                    <a href="{{ route('member.elections.results', $election) }}" class="w-full inline-flex items-center justify-center gap-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-extrabold px-4 py-3 rounded-xl transition-all">
                        View Election Results
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    </a>
                @endif
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>
@endsection
