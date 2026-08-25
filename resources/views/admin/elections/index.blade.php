@extends('layouts.admin')

@section('title', 'Elections Management - ML Sako')

@section('header')
<div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
    <div>
        <h1 class="text-2xl font-black tracking-tight text-slate-900 dark:text-white sm:text-3xl">Elections & Polls</h1>
        <p class="mt-1 text-xs font-semibold text-slate-500 dark:text-slate-400">Manage coop elections, add positions, and review real-time voting results.</p>
    </div>
    <div>
        <button id="btn-add-election" class="inline-flex items-center justify-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold px-4 py-2.5 rounded-xl shadow-md shadow-emerald-600/10 transition-all duration-200 hover:-translate-y-0.5">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            Create New Election
        </button>
    </div>
</div>
@endsection

@section('content')
<div class="space-y-6">
    @if($elections->isEmpty())
    <div class="bg-white dark:bg-slate-800 border border-slate-200/80 dark:border-slate-700/80 rounded-[2rem] p-12 text-center">
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-slate-50 dark:bg-slate-900 border border-slate-100 dark:border-slate-800 text-slate-400 mb-4 shadow-sm">
            <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
        </div>
        <h3 class="text-base font-bold text-slate-900 dark:text-white">No Elections Found</h3>
        <p class="mt-1.5 text-xs text-slate-500 dark:text-slate-400 max-w-sm mx-auto">Create your first election to allow cooperative members to cast votes for board positions and management.</p>
        <div class="mt-6">
            <button id="btn-add-election-empty" class="inline-flex items-center justify-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold px-4 py-2.5 rounded-xl shadow-md transition-all">
                Create First Election
            </button>
        </div>
    </div>
    @else
    <div class="bg-white dark:bg-slate-800 border border-slate-200/80 dark:border-slate-700/80 rounded-[2rem] overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-100 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-900/50">
                        <th class="px-6 py-4 text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Election Name</th>
                        <th class="px-6 py-4 text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Start Time</th>
                        <th class="px-6 py-4 text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">End Time</th>
                        <th class="px-6 py-4 text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Positions</th>
                        <th class="px-6 py-4 text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-[10px] font-extrabold text-slate-400 uppercase tracking-wider text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                    @foreach($elections as $election)
                    <tr class="hover:bg-slate-50/30 dark:hover:bg-slate-900/10 transition-colors">
                        <td class="px-6 py-4.5">
                            <div>
                                <div class="text-xs font-bold text-slate-900 dark:text-white">{{ $election->name }}</div>
                                @if($election->description)
                                <div class="text-[10px] text-slate-500 truncate max-w-xs mt-0.5">{{ $election->description }}</div>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4.5 text-xs font-semibold text-slate-600 dark:text-slate-300">
                            {{ $election->start_time->format('M d, Y h:i A') }}
                        </td>
                        <td class="px-6 py-4.5 text-xs font-semibold text-slate-600 dark:text-slate-300">
                            {{ $election->end_time->format('M d, Y h:i A') }}
                        </td>
                        <td class="px-6 py-4.5">
                            <span class="inline-flex items-center px-2 py-1 rounded-lg bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 text-[10px] font-bold">
                                {{ $election->positions_count }} defined
                            </span>
                        </td>
                        <td class="px-6 py-4.5">
                            @php $status = $election->computed_status; @endphp
                            @if($status === 'active')
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 text-[10px] font-extrabold uppercase tracking-wider">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                Ongoing
                            </span>
                            @elseif($status === 'upcoming')
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-amber-500/10 text-amber-600 dark:text-amber-400 text-[10px] font-extrabold uppercase tracking-wider">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                Scheduled
                            </span>
                            @else
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-slate-500/10 text-slate-600 dark:text-slate-400 text-[10px] font-extrabold uppercase tracking-wider">
                                <span class="w-1.5 h-1.5 rounded-full bg-slate-500"></span>
                                Completed
                            </span>
                            @endif
                        </td>
                        <td class="px-6 py-4.5 text-right space-x-1.5">
                            <a href="{{ route('admin.elections.show', $election) }}" class="inline-flex items-center justify-center p-1.5 rounded-lg text-slate-500 hover:text-emerald-600 hover:bg-emerald-500/5 transition-all" title="Manage Candidates & Positions">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/></svg>
                            </a>
                            <a href="{{ route('admin.elections.results', $election) }}" class="inline-flex items-center justify-center p-1.5 rounded-lg text-slate-500 hover:text-sky-600 hover:bg-sky-500/5 transition-all" title="View Results">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                            </a>
                            <button class="btn-edit-election inline-flex items-center justify-center p-1.5 rounded-lg text-slate-500 hover:text-amber-600 hover:bg-amber-500/5 transition-all"
                                data-id="{{ $election->id }}"
                                data-name="{{ $election->name }}"
                                data-description="{{ $election->description }}"
                                data-start_time="{{ $election->start_time->format('Y-m-d\TH:i') }}"
                                data-end_time="{{ $election->end_time->format('Y-m-d\TH:i') }}"
                                title="Edit details">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </button>
                            <form action="{{ route('admin.elections.destroy', $election) }}" method="POST" class="inline-block delete-election-form">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn-delete-election inline-flex items-center justify-center p-1.5 rounded-lg text-slate-500 hover:text-rose-600 hover:bg-rose-500/5 transition-all cursor-pointer" title="Delete Election">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($elections->hasPages())
        <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-700">
            {{ $elections->links() }}
        </div>
        @endif
    </div>
    @endif
