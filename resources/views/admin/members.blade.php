@extends('layouts.admin')

@section('title', 'Cooperative Members Directory - Sako Cooperative')

@push('styles')
<style>
    .btn-edit-member, .btn-delete-member, #btn-add-member {
        cursor: pointer !important;
    }
</style>
@endpush

@section('header')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 dark:text-slate-100 tracking-tight flex items-center gap-2.5">
            <i class="fa-solid fa-users text-emerald-600 dark:text-emerald-400 text-xl"></i>
            <span>Cooperative Members Directory</span>
        </h1>
        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 font-medium">
            Browse, register, search, and manage cooperative member profiles, contact credentials, and residential details.
        </p>
    </div>

    <button id="btn-add-member" class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs px-4 py-2.5 rounded-xl shadow-xs shadow-emerald-600/10 hover:shadow-emerald-600/20 hover:-translate-y-0.5 transition-all duration-200 cursor-pointer self-start sm:self-auto">
        <i class="fa-solid fa-user-plus text-xs"></i>
        <span>Register Member</span>
    </button>
</div>
@endsection

@section('content')
<div class="space-y-5 sm:space-y-6 animate-fade-in">

    <!-- Search & Metrics Panel -->
    <div class="bg-white dark:bg-slate-800 border border-slate-200/80 dark:border-slate-700/80 p-4 sm:p-5 rounded-2xl shadow-xs flex flex-col md:flex-row items-center justify-between gap-4">
        <form action="{{ route('admin.members') }}" method="GET" class="w-full md:max-w-md relative">
            <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-3 text-slate-400 dark:text-slate-500 text-xs"></i>
            <input type="text" name="search" value="{{ $search }}" 
                placeholder="Search members by name, email, company ID, contact..." 
                class="w-full pl-9 pr-14 py-2 text-xs font-medium border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 rounded-xl focus:border-emerald-500 dark:focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500/20 placeholder-slate-400 dark:placeholder-slate-500 transition-all outline-none">
            @if($search)
                <a href="{{ route('admin.members') }}" class="absolute right-3 top-2 text-xs font-bold text-slate-400 hover:text-slate-600 dark:text-slate-500 dark:hover:text-slate-300 transition-colors">Reset</a>
            @endif
        </form>

        <div class="flex items-center gap-2">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-100 dark:bg-slate-700/60 text-slate-700 dark:text-slate-300 text-xs font-semibold border border-slate-200/80 dark:border-slate-600">
                <i class="fa-solid fa-users text-slate-500 text-xs"></i>
                <span>Showing {{ $users->firstItem() ?? 0 }}-{{ $users->lastItem() ?? 0 }} of {{ number_format($users->total()) }} Registered Members</span>
            </span>
        </div>
    </div>

    <!-- COOPERATIVE MEMBERS TABLE -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-100/90 dark:bg-slate-800/95 border-b-2 border-slate-200/90 dark:border-slate-700 text-xs font-bold text-slate-800 dark:text-slate-100 uppercase tracking-wider">
                        <th class="px-5 py-3.5">
                            <span class="inline-flex items-center gap-1.5">
                                <i class="fa-solid fa-user text-slate-500 dark:text-slate-400 text-[11px]"></i>
                                Cooperative Member
                            </span>
                        </th>
                        <th class="px-5 py-3.5">
                            <span class="inline-flex items-center gap-1.5">
                                <i class="fa-solid fa-id-badge text-slate-500 dark:text-slate-400 text-[11px]"></i>
                                Company ID
                            </span>
                        </th>
                        <th class="px-5 py-3.5">
                            <span class="inline-flex items-center gap-1.5">
                                <i class="fa-solid fa-phone text-slate-500 dark:text-slate-400 text-[11px]"></i>
                                Contact Number
                            </span>
                        </th>
                        <th class="px-5 py-3.5">
                            <span class="inline-flex items-center gap-1.5">
                                <i class="fa-solid fa-location-dot text-slate-500 dark:text-slate-400 text-[11px]"></i>
                                Permanent Address
                            </span>
                        </th>
                        <th class="px-5 py-3.5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700/60 text-xs font-medium text-slate-700 dark:text-slate-300">
                    @forelse($users as $user)
                        <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-700/30 transition-colors">
                            <!-- Member Profile -->
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 flex items-center justify-center font-bold text-xs border border-emerald-200/60 dark:border-emerald-800/40 flex-shrink-0 shadow-xs">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <h4 class="font-bold text-slate-900 dark:text-white leading-tight truncate">{{ $user->name }}</h4>
                                        <p class="text-[11px] text-slate-500 dark:text-slate-400 font-medium truncate mt-0.5">{{ $user->email }}</p>
                                    </div>
                                </div>
                            </td>

                            <!-- Company ID -->
                            <td class="px-5 py-3.5">
                                <span class="font-mono font-bold text-slate-700 dark:text-slate-200 bg-slate-100 dark:bg-slate-900 border border-slate-200/80 dark:border-slate-700 px-2 py-0.5 rounded-lg text-xs">
                                    {{ $user->company_id ?: 'N/A' }}
                                </span>
                            </td>

                            <!-- Contact Number -->
                            <td class="px-5 py-3.5 font-semibold text-slate-600 dark:text-slate-300">
                                {{ $user->contact_number ?: 'N/A' }}
                            </td>

                            <!-- Permanent Address -->
                            <td class="px-5 py-3.5 max-w-[260px] truncate text-slate-500 dark:text-slate-400 text-xs" title="{{ $user->address }}">
                                {{ $user->address ?: 'N/A' }}
                            </td>

                            <!-- Action Buttons -->
                            <td class="px-5 py-3.5 text-right">
                                <div class="inline-flex items-center gap-1.5">
                                    <a href="{{ route('admin.members.pdf', $user->id) }}" target="_blank" class="p-1.5 rounded-lg border border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-950/30 transition-colors"
                                        title="Export Member PDF">
                                        <i class="fa-solid fa-file-pdf text-xs"></i>
                                    </a>

                                    <button class="btn-edit-member p-1.5 rounded-lg border border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400 hover:text-emerald-600 dark:hover:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-950/30 transition-colors cursor-pointer"
                                        data-id="{{ $user->id }}"
                                        data-name="{{ $user->name }}"
                                        data-email="{{ $user->email }}"
                                        data-company_id="{{ $user->company_id }}"
                                        data-contact_number="{{ $user->contact_number }}"
                                        data-address="{{ $user->address }}"
                                        title="Edit Member Details">
                                        <i class="fa-solid fa-pen-to-square text-xs"></i>
                                    </button>
                                    
                                    @if(auth()->id() !== $user->id)
                                        <button class="btn-delete-member p-1.5 rounded-lg border border-slate-200 dark:border-slate-700 text-slate-400 dark:text-slate-500 hover:text-rose-600 dark:hover:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/30 transition-colors cursor-pointer"
                                            data-id="{{ $user->id }}"
                                            data-name="{{ $user->name }}"
                                            title="Remove Member">
                                            <i class="fa-solid fa-trash-can text-xs"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-12 text-center text-slate-400 dark:text-slate-500 text-xs">
                                <div class="w-10 h-10 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 flex items-center justify-center mx-auto text-slate-400 mb-2">
                                    <i class="fa-solid fa-user-slash text-sm"></i>
                                </div>
                                No members match the current search query.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($users->hasPages())
            <div class="px-5 py-3.5 bg-slate-50 dark:bg-slate-900/50 border-t border-slate-200/80 dark:border-slate-700/80">
                {{ $users->appends(['search' => $search])->links() }}
            </div>
        @endif
    </div>

