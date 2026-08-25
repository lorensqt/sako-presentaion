@extends('layouts.admin')

@section('title', 'Manage Election Structure - ML Sako')

@section('header')
<div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.elections.index') }}" class="inline-flex items-center justify-center p-2 rounded-xl text-slate-500 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition-all">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <div>
            <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 text-[10px] font-black uppercase tracking-wider mb-1">
                {{ $election->computed_status }}
            </span>
            <h1 class="text-2xl font-black tracking-tight text-slate-900 dark:text-white">{{ $election->name }}</h1>
        </div>
    </div>
    <div class="flex items-center gap-2">
        <a href="{{ route('admin.elections.results', $election) }}" class="inline-flex items-center justify-center gap-2 bg-sky-50 dark:bg-sky-950/20 hover:bg-sky-100 dark:hover:bg-sky-900/30 text-sky-700 dark:text-sky-300 border border-sky-100 dark:border-sky-900/30 text-xs font-bold px-4 py-2.5 rounded-xl shadow-sm transition-all">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            View Live Tally
        </a>
        <button id="btn-edit-election" class="inline-flex items-center justify-center gap-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-bold px-4 py-2.5 rounded-xl transition-all cursor-pointer">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            Edit Info
        </button>
    </div>
</div>
@endsection

@section('content')
<div class="space-y-8">
    <!-- Meta Details Row -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Duration Box -->
        <div class="bg-white dark:bg-slate-800 border border-slate-200/80 dark:border-slate-700/80 rounded-[2rem] p-6 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 text-emerald-600 flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <span class="text-[10px] font-extrabold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Active Period</span>
                <p class="text-xs font-bold text-slate-800 dark:text-slate-200 mt-0.5 leading-snug">
                    {{ $election->start_time->format('M d') }} - {{ $election->end_time->format('M d, Y') }}
                </p>
                <p class="text-[10px] font-semibold text-slate-500 mt-0.5">
                    {{ $election->start_time->format('h:i A') }} to {{ $election->end_time->format('h:i A') }}
                </p>
            </div>
        </div>

        <!-- Description Box -->
        <div class="bg-white dark:bg-slate-800 border border-slate-200/80 dark:border-slate-700/80 rounded-[2rem] p-6 shadow-sm md:col-span-2 flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-sky-500/10 text-sky-600 flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div class="min-w-0 flex-1">
                <span class="text-[10px] font-extrabold text-slate-400 dark:text-slate-500 uppercase tracking-wider block">Description</span>
                <p class="text-xs font-semibold text-slate-600 dark:text-slate-300 mt-1 leading-relaxed truncate-2-lines" title="{{ $election->description ?? 'No description added.' }}">
                    {{ $election->description ?? 'No description added.' }}
                </p>
            </div>
        </div>
    </div>

    <!-- Main Dynamic Structure Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Add Position Card -->
        <div class="space-y-6">
            <div class="bg-white dark:bg-slate-800 border border-slate-200/80 dark:border-slate-700/80 rounded-[2rem] p-6 shadow-sm">
                <h3 class="text-sm font-extrabold text-slate-900 dark:text-white uppercase tracking-wider mb-4 flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Add Target Position
                </h3>
                <form action="{{ route('admin.elections.positions.store', $election) }}" method="POST" class="space-y-4">
                    @csrf
                    <div class="space-y-1.5">
                        <label for="pos_name" class="text-[10px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Position Name</label>
                        <input type="text" name="name" id="pos_name" required placeholder="e.g. Board Member"
                            class="w-full text-xs font-semibold px-4 py-3 bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 dark:focus:border-emerald-500 text-slate-900 dark:text-white transition-all">
                    </div>
                    <button type="submit" class="w-full inline-flex items-center justify-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold px-4 py-3 rounded-xl shadow-md shadow-emerald-600/10 transition-all">
                        Create Position
                    </button>
                </form>
            </div>

            <!-- Guidelines Card -->
            <div class="bg-emerald-50 dark:bg-slate-800/50 border border-emerald-100 dark:border-slate-700/80 rounded-[2rem] p-6 text-emerald-800 dark:text-emerald-400">
                <h4 class="text-[11px] font-black uppercase tracking-wider mb-2 flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                    Setup Guide
                </h4>
                <ol class="text-[10px] font-semibold space-y-2 leading-relaxed pl-4 list-decimal">
                    <li>Add the positions that members should vote for (e.g., Board of Directors, Audit Committee).</li>
                    <li>Add competing candidates manually to each position with names and photo uploads.</li>
                    <li>Verify all elements. Once voting begins, modifying details is restricted to ensure system auditing integrity.</li>
                </ol>
            </div>
        </div>

        <!-- Position & Candidates List -->
        <div class="lg:col-span-2 space-y-6">
            @if($election->positions->isEmpty())
            <div class="bg-white dark:bg-slate-800 border border-slate-200/80 dark:border-slate-700/80 rounded-[2rem] p-12 text-center">
                <div class="inline-flex items-center justify-center w-12 h-12 rounded-2xl bg-slate-50 dark:bg-slate-900 text-slate-400 mb-3">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </div>
                <h3 class="text-xs font-bold text-slate-800 dark:text-white">No Positions Created</h3>
                <p class="mt-1 text-[11px] text-slate-500 max-w-xs mx-auto">Use the sidebar form to add positions to this election. Every election needs at least one position before members can vote.</p>
            </div>
            @else
            <div class="space-y-6">
                @foreach($election->positions as $position)
                <div class="bg-white dark:bg-slate-800 border border-slate-200/80 dark:border-slate-700/80 rounded-[2rem] overflow-hidden shadow-sm">
                    <!-- Position Title Header -->
                    <div class="px-6 py-4 bg-slate-50/50 dark:bg-slate-900/50 border-b border-slate-100 dark:border-slate-700/80 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                            <h3 class="text-xs font-extrabold text-slate-800 dark:text-white tracking-wider uppercase">{{ $position->name }}</h3>
                        </div>
                        <form action="{{ route('admin.positions.destroy', $position) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this position? All added candidates and votes cast for this position will be lost permanently.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-rose-500 hover:text-rose-700 p-1.5 rounded-lg hover:bg-rose-50 dark:hover:bg-rose-950/20 transition-all" title="Delete Position">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </form>
                    </div>

                    <!-- Candidates Content -->
                    <div class="p-6 space-y-6">
                        <!-- Candidate Grid List -->
                        @if($position->candidates->isEmpty())
                        <div class="text-center py-4 text-slate-400 dark:text-slate-500 border border-dashed border-slate-200 dark:border-slate-700 rounded-2xl">
                            <p class="text-[11px] font-semibold">No candidates competing for this position yet.</p>
                        </div>
                        @else
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            @foreach($position->candidates as $candidate)
                            <div class="flex items-center justify-between p-3.5 bg-slate-50 dark:bg-slate-900/40 rounded-2xl border border-slate-100 dark:border-slate-700/60 shadow-sm hover:scale-[1.01] transition-all">
                                <div class="flex items-center gap-3.5 min-w-0">
                                    <!-- Photo thumbnail -->
                                    <div class="w-11 h-11 rounded-xl bg-slate-200 dark:bg-slate-800 overflow-hidden flex-shrink-0 flex items-center justify-center border border-slate-200/50 dark:border-slate-700 shadow-sm">
                                        @if($candidate->photo_path)
                                        <img src="{{ asset('storage/' . $candidate->photo_path) }}" alt="{{ $candidate->name }}" class="w-full h-full object-cover">
                                        @else
                                        <span class="text-xs font-black text-slate-500 dark:text-slate-400">{{ strtoupper(substr($candidate->name, 0, 2)) }}</span>
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <h4 class="text-xs font-bold text-slate-900 dark:text-white truncate" title="{{ $candidate->name }}">{{ $candidate->name }}</h4>
                                        <p class="text-[9px] font-extrabold text-emerald-600 uppercase tracking-widest mt-0.5">{{ $candidate->votes_count }} Votes cast</p>
                                    </div>
                                </div>
                                <form action="{{ route('admin.candidates.destroy', $candidate) }}" method="POST" onsubmit="return confirm('Are you sure you want to remove this candidate? Any votes cast for this person will be lost.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-slate-400 hover:text-rose-500 p-1 rounded-lg hover:bg-rose-500/5 transition-all">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </div>
                            @endforeach
                        </div>
                        @endif

                        <!-- Add Candidate Form (Collapsible/Stylized inline) -->
                        <div class="pt-4 border-t border-slate-100 dark:border-slate-700/50">
                            <form action="{{ route('admin.positions.candidates.store', $position) }}" method="POST" enctype="multipart/form-data" class="flex flex-col sm:flex-row items-end gap-3.5">
                                @csrf
                                <div class="flex-1 min-w-0 w-full space-y-1.5">
                                    <label class="text-[10px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Candidate Full Name</label>
                                    <input type="text" name="name" required placeholder="Enter candidate's full name"
                                        class="w-full text-xs font-semibold px-4.5 py-2.5 bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500/20 dark:focus:ring-emerald-500/10 focus:border-emerald-600 dark:focus:border-emerald-500 placeholder-slate-400 dark:placeholder-slate-600 text-slate-900 dark:text-white transition-all">
                                </div>
                                <div class="w-full sm:w-auto space-y-1.5 flex-shrink-0">
                                    <label class="text-[10px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider block">Photo (Optional)</label>
                                    <input type="file" name="photo" accept="image/*"
                                        class="text-xs file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-[10px] file:font-black file:bg-slate-100 dark:file:bg-slate-800 file:text-slate-700 dark:file:text-slate-300 hover:file:bg-slate-200 dark:hover:file:bg-slate-700/80 file:cursor-pointer text-slate-500 dark:text-slate-400">
                                </div>
                                <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center gap-1.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 text-xs font-bold px-5 py-2.5 border border-slate-200/50 dark:border-slate-700/80 rounded-xl shadow-sm transition-all flex-shrink-0 cursor-pointer">
                                    <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                                    Add Candidate
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>
</div>