</div>

<!-- CABINET CABINET/DRAWER: CREATE ELECTION -->
<div id="modal-add" class="fixed inset-0 z-50 hidden">
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-slate-950/40 dark:bg-slate-950/60 backdrop-blur-md opacity-0 transition-opacity duration-500 ease-[cubic-bezier(0.16,1,0.3,1)] pointer-events-none modal-overlay"></div>
    
    <!-- Floating Premium Panel (Side Drawer) -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-2xl shadow-slate-950/15 dark:shadow-slate-950/50 overflow-hidden h-[calc(100vh-2rem)] w-[calc(100vw-2rem)] sm:w-full sm:max-w-md fixed right-4 top-4 bottom-4 z-50 transform translate-x-[calc(100%+2rem)] transition-transform duration-500 ease-[cubic-bezier(0.16,1,0.3,1)] modal-container p-6 sm:p-8 flex flex-col">
        <!-- Header -->
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-4">
            <div>
                <h3 class="text-xl font-extrabold text-slate-900 dark:text-slate-100 tracking-tight serif-font">Create New Election</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 font-medium mt-1">Setup voting event timeframe and rules</p>
            </div>
            <button class="modal-close p-1.5 rounded-xl text-slate-400 hover:text-slate-700 dark:text-slate-500 dark:hover:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800/80 transition-all duration-200">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <!-- Form Wrapper -->
        <form action="{{ route('admin.elections.store') }}" method="POST" class="flex-1 flex flex-col overflow-hidden mt-6">
            @csrf
            
            <!-- Scrollable content area -->
            <div class="flex-1 overflow-y-auto pr-1 -mr-1 space-y-5">
                <!-- Election Name -->
                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-slate-700 dark:text-slate-300">Election Name</label>
                    <input type="text" name="name" required placeholder="e.g. 2026 Annual Board Selection" class="w-full px-4 py-2.5 text-xs font-medium border border-slate-200 dark:border-slate-700/80 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 rounded-xl outline-none focus:border-emerald-500 dark:focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 dark:focus:ring-emerald-500/10 placeholder-slate-400 dark:placeholder-slate-600 transition-all duration-200">
                </div>

                <!-- Description -->
                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-slate-700 dark:text-slate-300">Description / Rules</label>
                    <textarea name="description" rows="4" placeholder="Describe the rules, requirements and instructions of this election..." class="w-full px-4 py-2.5 text-xs font-medium border border-slate-200 dark:border-slate-700/80 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 rounded-xl outline-none focus:border-emerald-500 dark:focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 dark:focus:ring-emerald-500/10 placeholder-slate-400 dark:placeholder-slate-600 transition-all duration-200 resize-none"></textarea>
                </div>

                <!-- Start Date -->
                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-slate-700 dark:text-slate-300">Start Date & Time</label>
                    <input type="datetime-local" name="start_time" required class="w-full px-4 py-2.5 text-xs font-medium border border-slate-200 dark:border-slate-700/80 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 rounded-xl outline-none focus:border-emerald-500 dark:focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 dark:focus:ring-emerald-500/10 transition-all duration-200">
                </div>

                <!-- End Date -->
                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-slate-700 dark:text-slate-300">End Date & Time</label>
                    <input type="datetime-local" name="end_time" required class="w-full px-4 py-2.5 text-xs font-medium border border-slate-200 dark:border-slate-700/80 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 rounded-xl outline-none focus:border-emerald-500 dark:focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 dark:focus:ring-emerald-500/10 transition-all duration-200">
                </div>
            </div>

            <!-- Fixed Footer Action Bar -->
            <div class="pt-4 border-t border-slate-100 dark:border-slate-800/60 flex items-center justify-end gap-3 mt-4">
                <button type="button" class="modal-close px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-xs font-bold text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-800 dark:hover:text-slate-100 transition-colors">Cancel</button>
                <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 dark:bg-emerald-600 dark:hover:bg-emerald-500 text-white font-bold text-xs px-5 py-2.5 rounded-xl shadow-md shadow-emerald-600/15 dark:shadow-emerald-900/25 transition-all">Create Election</button>
            </div>
        </form>
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
        <form id="edit-election-form" action="" method="POST" class="flex-1 flex flex-col overflow-hidden mt-6">
            @csrf
            @method('PUT')
            
            <!-- Scrollable content area -->
            <div class="flex-1 overflow-y-auto pr-1 -mr-1 space-y-5">
                <!-- Election Name -->
                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-slate-700 dark:text-slate-300">Election Name</label>
                    <input type="text" name="name" id="edit-name" required class="w-full px-4 py-2.5 text-xs font-medium border border-slate-200 dark:border-slate-700/80 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 rounded-xl outline-none focus:border-emerald-500 dark:focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 dark:focus:ring-emerald-500/10 placeholder-slate-400 dark:placeholder-slate-600 transition-all duration-200">
                </div>

                <!-- Description -->
                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-slate-700 dark:text-slate-300">Description / Rules</label>
                    <textarea name="description" id="edit-description" rows="4" class="w-full px-4 py-2.5 text-xs font-medium border border-slate-200 dark:border-slate-700/80 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 rounded-xl outline-none focus:border-emerald-500 dark:focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 dark:focus:ring-emerald-500/10 placeholder-slate-400 dark:placeholder-slate-600 transition-all duration-200 resize-none"></textarea>
                </div>

                <!-- Start Date -->
                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-slate-700 dark:text-slate-300">Start Date & Time</label>
                    <input type="datetime-local" name="start_time" id="edit-start_time" required class="w-full px-4 py-2.5 text-xs font-medium border border-slate-200 dark:border-slate-700/80 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 rounded-xl outline-none focus:border-emerald-500 dark:focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 dark:focus:ring-emerald-500/10 transition-all duration-200">
                </div>

                <!-- End Date -->
                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-slate-700 dark:text-slate-300">End Date & Time</label>
                    <input type="datetime-local" name="end_time" id="edit-end_time" required class="w-full px-4 py-2.5 text-xs font-medium border border-slate-200 dark:border-slate-700/80 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 rounded-xl outline-none focus:border-emerald-500 dark:focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 dark:focus:ring-emerald-500/10 transition-all duration-200">
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

        // Trigger: Add Election
        const btnAdd = document.getElementById("btn-add-election");
        if(btnAdd) {
            btnAdd.addEventListener("click", () => openModal("modal-add"));
        }
        
        const btnAddEmpty = document.getElementById("btn-add-election-empty");
        if(btnAddEmpty) {
            btnAddEmpty.addEventListener("click", () => openModal("modal-add"));
        }

        // Trigger: Edit Election (Populate and Slide out drawer)
        document.querySelectorAll(".btn-edit-election").forEach(btn => {
            btn.addEventListener("click", function() {
                const id = this.getAttribute("data-id");
                const name = this.getAttribute("data-name");
                const description = this.getAttribute("data-description");
                const startTime = this.getAttribute("data-start_time");
                const endTime = this.getAttribute("data-end_time");

                // Set values in modal
                document.getElementById("edit-name").value = name;
                document.getElementById("edit-description").value = description || "";
                document.getElementById("edit-start_time").value = startTime;
                document.getElementById("edit-end_time").value = endTime;

                // Dynamically update form action url
                document.getElementById("edit-election-form").action = `/admin/elections/${id}`;

                // Open modal edit drawer
                openModal("modal-edit");
            });
        });

        // Trigger: SweetAlert Delete Confirmation
        document.querySelectorAll(".btn-delete-election").forEach(btn => {
            btn.addEventListener("click", function(e) {
                e.preventDefault();
                const form = this.closest("form");

                window.MLSAKOAlert.fire({
                    title: 'Delete Election?',
                    text: 'Are you sure you want to delete this election? All associated positions, candidates, and votes will be permanently deleted. This action is irreversible.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, Delete Permanently',
                    cancelButtonText: 'No, Cancel',
                    customClass: {
                        confirmButton: 'bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold px-6 py-3 rounded-xl transition-all shadow-md focus:ring-2 focus:ring-rose-500/20 outline-none me-3',
                        cancelButton: 'bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-bold px-6 py-3 rounded-xl transition-all border border-slate-200 dark:border-slate-700 outline-none'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
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
