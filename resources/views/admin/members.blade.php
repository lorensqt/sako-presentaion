@extends('layouts.admin')

@section('title', 'Members Directory - Sako Cooperative')

@section('header')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h1 class="text-2xl font-extrabold text-slate-900 dark:text-slate-100 tracking-tight serif-font">Members Directory</h1>
        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 font-semibold">List, search, filter, and manage privileges and profile information for all registered accounts.</p>
    </div>
    <div>
        <button id="btn-add-member" class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs px-4 py-2.5 rounded-xl shadow-md shadow-emerald-600/10 hover:shadow-emerald-600/20 hover:-translate-y-0.5 transition-all duration-200">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
            Add New Member
        </button>
    </div>
</div>
@endsection

@section('content')
<div class="space-y-6 animate-fade-in">

    <!-- Search & Filters Panel -->
    <div class="bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700/80 p-4 sm:p-5 rounded-3xl shadow-sm flex flex-col md:flex-row items-center justify-between gap-4">
        <form action="{{ route('admin.members') }}" method="GET" class="w-full md:max-w-md relative">
            <input type="text" name="search" value="{{ $search }}" placeholder="Search members by name, email, company ID or role..." class="w-full pl-10 pr-4 py-2.5 text-xs font-medium border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 rounded-xl focus:border-emerald-500 dark:focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 dark:focus:ring-emerald-500/10 placeholder-slate-400 dark:placeholder-slate-500 transition-all outline-none">
            <svg class="absolute left-3.5 top-3.5 w-4 h-4 text-slate-400 dark:text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            @if($search)
                <a href="{{ route('admin.members') }}" class="absolute right-3 top-3.5 text-xs font-bold text-slate-400 hover:text-slate-600 dark:text-slate-500 dark:hover:text-slate-300 transition-colors">Reset</a>
            @endif
        </form>
    </div>

    <!-- Members Table Ledger -->
    <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-100 dark:border-slate-700/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-900/50 border-b border-slate-100 dark:border-slate-700/80 text-[10px] font-extrabold text-slate-400 dark:text-slate-500 uppercase tracking-widest">
                        <th class="px-6 py-4.5">Account Member</th>
                        <th class="px-6 py-4.5">Company ID</th>
                        <th class="px-6 py-4.5">Contact Number</th>
                        <th class="px-6 py-4.5">Privilege Role</th>
                        <th class="px-6 py-4.5">Approval Groups</th>
                        <th class="px-6 py-4.5">Permanent Address</th>
                        <th class="px-6 py-4.5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 dark:divide-slate-700/50 text-xs font-medium text-slate-700 dark:text-slate-300">
                    @forelse($users as $user)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-900/30 transition-colors">
                            <td class="px-6 py-4 flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full bg-slate-100 dark:bg-slate-900 flex items-center justify-center font-bold text-slate-600 dark:text-slate-300 text-xs border border-slate-200/40 dark:border-slate-700/40">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <div>
                                    <h4 class="font-bold text-slate-950 dark:text-slate-100">{{ $user->name }}</h4>
                                    <p class="text-[10px] text-slate-400 dark:text-slate-500 font-semibold">{{ $user->email }}</p>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-bold font-mono text-slate-600 dark:text-slate-300 bg-slate-50 dark:bg-slate-900 border border-slate-100 dark:border-slate-700 px-2 py-1 rounded-lg">
                                    {{ $user->company_id ?: 'N/A' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 font-semibold text-slate-600 dark:text-slate-300">
                                {{ $user->contact_number ?: 'N/A' }}
                            </td>
                            <td class="px-6 py-4">
                                @if($user->role === 'super_admin')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-violet-50 dark:bg-violet-950/30 text-violet-700 dark:text-violet-400 border border-violet-100/40 dark:border-violet-900/30 uppercase tracking-wider">
                                        Super Admin
                                    </span>
                                @elseif($user->role === 'admin')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-emerald-50 dark:bg-emerald-950/30 text-emerald-700 dark:text-emerald-400 border border-emerald-100/40 dark:border-emerald-900/30 uppercase tracking-wider">
                                        Admin
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-blue-50 dark:bg-blue-950/30 text-blue-700 dark:text-blue-400 border border-blue-100/40 dark:border-blue-900/30 uppercase tracking-wider">
                                        Member
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap gap-1 max-w-[220px]">
                                    @forelse($user->roles as $userRole)
                                        @if($userRole->slug === 'sako_staff')
                                            <span class="px-2 py-0.5 rounded-full text-[9px] font-extrabold bg-sky-50 dark:bg-sky-950/30 text-sky-700 dark:text-sky-400 border border-sky-100 dark:border-sky-900/30 uppercase tracking-wider" title="{{ $userRole->description }}">
                                                Sako Staff
                                            </span>
                                        @elseif($userRole->slug === 'hrmd_staff')
                                            <span class="px-2 py-0.5 rounded-full text-[9px] font-extrabold bg-rose-50 dark:bg-rose-950/30 text-rose-700 dark:text-rose-400 border border-rose-100 dark:border-rose-900/30 uppercase tracking-wider" title="{{ $userRole->description }}">
                                                HRMD Rep
                                            </span>
                                        @elseif($userRole->slug === 'credit_committee')
                                            <span class="px-2 py-0.5 rounded-full text-[9px] font-extrabold bg-indigo-50 dark:bg-indigo-950/30 text-indigo-700 dark:text-indigo-400 border border-indigo-100 dark:border-indigo-900/30 uppercase tracking-wider" title="{{ $userRole->description }}">
                                                Credit Comm
                                            </span>
                                        @elseif($userRole->slug === 'accounting')
                                            <span class="px-2 py-0.5 rounded-full text-[9px] font-extrabold bg-amber-50 dark:bg-amber-950/30 text-amber-700 dark:text-amber-400 border border-amber-100 dark:border-amber-900/30 uppercase tracking-wider" title="{{ $userRole->description }}">
                                                Accounting
                                            </span>
                                        @elseif($userRole->slug === 'releasing_officer')
                                            <span class="px-2 py-0.5 rounded-full text-[9px] font-extrabold bg-teal-50 dark:bg-teal-950/30 text-teal-700 dark:text-teal-400 border border-teal-100 dark:border-teal-900/30 uppercase tracking-wider" title="{{ $userRole->description }}">
                                                Releasing
                                            </span>
                                        @else
                                            <span class="px-2 py-0.5 rounded-full text-[9px] font-extrabold bg-slate-50 dark:bg-slate-900/50 text-slate-600 dark:text-slate-400 border border-slate-100 dark:border-slate-800 uppercase tracking-wider" title="{{ $userRole->description }}">
                                                {{ $userRole->name }}
                                            </span>
                                        @endif
                                    @empty
                                        <span class="text-[10px] text-slate-400 dark:text-slate-500 font-semibold italic">Standard Member</span>
                                    @endforelse
                                </div>
                            </td>
                            <td class="px-6 py-4 max-w-[200px] truncate text-slate-500 dark:text-slate-400" title="{{ $user->address }}">
                                {{ $user->address ?: 'N/A' }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="inline-flex items-center gap-2">
                                    <a href="{{ route('admin.members.pdf', $user->id) }}" target="_blank" class="p-1.5 rounded-lg border border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 hover:border-indigo-300 dark:hover:border-indigo-800 transition-colors"
                                        title="Export Member PDF">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                    </a>

                                    <button class="btn-edit-member p-1.5 rounded-lg border border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400 hover:text-emerald-600 dark:hover:text-emerald-400 hover:border-emerald-300 dark:hover:border-emerald-800 transition-colors"
                                        data-id="{{ $user->id }}"
                                        data-name="{{ $user->name }}"
                                        data-email="{{ $user->email }}"
                                        data-company_id="{{ $user->company_id }}"
                                        data-role="{{ $user->role }}"
                                        data-contact_number="{{ $user->contact_number }}"
                                        data-address="{{ $user->address }}"
                                        data-roles="{{ json_encode($user->roles->pluck('id')->toArray()) }}"
                                        title="Edit Profile Details">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>
                                    
                                    @if(auth()->id() !== $user->id)
                                        <button class="btn-delete-member p-1.5 rounded-lg border border-slate-200 dark:border-slate-700 text-slate-400 dark:text-slate-500 hover:text-rose-600 dark:hover:text-rose-400 hover:border-rose-300 dark:hover:border-rose-800 transition-colors"
                                            data-id="{{ $user->id }}"
                                            data-name="{{ $user->name }}"
                                            title="Remove Account">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    @else
                                        <span class="p-1.5 text-2xs font-extrabold text-slate-400 dark:text-slate-500 uppercase select-none tracking-wider bg-slate-50 dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-lg">Active</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-400 dark:text-slate-500">
                                <svg class="w-10 h-10 mx-auto text-slate-300 dark:text-slate-700 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                No accounts match the current filters or query.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination Links -->
        @if($users->hasPages())
            <div class="px-6 py-4 bg-slate-50 dark:bg-slate-900/50 border-t border-slate-100 dark:border-slate-700/80">
                {{ $users->appends(['search' => $search])->links() }}
            </div>
        @endif
    </div>

</div>

<!-- MODAL: ADD MEMBER -->
<div id="modal-add" class="fixed inset-0 z-50 hidden">
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-slate-950/40 dark:bg-slate-950/60 backdrop-blur-md opacity-0 transition-opacity duration-500 ease-[cubic-bezier(0.16,1,0.3,1)] pointer-events-none modal-overlay"></div>
    
    <!-- Floating Premium Panel -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-2xl shadow-slate-950/15 dark:shadow-slate-950/50 overflow-hidden h-[calc(100vh-2rem)] w-[calc(100vw-2rem)] sm:w-full sm:max-w-md fixed right-4 top-4 bottom-4 z-50 transform translate-x-[calc(100%+2rem)] transition-transform duration-500 ease-[cubic-bezier(0.16,1,0.3,1)] modal-container p-6 sm:p-8 flex flex-col">
        <!-- Header -->
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-4">
            <div>
                <h3 class="text-xl font-extrabold text-slate-900 dark:text-slate-100 tracking-tight serif-font">Add New Account</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 font-medium mt-1">Register cooperative user profile</p>
            </div>
            <button class="modal-close p-1.5 rounded-xl text-slate-400 hover:text-slate-700 dark:text-slate-500 dark:hover:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800/80 transition-all duration-200">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <!-- Form Wrapper -->
        <form action="{{ route('admin.members.store') }}" method="POST" class="flex-1 flex flex-col overflow-hidden mt-6">
            @csrf
            
            <!-- Scrollable content area -->
            <div class="flex-1 overflow-y-auto pr-1 -mr-1 space-y-5">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-slate-700 dark:text-slate-300">Full Name</label>
                        <input type="text" name="name" required placeholder="John Doe" class="w-full px-4 py-2.5 text-xs font-medium border border-slate-200 dark:border-slate-700/80 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 rounded-xl outline-none focus:border-emerald-500 dark:focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 dark:focus:ring-emerald-500/10 placeholder-slate-400 dark:placeholder-slate-600 transition-all duration-200">
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-slate-700 dark:text-slate-300">Company ID</label>
                        <input type="text" name="company_id" required placeholder="20241001" class="w-full px-4 py-2.5 text-xs font-medium border border-slate-200 dark:border-slate-700/80 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 rounded-xl outline-none focus:border-emerald-500 dark:focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 dark:focus:ring-emerald-500/10 placeholder-slate-400 dark:placeholder-slate-600 transition-all duration-200">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-slate-700 dark:text-slate-300">Email Address</label>
                        <input type="email" name="email" required placeholder="john@example.com" class="w-full px-4 py-2.5 text-xs font-medium border border-slate-200 dark:border-slate-700/80 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 rounded-xl outline-none focus:border-emerald-500 dark:focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 dark:focus:ring-emerald-500/10 placeholder-slate-400 dark:placeholder-slate-600 transition-all duration-200">
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-slate-700 dark:text-slate-300">Contact Number</label>
                        <input type="text" name="contact_number" placeholder="09xxxxxxxxxx" class="w-full px-4 py-2.5 text-xs font-medium border border-slate-200 dark:border-slate-700/80 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 rounded-xl outline-none focus:border-emerald-500 dark:focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 dark:focus:ring-emerald-500/10 placeholder-slate-400 dark:placeholder-slate-600 transition-all duration-200">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-slate-700 dark:text-slate-300">Privilege Role</label>
                        <select name="role" required class="w-full px-4 py-2.5 text-xs font-medium border border-slate-200 dark:border-slate-700/80 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 rounded-xl outline-none focus:border-emerald-500 dark:focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 dark:focus:ring-emerald-500/10 transition-all duration-200">
                            <option value="member" selected>Member</option>
                            <option value="admin">Admin</option>
                            <option value="super_admin">Super Admin</option>
                        </select>
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-slate-700 dark:text-slate-300 flex items-center justify-between">
                            <span>Password</span>
                            <span class="text-[10px] text-slate-400 dark:text-slate-500 font-medium normal-case">(Defaults to 'password')</span>
                        </label>
                        <input type="password" name="password" placeholder="••••••••" class="w-full px-4 py-2.5 text-xs font-medium border border-slate-200 dark:border-slate-700/80 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 rounded-xl outline-none focus:border-emerald-500 dark:focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 dark:focus:ring-emerald-500/10 placeholder-slate-400 dark:placeholder-slate-600 transition-all duration-200">
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-xs font-semibold text-slate-700 dark:text-slate-300 block">Approval Groups & Committees</label>
                    <div class="grid grid-cols-2 gap-3 bg-slate-50/60 dark:bg-slate-950/40 border border-slate-100 dark:border-slate-800/80 p-4 rounded-xl">
                        @foreach($roles as $r)
                            <label class="flex items-center gap-3 cursor-pointer select-none py-1 group">
                                <input type="checkbox" name="roles[]" value="{{ $r->id }}" class="rounded text-emerald-600 dark:bg-slate-950 dark:border-slate-700 focus:ring-emerald-500/20 w-4.5 h-4.5 border-slate-200 dark:border-slate-700 transition-colors">
                                <span class="text-xs text-slate-700 dark:text-slate-300 font-medium group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">{{ $r->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-slate-700 dark:text-slate-300">Permanent Residential Address</label>
                    <textarea name="address" rows="2" placeholder="Brgy. Pahina Central, Cebu City" class="w-full px-4 py-2.5 text-xs font-medium border border-slate-200 dark:border-slate-700/80 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 rounded-xl outline-none focus:border-emerald-500 dark:focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 dark:focus:ring-emerald-500/10 placeholder-slate-400 dark:placeholder-slate-600 transition-all duration-200 resize-none"></textarea>
                </div>
            </div>

            <!-- Fixed Footer Action Bar -->
            <div class="pt-4 border-t border-slate-100 dark:border-slate-800/60 flex items-center justify-end gap-3 mt-4">
                <button type="button" class="modal-close px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-xs font-bold text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-800 dark:hover:text-slate-100 transition-colors">Cancel</button>
                <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 dark:bg-emerald-600 dark:hover:bg-emerald-500 text-white font-bold text-xs px-5 py-2.5 rounded-xl shadow-md shadow-emerald-600/15 dark:shadow-emerald-900/25 transition-all">Register Member</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL: EDIT MEMBER -->
<div id="modal-edit" class="fixed inset-0 z-50 hidden">
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-slate-950/40 dark:bg-slate-950/60 backdrop-blur-md opacity-0 transition-opacity duration-500 ease-[cubic-bezier(0.16,1,0.3,1)] pointer-events-none modal-overlay"></div>
    
    <!-- Floating Premium Panel -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-2xl shadow-slate-950/15 dark:shadow-slate-950/50 overflow-hidden h-[calc(100vh-2rem)] w-[calc(100vw-2rem)] sm:w-full sm:max-w-md fixed right-4 top-4 bottom-4 z-50 transform translate-x-[calc(100%+2rem)] transition-transform duration-500 ease-[cubic-bezier(0.16,1,0.3,1)] modal-container p-6 sm:p-8 flex flex-col">
        <!-- Header -->
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-4">
            <div>
                <h3 class="text-xl font-extrabold text-slate-900 dark:text-slate-100 tracking-tight serif-font">Edit Profile Details</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 font-medium mt-1">Modify Database Entity</p>
            </div>
            <button class="modal-close p-1.5 rounded-xl text-slate-400 hover:text-slate-700 dark:text-slate-500 dark:hover:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800/80 transition-all duration-200">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <!-- Form Wrapper -->
        <form id="form-edit" method="POST" class="flex-1 flex flex-col overflow-hidden mt-6">
            @csrf
            @method('PUT')
            
            <!-- Scrollable content area -->
            <div class="flex-1 overflow-y-auto pr-1 -mr-1 space-y-5">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-slate-700 dark:text-slate-300">Full Name</label>
                        <input type="text" name="name" id="edit-name" required placeholder="John Doe" class="w-full px-4 py-2.5 text-xs font-medium border border-slate-200 dark:border-slate-700/80 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 rounded-xl outline-none focus:border-emerald-500 dark:focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 dark:focus:ring-emerald-500/10 placeholder-slate-400 dark:placeholder-slate-600 transition-all duration-200">
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-slate-700 dark:text-slate-300">Company ID</label>
                        <input type="text" name="company_id" id="edit-company_id" required placeholder="20241001" class="w-full px-4 py-2.5 text-xs font-medium border border-slate-200 dark:border-slate-700/80 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 rounded-xl outline-none focus:border-emerald-500 dark:focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 dark:focus:ring-emerald-500/10 placeholder-slate-400 dark:placeholder-slate-600 transition-all duration-200">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-slate-700 dark:text-slate-300">Email Address</label>
                        <input type="email" name="email" id="edit-email" required placeholder="john@example.com" class="w-full px-4 py-2.5 text-xs font-medium border border-slate-200 dark:border-slate-700/80 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 rounded-xl outline-none focus:border-emerald-500 dark:focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 dark:focus:ring-emerald-500/10 placeholder-slate-400 dark:placeholder-slate-600 transition-all duration-200">
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-slate-700 dark:text-slate-300">Contact Number</label>
                        <input type="text" name="contact_number" id="edit-contact_number" placeholder="09xxxxxxxxxx" class="w-full px-4 py-2.5 text-xs font-medium border border-slate-200 dark:border-slate-700/80 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 rounded-xl outline-none focus:border-emerald-500 dark:focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 dark:focus:ring-emerald-500/10 placeholder-slate-400 dark:placeholder-slate-600 transition-all duration-200">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-slate-700 dark:text-slate-300">Privilege Role</label>
                        <select name="role" id="edit-role" required class="w-full px-4 py-2.5 text-xs font-medium border border-slate-200 dark:border-slate-700/80 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 rounded-xl outline-none focus:border-emerald-500 dark:focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 dark:focus:ring-emerald-500/10 transition-all duration-200">
                            <option value="member">Member</option>
                            <option value="admin">Admin</option>
                            <option value="super_admin">Super Admin</option>
                        </select>
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-slate-700 dark:text-slate-300 flex items-center justify-between">
                            <span>Reset Password</span>
                            <span class="text-[10px] text-slate-400 dark:text-slate-500 font-medium normal-case">(Leave blank to keep current)</span>
                        </label>
                        <input type="password" name="password" placeholder="••••••••" class="w-full px-4 py-2.5 text-xs font-medium border border-slate-200 dark:border-slate-700/80 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 rounded-xl outline-none focus:border-emerald-500 dark:focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 dark:focus:ring-emerald-500/10 placeholder-slate-400 dark:placeholder-slate-600 transition-all duration-200">
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-xs font-semibold text-slate-700 dark:text-slate-300 block">Approval Groups & Committees</label>
                    <div class="grid grid-cols-2 gap-3 bg-slate-50/60 dark:bg-slate-950/40 border border-slate-100 dark:border-slate-800/80 p-4 rounded-xl">
                        @foreach($roles as $r)
                            <label class="flex items-center gap-3 cursor-pointer select-none py-1 group">
                                <input type="checkbox" name="roles[]" value="{{ $r->id }}" class="rounded text-emerald-600 dark:bg-slate-950 dark:border-slate-700 focus:ring-emerald-500/20 w-4.5 h-4.5 border-slate-200 dark:border-slate-700 transition-colors">
                                <span class="text-xs text-slate-700 dark:text-slate-300 font-medium group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">{{ $r->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-slate-700 dark:text-slate-300">Permanent Residential Address</label>
                    <textarea name="address" id="edit-address" rows="2" placeholder="Brgy. Pahina Central, Cebu City" class="w-full px-4 py-2.5 text-xs font-medium border border-slate-200 dark:border-slate-700/80 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 rounded-xl outline-none focus:border-emerald-500 dark:focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 dark:focus:ring-emerald-500/10 placeholder-slate-400 dark:placeholder-slate-600 transition-all duration-200 resize-none"></textarea>
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

<!-- MODAL: CONFIRM DELETE -->
<div id="modal-delete" class="fixed inset-0 z-50 overflow-y-auto hidden flex items-center justify-center p-4">
    <div class="fixed inset-0 bg-slate-950/50 backdrop-blur-sm transition-opacity modal-overlay"></div>
    
    <div class="bg-white dark:bg-slate-900 rounded-[2rem] border border-slate-100 dark:border-slate-800 shadow-2xl overflow-hidden w-full max-w-md relative z-10 p-6 sm:p-8 space-y-6 transform scale-95 opacity-0 transition-all duration-300 modal-container">
        <div class="flex items-center gap-4 text-rose-600">
            <div class="w-12 h-12 rounded-2xl bg-rose-50 dark:bg-rose-950/20 border border-rose-100 dark:border-rose-900/30 flex items-center justify-center flex-shrink-0 shadow-sm shadow-rose-600/5">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </div>
            <div>
                <h3 class="text-lg font-bold text-slate-900 dark:text-slate-100 serif-font">Remove Account</h3>
                <p class="text-2xs text-rose-500 font-extrabold uppercase tracking-wider mt-0.5">Critical Action Required</p>
            </div>
        </div>

        <div class="text-xs text-slate-500 dark:text-slate-400 font-medium leading-relaxed">
            Are you absolutely sure you want to remove <span id="delete-member-name" class="font-extrabold text-slate-900 dark:text-slate-100"></span> from the cooperative database? This operation is permanent and all associated credentials will be terminated immediately.
        </div>

        <form id="form-delete" method="POST" class="pt-4 border-t border-slate-100 dark:border-slate-800/60 flex items-center justify-end gap-2.5">
            @csrf
            @method('DELETE')
            
            <button type="button" class="modal-close px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-xs font-bold text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-800 dark:hover:text-slate-100 transition-colors">Cancel</button>
            <button type="submit" class="bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs px-5 py-2.5 rounded-xl shadow-md shadow-rose-600/15 transition-all">Confirm Removal</button>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function () {
        
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
                    if (modalId === "modal-add" || modalId === "modal-edit") {
                        // Slide drawer from the right
                        container.classList.remove("translate-x-[calc(100%+2rem)]");
                        container.classList.add("translate-x-0");
                    } else {
                        // Zoom in standard centered modal
                        container.classList.remove("scale-95", "opacity-0");
                        container.classList.add("scale-100", "opacity-100");
                    }
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
                if (modalId === "modal-add" || modalId === "modal-edit") {
                    // Slide drawer back to the right
                    container.classList.add("translate-x-[calc(100%+2rem)]");
                    container.classList.remove("translate-x-0");
                } else {
                    // Zoom out standard centered modal
                    container.classList.add("scale-95", "opacity-0");
                    container.classList.remove("scale-100", "opacity-100");
                }
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

        // Trigger: Add Member
        const btnAdd = document.getElementById("btn-add-member");
        if(btnAdd) {
            btnAdd.addEventListener("click", () => openModal("modal-add"));
        }

        // Trigger: Edit Member
        document.querySelectorAll(".btn-edit-member").forEach(btn => {
            btn.addEventListener("click", function() {
                const id = this.getAttribute("data-id");
                const name = this.getAttribute("data-name");
                const email = this.getAttribute("data-email");
                const companyId = this.getAttribute("data-company_id");
                const role = this.getAttribute("data-role");
                const contact = this.getAttribute("data-contact_number");
                const address = this.getAttribute("data-address");
                const roles = JSON.parse(this.getAttribute("data-roles") || "[]");

                // Populate Fields
                document.getElementById("edit-name").value = name || '';
                document.getElementById("edit-email").value = email || '';
                document.getElementById("edit-company_id").value = companyId || '';
                document.getElementById("edit-role").value = role || 'member';
                document.getElementById("edit-contact_number").value = contact || '';
                document.getElementById("edit-address").value = address || '';

                // Check/Uncheck dynamic roles/groups in edit modal
                const editForm = document.getElementById("form-edit");
                editForm.querySelectorAll("input[name='roles[]']").forEach(cb => {
                    cb.checked = roles.includes(parseInt(cb.value));
                });

                // Dynamic Action URL Update
                const form = document.getElementById("form-edit");
                form.action = `/admin/members/${id}`;

                openModal("modal-edit");
            });
        });

        // Trigger: Delete Member
        document.querySelectorAll(".btn-delete-member").forEach(btn => {
            btn.addEventListener("click", function() {
                const id = this.getAttribute("data-id");
                const name = this.getAttribute("data-name");

                document.getElementById("delete-member-name").textContent = name;
                
                const form = document.getElementById("form-delete");
                form.action = `/admin/members/${id}`;

                openModal("modal-delete");
            });
        });

    });
</script>
@endpush