</div>

<!-- DRAWER MODAL: REGISTER MEMBER -->
<div id="modal-add-member" class="fixed inset-0 z-50 hidden">
    <div class="fixed inset-0 bg-slate-950/40 dark:bg-slate-950/60 backdrop-blur-md opacity-0 transition-opacity duration-300 pointer-events-none modal-overlay"></div>
    
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-2xl overflow-hidden h-[calc(100vh-2rem)] w-[calc(100vw-2rem)] sm:w-full sm:max-w-lg fixed right-4 top-4 bottom-4 z-50 transform translate-x-[calc(100%+2rem)] transition-transform duration-300 modal-container p-5 sm:p-6 flex flex-col">
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
            <div>
                <h3 class="text-base font-bold text-slate-900 dark:text-slate-100 flex items-center gap-2">
                    <i class="fa-solid fa-user-plus text-emerald-600 text-sm"></i>
                    <span>Register Cooperative Member</span>
                </h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Add cooperative borrower/investor profile</p>
            </div>
            <button class="modal-close p-1.5 rounded-xl text-slate-400 hover:text-slate-700 dark:text-slate-500 dark:hover:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors cursor-pointer">
                <i class="fa-solid fa-xmark text-base"></i>
            </button>
        </div>

        <form action="{{ route('admin.members.store') }}" method="POST" class="flex-1 flex flex-col overflow-hidden mt-4">
            @csrf
            
            <div class="flex-1 overflow-y-auto pr-1 space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div class="space-y-1">
                        <label class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider block">Full Name</label>
                        <input type="text" name="name" required placeholder="John Doe" class="w-full px-3 py-2 text-xs font-semibold border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 rounded-xl outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500/20 placeholder-slate-400 transition-all">
                    </div>
                    <div class="space-y-1">
                        <label class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider block">Company ID</label>
                        <input type="text" name="company_id" required placeholder="20241001" class="w-full px-3 py-2 text-xs font-semibold border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 rounded-xl outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500/20 placeholder-slate-400 transition-all">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div class="space-y-1">
                        <label class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider block">Email Address</label>
                        <input type="email" name="email" required placeholder="john@example.com" class="w-full px-3 py-2 text-xs font-semibold border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 rounded-xl outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500/20 placeholder-slate-400 transition-all">
                    </div>
                    <div class="space-y-1">
                        <label class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider block">Contact Number</label>
                        <input type="text" name="contact_number" placeholder="09xxxxxxxxxx" class="w-full px-3 py-2 text-xs font-semibold border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 rounded-xl outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500/20 placeholder-slate-400 transition-all">
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider flex items-center justify-between">
                        <span>Initial Password</span>
                        <span class="text-[9px] text-slate-400 normal-case">(Defaults to 'password')</span>
                    </label>
                    <input type="password" name="password" placeholder="••••••••" class="w-full px-3 py-2 text-xs font-semibold border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 rounded-xl outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500/20 placeholder-slate-400 transition-all">
                </div>

                <div class="space-y-1">
                    <label class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider block">Permanent Residential Address</label>
                    <textarea name="address" rows="3" placeholder="Brgy. Pahina Central, Cebu City" class="w-full px-3 py-2 text-xs font-medium border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 rounded-xl outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500/20 placeholder-slate-400 transition-all resize-none"></textarea>
                </div>
            </div>

            <div class="pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-end gap-2 mt-3">
                <button type="button" class="modal-close px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 text-xs font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors cursor-pointer">Cancel</button>
                <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs px-4 py-2 rounded-xl shadow-xs shadow-emerald-600/10 transition-all cursor-pointer">Register Member</button>
            </div>
        </form>
    </div>
