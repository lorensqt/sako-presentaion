@extends('layouts.admin')

@section('title', 'Admin Overview Panel - Sako Cooperative')

@section('header')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight serif-font">Overview Panel</h1>
        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 font-semibold">Real-time indicators, operational stats, and general configuration health of the cooperative.</p>
    </div>
    <div class="flex items-center gap-2.5">
        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-50 dark:bg-emerald-950/20 text-emerald-800 dark:text-emerald-400 text-xs font-bold border border-emerald-100/50 dark:border-emerald-900/30">
            <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse"></span>
            Node Status: Stable
        </span>
        <span class="text-xs text-slate-400 font-bold bg-slate-100 dark:bg-slate-800 px-3 py-1.5 rounded-xl border border-slate-200 dark:border-slate-700">
            System Admin Root
        </span>
    </div>
</div>
@endsection

@section('content')
<div class="space-y-8 animate-fade-in">

    <!-- Operational KPI Metrics Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        
        <!-- KPI 1: Ledger Savings Pool -->
        <div class="bg-gradient-to-tr from-emerald-800 to-teal-950 text-white p-6 rounded-3xl shadow-xl relative overflow-hidden group">
            <div class="absolute -top-12 -right-12 w-32 h-32 bg-emerald-500/10 rounded-full blur-2xl"></div>
            <div class="space-y-3 relative z-10">
                <p class="text-[10px] font-bold text-emerald-300 uppercase tracking-widest">Cooperative Ledger Pool</p>
                <h3 class="text-3xl font-black serif-font">₱{{ number_format($cooperativeSavings, 2) }}</h3>
                <div class="pt-2 border-t border-emerald-800/40 flex justify-between items-center text-[10px] text-emerald-200 font-semibold">
                    <span>Active Deposits</span>
                    <span class="text-emerald-400">Stable Pool</span>
                </div>
            </div>
        </div>

        <!-- KPI 2: Active Loan Volume -->
        <div class="bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700/50 p-6 rounded-3xl shadow-sm relative overflow-hidden group">
            <div class="space-y-3">
                <p class="text-[10px] font-extrabold text-slate-400 dark:text-slate-500 uppercase tracking-widest">Outstanding Loans</p>
                <h3 class="text-3xl font-bold text-slate-900 dark:text-white serif-font">₱{{ number_format($activeLoansVolume, 2) }}</h3>
                <div class="pt-2 border-t border-slate-100 dark:border-slate-700/50 flex justify-between items-center text-[10px] text-slate-500 dark:text-slate-400 font-semibold">
                    <span>Term limit: 24-36 months</span>
                    <span class="text-emerald-600 dark:text-emerald-400 font-bold">Low Default Risk</span>
                </div>
            </div>
        </div>

        <!-- KPI 3: Total Accounts Directory -->
        <div class="bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700/50 p-6 rounded-3xl shadow-sm relative overflow-hidden group">
            <div class="space-y-3">
                <p class="text-[10px] font-extrabold text-slate-400 dark:text-slate-500 uppercase tracking-widest">System Accounts Directory</p>
                <h3 class="text-3xl font-bold text-slate-900 dark:text-white serif-font">{{ $totalUsersCount }} Accounts</h3>
                <div class="pt-2 border-t border-slate-100 dark:border-slate-700/50 flex justify-between items-center text-[10px] text-slate-500 dark:text-slate-400 font-semibold">
                    <span>{{ $membersCount }} Members</span>
                    <span class="text-emerald-600 dark:text-emerald-400 font-bold">{{ $adminsCount }} Admins</span>
                </div>
            </div>
        </div>

        <!-- KPI 4: Pending Loan Approvals Queue -->
        <div class="bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700/50 p-6 rounded-3xl shadow-sm relative overflow-hidden group">
            <div class="space-y-3">
                <p class="text-[10px] font-extrabold text-slate-400 dark:text-slate-500 uppercase tracking-widest">Pending Approvals Queue</p>
                <h3 class="text-3xl font-bold text-slate-900 dark:text-white serif-font">{{ $pendingApprovalsCount }} Applications</h3>
                <div class="pt-2 border-t border-slate-100 dark:border-slate-700/50 flex justify-between items-center text-[10px] text-slate-500 dark:text-slate-400 font-semibold">
                    <span>Reserve: ₱{{ number_format($reservePool, 2) }}</span>
                    <span class="text-rose-500 font-bold">Action Needed</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Active Details Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        
        <!-- Left Column: Recently Joined Members -->
        <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-100 dark:border-slate-700/50 shadow-sm p-6 sm:p-8 space-y-6">
            <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-700/50 pb-4">
                <div>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white serif-font">Recently Joined Members</h3>
                    <p class="text-[11px] text-slate-400 dark:text-slate-500 mt-0.5">Most recent user registrations in the cooperative.</p>
                </div>
                <a href="{{ route('admin.members') }}" class="px-3.5 py-1.5 rounded-lg border border-slate-200 dark:border-slate-700 text-xs font-bold text-slate-600 dark:text-slate-350 hover:bg-slate-50 dark:hover:bg-slate-900/30 transition-all">
                    Manage Directory &rarr;
                </a>
            </div>

            @if($recentMembers->isEmpty())
                <div class="py-12 text-center text-slate-400 dark:text-slate-500 text-sm">
                    No members registered yet in the system.
                </div>
            @else
                <div class="divide-y divide-slate-100 dark:divide-slate-700/50">
                    @foreach($recentMembers as $member)
                        <div class="py-3.5 flex items-center justify-between group">
                            <div class="flex items-center gap-3.5">
                                <div class="w-10 h-10 rounded-full bg-slate-100 dark:bg-slate-900 flex items-center justify-center text-slate-600 dark:text-slate-400 font-bold text-sm">
                                    {{ strtoupper(substr($member->name, 0, 1)) }}
                                </div>
                                <div>
                                    <h4 class="text-sm font-bold text-slate-900 dark:text-slate-100 group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">{{ $member->name }}</h4>
                                    <p class="text-xs text-slate-400 dark:text-slate-500">ID: {{ $member->company_id ?: 'N/A' }} | {{ $member->email }}</p>
                                </div>
                            </div>
                            <div>
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-50 dark:bg-emerald-950/20 text-emerald-700 dark:text-emerald-400 border border-emerald-100/40 dark:border-emerald-900/20">
                                    Member
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Right Column: Audit Trail Preview -->
        <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-100 dark:border-slate-700/50 shadow-sm p-6 sm:p-8 space-y-6">
            <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-700/50 pb-4">
                <div>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white serif-font">Security Audit Log</h3>
                    <p class="text-[11px] text-slate-400 dark:text-slate-500 mt-0.5">Active system security, privilege, and transaction checks.</p>
                </div>
                <span class="inline-flex items-center gap-1 text-[10px] font-extrabold text-slate-500 dark:text-slate-400 bg-slate-100 dark:bg-slate-900 px-2 py-1 rounded-md border border-slate-200 dark:border-slate-800 uppercase tracking-wider">
                    Full Log Enabled
                </span>
            </div>

            <div class="space-y-4">
                @foreach($recentAuditLogs as $log)
                    <div class="p-3.5 bg-slate-50 dark:bg-slate-900/40 border border-slate-100 dark:border-slate-800 rounded-2xl flex items-start gap-3">
                        <div class="p-1.5 rounded-lg bg-slate-950 text-white flex items-center justify-center mt-0.5">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                        </div>
                        <div class="flex-1 space-y-1">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-extrabold text-slate-800 dark:text-slate-200 tracking-wide uppercase">{{ $log['action'] }}</span>
                                <span class="text-[10px] text-slate-400 dark:text-slate-500 font-semibold">{{ $log['time'] }}</span>
                            </div>
                            <p class="text-xs text-slate-600 dark:text-slate-350 font-medium leading-relaxed">{{ $log['description'] }}</p>
                            <p class="text-[10px] text-slate-400 dark:text-slate-500 font-bold">Operator: {{ $log['user'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

    </div>

</div>
@endsection
