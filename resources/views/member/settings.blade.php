@extends('layouts.user')

@section('title', 'My Settings - ML Sako')

@section('header')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight serif-font">Portal Settings</h1>
        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1.5 font-medium">Manage your portal preferences, secure credentials, and contact details.</p>
    </div>
</div>
@endsection

@section('content')
<div class="space-y-8 animate-fade-in max-w-4xl">

    <!-- Success Session Notification -->
    @if(session('success'))
        <div class="p-4 bg-emerald-50 dark:bg-emerald-950/20 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 rounded-2xl text-xs font-bold flex items-center gap-3 shadow-sm">
            <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    <!-- Validation Error Alert -->
    @if($errors->any())
        <div class="p-4 bg-rose-50 dark:bg-rose-950/20 border border-rose-200 dark:border-rose-900/50 text-rose-800 dark:text-rose-300 rounded-2xl text-xs font-bold space-y-1 shadow-sm">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-rose-600 dark:text-rose-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <span>Please correct the following errors:</span>
            </div>
            <ul class="list-disc pl-5 font-semibold space-y-0.5 mt-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        
        <!-- Profile Summary (Left column) -->
        <div class="md:col-span-1 space-y-6">
            <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-100 dark:border-slate-700/80 shadow-sm p-6 space-y-5">
                <div class="text-center pb-4 border-b border-slate-100 dark:border-slate-700/60">
                    <div class="w-20 h-20 rounded-full bg-emerald-50 dark:bg-emerald-950/30 text-emerald-700 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-800/50 flex items-center justify-center font-black text-2xl mx-auto mb-3 shadow-sm">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                    <h3 class="font-bold text-slate-900 dark:text-white text-base leading-snug">{{ Auth::user()->name }}</h3>
                    <p class="text-[10px] text-slate-400 dark:text-slate-500 font-extrabold uppercase tracking-widest mt-1.5">{{ ucwords(str_replace('_', ' ', Auth::user()->role)) }}</p>
                </div>

                <div class="space-y-4 text-xs font-semibold text-slate-600 dark:text-slate-400">
                    <div class="space-y-1">
                        <span class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider block">Company ID</span>
                        <p class="text-slate-900 dark:text-slate-200 font-mono font-bold">{{ Auth::user()->company_id ?: 'N/A' }}</p>
                    </div>
                    <div class="space-y-1">
                        <span class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider block">Email Address</span>
                        <p class="text-slate-900 dark:text-slate-200 truncate" title="{{ Auth::user()->email }}">{{ Auth::user()->email }}</p>
                    </div>
                </div>
            </div>

            <!-- Portal Trust/Info Card -->
            <div class="bg-slate-50 dark:bg-slate-800/40 rounded-3xl border border-slate-100 dark:border-slate-700/50 p-6 space-y-3">
                <h4 class="text-xs font-bold text-slate-800 dark:text-slate-200 uppercase tracking-wider">Securing Your Account</h4>
                <p class="text-[11px] text-slate-500 dark:text-slate-400 leading-relaxed">
                    ML Sako implements end-to-end cryptographic and physical audit trails. Your profile contact details are validated during payouts, and your registered e-signature is embedded inside certified loan agreements.
                </p>
            </div>
        </div>

        <!-- Editable Form Section (Right column) -->
        <div class="md:col-span-2 space-y-6">
            <form action="{{ route('member.settings.update') }}" method="POST" enctype="multipart/form-data" class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-100 dark:border-slate-700/80 shadow-sm p-6 sm:p-8 space-y-6">
                @csrf
                
                <div>
                    <h3 class="text-base font-bold text-slate-900 dark:text-white serif-font">Update Contact Profile</h3>
                    <p class="text-[11px] text-slate-400 dark:text-slate-500 mt-0.5">Keep your cooperative records current so staff can verify your filings.</p>
                </div>

                <div class="space-y-5">
                    <!-- Contact Number -->
                    <div class="space-y-1.5">
                        <label for="contact_number" class="text-[10px] font-extrabold uppercase text-slate-700 dark:text-slate-300 tracking-wider block">Contact Number</label>
                        <input type="text" name="contact_number" id="contact_number" 
                            value="{{ old('contact_number', Auth::user()->contact_number) }}" 
                            placeholder="e.g. 0917 123 4567" 
                            class="w-full px-4 py-2.5 text-xs font-bold border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100 rounded-xl outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/10 transition-all placeholder-slate-400 dark:placeholder-slate-500">
                    </div>

                    <!-- Registered Address -->
                    <div class="space-y-1.5">
                        <label for="address" class="text-[10px] font-extrabold uppercase text-slate-700 dark:text-slate-300 tracking-wider block">Registered Home Address</label>
                        <textarea name="address" id="address" rows="3" 
                            placeholder="Street, Barangay, City, Province" 
                            class="w-full px-4 py-2.5 text-xs font-bold border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100 rounded-xl outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/10 transition-all resize-none placeholder-slate-400 dark:placeholder-slate-500">{{ old('address', Auth::user()->address) }}</textarea>
                    </div>

                    <!-- Signature Upload Block -->
                    <div class="pt-2 space-y-4">
                        <div>
                            <label class="text-[10px] font-extrabold uppercase text-slate-700 dark:text-slate-300 tracking-wider block">Official E-Signature</label>
                            <p class="text-[11px] text-slate-400 dark:text-slate-500 mt-0.5">This signature will be embedded into your official loan agreements and co-maker endorsements.</p>
                        </div>

                        <!-- SignWell Tip Section -->
                        <div class="flex items-start gap-3 p-4 bg-emerald-50/40 dark:bg-slate-900/40 border border-emerald-100/50 dark:border-slate-800 rounded-2xl text-[11px] text-slate-600 dark:text-slate-400 leading-relaxed shadow-sm">
                            <div class="w-8 h-8 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 flex items-center justify-center flex-shrink-0 border border-emerald-100/50 dark:border-emerald-800/30">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="font-extrabold text-slate-850 dark:text-slate-200">Need a clean digital signature?</p>
                                <p class="mt-0.5 text-slate-500 dark:text-slate-400">You can draw and download a free transparent signature image from 
                                    <a href="https://www.signwell.com/online-signature/" target="_blank" class="inline-flex items-center gap-0.5 font-bold text-emerald-600 dark:text-emerald-400 hover:text-emerald-700 dark:hover:text-emerald-300 hover:underline">
                                        <span>SignWell</span>
                                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                        </svg>
                                    </a>, then upload the PNG here.
                                </p>
                            </div>
                        </div>

                        <!-- Signature display and upload area -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 items-center">
                            
                            <!-- Current Signature Frame -->
                            <div class="bg-slate-50 dark:bg-slate-900 border border-dashed border-slate-200 dark:border-slate-700 p-4 rounded-2xl flex flex-col items-center justify-center min-h-[120px] text-center">
                                @if(Auth::user()->signature)
                                    <p class="text-[9px] font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider mb-2">My Registered E-Sign</p>
                                    <img src="{{ asset('storage/' . Auth::user()->signature) }}" alt="My E-Sign" class="max-h-16 w-auto object-contain bg-white dark:bg-slate-800 p-2.5 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm">
                                @else
                                    <div class="w-10 h-10 rounded-full bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex items-center justify-center text-slate-400 mb-1.5">
                                        🖊️
                                    </div>
                                    <p class="text-[10px] text-slate-500 dark:text-slate-400 font-bold uppercase tracking-wider">No Signature Uploaded</p>
                                    <p class="text-[9px] text-slate-400 dark:text-slate-500 mt-0.5 max-w-[150px]">Required to certify loans & agreements.</p>
                                @endif
                            </div>

                            <!-- Upload Input Field -->
                            <div class="space-y-1.5">
                                <label for="signature" class="text-[10px] font-extrabold uppercase text-slate-400 dark:text-slate-500 tracking-wider">Upload signature image</label>
                                <input type="file" name="signature" id="signature" accept="image/png, image/jpeg, image/jpg, image/svg+xml"
                                    class="w-full text-xs text-slate-500 dark:text-slate-400 file:mr-4 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-[10px] file:font-extrabold file:bg-emerald-50 dark:file:bg-emerald-950/40 file:text-emerald-700 dark:file:text-emerald-400 hover:file:bg-emerald-100 dark:hover:file:bg-emerald-950/60 file:cursor-pointer cursor-pointer border border-slate-200 dark:border-slate-700 rounded-xl p-1 bg-white dark:bg-slate-900">
                                <p class="text-[9px] text-slate-400 dark:text-slate-500 font-medium">Accepts transparent PNG or JPG. Max file size: 2MB.</p>
                            </div>

                        </div>
                    </div>
                </div>

                <!-- Action Button -->
                <div class="pt-5 border-t border-slate-100 dark:border-slate-700/60 flex justify-end">
                    <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 dark:bg-emerald-600 dark:hover:bg-emerald-500 text-white font-extrabold text-xs px-6 py-3 rounded-xl shadow-md shadow-emerald-600/10 hover:shadow-emerald-600/20 active:scale-[0.98] transition-all flex items-center gap-2 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                        </svg>
                        Save Preferences
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>
@endsection