</div>

<!-- DRAWER MODAL: EDIT MEMBER -->
<div id="modal-edit-member" class="fixed inset-0 z-50 hidden">
    <div class="fixed inset-0 bg-slate-950/40 dark:bg-slate-950/60 backdrop-blur-md opacity-0 transition-opacity duration-300 pointer-events-none modal-overlay"></div>
    
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-2xl overflow-hidden h-[calc(100vh-2rem)] w-[calc(100vw-2rem)] sm:w-full sm:max-w-lg fixed right-4 top-4 bottom-4 z-50 transform translate-x-[calc(100%+2rem)] transition-transform duration-300 modal-container p-5 sm:p-6 flex flex-col">
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
            <div>
                <h3 class="text-base font-bold text-slate-900 dark:text-slate-100 flex items-center gap-2">
                    <i class="fa-solid fa-pen-to-square text-emerald-600 text-sm"></i>
                    <span>Edit Member Details</span>
                </h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Update cooperative borrower/investor profile</p>
            </div>
            <button class="modal-close p-1.5 rounded-xl text-slate-400 hover:text-slate-700 dark:text-slate-500 dark:hover:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors cursor-pointer">
                <i class="fa-solid fa-xmark text-base"></i>
            </button>
        </div>

        <form id="form-edit-member" method="POST" class="flex-1 flex flex-col overflow-hidden mt-4">
            @csrf
            @method('PUT')
            
            <div class="flex-1 overflow-y-auto pr-1 space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div class="space-y-1">
                        <label class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider block">Full Name</label>
                        <input type="text" name="name" id="edit-member-name" required placeholder="John Doe" class="w-full px-3 py-2 text-xs font-semibold border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 rounded-xl outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500/20 placeholder-slate-400 transition-all">
                    </div>
                    <div class="space-y-1">
                        <label class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider block">Company ID</label>
                        <input type="text" name="company_id" id="edit-member-company_id" required placeholder="20241001" class="w-full px-3 py-2 text-xs font-semibold border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 rounded-xl outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500/20 placeholder-slate-400 transition-all">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div class="space-y-1">
                        <label class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider block">Email Address</label>
                        <input type="email" name="email" id="edit-member-email" required placeholder="john@example.com" class="w-full px-3 py-2 text-xs font-semibold border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 rounded-xl outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500/20 placeholder-slate-400 transition-all">
                    </div>
                    <div class="space-y-1">
                        <label class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider block">Contact Number</label>
                        <input type="text" name="contact_number" id="edit-member-contact_number" placeholder="09xxxxxxxxxx" class="w-full px-3 py-2 text-xs font-semibold border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 rounded-xl outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500/20 placeholder-slate-400 transition-all">
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider flex items-center justify-between">
                        <span>Reset Password</span>
                        <span class="text-[9px] text-slate-400 normal-case">(Leave blank to keep current)</span>
                    </label>
                    <input type="password" name="password" placeholder="••••••••" class="w-full px-3 py-2 text-xs font-semibold border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 rounded-xl outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500/20 placeholder-slate-400 transition-all">
                </div>

                <div class="space-y-1">
                    <label class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider block">Permanent Residential Address</label>
                    <textarea name="address" id="edit-member-address" rows="3" placeholder="Brgy. Pahina Central, Cebu City" class="w-full px-3 py-2 text-xs font-medium border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 rounded-xl outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500/20 placeholder-slate-400 transition-all resize-none"></textarea>
                </div>
            </div>

            <div class="pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-end gap-2 mt-3">
                <button type="button" class="modal-close px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 text-xs font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors cursor-pointer">Cancel</button>
                <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs px-4 py-2 rounded-xl shadow-xs shadow-emerald-600/10 transition-all cursor-pointer">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL: CONFIRM DELETE -->
