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

    <!-- HRMD SEQUENTIAL APPROVAL CHAIN HIERARCHY OVERVIEW -->
    @if(isset($hrmdStaffList) && $hrmdStaffList->isNotEmpty())
        <div class="bg-gradient-to-r from-amber-500/10 via-amber-500/5 to-transparent border border-amber-200/80 dark:border-amber-900/50 p-4 sm:p-5 rounded-2xl shadow-xs space-y-3">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-amber-500 text-white flex items-center justify-center text-xs font-black shadow-xs">
                        <i class="fa-solid fa-arrow-down-1-9"></i>
                    </div>
                    <div>
                        <h4 class="text-xs sm:text-sm font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
                            <span>HRMD Approval Chain Hierarchy</span>
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-amber-100 dark:bg-amber-950/60 text-amber-700 dark:text-amber-400 border border-amber-300/40">
                                Sequential Order: 1 &rarr; Last
                            </span>
                        </h4>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400">
                            Loans passing through HRMD verification strictly require sequential sign-offs in the exact numerical order shown below before moving to Credit Committee.
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-2 text-xs">
                    <span class="text-[11px] font-semibold text-slate-500 dark:text-slate-400">
                        Next Suggested Sequence:
                        <strong class="text-amber-600 dark:text-amber-400 font-extrabold">#{{ ($hrmdStaffList->whereNotNull('hrmd_sequence')->max('hrmd_sequence') ?? 0) + 1 }}</strong>
                    </span>
                </div>
            </div>

            <!-- Steps Ribbon / Flow -->
            <div class="flex flex-wrap items-center gap-2 pt-1">
                @php $hasHrmdWithSequence = false; @endphp
                @foreach($hrmdStaffList->whereNotNull('hrmd_sequence')->sortBy('hrmd_sequence') as $hrUser)
                    @php $hasHrmdWithSequence = true; @endphp
                    <div class="flex items-center gap-1.5">
                        <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-white dark:bg-slate-900 border border-amber-300/80 dark:border-amber-800/80 shadow-2xs">
                            <span class="w-5 h-5 rounded-lg bg-amber-500 text-white font-black text-[10px] flex items-center justify-center">
                                {{ $hrUser->hrmd_sequence }}
                            </span>
                            <div class="text-left leading-tight">
                                <span class="text-xs font-bold text-slate-900 dark:text-slate-100 block">{{ $hrUser->name }}</span>
                                <span class="text-[9px] text-slate-400 font-mono">ID: {{ $hrUser->company_id }}</span>
                            </div>
                        </div>
                        @if(!$loop->last)
                            <i class="fa-solid fa-arrow-right text-[10px] text-amber-500/70"></i>
                        @endif
                    </div>
                @endforeach

                @if(!$hasHrmdWithSequence)
                    <span class="text-xs text-amber-700 dark:text-amber-400 italic">No HRMD staff have been assigned an approval sequence number yet. Assign sequence numbers using the Edit button below.</span>
                @endif

                <!-- Unassigned HRMD staff warning chips -->
                @php $unassignedHrmd = $hrmdStaffList->whereNull('hrmd_sequence'); @endphp
                @if($unassignedHrmd->isNotEmpty())
                    <div class="flex items-center gap-1.5 ml-auto">
                        <span class="text-[10px] font-extrabold uppercase tracking-wider text-rose-500 flex items-center gap-1">
                            <i class="fa-solid fa-triangle-exclamation"></i>
                            Needs Sequence:
                        </span>
                        @foreach($unassignedHrmd as $unassignedUser)
                            <span class="px-2 py-0.5 rounded-lg bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-400 text-[10px] font-bold border border-rose-200 dark:border-rose-900">
                                {{ $unassignedUser->name }}
                            </span>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    @endif

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
                                <div class="flex flex-wrap gap-1 max-w-[200px]">
                                    @forelse($user->roles as $userRole)
                                        @if($userRole->slug === 'hrmd_staff')
                                            @if($user->hrmd_sequence)
                                                <span class="px-2 py-0.5 rounded-full text-[9px] font-black bg-amber-100 dark:bg-amber-950/70 text-amber-800 dark:text-amber-300 border border-amber-300 dark:border-amber-700 uppercase tracking-wider inline-flex items-center gap-1 shadow-2xs" title="HRMD Sequential Sign-off Order: Sequence #{{ $user->hrmd_sequence }}">
                                                    <i class="fa-solid fa-list-ol text-[8px] text-amber-600"></i>
                                                    <span>HR Approver #{{ $user->hrmd_sequence }}</span>
                                                </span>
                                            @else
                                                <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-400 border border-rose-300 dark:border-rose-800 uppercase tracking-wider inline-flex items-center gap-1" title="HRMD Staff has no sequence assigned yet. Click Edit to assign order.">
                                                    <i class="fa-solid fa-triangle-exclamation text-[8.5px] text-rose-500"></i>
                                                    <span>HRMD (Unsequenced)</span>
                                                </span>
                                            @endif
                                        @else
                                            <span class="px-2 py-0.5 rounded-full text-[9px] font-extrabold bg-slate-100 dark:bg-slate-900 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-800 uppercase tracking-wider inline-flex items-center gap-1" title="{{ $userRole->description }}">
                                                <span>{{ $userRole->name }}</span>
                                            </span>
                                        @endif
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
                                        data-hrmd_sequence="{{ $user->hrmd_sequence }}"
                                        data-signature="{{ $user->signature ? asset('storage/' . $user->signature) : '' }}"
                                        data-roles="{{ json_encode($user->roles->pluck('id')->toArray()) }}"
                                        data-admin_permissions="{{ json_encode($user->admin_permissions ?? []) }}"
                                        title="Edit Permissions & Details">
                                        <i class="fa-solid fa-user-pen text-xs"></i>
                                    </button>

                                    @if(auth()->id() !== $user->id)
                                        <button class="btn-reset-admin-credentials p-1.5 rounded-lg border border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400 hover:text-amber-600 dark:hover:text-amber-400 hover:bg-amber-50 dark:hover:bg-amber-950/30 transition-colors cursor-pointer"
                                            data-id="{{ $user->id }}"
                                            data-name="{{ $user->name }}"
                                            data-company_id="{{ $user->company_id }}"
                                            data-email="{{ $user->email }}"
                                            data-role="{{ $user->role }}"
                                            data-has_pin="{{ !is_null($user->pin) ? '1' : '0' }}"
                                            data-pin_attempts="{{ $user->pin_attempts }}"
                                            title="Force Reset Password & PIN">
                                            <i class="fa-solid fa-key text-xs"></i>
                                        </button>

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

                <!-- Workflow Approval Committees (Cooperative Staff Roles) -->
                <div class="space-y-1.5 border-t border-slate-100 dark:border-slate-800 pt-3">
                    <div class="flex items-center justify-between">
                        <label class="text-[10px] font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider flex items-center gap-1.5">
                            <i class="fa-solid fa-users-gear text-emerald-600 text-xs"></i>
                            <span>Cooperative Staff Roles / Committees</span>
                        </label>
                        <span class="text-[9.5px] text-slate-400 font-semibold">Assign role(s) to operator</span>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 bg-slate-50 dark:bg-slate-950/40 border border-slate-200/60 dark:border-slate-800 p-3 rounded-xl">
                        @foreach($roles as $r)
                            <label class="flex items-center justify-between p-2 rounded-lg border border-slate-200/70 dark:border-slate-800 bg-white dark:bg-slate-900 hover:border-emerald-500 cursor-pointer select-none group transition-colors">
                                <div class="flex items-center gap-2">
                                    <input type="checkbox" name="roles[]" value="{{ $r->id }}" data-slug="{{ $r->slug }}" class="add-role-cb rounded text-emerald-600 dark:bg-slate-950 dark:border-slate-700 focus:ring-emerald-500 h-4 w-4 border-slate-300 dark:border-slate-700 transition-colors cursor-pointer">
                                    <span class="text-xs text-slate-700 dark:text-slate-300 font-semibold group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">{{ $r->name }}</span>
                                </div>
                                @if($r->slug === 'hrmd_staff')
                                    <span class="text-[8.5px] font-extrabold text-amber-700 dark:text-amber-300 bg-amber-100/90 dark:bg-amber-950/60 px-1.5 py-0.5 rounded border border-amber-300/60 dark:border-amber-800">
                                        Sequence
                                    </span>
                                @endif
                            </label>
                        @endforeach
                    </div>

                    <!-- HRMD Sequence Field (Dynamically Revealed when HRMD Staff role is checked) -->
                    <div id="add-hrmd-seq-container" class="hidden mt-2.5 p-3 bg-amber-50/70 dark:bg-amber-950/30 border border-amber-200/70 dark:border-amber-900/40 rounded-xl space-y-2 transition-all">
                        <div class="flex items-center justify-between">
                            <label class="text-[10px] font-bold text-amber-900 dark:text-amber-300 uppercase tracking-wider flex items-center gap-1.5">
                                <i class="fa-solid fa-list-ol text-amber-600 text-xs"></i>
                                <span>HRMD Approval Sequence</span>
                            </label>
                            <span class="text-[9px] font-semibold text-amber-600 dark:text-amber-400">1, 2, 3... (No Limit)</span>
                        </div>

                        <!-- Current Chain Reference Box in Add -->
                        @if(isset($hrmdStaffList))
                            <div class="p-2.5 bg-white dark:bg-slate-950 rounded-lg border border-amber-200/80 dark:border-amber-900/60 space-y-1.5">
                                <div class="flex items-center justify-between text-[10px]">
                                    <span class="font-extrabold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Current Assigned Sequences:</span>
                                    <span class="text-[9.5px] font-semibold text-slate-500">
                                        Next available: <strong class="text-emerald-600 dark:text-emerald-400 font-extrabold">#{{ ($hrmdStaffList->whereNotNull('hrmd_sequence')->max('hrmd_sequence') ?? 0) + 1 }}</strong>
                                    </span>
                                </div>
                                <div class="flex flex-wrap gap-1.5 max-h-24 overflow-y-auto">
                                    @forelse($hrmdStaffList->whereNotNull('hrmd_sequence')->sortBy('hrmd_sequence') as $h)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-amber-100/90 dark:bg-amber-950/60 text-amber-900 dark:text-amber-300 text-[10px] font-semibold border border-amber-300/70 dark:border-amber-800">
                                            <span class="font-black text-amber-700 dark:text-amber-400">#{{ $h->hrmd_sequence }}</span>
                                            <span>{{ $h->name }}</span>
                                        </span>
                                    @empty
                                        <span class="text-[10px] text-slate-400 italic">No sequences currently assigned. Start with sequence 1.</span>
                                    @endforelse
                                </div>
                            </div>
                        @endif

                        <input type="number" name="hrmd_sequence" id="add-admin-hrmd-sequence" min="1" step="1" placeholder="e.g. 1 (1st review), 2 (Supervisor), 3 (Director)..." class="w-full px-3 py-1.5 text-xs font-semibold border border-amber-200 dark:border-amber-800/80 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 rounded-lg outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500/20 placeholder-slate-400 transition-all">
                        <p class="text-[9px] text-slate-500 dark:text-slate-400 leading-normal">
                            Determines sequential order of review when this admin evaluates loans at the HRMD stage. Approvals advance sequentially (1 to last) before moving to Credit Committee.
                        </p>
                    </div>
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
                        <p class="text-[9px] text-slate-500 dark:text-slate-400 leading-normal">
                            Determines sequential order of review when this admin evaluates loans at the HRMD stage. Approvals advance sequentially (1 to last) before moving to Credit Committee.
                        </p>
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

                <!-- Workflow Approval Committees (Cooperative Staff Roles) in Edit -->
                <div class="space-y-1.5 border-t border-slate-100 dark:border-slate-800 pt-3">
                    <div class="flex items-center justify-between">
                        <label class="text-[10px] font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider flex items-center gap-1.5">
                            <i class="fa-solid fa-users-gear text-emerald-600 text-xs"></i>
                            <span>Cooperative Staff Roles / Committees</span>
                        </label>
                        <span class="text-[9.5px] text-slate-400 font-semibold">Assign role(s) to operator</span>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 bg-slate-50 dark:bg-slate-950/40 border border-slate-200/60 dark:border-slate-800 p-3 rounded-xl">
                        @foreach($roles as $r)
                            <label class="flex items-center justify-between p-2 rounded-lg border border-slate-200/70 dark:border-slate-800 bg-white dark:bg-slate-900 hover:border-emerald-500 cursor-pointer select-none group transition-colors">
                                <div class="flex items-center gap-2">
                                    <input type="checkbox" name="roles[]" value="{{ $r->id }}" data-slug="{{ $r->slug }}" class="edit-role-cb rounded text-emerald-600 dark:bg-slate-950 dark:border-slate-700 focus:ring-emerald-500 h-4 w-4 border-slate-300 dark:border-slate-700 transition-colors cursor-pointer">
                                    <span class="text-xs text-slate-700 dark:text-slate-300 font-semibold group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">{{ $r->name }}</span>
                                </div>
                                @if($r->slug === 'hrmd_staff')
                                    <span class="text-[8.5px] font-extrabold text-amber-700 dark:text-amber-300 bg-amber-100/90 dark:bg-amber-950/60 px-1.5 py-0.5 rounded border border-amber-300/60 dark:border-amber-800">
                                        Sequence
                                    </span>
                                @endif
                            </label>
                        @endforeach
                    </div>

                    <!-- HRMD Sequence Field in Edit (Dynamically Revealed when HRMD Staff role is checked) -->
                    <div id="edit-hrmd-seq-container" class="hidden mt-2.5 p-3 bg-amber-50/70 dark:bg-amber-950/30 border border-amber-200/70 dark:border-amber-900/40 rounded-xl space-y-2 transition-all">
                        <div class="flex items-center justify-between">
                            <label class="text-[10px] font-bold text-amber-900 dark:text-amber-300 uppercase tracking-wider flex items-center gap-1.5">
                                <i class="fa-solid fa-list-ol text-amber-600 text-xs"></i>
                                <span>HRMD Approval Sequence</span>
                            </label>
                            <span id="edit-admin-current-seq-badge" class="text-[9.5px] px-2 py-0.5 rounded-full bg-amber-200/90 dark:bg-amber-900/80 text-amber-900 dark:text-amber-200 font-extrabold border border-amber-300 dark:border-amber-700"></span>
                        </div>

                        <!-- Current Chain Reference Box in Edit -->
                        @if(isset($hrmdStaffList))
                            <div class="p-2.5 bg-white dark:bg-slate-950 rounded-lg border border-amber-200/80 dark:border-amber-900/60 space-y-1.5">
                                <div class="flex items-center justify-between text-[10px]">
                                    <span class="font-extrabold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Current HRMD Chain:</span>
                                    <span class="text-[9.5px] font-semibold text-slate-500">
                                        Next available: <strong class="text-emerald-600 dark:text-emerald-400 font-extrabold">#{{ ($hrmdStaffList->whereNotNull('hrmd_sequence')->max('hrmd_sequence') ?? 0) + 1 }}</strong>
                                    </span>
                                </div>
                                <div class="flex flex-wrap gap-1.5 max-h-24 overflow-y-auto">
                                    @forelse($hrmdStaffList->whereNotNull('hrmd_sequence')->sortBy('hrmd_sequence') as $h)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-amber-100/90 dark:bg-amber-950/60 text-amber-900 dark:text-amber-300 text-[10px] font-semibold border border-amber-300/70 dark:border-amber-800">
                                            <span class="font-black text-amber-700 dark:text-amber-400">#{{ $h->hrmd_sequence }}</span>
                                            <span>{{ $h->name }}</span>
                                        </span>
                                    @empty
                                        <span class="text-[10px] text-slate-400 italic">No sequences currently assigned. Start with sequence 1.</span>
                                    @endforelse
                                </div>
                            </div>
                        @endif

                        <input type="number" name="hrmd_sequence" id="edit-admin-hrmd-sequence" min="1" step="1" placeholder="e.g. 1 (1st review), 2 (Supervisor), 3 (Director)..." class="w-full px-3 py-1.5 text-xs font-semibold border border-amber-200 dark:border-amber-800/80 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 rounded-lg outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500/20 placeholder-slate-400 transition-all">
                        <p class="text-[9px] text-slate-500 dark:text-slate-400 leading-normal">
                            Determines sequential order of review when this admin evaluates loans at the HRMD stage. Approvals advance sequentially (1 to last) before moving to Credit Committee.
                        </p>
                    </div>
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

                <!-- E-Signature Upload & Preview in Edit -->
                <div class="space-y-1.5 border-t border-slate-100 dark:border-slate-800 pt-3">
                    <div class="flex items-center justify-between">
                        <label class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider flex items-center gap-1.5">
                            <i class="fa-solid fa-file-signature text-emerald-600 text-xs"></i>
                            <span>Official E-Signature</span>
                        </label>
                        <span class="text-[9px] text-slate-400 font-semibold">(PNG / SVG transparent)</span>
                    </div>
                    <div id="edit-signature-preview-container" class="hidden items-center justify-between p-2.5 bg-slate-50 dark:bg-slate-950/40 border border-slate-200/60 dark:border-slate-800 rounded-xl mb-2">
                        <div class="flex items-center gap-3">
                            <img id="edit-signature-preview-img" src="" alt="Current Signature" class="h-10 w-auto max-w-[120px] object-contain bg-white dark:bg-slate-900 p-1 rounded-lg border border-slate-200 dark:border-slate-700">
                            <div>
                                <span class="text-[11px] font-bold text-slate-800 dark:text-slate-200 block">Registered on file</span>
                                <span class="text-[9px] text-slate-400">Used for official sign-offs and PDF contracts.</span>
                            </div>
                        </div>
                        <button type="button" id="btn-remove-admin-sig" class="px-2.5 py-1 rounded-lg text-[10px] font-bold text-rose-600 dark:text-rose-400 bg-rose-50 dark:bg-rose-950/40 hover:bg-rose-100 dark:hover:bg-rose-900/50 border border-rose-200 dark:border-rose-800 transition-colors flex items-center gap-1 cursor-pointer" title="Remove obsolete signature so admin can re-upload">
                            <i class="fa-solid fa-trash-can text-[9px]"></i>
                            <span>Clear</span>
                        </button>
                    </div>
                    <input type="hidden" name="remove_signature" id="admin-remove-signature-flag" value="0">
                    <input type="file" name="signature" accept="image/png, image/jpeg, image/jpg, image/svg+xml" class="w-full px-3 py-1.5 text-xs font-semibold border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 rounded-xl outline-none focus:border-emerald-500 file:mr-3 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-[10px] file:font-bold file:bg-emerald-50 file:text-emerald-700 dark:file:bg-emerald-950/40 dark:file:text-emerald-300 hover:file:bg-emerald-100 cursor-pointer">
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