<!-- CABINET CABINET/DRAWER: EDIT ELECTION -->
<div id="modal-edit" class="fixed inset-0 z-50 hidden">
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-slate-950/40 dark:bg-slate-950/60 backdrop-blur-md opacity-0 transition-opacity duration-500 ease-[cubic-bezier(0.16,1,0.3,1)] pointer-events-none modal-overlay"></div>
    
    <!-- Floating Premium Panel (Side Drawer) -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-2xl shadow-slate-950/15 dark:shadow-slate-950/50 overflow-hidden h-[calc(100vh-2rem)] w-[calc(100vw-2rem)] sm:w-full sm:max-w-md fixed right-4 top-4 bottom-4 z-50 transform translate-x-[calc(100%+2rem)] transition-transform duration-500 ease-[cubic-bezier(0.16,1,0.3,1)] modal-container p-6 sm:p-8 flex flex-col">
        <!-- Header -->
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-4">
            <div>
                <h3 class="text-xl font-extrabold text-slate-900 dark:text-slate-100 tracking-tight serif-font">Edit Election Details</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 font-medium mt-1">Modify database election record</p>
            </div>
            <button class="modal-close p-1.5 rounded-xl text-slate-400 hover:text-slate-700 dark:text-slate-500 dark:hover:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800/80 transition-all duration-200">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <!-- Form Wrapper -->
        <form action="{{ route('admin.elections.update', $election) }}" method="POST" class="flex-1 flex flex-col overflow-hidden mt-6">
            @csrf
            @method('PUT')
            
            <!-- Scrollable content area -->
            <div class="flex-1 overflow-y-auto pr-1 -mr-1 space-y-5">
                <!-- Election Name -->
                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-slate-700 dark:text-slate-300">Election Name</label>
                    <input type="text" name="name" required value="{{ $election->name }}" class="w-full px-4 py-2.5 text-xs font-medium border border-slate-200 dark:border-slate-700/80 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 rounded-xl outline-none focus:border-emerald-500 dark:focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 dark:focus:ring-emerald-500/10 placeholder-slate-400 dark:placeholder-slate-600 transition-all duration-200">
                </div>

                <!-- Description -->
                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-slate-700 dark:text-slate-300">Description / Rules</label>
                    <textarea name="description" rows="4" class="w-full px-4 py-2.5 text-xs font-medium border border-slate-200 dark:border-slate-700/80 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 rounded-xl outline-none focus:border-emerald-500 dark:focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 dark:focus:ring-emerald-500/10 placeholder-slate-400 dark:placeholder-slate-600 transition-all duration-200 resize-none">{{ $election->description }}</textarea>
                </div>

                <!-- Start Date -->
                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-slate-700 dark:text-slate-300">Start Date & Time</label>
                    <input type="datetime-local" name="start_time" required value="{{ $election->start_time->format('Y-m-d\TH:i') }}" class="w-full px-4 py-2.5 text-xs font-medium border border-slate-200 dark:border-slate-700/80 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 rounded-xl outline-none focus:border-emerald-500 dark:focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 dark:focus:ring-emerald-500/10 transition-all duration-200">
                </div>

                <!-- End Date -->
                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-slate-700 dark:text-slate-300">End Date & Time</label>
                    <input type="datetime-local" name="end_time" required value="{{ $election->end_time->format('Y-m-d\TH:i') }}" class="w-full px-4 py-2.5 text-xs font-medium border border-slate-200 dark:border-slate-700/80 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 rounded-xl outline-none focus:border-emerald-500 dark:focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 dark:focus:ring-emerald-500/10 transition-all duration-200">
                </div>
            </div>

            <!-- Fixed Footer Action Bar -->
            <div class="pt-4 border-t border-slate-100 dark:border-slate-800/60 flex items-center justify-end gap-3 mt-4">
                <button type="button" class="modal-close px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-xs font-bold text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-800 dark:hover:text-slate-100 transition-colors">Cancel</button>
                <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 dark:bg-emerald-600 dark:hover:bg-emerald-500 text-white font-bold text-xs px-5 py-2.5 rounded-xl shadow-md shadow-emerald-600/15 dark:shadow-emerald-900/25 transition-all">Save Changes</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Modal toggling controls (dynamic handling for center modals and right drawers)
        function openModal(modalId) {
            const modal = document.getElementById(modalId);
            const overlay = modal.querySelector(".modal-overlay");
            const container = modal.querySelector(".modal-container");
            
            modal.classList.remove("hidden");
            
            // Allow browser layout pass to register classes
            setTimeout(() => {
                if (overlay) {
                    overlay.classList.remove("opacity-0", "pointer-events-none");
                    overlay.classList.add("opacity-100", "pointer-events-auto");
                }
                
                if (container) {
                    // Slide drawer from the right
                    container.classList.remove("translate-x-[calc(100%+2rem)]");
                    container.classList.add("translate-x-0");
                }
            }, 50);
        }

        function closeModal(modalId) {
            const modal = document.getElementById(modalId);
            const overlay = modal.querySelector(".modal-overlay");
            const container = modal.querySelector(".modal-container");
            
            if (overlay) {
                overlay.classList.add("opacity-0", "pointer-events-none");
                overlay.classList.remove("opacity-100", "pointer-events-auto");
            }
            
            if (container) {
                // Slide drawer back to the right
                container.classList.add("translate-x-[calc(100%+2rem)]");
                container.classList.remove("translate-x-0");
            }
            
            // Hide the wrapper after animation completes
            setTimeout(() => {
                modal.classList.add("hidden");
            }, 500);
        }

        // Setup click listeners for cancel/close controls
        document.querySelectorAll(".modal-close, .modal-overlay").forEach(btn => {
            btn.addEventListener("click", function() {
                const modal = this.closest('[id^="modal-"]');
                closeModal(modal.id);
            });
        });

        // Trigger: Edit Election Detail inline drawer
        const btnEdit = document.getElementById("btn-edit-election");
        if(btnEdit) {
            btnEdit.addEventListener("click", () => openModal("modal-edit"));
        }
    });
</script>
@endpush

@push('styles')
<style>
    /* Invert calendar picker indicator in dark mode for clear visibility */
    .dark input[type="datetime-local"]::-webkit-calendar-picker-indicator {
        filter: invert(1) brightness(0.9) contrast(0.9);
        cursor: pointer;
    }
</style>
@endpush