<div id="modal-delete-member" class="fixed inset-0 z-50 overflow-y-auto hidden flex items-center justify-center p-4">
    <div class="fixed inset-0 bg-slate-950/50 backdrop-blur-sm transition-opacity modal-overlay"></div>
    
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-2xl overflow-hidden w-full max-w-md relative z-10 p-5 sm:p-6 space-y-4 transform scale-95 opacity-0 transition-all duration-300 modal-container">
        <div class="flex items-center gap-3 text-rose-600">
            <div class="w-10 h-10 rounded-xl bg-rose-50 dark:bg-rose-950/20 border border-rose-100 dark:border-rose-900/30 flex items-center justify-center flex-shrink-0 text-base">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <div>
                <h3 class="text-base font-bold text-slate-900 dark:text-slate-100">Remove Member</h3>
                <p class="text-[10px] text-rose-500 font-extrabold uppercase tracking-wider">Permanent Action</p>
            </div>
        </div>

        <div class="text-xs text-slate-600 dark:text-slate-300 font-medium leading-relaxed">
            Are you sure you want to remove member account <span id="delete-member-name" class="font-extrabold text-slate-900 dark:text-slate-100"></span>? All associated profile records will be removed.
        </div>

        <form id="form-delete-member" method="POST" class="pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-end gap-2">
            @csrf
            @method('DELETE')
            
            <button type="button" class="modal-close px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 text-xs font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors cursor-pointer">Cancel</button>
            <button type="submit" class="bg-rose-600 hover:bg-rose-700 text-white font-semibold text-xs px-4 py-2 rounded-xl shadow-xs transition-all cursor-pointer">Confirm Removal</button>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function () {
        
        function openModal(modalId) {
            const modal = document.getElementById(modalId);
            if (!modal) return;
            const overlay = modal.querySelector(".modal-overlay");
            const container = modal.querySelector(".modal-container");
            
            modal.classList.remove("hidden");
            
            setTimeout(() => {
                if (overlay) {
                    overlay.classList.remove("opacity-0", "pointer-events-none");
                    overlay.classList.add("opacity-100", "pointer-events-auto");
                }
                
                if (container) {
                    if (modalId === "modal-add-member" || modalId === "modal-edit-member") {
                        container.classList.remove("translate-x-[calc(100%+2rem)]");
                        container.classList.add("translate-x-0");
                    } else {
                        container.classList.remove("scale-95", "opacity-0");
                        container.classList.add("scale-100", "opacity-100");
                    }
                }
            }, 30);
        }

        function closeModal(modalId) {
            const modal = document.getElementById(modalId);
            if (!modal) return;
            const overlay = modal.querySelector(".modal-overlay");
            const container = modal.querySelector(".modal-container");
            
            if (overlay) {
                overlay.classList.add("opacity-0", "pointer-events-none");
                overlay.classList.remove("opacity-100", "pointer-events-auto");
            }
            
            if (container) {
                if (modalId === "modal-add-member" || modalId === "modal-edit-member") {
                    container.classList.add("translate-x-[calc(100%+2rem)]");
                    container.classList.remove("translate-x-0");
                } else {
                    container.classList.add("scale-95", "opacity-0");
                    container.classList.remove("scale-100", "opacity-100");
                }
            }
            
            setTimeout(() => {
                modal.classList.add("hidden");
            }, 250);
        }

        document.querySelectorAll(".modal-close, .modal-overlay").forEach(btn => {
            btn.addEventListener("click", function() {
                const modal = this.closest('[id^="modal-"]');
                if (modal) closeModal(modal.id);
            });
        });

        document.addEventListener("keydown", function(e) {
            if (e.key === "Escape") {
                const openModalEl = document.querySelector('[id^="modal-"]:not(.hidden)');
                if (openModalEl) closeModal(openModalEl.id);
            }
        });

        const btnAddMember = document.getElementById("btn-add-member");
        if (btnAddMember) {
            btnAddMember.addEventListener("click", () => openModal("modal-add-member"));
        }

        document.querySelectorAll(".btn-edit-member").forEach(btn => {
            btn.addEventListener("click", function() {
                const id = this.getAttribute("data-id");
                const name = this.getAttribute("data-name");
                const email = this.getAttribute("data-email");
                const companyId = this.getAttribute("data-company_id");
                const contact = this.getAttribute("data-contact_number");
                const address = this.getAttribute("data-address");

                document.getElementById("edit-member-name").value = name || '';
                document.getElementById("edit-member-email").value = email || '';
                document.getElementById("edit-member-company_id").value = companyId || '';
                document.getElementById("edit-member-contact_number").value = contact || '';
                document.getElementById("edit-member-address").value = address || '';

                const editForm = document.getElementById("form-edit-member");
                editForm.action = `/admin/members/${id}`;

                openModal("modal-edit-member");
            });
        });

        document.querySelectorAll(".btn-delete-member").forEach(btn => {
            btn.addEventListener("click", function() {
                const id = this.getAttribute("data-id");
                const name = this.getAttribute("data-name");

                document.getElementById("delete-member-name").textContent = name;
                
                const form = document.getElementById("form-delete-member");
                form.action = `/admin/members/${id}`;

                openModal("modal-delete-member");
            });
        });

    });
</script>
@endpush
