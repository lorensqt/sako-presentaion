@extends('layouts.user')

@section('title', 'Election Results - ML Sako')

@section('header')
<div class="flex items-center gap-3">
    <a href="{{ route('member.elections.index') }}" class="inline-flex items-center justify-center p-2 rounded-xl text-slate-500 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition-all">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
    </a>
    <div>
        <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-slate-500/10 text-slate-600 dark:text-slate-400 text-[10px] font-black uppercase tracking-wider mb-1">
            Concluded Election
        </span>
        <h1 class="text-2xl font-black tracking-tight text-slate-900 dark:text-white">{{ $election->name }} Results</h1>
    </div>
</div>
@endsection

@section('content')
<div class="max-w-4xl mx-auto space-y-8">
    <!-- Notice -->
    <div class="p-5 rounded-3xl sm:rounded-[2rem] bg-emerald-50 dark:bg-slate-800/60 border border-emerald-100 dark:border-slate-700/80 flex gap-4 text-emerald-800 dark:text-emerald-400">
        <div class="w-10 h-10 rounded-xl bg-emerald-500/10 text-emerald-600 flex items-center justify-center flex-shrink-0">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div class="text-xs flex-1">
            <h4 class="font-extrabold uppercase tracking-wider">Final Standing Certified</h4>
            <p class="mt-1 font-semibold leading-relaxed">This election concluded on {{ $election->end_time->format('F d, Y \a\t h:i A') }}. Below are the official tallies of votes cast by eligible cooperative members. Leaders/winners are highlighted with a gold trophy icon.</p>
        </div>
    </div>

    <!-- Tally Grid by Position -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @foreach($election->positions as $position)
        <div class="bg-white dark:bg-slate-800 border border-slate-200/80 dark:border-slate-700/80 rounded-3xl sm:rounded-[2rem] overflow-hidden shadow-sm">
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
@endsection
