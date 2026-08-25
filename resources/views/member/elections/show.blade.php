@extends('layouts.user')

@section('title', 'Cast Your Ballot - ML Sako')

@section('content')
<div class="max-w-4xl mx-auto space-y-10 pb-16 animate-fade-in">
    
    <!-- Premium Header Hero Card with Gradient Backdrop -->
    <div class="relative overflow-hidden bg-gradient-to-br from-emerald-600 via-emerald-700 to-teal-800 text-white rounded-[2.5rem] p-8 sm:p-10 shadow-xl shadow-emerald-950/10 dark:shadow-emerald-950/30">
        <!-- Glowing backdrop decorations -->
        <div class="absolute -right-10 -top-10 w-40 h-40 rounded-full bg-white/5 blur-2xl"></div>
        <div class="absolute right-1/4 -bottom-12 w-60 h-60 rounded-full bg-emerald-500/10 blur-3xl"></div>
        
        <div class="relative z-10 flex flex-col md:flex-row md:items-center md:justify-between gap-6">
            <div class="space-y-3.5">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 backdrop-blur-md text-[10px] font-black uppercase tracking-wider">
                    <span class="w-2 h-2 rounded-full bg-emerald-300 animate-pulse"></span>
                    Official Ballot Box Open
                </div>
                <h1 class="text-3xl sm:text-4xl font-black tracking-tight leading-tight">{{ $election->name }}</h1>
                @if($election->description)
                <p class="text-emerald-100/90 text-xs sm:text-sm font-medium leading-relaxed max-w-2xl">{{ $election->description }}</p>
                @endif
            </div>

            <!-- Timer / Metadata Pill -->
            <div class="bg-white/10 backdrop-blur-md border border-white/10 rounded-3xl p-5 flex flex-col items-center justify-center text-center flex-shrink-0 min-w-[180px]">
                <svg class="w-5 h-5 text-emerald-300 mb-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span class="text-[10px] font-extrabold text-emerald-200 uppercase tracking-widest">Time Remaining</span>
                <span class="text-xs font-bold mt-1.5">{{ $election->end_time->diffForHumans(null, true) }} left</span>
                <span class="text-[9px] text-emerald-300/80 font-semibold mt-0.5">Closes: {{ $election->end_time->format('h:i A') }}</span>
            </div>
        </div>
    </div>

    <!-- Official Ballot Form -->
    <form id="ballot-form" action="{{ route('member.elections.vote', $election) }}" method="POST" class="space-y-12">
        @csrf

        @foreach($election->positions as $position)
        <div class="position-group space-y-5" data-position-id="{{ $position->id }}">
            <!-- Elegant Position Section Divider -->
            <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800/80 pb-3">
                <div class="flex items-center gap-3">
                    <div class="w-3.5 h-3.5 rounded-full bg-emerald-600 flex items-center justify-center shadow-md shadow-emerald-500/20">
                        <div class="w-1.5 h-1.5 rounded-full bg-white"></div>
                    </div>
                    <h3 class="text-sm sm:text-base font-extrabold text-slate-900 dark:text-white uppercase tracking-wider">{{ $position->name }}</h3>
                </div>
                <span class="text-[10px] text-rose-500 dark:text-rose-400 font-extrabold uppercase tracking-wider bg-rose-50 dark:bg-rose-950/20 px-2.5 py-1 rounded-lg select-badge transition-all">
                    Required Selection
                </span>
            </div>

            <!-- Position Candidates Grid -->
            @if($position->candidates->isEmpty())
                <div class="text-center py-8 text-slate-400 dark:text-slate-500 border border-dashed border-slate-200 dark:border-slate-700/80 rounded-3xl">
                    <p class="text-xs font-semibold">No candidates have been registered for this position.</p>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                    @foreach($position->candidates as $candidate)
                    <label class="candidate-card relative flex flex-col items-center justify-between p-6 bg-white dark:bg-slate-800 border border-slate-200/60 dark:border-slate-700/60 rounded-[2.2rem] cursor-pointer hover:shadow-lg dark:hover:bg-slate-800/80 transition-all duration-300 group text-center select-none shadow-sm">
                        <!-- Hidden radio inputs -->
                        <input type="radio" name="votes[{{ $position->id }}]" value="{{ $candidate->id }}" required class="hidden-radio sr-only" data-pos-id="{{ $position->id }}">

                        <!-- Selected Highlight Overlay (Outer border + ring glow) -->
                        <div class="active-border pointer-events-none absolute inset-0 border-2 border-transparent rounded-[2.2rem] transition-all duration-300"></div>

                        <div class="flex flex-col items-center w-full">
                            <!-- Premium Avatar frame -->
                            <div class="w-24 h-24 rounded-[1.8rem] bg-slate-50 dark:bg-slate-900 overflow-hidden flex items-center justify-center border border-slate-200/50 dark:border-slate-700/80 shadow-inner mb-5 transition-all duration-300 group-hover:scale-105 group-hover:border-emerald-500/20 relative">
                                @if($candidate->photo_path)
                                <img src="{{ asset('storage/' . $candidate->photo_path) }}" alt="{{ $candidate->name }}" class="w-full h-full object-cover">
                                @else
                                <div class="w-full h-full bg-gradient-to-br from-emerald-500/10 to-teal-500/10 flex items-center justify-center">
                                    <span class="text-xl font-black text-emerald-600 dark:text-emerald-400 tracking-wider">{{ strtoupper(substr($candidate->name, 0, 2)) }}</span>
                                </div>
                                @endif
                            </div>

                            <!-- Name -->
                            <h4 class="text-xs sm:text-sm font-extrabold text-slate-800 dark:text-slate-100 group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors duration-200 leading-tight px-2">{{ $candidate->name }}</h4>
                        </div>

                        <!-- Checkbox Indicator (Enhanced contrast with explicit white stroke checkmark in light/dark) -->
                        <div class="mt-5 flex justify-center w-full">
                            <span class="selection-circle w-6 h-6 rounded-full border-2 border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 flex items-center justify-center transition-all duration-300 shadow-inner group-hover:border-emerald-500">
                                <svg class="w-4 h-4 text-white stroke-white opacity-0 transition-all duration-300 transform scale-75" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3.5" d="M5 13l4 4L19 7"/>
                                </svg>
                            </span>
                        </div>
                    </label>
                    @endforeach
                </div>
            @endif
        </div>
        @endforeach

        <!-- Modern Action Console Footbar -->
        <div class="bg-white dark:bg-slate-800 border border-slate-200/60 dark:border-slate-700/60 rounded-[2rem] p-6 shadow-md flex flex-col sm:flex-row items-center justify-between gap-6 mt-16">
            <div class="text-center sm:text-left flex items-center gap-3">
                <div class="flex-shrink-0 w-10 h-10 rounded-2xl bg-amber-500/10 dark:bg-amber-950/20 text-amber-600 dark:text-amber-400 flex items-center justify-center" id="validation-icon-container">
                    <svg class="w-5.5 h-5.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <div>
                    <h5 class="text-xs font-black text-slate-800 dark:text-white uppercase tracking-wider" id="validation-title">Ballot Incomplete</h5>
                    <p id="validation-notice" class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 leading-snug mt-0.5">Please make exactly one candidate selection for each position above.</p>
                </div>
            </div>
            
            <button id="submit-ballot" type="submit" disabled class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-slate-200 dark:bg-slate-700 text-slate-400 dark:text-slate-500 text-xs font-black px-8 py-4 rounded-xl cursor-not-allowed shadow-md transition-all duration-300">
                Submit Your Official Ballot
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const radios = document.querySelectorAll(".hidden-radio");
        const cards = document.querySelectorAll(".candidate-card");
        const submitBtn = document.getElementById("submit-ballot");
        const valNotice = document.getElementById("validation-notice");
        const valTitle = document.getElementById("validation-title");
        const valIconContainer = document.getElementById("validation-icon-container");

        const totalPositions = {{ count($election->positions) }};

        function updateBallotState() {
            cards.forEach(card => {
                const input = card.querySelector(".hidden-radio");
                const border = card.querySelector(".active-border");
                const circle = card.querySelector(".selection-circle");
                const svg = circle.querySelector("svg");

                if (input.checked) {
                    // Selected Card State (emerald glow + high contrast check circle)
                    card.classList.add("border-emerald-500", "bg-emerald-50/10", "dark:bg-emerald-950/5", "scale-[1.02]", "shadow-md");
                    card.classList.remove("border-slate-200/60", "dark:border-slate-700/60");
                    border.classList.add("border-emerald-500", "ring-4", "ring-emerald-500/10");
                    circle.classList.add("bg-emerald-600", "border-emerald-600");
                    circle.classList.remove("bg-white", "dark:bg-slate-900");
                    svg.classList.remove("opacity-0", "scale-75");
                    svg.classList.add("opacity-100", "scale-100");
                } else {
                    // Normal / Unselected Card State
                    card.classList.remove("border-emerald-500", "bg-emerald-50/10", "dark:bg-emerald-950/5", "scale-[1.02]", "shadow-md");
                    card.classList.add("border-slate-200/60", "dark:border-slate-700/60");
                    border.classList.remove("border-emerald-500", "ring-4", "ring-emerald-500/10");
                    circle.classList.remove("bg-emerald-600", "border-emerald-600");
                    circle.classList.add("bg-white", "dark:bg-slate-900");
                    svg.classList.add("opacity-0", "scale-75");
                    svg.classList.remove("opacity-100", "scale-100");
                }
            });

            // Calculate completed votes
            const checkedRadios = document.querySelectorAll(".hidden-radio:checked");
            const votedPositionsCount = new Set(Array.from(checkedRadios).map(r => r.getAttribute("data-pos-id"))).size;

            // Update Position Header indicators
            const groups = document.querySelectorAll(".position-group");
            groups.forEach(group => {
                const posId = group.getAttribute("data-position-id");
                const checked = group.querySelector(".hidden-radio:checked");
                const badge = group.querySelector(".select-badge");

                if (checked) {
                    badge.innerHTML = "Selection Ready";
                    badge.className = "text-[10px] text-emerald-600 dark:text-emerald-400 font-extrabold uppercase tracking-wider bg-emerald-500/10 px-2.5 py-1 rounded-lg select-badge";
                } else {
                    badge.innerHTML = "Required Selection";
                    badge.className = "text-[10px] text-rose-500 dark:text-rose-400 font-extrabold uppercase tracking-wider bg-rose-50 dark:bg-rose-950/20 px-2.5 py-1 rounded-lg select-badge";
                }
            });

            // Enforce Console Action State
            if (votedPositionsCount === totalPositions) {
                // All Positions Filled State
                submitBtn.disabled = false;
                submitBtn.className = "w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-black px-8 py-4 rounded-xl shadow-lg shadow-emerald-600/15 hover:shadow-emerald-600/25 cursor-pointer transition-all duration-300 hover:-translate-y-0.5";
                
                valTitle.innerHTML = "Ballot Ready to Cast";
                valTitle.className = "text-xs font-black text-emerald-600 dark:text-emerald-400 uppercase tracking-wider";
                valNotice.innerHTML = "All positions filled successfully. Confirm and cast your official vote.";
                valIconContainer.className = "flex-shrink-0 w-10 h-10 rounded-2xl bg-emerald-500/10 text-emerald-600 flex items-center justify-center animate-bounce";
                valIconContainer.innerHTML = `<svg class="w-5.5 h-5.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>`;
            } else {
                // Incomplete State
                submitBtn.disabled = true;
                submitBtn.className = "w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-slate-200 dark:bg-slate-700 text-slate-400 dark:text-slate-500 text-xs font-black px-8 py-4 rounded-xl cursor-not-allowed shadow-md transition-all duration-300";
                
                valTitle.innerHTML = "Ballot Incomplete";
                valTitle.className = "text-xs font-black text-slate-800 dark:text-white uppercase tracking-wider";
                valNotice.innerHTML = "Please make exactly one candidate selection for each position above.";
                valIconContainer.className = "flex-shrink-0 w-10 h-10 rounded-2xl bg-amber-500/10 dark:bg-amber-950/20 text-amber-600 dark:text-amber-400 flex items-center justify-center";
                valIconContainer.innerHTML = `<svg class="w-5.5 h-5.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>`;
            }
        }

        // Bind clicks to card elements
        cards.forEach(card => {
            card.addEventListener("click", function () {
                const radio = this.querySelector(".hidden-radio");
                radio.checked = true;
                updateBallotState();
            });
        });

        radios.forEach(radio => {
            radio.addEventListener("change", updateBallotState);
        });

        // Double confirmation block on submit
        const form = document.getElementById("ballot-form");
        form.addEventListener("submit", function (e) {
            e.preventDefault();
            
            window.MLSAKOAlert.fire({
                title: 'Cast Official Ballot?',
                text: 'You are about to cast your official ballot. This action is tracked and cannot be undone or updated. Are you sure you wish to submit?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, Submit Ballot',
                cancelButtonText: 'No, Let me review',
                customClass: {
                    confirmButton: 'bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold px-6 py-3 rounded-xl transition-all shadow-md focus:ring-2 focus:ring-emerald-500/20 outline-none me-3 cursor-pointer',
                    cancelButton: 'bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-bold px-6 py-3 rounded-xl transition-all border border-slate-200 dark:border-slate-700 outline-none cursor-pointer'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
</script>
@endpush
