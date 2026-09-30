@extends('layouts.admin')

@section('title', 'System Administrators & Staff - Sako Cooperative')

@push('styles')
<style>
    .btn-edit-admin, .btn-delete-admin, #btn-add-admin {
        cursor: pointer !important;
    }
</style>
@endpush

@section('header')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 dark:text-slate-100 tracking-tight flex items-center gap-2.5">
            <i class="fa-solid fa-user-shield text-emerald-600 dark:text-emerald-400 text-xl"></i>
            <span>System Administrators &amp; Staff Console</span>
        </h1>
        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 font-medium">
            Super Administrator exclusive console to configure internal system operators, credentials, e-signatures, and granular admin tab permissions.
        </p>
    </div>

    <button id="btn-add-admin" class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs px-4 py-2.5 rounded-xl shadow-xs shadow-emerald-600/10 hover:shadow-emerald-600/20 hover:-translate-y-0.5 transition-all duration-200 cursor-pointer self-start sm:self-auto">
        <i class="fa-solid fa-user-shield text-xs"></i>
        <span>Register Administrator</span>
    </button>
</div>
@endsection

@section('content')
<div class="space-y-5 sm:space-y-6 animate-fade-in">

    <!-- Search & Metrics Panel -->
    <div class="bg-white dark:bg-slate-800 border border-slate-200/80 dark:border-slate-700/80 p-4 sm:p-5 rounded-2xl shadow-xs flex flex-col md:flex-row items-center justify-between gap-4">
        <form action="{{ route('admin.administrators') }}" method="GET" class="w-full md:max-w-md relative">
            <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-3 text-slate-400 dark:text-slate-500 text-xs"></i>
            <input type="text" name="search" value="{{ $search }}" 
                placeholder="Search administrators by name, login ID, email..." 
                class="w-full pl-9 pr-14 py-2 text-xs font-medium border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 rounded-xl focus:border-emerald-500 dark:focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500/20 placeholder-slate-400 dark:placeholder-slate-500 transition-all outline-none">
            @if($search)
                <a href="{{ route('admin.administrators') }}" class="absolute right-3 top-2 text-xs font-bold text-slate-400 hover:text-slate-600 dark:text-slate-500 dark:hover:text-slate-300 transition-colors">Reset</a>
            @endif
        </form>

        <div class="flex items-center gap-2">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-100 dark:bg-slate-700/60 text-slate-700 dark:text-slate-300 text-xs font-semibold border border-slate-200/80 dark:border-slate-600">
                <i class="fa-solid fa-user-shield text-slate-500 text-xs"></i>
                <span>Showing {{ $users->firstItem() ?? 0 }}-{{ $users->lastItem() ?? 0 }} of {{ number_format($users->total()) }} System Administrators</span>
            </span>
        </div>
    </div>

    <!-- ADMINISTRATORS LEDGER TABLE -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-100/90 dark:bg-slate-800/95 border-b-2 border-slate-200/90 dark:border-slate-700 text-xs font-bold text-slate-800 dark:text-slate-100 uppercase tracking-wider">
                        <th class="px-5 py-3.5">
                            <span class="inline-flex items-center gap-1.5">
                                <i class="fa-solid fa-user-shield text-slate-500 dark:text-slate-400 text-[11px]"></i>
                                Administrator Name
                            </span>
                        </th>
                        <th class="px-5 py-3.5">
                            <span class="inline-flex items-center gap-1.5">
                                <i class="fa-solid fa-id-badge text-slate-500 dark:text-slate-400 text-[11px]"></i>
                                Login Identifier
                            </span>
                        </th>
                        <th class="px-5 py-3.5">
                            <span class="inline-flex items-center gap-1.5">
                                <i class="fa-solid fa-file-signature text-slate-500 dark:text-slate-400 text-[11px]"></i>
                                E-Signature
                            </span>
                        </th>
                        <th class="px-5 py-3.5">
                            <span class="inline-flex items-center gap-1.5">
                                <i class="fa-solid fa-shield-halved text-slate-500 dark:text-slate-400 text-[11px]"></i>
                                Privilege Tier
                            </span>
                        </th>
                        <th class="px-5 py-3.5">
                            <span class="inline-flex items-center gap-1.5">
                                <i class="fa-solid fa-lock-open text-slate-500 dark:text-slate-400 text-[11px]"></i>
                                Accessible Admin Modules
                            </span>
                        </th>
                        <th class="px-5 py-3.5">
                            <span class="inline-flex items-center gap-1.5">
                                <i class="fa-solid fa-users-gear text-slate-500 dark:text-slate-400 text-[11px]"></i>
                                Approval Committees
                            </span>
                        </th>
                        <th class="px-5 py-3.5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700/60 text-xs font-medium text-slate-700 dark:text-slate-300">
                    @forelse($users as $user)
                        <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-700/30 transition-colors">
                            <!-- Admin Profile -->
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl {{ $user->role === 'super_admin' ? 'bg-violet-100 text-violet-700 dark:bg-violet-950/50 dark:text-violet-300 border border-violet-200 dark:border-violet-800' : 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 border border-emerald-200/60 dark:border-emerald-800/40' }} flex items-center justify-center font-bold text-xs flex-shrink-0 shadow-xs">
                                        @if($user->role === 'super_admin')
                                            <i class="fa-solid fa-crown text-[11px]"></i>
                                        @else
                                            <i class="fa-solid fa-shield-halved text-[11px]"></i>
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <h4 class="font-bold text-slate-900 dark:text-white leading-tight truncate flex items-center gap-1.5">
                                            <span>{{ $user->name }}</span>
                                            @if(auth()->id() === $user->id)
                                                <span class="text-[9px] font-black uppercase tracking-wider text-emerald-600 bg-emerald-50 dark:bg-emerald-950/50 px-1.5 py-0.5 rounded border border-emerald-200/60 dark:border-emerald-800/40">You</span>
                                            @endif
                                        </h4>
                                        <p class="text-[11px] text-slate-400 dark:text-slate-500 font-medium truncate mt-0.5">{{ $user->email ?: 'Internal Account' }}</p>
                                    </div>
                                </div>
                            </td>

                            <!-- Company ID / Login Identifier -->
                            <td class="px-5 py-3.5">
                                <span class="font-mono font-bold text-slate-700 dark:text-slate-200 bg-slate-100 dark:bg-slate-900 border border-slate-200/80 dark:border-slate-700 px-2 py-0.5 rounded-lg text-xs">
                                    {{ $user->company_id ?: 'N/A' }}
                                </span>
                            </td>

                            <!-- E-Signature Preview -->
                            <td class="px-5 py-3.5">
                                @if($user->signature && file_exists(storage_path('app/public/' . $user->signature)))
                                    <div class="h-9 w-24 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700/80 rounded-lg p-1 flex items-center justify-center shadow-2xs">
                                        <img src="{{ asset('storage/' . $user->signature) }}" alt="E-Sign" class="max-h-full max-w-full object-contain">
                                    </div>
                                @else
                                    <span class="inline-flex items-center gap-1 text-[10px] text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-950/30 border border-amber-200/60 dark:border-amber-800/40 px-2 py-0.5 rounded font-bold uppercase tracking-wider">
                                        <i class="fa-solid fa-circle-exclamation text-[9px]"></i>
                                        <span>Missing</span>
                                    </span>
                                @endif
                            </td>

                            <!-- Tier Role -->
                            <td class="px-5 py-3.5">
                                @if($user->role === 'super_admin')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-violet-50 dark:bg-violet-950/30 text-violet-700 dark:text-violet-300 border border-violet-200/60 dark:border-violet-800/40 uppercase tracking-wider">
                                        <i class="fa-solid fa-crown text-[10px]"></i>
                                        Super Admin
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-emerald-50 dark:bg-emerald-950/30 text-emerald-700 dark:text-emerald-300 border border-emerald-200/60 dark:border-emerald-800/40 uppercase tracking-wider">
                                        <i class="fa-solid fa-shield text-[10px]"></i>
                                        Staff Admin
                                    </span>
                                @endif
                            </td>

                            <!-- Accessible Admin Modules -->
                            <td class="px-5 py-3.5">
                                @if($user->role === 'super_admin')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[10px] font-extrabold bg-violet-50 dark:bg-violet-950/40 text-violet-700 dark:text-violet-300 border border-violet-200 dark:border-violet-800/50">
                                        <i class="fa-solid fa-star text-amber-500 text-[10px]"></i>
                                        <span>Full Access (All Modules)</span>
                                    </span>
                                @else
                                    @php
                                        $perms = $user->admin_permissions;
                                    @endphp
                                    @if($perms === null)
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[10px] font-bold bg-emerald-50 dark:bg-emerald-950/30 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/40">
                                            <i class="fa-solid fa-unlock text-[10px]"></i>
                                            <span>Legacy Unrestricted Access</span>
                                        </span>
                                    @elseif(empty($perms))
                                        <span class="text-xs text-rose-500 font-semibold italic flex items-center gap-1">
                                            <i class="fa-solid fa-lock text-[10px]"></i>
                                            <span>No Admin Modules Granted</span>
                                        </span>
                                    @else
                                        <div class="flex flex-wrap gap-1 max-w-[300px]">
                                            @foreach($perms as $p)
                                                @if(isset($availablePermissions[$p]))
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold bg-slate-100 dark:bg-slate-900 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700/80" title="{{ $availablePermissions[$p]['description'] }}">
                                                        <i class="{{ $availablePermissions[$p]['icon'] }} text-[9px] text-emerald-600 dark:text-emerald-400"></i>
                                                        <span>{{ $availablePermissions[$p]['label'] }}</span>
                                                    </span>
                                                @endif
                                            @endforeach
                                        </div>
                                    @endif
                                @endif
                            </td>

                            <!-- Workflow Committees -->
                            <td class="px-5 py-3.5">
                                <div class="flex flex-wrap gap-1 max-w-[180px]">
                                    @forelse($user->roles as $userRole)
                                        <span class="px-2 py-0.5 rounded-full text-[9px] font-extrabold bg-slate-100 dark:bg-slate-900 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-800 uppercase tracking-wider" title="{{ $userRole->description }}">
                                            {{ $userRole->name }}
                                        </span>
                                    @empty
                                        <span class="text-[11px] text-slate-400 dark:text-slate-500 italic">None</span>
                                    @endforelse
                                </div>
                            </td>

                            <!-- Actions -->
                            <td class="px-5 py-3.5 text-right">
                                <div class="inline-flex items-center gap-1.5">
                                    <button class="btn-edit-admin p-1.5 rounded-lg border border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400 hover:text-emerald-600 dark:hover:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-950/30 transition-colors cursor-pointer"
                                        data-id="{{ $user->id }}"
                                        data-name="{{ $user->name }}"
                                        data-company_id="{{ $user->company_id }}"
                                        data-email="{{ $user->email }}"
                                        data-role="{{ $user->role }}"
                                        data-signature="{{ $user->signature ? asset('storage/' . $user->signature) : '' }}"
                                        data-roles="{{ json_encode($user->roles->pluck('id')->toArray()) }}"
                                        data-admin_permissions="{{ json_encode($user->admin_permissions ?? []) }}"
                                        title="Edit Permissions & Details">
                                        <i class="fa-solid fa-user-pen text-xs"></i>
                                    </button>

                                    @if(auth()->id() !== $user->id)
                                        <button class="btn-delete-admin p-1.5 rounded-lg border border-slate-200 dark:border-slate-700 text-slate-400 dark:text-slate-500 hover:text-rose-600 dark:hover:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/30 transition-colors cursor-pointer"
                                            data-id="{{ $user->id }}"
                                            data-name="{{ $user->name }}"
                                            title="Remove Administrator">
                                            <i class="fa-solid fa-trash-can text-xs"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-12 text-center text-slate-400 dark:text-slate-500 text-xs">
                                <div class="w-10 h-10 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 flex items-center justify-center mx-auto text-slate-400 mb-2">
                                    <i class="fa-solid fa-user-shield text-sm"></i>
                                </div>
                                No administrators found matching your query.
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

<!-- DRAWER MODAL: REGISTER ADMINISTRATOR -->
<div id="modal-add-admin" class="fixed inset-0 z-50 hidden">
    <div class="fixed inset-0 bg-slate-950/40 dark:bg-slate-950/60 backdrop-blur-md opacity-0 transition-opacity duration-300 pointer-events-none modal-overlay"></div>
    
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-2xl overflow-hidden h-[calc(100vh-2rem)] w-[calc(100vw-2rem)] sm:w-full sm:max-w-lg fixed right-4 top-4 bottom-4 z-50 transform translate-x-[calc(100%+2rem)] transition-transform duration-300 modal-container p-5 sm:p-6 flex flex-col">
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
            <div>
                <h3 class="text-base font-bold text-slate-900 dark:text-slate-100 flex items-center gap-2">
                    <i class="fa-solid fa-user-shield text-emerald-600 text-sm"></i>
                    <span>Register New Administrator</span>
                </h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Create internal operator account with custom module access and e-signature</p>
            </div>
            <button class="modal-close p-1.5 rounded-xl text-slate-400 hover:text-slate-700 dark:text-slate-500 dark:hover:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors cursor-pointer">
                <i class="fa-solid fa-xmark text-base"></i>
            </button>
        </div>

        <form action="{{ route('admin.administrators.store') }}" method="POST" enctype="multipart/form-data" class="flex-1 flex flex-col overflow-hidden mt-4">
            @csrf
            
            <div class="flex-1 overflow-y-auto pr-1 space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div class="space-y-1">
                        <label class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider block">Operator Full Name</label>
                        <input type="text" name="name" required placeholder="Maria Santos" class="w-full px-3 py-2 text-xs font-semibold border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 rounded-xl outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500/20 placeholder-slate-400 transition-all">
                    </div>
                    <div class="space-y-1">
                        <label class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider block">Login Username / ID</label>
                        <input type="text" name="company_id" required placeholder="admin_loans_01" class="w-full px-3 py-2 text-xs font-semibold border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 rounded-xl outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500/20 placeholder-slate-400 transition-all">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div class="space-y-1">
                        <label class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider flex items-center justify-between">
                            <span>Internal Email</span>
                            <span class="text-[9px] text-slate-400 normal-case">(Optional)</span>
                        </label>
                        <input type="email" name="email" placeholder="staff@coop.internal" class="w-full px-3 py-2 text-xs font-semibold border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 rounded-xl outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500/20 placeholder-slate-400 transition-all">
                    </div>
                    <div class="space-y-1">
                        <label class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider flex items-center justify-between">
                            <span>Password</span>
                            <span class="text-[9px] text-slate-400 normal-case">(Defaults to 'password')</span>
                        </label>
                        <input type="password" name="password" placeholder="••••••••" class="w-full px-3 py-2 text-xs font-semibold border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 rounded-xl outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500/20 placeholder-slate-400 transition-all">
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider block">Privilege Tier</label>
                    <select name="role" id="add-admin-role" required class="w-full px-3 py-2 text-xs font-semibold border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 rounded-xl outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500/20 transition-all cursor-pointer">
                        <option value="admin" selected>Staff Admin (Granular Module Access)</option>
                        <option value="super_admin">Super Admin (Unrestricted Full Access)</option>
                    </select>
                </div>

                <!-- E-Signature File Upload -->
                <div class="space-y-1.5 border-t border-slate-100 dark:border-slate-800 pt-3">
                    <div class="flex items-center justify-between">
                        <label class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider flex items-center gap-1.5">
                            <i class="fa-solid fa-file-signature text-emerald-600 text-xs"></i>
                            <span>Official E-Signature</span>
                        </label>
                        <span class="text-[9px] text-slate-400 font-semibold">(PNG / SVG transparent)</span>
                    </div>
                    <input type="file" name="signature" accept="image/png, image/jpeg, image/jpg, image/svg+xml" class="w-full px-3 py-1.5 text-xs font-semibold border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 rounded-xl outline-none focus:border-emerald-500 file:mr-3 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-[10px] file:font-bold file:bg-emerald-50 file:text-emerald-700 dark:file:bg-emerald-950/40 dark:file:text-emerald-300 hover:file:bg-emerald-100 cursor-pointer">
                </div>

                <!-- Admin Granular Page Access Permissions Grid -->
                <div id="add-admin-permissions-group" class="space-y-2 border-t border-slate-100 dark:border-slate-800 pt-3">
                    <div class="flex items-center justify-between">
                        <label class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider flex items-center gap-1.5">
                            <i class="fa-solid fa-lock-open text-emerald-600 text-xs"></i>
                            <span>Accessible Admin Pages</span>
                        </label>
                        <div class="flex items-center gap-2">
                            <button type="button" class="btn-select-all-perms text-[10px] font-bold text-emerald-600 hover:text-emerald-700 cursor-pointer">Select All</button>
                            <span class="text-slate-300 dark:text-slate-600">|</span>
                            <button type="button" class="btn-clear-all-perms text-[10px] font-bold text-slate-400 hover:text-slate-600 cursor-pointer">Clear</button>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 bg-slate-50 dark:bg-slate-950/40 border border-slate-200/60 dark:border-slate-800 p-3 rounded-xl max-h-56 overflow-y-auto">
                        @foreach($availablePermissions as $permKey => $permMeta)
                            <label class="flex items-start gap-2 p-2 rounded-lg border border-slate-200/70 dark:border-slate-700/60 bg-white dark:bg-slate-900 hover:border-emerald-500 cursor-pointer transition-colors select-none group">
                                <input type="checkbox" name="admin_permissions[]" value="{{ $permKey }}" class="mt-0.5 rounded text-emerald-600 focus:ring-emerald-500 h-4 w-4 border-slate-300 dark:border-slate-700 dark:bg-slate-950 cursor-pointer">
                                <div class="min-w-0">
                                    <p class="text-xs font-bold text-slate-800 dark:text-slate-200 group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors flex items-center gap-1.5">
                                        <i class="{{ $permMeta['icon'] }} text-[10px] text-emerald-600 dark:text-emerald-400"></i>
                                        <span class="truncate">{{ $permMeta['label'] }}</span>
                                    </p>
                                    <p class="text-[9px] text-slate-400 dark:text-slate-500 leading-tight mt-0.5 line-clamp-1">{{ $permMeta['description'] }}</p>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- Super Admin Notice Banner -->
                <div id="add-super-banner" class="hidden p-3 bg-violet-50 dark:bg-violet-950/30 border border-violet-200/80 dark:border-violet-800/40 rounded-xl text-violet-700 dark:text-violet-300 text-xs font-semibold flex items-center gap-2.5">
                    <i class="fa-solid fa-crown text-violet-600 dark:text-violet-400 text-sm"></i>
                    <span>Super Administrators automatically have unrestricted access across all admin pages, settings, and logs.</span>
                </div>

                <!-- Workflow Approval Committees -->
                <div class="space-y-1.5 border-t border-slate-100 dark:border-slate-800 pt-3">
                    <label class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider block">Workflow Approval Committees</label>
                    <div class="grid grid-cols-2 gap-2 bg-slate-50 dark:bg-slate-950/40 border border-slate-200/60 dark:border-slate-800 p-3 rounded-xl">
                        @foreach($roles as $r)
                            <label class="flex items-center gap-2 cursor-pointer select-none py-1 group">
                                <input type="checkbox" name="roles[]" value="{{ $r->id }}" class="rounded text-emerald-600 dark:bg-slate-950 dark:border-slate-700 focus:ring-emerald-500 h-4 w-4 border-slate-300 dark:border-slate-700 transition-colors cursor-pointer">
                                <span class="text-xs text-slate-700 dark:text-slate-300 font-semibold group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">{{ $r->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-end gap-2 mt-3">
                <button type="button" class="modal-close px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 text-xs font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors cursor-pointer">Cancel</button>
                <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs px-4 py-2 rounded-xl shadow-xs shadow-emerald-600/10 transition-all cursor-pointer">Create Administrator</button>
            </div>
        </form>
    </div>
</div>

<!-- DRAWER MODAL: EDIT ADMINISTRATOR -->
<div id="modal-edit-admin" class="fixed inset-0 z-50 hidden">
    <div class="fixed inset-0 bg-slate-950/40 dark:bg-slate-950/60 backdrop-blur-md opacity-0 transition-opacity duration-300 pointer-events-none modal-overlay"></div>
    
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-2xl overflow-hidden h-[calc(100vh-2rem)] w-[calc(100vw-2rem)] sm:w-full sm:max-w-lg fixed right-4 top-4 bottom-4 z-50 transform translate-x-[calc(100%+2rem)] transition-transform duration-300 modal-container p-5 sm:p-6 flex flex-col">
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
            <div>
                <h3 class="text-base font-bold text-slate-900 dark:text-slate-100 flex items-center gap-2">
                    <i class="fa-solid fa-user-pen text-emerald-600 text-sm"></i>
                    <span>Edit Administrator &amp; Permissions</span>
                </h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Update operator credentials, official signature, and tab permissions</p>
            </div>
            <button class="modal-close p-1.5 rounded-xl text-slate-400 hover:text-slate-700 dark:text-slate-500 dark:hover:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors cursor-pointer">
                <i class="fa-solid fa-xmark text-base"></i>
            </button>
        </div>

        <form id="form-edit-admin" method="POST" enctype="multipart/form-data" class="flex-1 flex flex-col overflow-hidden mt-4">
            @csrf
            @method('PUT')
            
            <div class="flex-1 overflow-y-auto pr-1 space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div class="space-y-1">
                        <label class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider block">Operator Full Name</label>
                        <input type="text" name="name" id="edit-admin-name" required placeholder="Maria Santos" class="w-full px-3 py-2 text-xs font-semibold border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 rounded-xl outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500/20 placeholder-slate-400 transition-all">
                    </div>
                    <div class="space-y-1">
                        <label class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider block">Login Username / ID</label>
                        <input type="text" name="company_id" id="edit-admin-company_id" required placeholder="admin_loans_01" class="w-full px-3 py-2 text-xs font-semibold border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 rounded-xl outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500/20 placeholder-slate-400 transition-all">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div class="space-y-1">
                        <label class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider flex items-center justify-between">
                            <span>Internal Email</span>
                            <span class="text-[9px] text-slate-400 normal-case">(Optional)</span>
                        </label>
                        <input type="email" name="email" id="edit-admin-email" placeholder="staff@coop.internal" class="w-full px-3 py-2 text-xs font-semibold border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 rounded-xl outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500/20 placeholder-slate-400 transition-all">
                    </div>
                    <div class="space-y-1">
                        <label class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider flex items-center justify-between">
                            <span>Reset Password</span>
                            <span class="text-[9px] text-slate-400 normal-case">(Leave blank to keep)</span>
                        </label>
                        <input type="password" name="password" placeholder="••••••••" class="w-full px-3 py-2 text-xs font-semibold border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 rounded-xl outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500/20 placeholder-slate-400 transition-all">
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider block">Privilege Tier</label>
                    <select name="role" id="edit-admin-role" required class="w-full px-3 py-2 text-xs font-semibold border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 rounded-xl outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500/20 transition-all cursor-pointer">
                        <option value="admin">Staff Admin (Granular Module Access)</option>
                        <option value="super_admin">Super Admin (Unrestricted Full Access)</option>
                    </select>
                </div>

                <!-- E-Signature Upload & Preview in Edit -->
                <div class="space-y-1.5 border-t border-slate-100 dark:border-slate-800 pt-3">
                    <div class="flex items-center justify-between">
                        <label class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider flex items-center gap-1.5">
                            <i class="fa-solid fa-file-signature text-emerald-600 text-xs"></i>
                            <span>Official E-Signature</span>
                        </label>
                        <span class="text-[9px] text-slate-400 font-semibold">(PNG / SVG transparent)</span>
                    </div>
                    <div id="edit-signature-preview-container" class="hidden items-center gap-3 p-2 bg-slate-50 dark:bg-slate-950/40 border border-slate-200/60 dark:border-slate-800 rounded-xl mb-2">
                        <img id="edit-signature-preview-img" src="" alt="Current Signature" class="h-10 w-auto max-w-[120px] object-contain bg-white dark:bg-slate-900 p-1 rounded-lg border border-slate-200 dark:border-slate-700">
                        <span class="text-[10px] text-slate-400 dark:text-slate-500 font-medium leading-tight">Currently registered signature on file. Upload below to replace.</span>
                    </div>
                    <input type="file" name="signature" accept="image/png, image/jpeg, image/jpg, image/svg+xml" class="w-full px-3 py-1.5 text-xs font-semibold border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 rounded-xl outline-none focus:border-emerald-500 file:mr-3 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-[10px] file:font-bold file:bg-emerald-50 file:text-emerald-700 dark:file:bg-emerald-950/40 dark:file:text-emerald-300 hover:file:bg-emerald-100 cursor-pointer">
                </div>

                <!-- Admin Granular Page Access Permissions Grid -->
                <div id="edit-admin-permissions-group" class="space-y-2 border-t border-slate-100 dark:border-slate-800 pt-3">
                    <div class="flex items-center justify-between">
                        <label class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider flex items-center gap-1.5">
                            <i class="fa-solid fa-lock-open text-emerald-600 text-xs"></i>
                            <span>Accessible Admin Pages</span>
                        </label>
                        <div class="flex items-center gap-2">
                            <button type="button" class="btn-select-all-perms text-[10px] font-bold text-emerald-600 hover:text-emerald-700 cursor-pointer">Select All</button>
                            <span class="text-slate-300 dark:text-slate-600">|</span>
                            <button type="button" class="btn-clear-all-perms text-[10px] font-bold text-slate-400 hover:text-slate-600 cursor-pointer">Clear</button>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 bg-slate-50 dark:bg-slate-950/40 border border-slate-200/60 dark:border-slate-800 p-3 rounded-xl max-h-56 overflow-y-auto">
                        @foreach($availablePermissions as $permKey => $permMeta)
                            <label class="flex items-start gap-2 p-2 rounded-lg border border-slate-200/70 dark:border-slate-700/60 bg-white dark:bg-slate-900 hover:border-emerald-500 cursor-pointer transition-colors select-none group">
                                <input type="checkbox" name="admin_permissions[]" value="{{ $permKey }}" class="edit-perm-cb mt-0.5 rounded text-emerald-600 focus:ring-emerald-500 h-4 w-4 border-slate-300 dark:border-slate-700 dark:bg-slate-950 cursor-pointer">
                                <div class="min-w-0">
                                    <p class="text-xs font-bold text-slate-800 dark:text-slate-200 group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors flex items-center gap-1.5">
                                        <i class="{{ $permMeta['icon'] }} text-[10px] text-emerald-600 dark:text-emerald-400"></i>
                                        <span class="truncate">{{ $permMeta['label'] }}</span>
                                    </p>
                                    <p class="text-[9px] text-slate-400 dark:text-slate-500 leading-tight mt-0.5 line-clamp-1">{{ $permMeta['description'] }}</p>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- Super Admin Notice Banner in Edit -->
                <div id="edit-super-banner" class="hidden p-3 bg-violet-50 dark:bg-violet-950/30 border border-violet-200/80 dark:border-violet-800/40 rounded-xl text-violet-700 dark:text-violet-300 text-xs font-semibold flex items-center gap-2.5">
                    <i class="fa-solid fa-crown text-violet-600 dark:text-violet-400 text-sm"></i>
                    <span>Super Administrators automatically have unrestricted access across all admin pages, settings, and logs.</span>
                </div>

                <!-- Workflow Approval Committees -->
                <div class="space-y-1.5 border-t border-slate-100 dark:border-slate-800 pt-3">
                    <label class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider block">Workflow Approval Committees</label>
                    <div class="grid grid-cols-2 gap-2 bg-slate-50 dark:bg-slate-950/40 border border-slate-200/60 dark:border-slate-800 p-3 rounded-xl">
                        @foreach($roles as $r)
                            <label class="flex items-center gap-2 cursor-pointer select-none py-1 group">
                                <input type="checkbox" name="roles[]" value="{{ $r->id }}" class="edit-role-cb rounded text-emerald-600 dark:bg-slate-950 dark:border-slate-700 focus:ring-emerald-500 h-4 w-4 border-slate-300 dark:border-slate-700 transition-colors cursor-pointer">
                                <span class="text-xs text-slate-700 dark:text-slate-300 font-semibold group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">{{ $r->name }}</span>
                            </label>
                        @endforeach
                    </div>
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
<div id="modal-delete-admin" class="fixed inset-0 z-50 overflow-y-auto hidden flex items-center justify-center p-4">
    <div class="fixed inset-0 bg-slate-950/50 backdrop-blur-sm transition-opacity modal-overlay"></div>
    
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-2xl overflow-hidden w-full max-w-md relative z-10 p-5 sm:p-6 space-y-4 transform scale-95 opacity-0 transition-all duration-300 modal-container">
        <div class="flex items-center gap-3 text-rose-600">
            <div class="w-10 h-10 rounded-xl bg-rose-50 dark:bg-rose-950/20 border border-rose-100 dark:border-rose-900/30 flex items-center justify-center flex-shrink-0 text-base">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <div>
                <h3 class="text-base font-bold text-slate-900 dark:text-slate-100">Revoke Administrative Access</h3>
                <p class="text-[10px] text-rose-500 font-extrabold uppercase tracking-wider">Super Administrator Action</p>
            </div>
        </div>

        <div class="text-xs text-slate-600 dark:text-slate-300 font-medium leading-relaxed">
            Are you sure you want to permanently remove administrator account <span id="delete-admin-name" class="font-extrabold text-slate-900 dark:text-slate-100"></span>? Their administrative credentials and privileges will be terminated immediately.
        </div>

        <form id="form-delete-admin" method="POST" class="pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-end gap-2">
            @csrf
            @method('DELETE')
            
            <button type="button" class="modal-close px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 text-xs font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors cursor-pointer">Cancel</button>
            <button type="submit" class="bg-rose-600 hover:bg-rose-700 text-white font-semibold text-xs px-4 py-2 rounded-xl shadow-xs transition-all cursor-pointer">Confirm Revocation</button>
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
                    if (modalId === "modal-add-admin" || modalId === "modal-edit-admin") {
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
                if (modalId === "modal-add-admin" || modalId === "modal-edit-admin") {
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

        function handleRoleToggle(selectEl, permsGroupEl, bannerEl) {
            if (!selectEl) return;
            if (selectEl.value === 'super_admin') {
                if (permsGroupEl) permsGroupEl.classList.add('hidden');
                if (bannerEl) bannerEl.classList.remove('hidden');
            } else {
                if (permsGroupEl) permsGroupEl.classList.remove('hidden');
                if (bannerEl) bannerEl.classList.add('hidden');
            }
        }

        const addRoleSelect = document.getElementById("add-admin-role");
        const addPermsGroup = document.getElementById("add-admin-permissions-group");
        const addSuperBanner = document.getElementById("add-super-banner");
        if (addRoleSelect) {
            addRoleSelect.addEventListener("change", function() {
                handleRoleToggle(this, addPermsGroup, addSuperBanner);
            });
            handleRoleToggle(addRoleSelect, addPermsGroup, addSuperBanner);
        }

        const editRoleSelect = document.getElementById("edit-admin-role");
        const editPermsGroup = document.getElementById("edit-admin-permissions-group");
        const editSuperBanner = document.getElementById("edit-super-banner");
        if (editRoleSelect) {
            editRoleSelect.addEventListener("change", function() {
                handleRoleToggle(this, editPermsGroup, editSuperBanner);
            });
        }

        document.querySelectorAll(".btn-select-all-perms").forEach(btn => {
            btn.addEventListener("click", function() {
                const group = this.closest('.space-y-2');
                if (group) group.querySelectorAll("input[type='checkbox']").forEach(cb => cb.checked = true);
            });
        });

        document.querySelectorAll(".btn-clear-all-perms").forEach(btn => {
            btn.addEventListener("click", function() {
                const group = this.closest('.space-y-2');
                if (group) group.querySelectorAll("input[type='checkbox']").forEach(cb => cb.checked = false);
            });
        });

        const btnAddAdmin = document.getElementById("btn-add-admin");
        if (btnAddAdmin) {
            btnAddAdmin.addEventListener("click", () => openModal("modal-add-admin"));
        }

        document.querySelectorAll(".btn-edit-admin").forEach(btn => {
            btn.addEventListener("click", function() {
                const id = this.getAttribute("data-id");
                const name = this.getAttribute("data-name");
                const companyId = this.getAttribute("data-company_id");
                const email = this.getAttribute("data-email");
                const role = this.getAttribute("data-role");
                const signatureUrl = this.getAttribute("data-signature");
                const roles = JSON.parse(this.getAttribute("data-roles") || "[]");
                const adminPerms = JSON.parse(this.getAttribute("data-admin_permissions") || "[]");

                document.getElementById("edit-admin-name").value = name || '';
                document.getElementById("edit-admin-company_id").value = companyId || '';
                document.getElementById("edit-admin-email").value = email || '';

                if (editRoleSelect) {
                    editRoleSelect.value = role || 'admin';
                    handleRoleToggle(editRoleSelect, editPermsGroup, editSuperBanner);
                }

                // Handle E-Signature preview
                const previewContainer = document.getElementById("edit-signature-preview-container");
                const previewImg = document.getElementById("edit-signature-preview-img");
                if (signatureUrl && previewContainer && previewImg) {
                    previewImg.src = signatureUrl;
                    previewContainer.classList.remove("hidden");
                    previewContainer.classList.add("flex");
                } else if (previewContainer) {
                    previewContainer.classList.add("hidden");
                    previewContainer.classList.remove("flex");
                }

                const editForm = document.getElementById("form-edit-admin");
                editForm.querySelectorAll(".edit-role-cb").forEach(cb => {
                    cb.checked = roles.includes(parseInt(cb.value));
                });

                editForm.querySelectorAll(".edit-perm-cb").forEach(cb => {
                    cb.checked = Array.isArray(adminPerms) && adminPerms.includes(cb.value);
                });

                editForm.action = `/admin/administrators/${id}`;

                openModal("modal-edit-admin");
            });
        });

        document.querySelectorAll(".btn-delete-admin").forEach(btn => {
            btn.addEventListener("click", function() {
                const id = this.getAttribute("data-id");
                const name = this.getAttribute("data-name");

                document.getElementById("delete-admin-name").textContent = name;
                
                const form = document.getElementById("form-delete-admin");
                form.action = `/admin/administrators/${id}`;

                openModal("modal-delete-admin");
            });
        });

    });
</script>
@endpush