<!-- MODAL: FORCE RESET ADMINISTRATOR CREDENTIALS -->
<div id="modal-reset-admin-credentials" class="fixed inset-0 z-50 overflow-y-auto hidden flex items-center justify-center p-3 sm:p-4">
    <div class="fixed inset-0 bg-slate-950/60 backdrop-blur-md transition-opacity modal-overlay"></div>
    
    <div class="bg-white dark:bg-slate-900 rounded-2xl sm:rounded-3xl border border-slate-200/90 dark:border-slate-800 shadow-2xl overflow-hidden w-full max-w-lg max-h-[92dvh] sm:max-h-[88vh] flex flex-col relative z-10 transform scale-95 opacity-0 transition-all duration-300 modal-container">
        
        <!-- Pinned Header -->
        <div class="flex items-center justify-between p-4 sm:p-5 border-b border-slate-100 dark:border-slate-800/80 flex-shrink-0 bg-white/90 dark:bg-slate-900/90 backdrop-blur-md">
            <div class="flex items-center gap-3 min-w-0">
                <div class="w-10 h-10 rounded-xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200/80 dark:border-amber-800/60 flex items-center justify-center flex-shrink-0 text-amber-600 dark:text-amber-400 text-base shadow-2xs">
                    <i class="fa-solid fa-key"></i>
                </div>
                <div class="min-w-0">
                    <h3 class="text-base sm:text-lg font-bold text-slate-900 dark:text-slate-100 truncate">Force Reset Admin Credentials</h3>
                    <p class="text-[10px] text-amber-600 dark:text-amber-400 font-extrabold uppercase tracking-wider truncate">Super Administrator Privilege Override</p>
                </div>
            </div>
            <button type="button" class="modal-close w-9 h-9 rounded-xl text-slate-400 hover:text-slate-700 dark:text-slate-500 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors flex items-center justify-center cursor-pointer flex-shrink-0" aria-label="Close modal">
                <i class="fa-solid fa-xmark text-base"></i>
            </button>
        </div>

        <form id="form-reset-admin-credentials" method="POST" class="flex-1 flex flex-col min-h-0 overflow-hidden">
            @csrf

            <!-- Scrollable Body -->
            <div class="flex-1 overflow-y-auto overscroll-contain p-4 sm:p-5 space-y-4">
                
                <!-- Target Admin Profile Banner (Responsive Flex-Col on narrow screens) -->
                <div class="p-3.5 bg-slate-50 dark:bg-slate-950/50 rounded-xl sm:rounded-2xl border border-slate-200/70 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="w-10 h-10 rounded-full bg-violet-100 dark:bg-violet-950/60 text-violet-700 dark:text-violet-400 flex items-center justify-center font-bold text-xs uppercase flex-shrink-0 shadow-2xs" id="reset-admin-avatar">
                            A
                        </div>
                        <div class="min-w-0">
                            <h4 class="text-xs sm:text-sm font-bold text-slate-900 dark:text-slate-100 truncate" id="reset-admin-name">Admin Name</h4>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400 truncate" id="reset-admin-email">admin@coop.internal</p>
                        </div>
                    </div>
                    <div class="flex sm:flex-col items-center sm:items-end justify-between sm:justify-center gap-1 flex-shrink-0 pt-2 sm:pt-0 border-t sm:border-t-0 border-slate-200/60 dark:border-slate-800">
                        <span class="text-[10px] font-mono font-bold px-2 py-0.5 rounded-md bg-slate-200/80 dark:bg-slate-800 text-slate-700 dark:text-slate-300 inline-block" id="reset-admin-company-id">ID: admin01</span>
                        <span class="text-[10px] font-semibold text-emerald-600 dark:text-emerald-400 inline-block" id="reset-admin-pin-status">PIN: Configured</span>
                    </div>
                </div>

                <!-- Option 1: Reset Password -->
                <div class="p-4 bg-white dark:bg-slate-900 rounded-xl sm:rounded-2xl border border-slate-200 dark:border-slate-800 shadow-2xs space-y-3">
                    <label class="flex items-center justify-between cursor-pointer select-none">
                        <span class="text-xs sm:text-sm font-bold text-slate-800 dark:text-slate-200 flex items-center gap-2.5">
                            <input type="checkbox" name="reset_password" value="1" id="cb-reset-admin-password" checked class="w-4 h-4 rounded text-emerald-600 focus:ring-emerald-500 border-slate-300 dark:border-slate-700 dark:bg-slate-950 cursor-pointer">
                            <span>Reset Login Password</span>
                        </span>
                        <span class="text-[10px] text-slate-400 font-medium bg-slate-100 dark:bg-slate-800 px-2 py-0.5 rounded">Min. 6 chars</span>
                    </label>

                    <div id="password-field-admin-container" class="space-y-2.5 pt-1">
                        <div class="relative flex items-center">
                            <input type="password" name="password" id="input-new-admin-password" value="password" placeholder="Enter new password" class="w-full px-3.5 py-2.5 pr-24 text-base sm:text-sm font-mono font-semibold border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 rounded-xl outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 placeholder-slate-400 transition-all">
                            
                            <div class="absolute right-1.5 flex items-center gap-1">
                                <button type="button" id="btn-toggle-admin-password-visibility" class="w-8 h-8 rounded-lg text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors flex items-center justify-center cursor-pointer" title="Toggle visibility" aria-label="Toggle password visibility">
                                    <i class="fa-solid fa-eye text-xs"></i>
                                </button>
                                <button type="button" id="btn-copy-admin-password" class="w-8 h-8 rounded-lg text-slate-400 hover:text-emerald-600 dark:hover:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-950/40 transition-colors flex items-center justify-center cursor-pointer" title="Copy to clipboard" aria-label="Copy password">
                                    <i class="fa-solid fa-copy text-xs"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Quick Presets -->
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pt-0.5">
                            <div class="flex items-center gap-1.5">
                                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Quick Fill:</span>
                                <span id="copy-admin-feedback-badge" class="hidden text-[10px] font-semibold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/50 px-2 py-0.5 rounded animate-fade-in">Copied!</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <button type="button" id="btn-preset-default-admin-pwd" class="flex-1 sm:flex-none px-3 py-1.5 rounded-lg text-xs font-semibold bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 transition-colors cursor-pointer text-center">
                                    'password'
                                </button>
                                <button type="button" id="btn-generate-strong-admin-pwd" class="flex-1 sm:flex-none px-3 py-1.5 rounded-lg text-xs font-semibold bg-emerald-50 hover:bg-emerald-100 dark:bg-emerald-950/40 dark:hover:bg-emerald-900/60 text-emerald-700 dark:text-emerald-300 transition-colors cursor-pointer flex items-center justify-center gap-1.5 text-center">
                                    <i class="fa-solid fa-arrows-rotate text-[10px]"></i>
                                    <span>Generate Strong</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Option 2: Reset Security PIN -->
                <div class="p-4 bg-white dark:bg-slate-900 rounded-xl sm:rounded-2xl border border-slate-200 dark:border-slate-800 shadow-2xs space-y-3">
                    <label class="flex items-center justify-between cursor-pointer select-none">
                        <span class="text-xs sm:text-sm font-bold text-slate-800 dark:text-slate-200 flex items-center gap-2.5">
                            <input type="checkbox" name="reset_pin" value="1" id="cb-reset-admin-pin" checked class="w-4 h-4 rounded text-emerald-600 focus:ring-emerald-500 border-slate-300 dark:border-slate-700 dark:bg-slate-950 cursor-pointer">
                            <span>Reset 6-Digit Security PIN</span>
                        </span>
                        <span class="text-[10px] text-amber-600 dark:text-amber-400 font-semibold bg-amber-50 dark:bg-amber-950/40 px-2 py-0.5 rounded" id="reset-admin-lockout-badge">Lifts Lockouts</span>
                    </label>

                    <div id="pin-field-admin-container" class="space-y-2.5 pt-1">
                        <div class="space-y-2">
                            <!-- Radio Clear -->
                            <label class="flex items-start gap-3 p-3 rounded-xl border border-slate-200/80 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-950/40 cursor-pointer transition-colors select-none group">
                                <input type="radio" name="pin_mode" value="clear" id="pin-admin-mode-clear" checked class="mt-0.5 text-emerald-600 focus:ring-emerald-500 cursor-pointer">
                                <div class="min-w-0">
                                    <span class="text-xs sm:text-sm font-semibold text-slate-800 dark:text-slate-200 group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors block">Require operator to configure new PIN on next login (Recommended)</span>
                                    <span class="text-[11px] text-slate-400 leading-normal block mt-0.5">Clears current PIN and resets failed attempts. Operator will be prompted by the security overlay upon signing in.</span>
                                </div>
                            </label>

                            <!-- Radio Manual -->
                            <label class="flex items-start gap-3 p-3 rounded-xl border border-slate-200/80 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-950/40 cursor-pointer transition-colors select-none group">
                                <input type="radio" name="pin_mode" value="manual" id="pin-admin-mode-manual" class="mt-0.5 text-emerald-600 focus:ring-emerald-500 cursor-pointer">
                                <div class="flex-1 min-w-0">
                                    <span class="text-xs sm:text-sm font-semibold text-slate-800 dark:text-slate-200 group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors block">Assign manual temporary 6-digit PIN</span>
                                    <span class="text-[11px] text-slate-400 block mb-2">Directly set an initial 6-digit numeric PIN for the administrator.</span>
                                    
                                    <div class="relative flex items-center max-w-[200px]">
                                        <input type="password" name="pin" id="input-manual-admin-pin" maxlength="6" inputmode="numeric" pattern="[0-9]{6}" placeholder="••••••" disabled class="w-full px-3 py-2 pr-10 text-base sm:text-sm font-mono font-bold tracking-widest text-center border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100 rounded-xl outline-none focus:border-emerald-500 disabled:opacity-50 disabled:bg-slate-100 dark:disabled:bg-slate-800 transition-all">
                                        <button type="button" id="btn-toggle-manual-admin-pin-visibility" class="absolute right-1 w-8 h-8 rounded-lg text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors flex items-center justify-center cursor-pointer" title="Toggle PIN visibility">
                                            <i class="fa-solid fa-eye text-xs"></i>
                                        </button>
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Disclaimer Notice -->
                <div class="p-3 rounded-xl bg-amber-50 dark:bg-amber-950/20 border border-amber-200/60 dark:border-amber-900/30 flex items-start gap-2.5 text-amber-700 dark:text-amber-300 text-xs leading-relaxed">
                    <i class="fa-solid fa-circle-exclamation mt-0.5 flex-shrink-0 text-amber-600 dark:text-amber-400"></i>
                    <span>This action immediately overrides administrative credentials, resets failed attempts, and is logged in the system security audit trail.</span>
                </div>

            </div>

            <!-- Pinned Footer (Stacked full width on mobile, row on sm+) -->
            <div class="p-4 sm:p-5 border-t border-slate-100 dark:border-slate-800/80 bg-slate-50/70 dark:bg-slate-900/90 backdrop-blur-md flex-shrink-0 flex flex-col-reverse sm:flex-row sm:items-center sm:justify-end gap-2.5">
                <button type="button" class="modal-close w-full sm:w-auto px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-xs font-semibold text-slate-600 dark:text-slate-300 hover:bg-white dark:hover:bg-slate-800 transition-colors cursor-pointer text-center">Cancel</button>
                <button type="submit" id="btn-submit-admin-reset" class="w-full sm:w-auto bg-amber-600 hover:bg-amber-700 text-white font-semibold text-xs px-5 py-2.5 rounded-xl shadow-xs transition-all cursor-pointer flex items-center justify-center gap-2">
                    <i class="fa-solid fa-shield-halved text-xs"></i>
                    <span>Confirm Force Reset</span>
                </button>
            </div>
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

        const addHrmdSeqContainer = document.getElementById("add-hrmd-seq-container");
        const addHrmdSeqInput = document.getElementById("add-admin-hrmd-sequence");
        const addHrmdCb = document.querySelector('input.add-role-cb[data-slug="hrmd_staff"]');

        const editHrmdSeqContainer = document.getElementById("edit-hrmd-seq-container");
        const editHrmdSeqInput = document.getElementById("edit-admin-hrmd-sequence");
        const editHrmdCb = document.querySelector('input.edit-role-cb[data-slug="hrmd_staff"]');

        const btnAddAdmin = document.getElementById("btn-add-admin");
        if (btnAddAdmin) {
            btnAddAdmin.addEventListener("click", () => {
                if (addHrmdSeqContainer) addHrmdSeqContainer.classList.add("hidden");
                if (addHrmdSeqInput) addHrmdSeqInput.value = "";
                document.querySelectorAll(".add-role-cb").forEach(cb => cb.checked = false);
                openModal("modal-add-admin");
            });
        }

        // Add modal: Show/hide sequence input when HRMD role is checked/unchecked
        if (addHrmdCb && addHrmdSeqContainer) {
            addHrmdCb.addEventListener("change", function() {
                if (this.checked) {
                    addHrmdSeqContainer.classList.remove("hidden");
                    if (addHrmdSeqInput && !addHrmdSeqInput.value) {
                        addHrmdSeqInput.focus();
                    }
                } else {
                    addHrmdSeqContainer.classList.add("hidden");
                    if (addHrmdSeqInput) addHrmdSeqInput.value = "";
                }
            });
        }

        // Edit modal: Show/hide sequence input when HRMD role is checked/unchecked
        if (editHrmdCb && editHrmdSeqContainer) {
            editHrmdCb.addEventListener("change", function() {
                if (this.checked) {
                    editHrmdSeqContainer.classList.remove("hidden");
                    if (editHrmdSeqInput && !editHrmdSeqInput.value) {
                        editHrmdSeqInput.focus();
                    }
                } else {
                    editHrmdSeqContainer.classList.add("hidden");
                    if (editHrmdSeqInput) editHrmdSeqInput.value = "";
                }
            });
        }

        // Auto-check HRMD Staff role checkbox when sequence is typed
        if (addHrmdSeqInput) {
            addHrmdSeqInput.addEventListener("input", function() {
                if (addHrmdCb && this.value && Number(this.value) > 0) {
                    addHrmdCb.checked = true;
                    if (addHrmdSeqContainer) addHrmdSeqContainer.classList.remove("hidden");
                }
            });
        }

        if (editHrmdSeqInput) {
            editHrmdSeqInput.addEventListener("input", function() {
                if (editHrmdCb && this.value && Number(this.value) > 0) {
                    editHrmdCb.checked = true;
                    if (editHrmdSeqContainer) editHrmdSeqContainer.classList.remove("hidden");
                }
            });
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
                const hrmdSequence = this.getAttribute("data-hrmd_sequence");

                document.getElementById("edit-admin-name").value = name || '';
                document.getElementById("edit-admin-company_id").value = companyId || '';
                document.getElementById("edit-admin-email").value = email || '';

                if (editHrmdSeqInput) {
                    editHrmdSeqInput.value = hrmdSequence || '';
                }

                const curSeqBadge = document.getElementById("edit-admin-current-seq-badge");
                if (curSeqBadge) {
                    if (hrmdSequence) {
                        curSeqBadge.textContent = `Current: Sequence #${hrmdSequence}`;
                        curSeqBadge.className = "text-[9.5px] px-2 py-0.5 rounded-full bg-amber-200/90 dark:bg-amber-900/80 text-amber-900 dark:text-amber-200 font-extrabold border border-amber-300 dark:border-amber-700";
                    } else {
                        curSeqBadge.textContent = "Current: No Sequence Assigned";
                        curSeqBadge.className = "text-[9.5px] px-2 py-0.5 rounded-full bg-rose-100 dark:bg-rose-950/60 text-rose-700 dark:text-rose-400 font-extrabold border border-rose-300 dark:border-rose-800";
                    }
                }

                if (editRoleSelect) {
                    editRoleSelect.value = role || 'admin';
                    handleRoleToggle(editRoleSelect, editPermsGroup, editSuperBanner);
                }

                // Handle E-Signature preview
                const previewContainer = document.getElementById("edit-signature-preview-container");
                const previewImg = document.getElementById("edit-signature-preview-img");
                const removeSigFlag = document.getElementById("admin-remove-signature-flag");
                if (removeSigFlag) removeSigFlag.value = "0";

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

                // Dynamically show or hide HRMD sequence container based on assigned role
                if (editHrmdCb && editHrmdSeqContainer) {
                    if (editHrmdCb.checked) {
                        editHrmdSeqContainer.classList.remove("hidden");
                    } else {
                        editHrmdSeqContainer.classList.add("hidden");
                    }
                }

                editForm.querySelectorAll(".edit-perm-cb").forEach(cb => {
                    cb.checked = Array.isArray(adminPerms) && adminPerms.includes(cb.value);
                });

                editForm.action = `/admin/administrators/${id}`;

                openModal("modal-edit-admin");
            });
        });

        // Clear Admin Signature button in Edit Drawer
        const btnRemoveAdminSig = document.getElementById("btn-remove-admin-sig");
        if (btnRemoveAdminSig) {
            btnRemoveAdminSig.addEventListener("click", function() {
                const removeSigFlag = document.getElementById("admin-remove-signature-flag");
                const sigPreviewContainer = document.getElementById("edit-signature-preview-container");
                if (removeSigFlag) removeSigFlag.value = "1";
                if (sigPreviewContainer) {
                    sigPreviewContainer.classList.add("hidden");
                    sigPreviewContainer.classList.remove("flex");
                }
            });
        }

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

        // --- Force Reset Administrator Credentials Handler ---
        const formResetAdminCreds = document.getElementById("form-reset-admin-credentials");
        const cbResetAdminPwd = document.getElementById("cb-reset-admin-password");
        const cbResetAdminPin = document.getElementById("cb-reset-admin-pin");
        const pwdAdminContainer = document.getElementById("password-field-admin-container");
        const pinAdminContainer = document.getElementById("pin-field-admin-container");
        const inputNewAdminPwd = document.getElementById("input-new-admin-password");
        const inputManualAdminPin = document.getElementById("input-manual-admin-pin");
        const pinAdminModeClear = document.getElementById("pin-admin-mode-clear");
        const pinAdminModeManual = document.getElementById("pin-admin-mode-manual");
        const btnSubmitAdminReset = document.getElementById("btn-submit-admin-reset");
        const btnToggleAdminPwd = document.getElementById("btn-toggle-admin-password-visibility");
        const btnCopyAdminPwd = document.getElementById("btn-copy-admin-password");
        const btnPresetDefaultAdminPwd = document.getElementById("btn-preset-default-admin-pwd");
        const btnGenerateStrongAdminPwd = document.getElementById("btn-generate-strong-admin-pwd");

        function updateResetAdminFormState() {
            if (!cbResetAdminPwd || !cbResetAdminPin) return;

            if (cbResetAdminPwd.checked) {
                pwdAdminContainer.classList.remove("hidden");
                inputNewAdminPwd.disabled = false;
            } else {
                pwdAdminContainer.classList.add("hidden");
                inputNewAdminPwd.disabled = true;
            }

            if (cbResetAdminPin.checked) {
                pinAdminContainer.classList.remove("hidden");
                if (pinAdminModeManual.checked) {
                    inputManualAdminPin.disabled = false;
                } else {
                    inputManualAdminPin.disabled = true;
                }
            } else {
                pinAdminContainer.classList.add("hidden");
                inputManualAdminPin.disabled = true;
            }

            const atLeastOne = cbResetAdminPwd.checked || cbResetAdminPin.checked;
            btnSubmitAdminReset.disabled = !atLeastOne;
            if (!atLeastOne) {
                btnSubmitAdminReset.classList.add("opacity-50", "cursor-not-allowed");
            } else {
                btnSubmitAdminReset.classList.remove("opacity-50", "cursor-not-allowed");
            }
        }

        if (cbResetAdminPwd) cbResetAdminPwd.addEventListener("change", updateResetAdminFormState);
        if (cbResetAdminPin) cbResetAdminPin.addEventListener("change", updateResetAdminFormState);
        if (pinAdminModeClear) pinAdminModeClear.addEventListener("change", updateResetAdminFormState);
        if (pinAdminModeManual) {
            pinAdminModeManual.addEventListener("change", function() {
                updateResetAdminFormState();
                if (pinAdminModeManual.checked) inputManualAdminPin.focus();
            });
        }

        if (btnToggleAdminPwd) {
            btnToggleAdminPwd.addEventListener("click", function() {
                const icon = this.querySelector("i");
                if (inputNewAdminPwd.type === "password") {
                    inputNewAdminPwd.type = "text";
                    icon.classList.remove("fa-eye");
                    icon.classList.add("fa-eye-slash");
                } else {
                    inputNewAdminPwd.type = "password";
                    icon.classList.remove("fa-eye-slash");
                    icon.classList.add("fa-eye");
                }
            });
        }

        if (btnPresetDefaultAdminPwd) {
            btnPresetDefaultAdminPwd.addEventListener("click", function() {
                inputNewAdminPwd.value = "password";
                inputNewAdminPwd.type = "text";
                const icon = btnToggleAdminPwd.querySelector("i");
                if (icon) {
                    icon.classList.remove("fa-eye");
                    icon.classList.add("fa-eye-slash");
                }
            });
        }

        if (btnGenerateStrongAdminPwd) {
            btnGenerateStrongAdminPwd.addEventListener("click", function() {
                const chars = "ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz23456789";
                let randomPart = "";
                for (let i = 0; i < 4; i++) {
                    randomPart += chars.charAt(Math.floor(Math.random() * chars.length));
                }
                inputNewAdminPwd.value = `Sako#${randomPart}`;
                inputNewAdminPwd.type = "text";
                const icon = btnToggleAdminPwd.querySelector("i");
                if (icon) {
                    icon.classList.remove("fa-eye");
                    icon.classList.add("fa-eye-slash");
                }
            });
        }

        const btnToggleManualAdminPin = document.getElementById("btn-toggle-manual-admin-pin-visibility");
        if (btnToggleManualAdminPin) {
            btnToggleManualAdminPin.addEventListener("click", function() {
                const icon = this.querySelector("i");
                if (inputManualAdminPin.type === "password") {
                    inputManualAdminPin.type = "text";
                    icon.classList.remove("fa-eye");
                    icon.classList.add("fa-eye-slash");
                } else {
                    inputManualAdminPin.type = "password";
                    icon.classList.remove("fa-eye-slash");
                    icon.classList.add("fa-eye");
                }
            });
        }

        const copyAdminFeedbackBadge = document.getElementById("copy-admin-feedback-badge");
        if (btnCopyAdminPwd) {
            btnCopyAdminPwd.addEventListener("click", function() {
                if (!inputNewAdminPwd.value) return;
                navigator.clipboard.writeText(inputNewAdminPwd.value).then(() => {
                    const icon = this.querySelector("i");
                    icon.classList.remove("fa-copy");
                    icon.classList.add("fa-check", "text-emerald-600");
                    if (copyAdminFeedbackBadge) {
                        copyAdminFeedbackBadge.classList.remove("hidden");
                    }
                    setTimeout(() => {
                        icon.classList.remove("fa-check", "text-emerald-600");
                        icon.classList.add("fa-copy");
                        if (copyAdminFeedbackBadge) {
                            copyAdminFeedbackBadge.classList.add("hidden");
                        }
                    }, 1500);
                });
            });
        }

        if (formResetAdminCreds) {
            formResetAdminCreds.addEventListener("submit", function(e) {
                if (cbResetAdminPwd.checked && (!inputNewAdminPwd.value || inputNewAdminPwd.value.length < 6)) {
                    e.preventDefault();
                    alert("Please provide a password with at least 6 characters.");
                    inputNewAdminPwd.focus();
                    return;
                }

                if (cbResetAdminPin.checked && pinAdminModeManual.checked) {
                    const pinVal = inputManualAdminPin.value.trim();
                    if (!/^\d{6}$/.test(pinVal)) {
                        e.preventDefault();
                        alert("The manual PIN must be exactly 6 numeric digits.");
                        inputManualAdminPin.focus();
                        return;
                    }
                }
            });
        }

        document.querySelectorAll(".btn-reset-admin-credentials").forEach(btn => {
            btn.addEventListener("click", function() {
                const id = this.getAttribute("data-id");
                const name = this.getAttribute("data-name");
                const email = this.getAttribute("data-email");
                const companyId = this.getAttribute("data-company_id");
                const role = this.getAttribute("data-role");
                const hasPin = this.getAttribute("data-has_pin") === "1";
                const pinAttempts = parseInt(this.getAttribute("data-pin_attempts") || "0", 10);

                document.getElementById("reset-admin-name").textContent = name;
                document.getElementById("reset-admin-email").textContent = email || 'Internal Account';
                document.getElementById("reset-admin-company-id").textContent = `ID: ${companyId || 'N/A'}`;
                document.getElementById("reset-admin-avatar").textContent = (name || 'A').charAt(0).toUpperCase();

                const pinStatusEl = document.getElementById("reset-admin-pin-status");
                if (hasPin) {
                    pinStatusEl.textContent = "PIN: Configured";
                    pinStatusEl.className = "text-[9px] font-semibold text-emerald-600 dark:text-emerald-400 mt-0.5 inline-block";
                } else {
                    pinStatusEl.textContent = "PIN: Not Configured";
                    pinStatusEl.className = "text-[9px] font-semibold text-slate-400 mt-0.5 inline-block";
                }

                const lockoutBadge = document.getElementById("reset-admin-lockout-badge");
                if (pinAttempts >= 3) {
                    lockoutBadge.textContent = `Account Locked (${pinAttempts}/3 failed PINs) - Will Unlock`;
                    lockoutBadge.className = "text-[9px] text-rose-600 dark:text-rose-400 font-bold";
                } else if (pinAttempts > 0) {
                    lockoutBadge.textContent = `${pinAttempts}/3 Failed PINs (Will Reset)`;
                    lockoutBadge.className = "text-[9px] text-amber-600 dark:text-amber-400 font-semibold";
                } else {
                    lockoutBadge.textContent = "Lifts Account Lockouts";
                    lockoutBadge.className = "text-[9px] text-emerald-600 dark:text-emerald-400 font-semibold";
                }

                formResetAdminCreds.action = `/admin/administrators/${id}/reset-credentials`;

                // Reset inputs to clean defaults
                cbResetAdminPwd.checked = true;
                cbResetAdminPin.checked = true;
                pinAdminModeClear.checked = true;
                pinAdminModeManual.checked = false;
                inputManualAdminPin.value = "";
                inputNewAdminPwd.value = "password";
                inputNewAdminPwd.type = "password";
                if (btnToggleAdminPwd) {
                    const toggleIcon = btnToggleAdminPwd.querySelector("i");
                    if (toggleIcon) {
                        toggleIcon.classList.remove("fa-eye-slash");
                        toggleIcon.classList.add("fa-eye");
                    }
                }

                updateResetAdminFormState();
                openModal("modal-reset-admin-credentials");
            });
        });

    });
</script>
@endpush
