@if (auth()->check() && !session('pin_verified'))
@php
    $authUser = auth()->user();
    $email = $authUser->email ?? '';
    $hasEmail = !empty($email);
    
    $obfuscatedEmail = '';
    if ($hasEmail) {
        $parts = explode('@', $email);
        $name = $parts[0];
        $domain = $parts[1] ?? '';
        if (strlen($name) > 3) {
            $obfuscatedName = substr($name, 0, 1) . str_repeat('•', min(strlen($name) - 2, 6)) . substr($name, -1);
        } else {
            $obfuscatedName = substr($name, 0, 1) . str_repeat('•', max(strlen($name) - 1, 1));
        }
        $obfuscatedEmail = $obfuscatedName . '@' . $domain;
    }
    
    $hasPin = !is_null($authUser->pin);
    $hasActiveOtp = session()->has('login_otp') && session('login_otp_expires_at') > now();
    
    // Choose initial active tab:
    // If user has an active OTP sent, open OTP verify; otherwise default to PIN
    $initialMode = ($hasActiveOtp && $hasEmail) ? 'otp' : 'pin';
@endphp

<style>
    @keyframes pin-shake {
        0%, 100% { transform: translateX(0); }
        15%, 45%, 75% { transform: translateX(-6px); }
        30%, 60%, 90% { transform: translateX(6px); }
    }
    .animate-pin-shake {
        animation: pin-shake 0.45s cubic-bezier(0.36, 0.07, 0.19, 0.97) both;
    }

    @keyframes pin-scan {
        0% { transform: translateX(-100%); }
        100% { transform: translateX(100%); }
    }
    .animate-pin-scan {
        animation: pin-scan 1.4s ease-in-out infinite;
    }

    @keyframes success-pop {
        0% { transform: scale(0.9); opacity: 0; }
        60% { transform: scale(1.05); }
        100% { transform: scale(1); opacity: 1; }
    }
    .animate-success-pop {
        animation: success-pop 0.35s ease-out forwards;
    }

    /* Remove native numeric spin-buttons */
    .digit-cell::-webkit-outer-spin-button,
    .digit-cell::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
    .digit-cell {
        -moz-appearance: textfield;
        caret-color: #10b981;
    }
</style>

<div id="pin-security-backdrop" class="fixed inset-0 z-[9999] flex flex-col items-center justify-center p-4 overflow-y-auto bg-slate-950/85 backdrop-blur-2xl transition-opacity duration-300">
    
    <!-- Ambient Radial Halo Effect -->
    <div class="pointer-events-none absolute inset-0 flex items-center justify-center">
        <div class="w-[500px] h-[500px] bg-emerald-500/10 rounded-full blur-[120px]"></div>
    </div>

    <!-- Main Verification Surface -->
    <div class="relative w-full max-w-md bg-slate-900/90 text-slate-100 rounded-3xl border border-slate-700/60 shadow-[0_25px_60px_-15px_rgba(0,0,0,0.8),0_0_40px_rgba(16,185,129,0.12)] p-6 sm:p-8 space-y-6 text-center backdrop-blur-xl transition-all duration-300">

        <!-- Top Header & Security Badge -->
        <div class="space-y-3">
            <div class="flex items-center justify-center gap-2">
                <div class="relative flex items-center justify-center w-12 h-12 rounded-2xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 shadow-inner">
                    <img src="{{ asset('img/sako-logo-nobg.png') }}" alt="ML Sako Logo" class="h-7 w-7 object-contain drop-shadow">
                    <span class="absolute -top-1 -right-1 flex h-3 w-3">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span>
                    </span>
                </div>
            </div>

            <div class="space-y-1">
                <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-[10px] font-bold uppercase tracking-wider">
                    <i class="fa-solid fa-shield-halved text-[9px]"></i>
                    <span>Cooperative Security Gateway</span>
                </div>
                <h1 id="overlay-main-title" class="text-xl sm:text-2xl font-black text-white tracking-tight serif-font">
                    Enter Security PIN
                </h1>
                <p id="overlay-main-desc" class="text-xs text-slate-400 max-w-xs mx-auto leading-relaxed">
                    Ready and waiting for your 6-digit PIN to authenticate this session.
                </p>
            </div>
        </div>

        <!-- Segmented Tab Switcher (PIN vs Email OTP) -->
        <div class="flex items-center p-1 bg-slate-950/60 border border-slate-800 rounded-2xl">
            <button type="button" id="tab-btn-pin" onclick="switchVerificationTab('pin')" 
                class="flex-1 flex items-center justify-center gap-2 py-2 text-xs font-bold rounded-xl transition-all duration-200 cursor-pointer text-white bg-emerald-600 shadow-md shadow-emerald-950/40">
                <i class="fa-solid fa-key text-[11px]"></i>
                <span>{{ $hasPin ? 'Security PIN' : 'Set Up PIN' }}</span>
            </button>
            @if ($hasEmail)
                <button type="button" id="tab-btn-otp" onclick="switchVerificationTab('otp')" 
                    class="flex-1 flex items-center justify-center gap-2 py-2 text-xs font-semibold rounded-xl transition-all duration-200 cursor-pointer text-slate-400 hover:text-slate-200 hover:bg-slate-800/40">
                    <i class="fa-solid fa-envelope text-[11px]"></i>
                    <span>Email OTP</span>
                </button>
            @endif
        </div>

        <!-- Dynamic Feedback / Error Banner -->
        <div id="overlay-feedback-banner" class="hidden p-3 rounded-2xl text-xs font-semibold text-center leading-relaxed transition-all">
        </div>

        <!-- Loading / Verifying Shimmer Overlay (Hidden by default, shown during auto-verification) -->
        <div id="overlay-loading-indicator" class="hidden py-4 space-y-3">
            <div class="relative w-full h-1 bg-slate-800 rounded-full overflow-hidden">
                <div class="absolute inset-y-0 w-1/2 bg-gradient-to-r from-transparent via-emerald-400 to-transparent animate-pin-scan"></div>
            </div>
            <div class="flex items-center justify-center gap-2 text-xs font-bold text-emerald-400">
                <i class="fa-solid fa-circle-notch fa-spin text-sm"></i>
                <span id="overlay-loading-text">Verifying credentials...</span>
            </div>
        </div>

        <!-- Success Indicator (Shown briefly when verification passes) -->
        <div id="overlay-success-indicator" class="hidden py-4 space-y-2 animate-success-pop">
            <div class="w-12 h-12 rounded-full bg-emerald-500/20 border border-emerald-400/50 text-emerald-400 flex items-center justify-center mx-auto shadow-[0_0_20px_rgba(16,185,129,0.3)]">
                <i class="fa-solid fa-check text-xl"></i>
            </div>
            <p class="text-sm font-extrabold text-white">Identity Confirmed</p>
            <p class="text-xs text-emerald-400 font-medium">Entering your cooperative portal...</p>
        </div>

        <!-- SECTION 1: PIN VERIFICATION (FOR EXISTING PIN) -->
        @if ($hasPin)
            <div id="pane-pin-verify" class="space-y-4">
                <div class="space-y-2">
                    <div id="pin-verify-boxes" class="flex justify-center items-center gap-2 sm:gap-2.5">
                        @for ($i = 0; $i < 6; $i++)
                            <input type="password" maxlength="1" pattern="[0-9]*" inputmode="numeric" autocomplete="one-time-code"
                                data-index="{{ $i }}"
                                class="digit-cell pin-digit w-11 h-14 sm:w-12 sm:h-16 text-center text-2xl font-mono font-bold bg-slate-950/70 border border-slate-700/80 rounded-2xl text-white placeholder-slate-600 focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/30 transition-all duration-150 shadow-inner">
                        @endfor
                    </div>
                </div>

                <div class="flex items-center justify-between text-[11px] text-slate-400 px-1 pt-1">
                    <span class="inline-flex items-center gap-1.5">
                        <i class="fa-solid fa-shield text-[10px] text-emerald-500"></i>
                        <span>Auto-verifying on 6th digit</span>
                    </span>
                    <button type="button" onclick="clearPinInputs('pin-verify-boxes')" class="text-slate-400 hover:text-slate-200 transition-colors cursor-pointer">
                        Clear
                    </button>
                </div>
            </div>
        @else
            <!-- SECTION 2: PIN SETUP (FOR NEW PIN) -->
            <div id="pane-pin-setup" class="space-y-4 text-left">
                <!-- Create PIN -->
                <div class="space-y-1.5">
                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400">
                        1. Create 6-Digit PIN
                    </label>
                    <div id="pin-create-boxes" class="flex justify-center items-center gap-1.5 sm:gap-2">
                        @for ($i = 0; $i < 6; $i++)
                            <input type="password" maxlength="1" pattern="[0-9]*" inputmode="numeric"
                                data-index="{{ $i }}"
                                class="digit-cell pin-create-digit w-10 h-12 sm:w-11 sm:h-13 text-center text-xl font-mono font-bold bg-slate-950/70 border border-slate-700/80 rounded-xl text-white focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/30 transition-all shadow-inner">
                        @endfor
                    </div>
                </div>

                <!-- Confirm PIN -->
                <div class="space-y-1.5">
                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400">
                        2. Confirm 6-Digit PIN
                    </label>
                    <div id="pin-confirm-boxes" class="flex justify-center items-center gap-1.5 sm:gap-2">
                        @for ($i = 0; $i < 6; $i++)
                            <input type="password" maxlength="1" pattern="[0-9]*" inputmode="numeric"
                                data-index="{{ $i }}"
                                class="digit-cell pin-confirm-digit w-10 h-12 sm:w-11 sm:h-13 text-center text-xl font-mono font-bold bg-slate-950/70 border border-slate-700/80 rounded-xl text-white focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/30 transition-all shadow-inner">
                        @endfor
                    </div>
                </div>

                <div class="text-[11px] text-slate-400 text-center pt-1">
                    Auto-submits once matching confirmation is entered.
                </div>
            </div>
        @endif

        <!-- SECTION 3: EMAIL OTP PANE -->
        @if ($hasEmail)
            <div id="pane-otp" class="hidden space-y-4">
                
                <!-- Subpane A: Need to Send OTP -->
                <div id="otp-send-subpane" class="{{ $hasActiveOtp ? 'hidden' : '' }} space-y-4">
                    <div class="p-4 bg-slate-950/60 border border-slate-800 rounded-2xl space-y-1 text-center">
                        <span class="text-[10px] font-bold text-emerald-400 uppercase tracking-widest">
                            Registered Delivery Email
                        </span>
                        <p class="text-sm font-mono font-bold text-white tracking-wide">
                            {{ $obfuscatedEmail }}
                        </p>
                    </div>

                    <button type="button" id="btn-request-otp" onclick="requestOtpCode()" 
                        class="w-full py-3 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs rounded-xl shadow-lg shadow-emerald-950/60 active:scale-[0.98] transition-all duration-200 cursor-pointer flex items-center justify-center gap-2">
                        <i class="fa-solid fa-paper-plane text-xs"></i>
                        <span>Send 6-Digit Verification Code</span>
                    </button>
                </div>

                <!-- Subpane B: OTP Inputs (Ready & Waiting) -->
                <div id="otp-verify-subpane" class="{{ $hasActiveOtp ? '' : 'hidden' }} space-y-4">
                    <div class="space-y-2">
                        <div id="otp-verify-boxes" class="flex justify-center items-center gap-2 sm:gap-2.5">
                            @for ($i = 0; $i < 6; $i++)
                                <input type="text" maxlength="1" pattern="[0-9]*" inputmode="numeric" autocomplete="one-time-code"
                                    data-index="{{ $i }}"
                                    class="digit-cell otp-digit w-11 h-14 sm:w-12 sm:h-16 text-center text-2xl font-mono font-bold bg-slate-950/70 border border-slate-700/80 rounded-2xl text-white placeholder-slate-600 focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/30 transition-all duration-150 shadow-inner">
                            @endfor
                        </div>
                    </div>

                    <div class="flex items-center justify-between text-[11px] text-slate-400 px-1 pt-1">
                        <span id="otp-timer-label" class="inline-flex items-center gap-1.5 font-medium">
                            <i class="fa-regular fa-clock text-[10px] text-emerald-400"></i>
                            <span id="otp-countdown-text">Resend in 60s</span>
                        </span>
                        <button type="button" id="btn-resend-otp" onclick="requestOtpCode()" class="hidden text-emerald-400 hover:text-emerald-300 font-bold transition-colors cursor-pointer">
                            Resend Code
                        </button>
                    </div>
                </div>

            </div>
        @endif

        <!-- Card Bottom Bar: Authenticated Session Info & Logout Escape -->
        <div class="pt-4 border-t border-slate-800/80 flex items-center justify-between text-left">
            <div class="flex items-center gap-2.5 min-w-0">
                <div class="w-8 h-8 rounded-full bg-emerald-500/20 border border-emerald-500/40 text-emerald-300 font-bold text-xs flex items-center justify-center shrink-0">
                    {{ strtoupper(substr($authUser->name, 0, 1)) }}
                </div>
                <div class="min-w-0">
                    <p class="text-xs font-bold text-white truncate max-w-[170px] sm:max-w-[200px]">{{ $authUser->name }}</p>
                    <p class="text-[10px] text-slate-400 uppercase tracking-wider font-semibold">{{ strtoupper(str_replace('_', ' ', $authUser->role)) }}</p>
                </div>
            </div>

            <form action="{{ route('logout') }}" method="POST" class="shrink-0">
                @csrf
                <button type="submit" title="Sign out this session" 
                    class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-[11px] font-bold text-slate-400 hover:text-rose-400 hover:bg-rose-500/10 transition-all duration-150 cursor-pointer">
                    <i class="fa-solid fa-arrow-right-from-bracket text-xs"></i>
                    <span>Sign Out</span>
                </button>
            </form>
        </div>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const hasPin = {{ $hasPin ? 'true' : 'false' }};
    const hasEmail = {{ $hasEmail ? 'true' : 'false' }};
    let currentTab = '{{ $initialMode }}'; // 'pin' or 'otp'
    let isVerifying = false;
    let otpCountdownInterval = null;

    // Elements
    const backdrop = document.getElementById('pin-security-backdrop');
    const titleEl = document.getElementById('overlay-main-title');
    const descEl = document.getElementById('overlay-main-desc');
    const bannerEl = document.getElementById('overlay-feedback-banner');
    const loaderEl = document.getElementById('overlay-loading-indicator');
    const loaderText = document.getElementById('overlay-loading-text');
    const successEl = document.getElementById('overlay-success-indicator');

    const tabBtnPin = document.getElementById('tab-btn-pin');
    const tabBtnOtp = document.getElementById('tab-btn-otp');

    const panePinVerify = document.getElementById('pane-pin-verify');
    const panePinSetup = document.getElementById('pane-pin-setup');
    const paneOtp = document.getElementById('pane-otp');
    const otpSendSubpane = document.getElementById('otp-send-subpane');
    const otpVerifySubpane = document.getElementById('otp-verify-subpane');

    // Tab Switcher
    window.switchVerificationTab = function(mode) {
        if (isVerifying) return;
        currentTab = mode;
        clearFeedback();

        if (mode === 'pin') {
            // Update Tab styles
            tabBtnPin.className = "flex-1 flex items-center justify-center gap-2 py-2 text-xs font-bold rounded-xl transition-all duration-200 cursor-pointer text-white bg-emerald-600 shadow-md shadow-emerald-950/40";
            if (tabBtnOtp) {
                tabBtnOtp.className = "flex-1 flex items-center justify-center gap-2 py-2 text-xs font-semibold rounded-xl transition-all duration-200 cursor-pointer text-slate-400 hover:text-slate-200 hover:bg-slate-800/40";
            }

            if (paneOtp) paneOtp.classList.add('hidden');

            if (hasPin) {
                if (panePinVerify) panePinVerify.classList.remove('hidden');
                titleEl.innerText = "Enter Security PIN";
                descEl.innerText = "Ready and waiting for your 6-digit PIN to authenticate this session.";
                focusFirstInput('pin-verify-boxes');
            } else {
                if (panePinSetup) panePinSetup.classList.remove('hidden');
                titleEl.innerText = "Create Security PIN";
                descEl.innerText = "Protect your cooperative account by setting up a 6-digit access PIN.";
                focusFirstInput('pin-create-boxes');
            }
        } else if (mode === 'otp') {
            tabBtnPin.className = "flex-1 flex items-center justify-center gap-2 py-2 text-xs font-semibold rounded-xl transition-all duration-200 cursor-pointer text-slate-400 hover:text-slate-200 hover:bg-slate-800/40";
            if (tabBtnOtp) {
                tabBtnOtp.className = "flex-1 flex items-center justify-center gap-2 py-2 text-xs font-bold rounded-xl transition-all duration-200 cursor-pointer text-white bg-emerald-600 shadow-md shadow-emerald-950/40";
            }

            if (panePinVerify) panePinVerify.classList.add('hidden');
            if (panePinSetup) panePinSetup.classList.add('hidden');
            if (paneOtp) paneOtp.classList.remove('hidden');

            // If OTP verify subpane is visible, focus its first box
            if (otpVerifySubpane && !otpVerifySubpane.classList.contains('hidden')) {
                titleEl.innerText = "Enter Email Verification Code";
                descEl.innerText = "Please enter the 6-digit code sent to your registered email address.";
                focusFirstInput('otp-verify-boxes');
            } else {
                titleEl.innerText = "Email Authentication";
                descEl.innerText = "Generate and receive a secure one-time passcode on your registered email.";
            }
        }
    };

    // Feedback Helpers
    function showFeedback(message, type = 'error') {
        if (!bannerEl) return;
        bannerEl.classList.remove('hidden', 'bg-rose-500/15', 'border-rose-500/40', 'text-rose-400', 'bg-emerald-500/15', 'border-emerald-500/40', 'text-emerald-400');
        if (type === 'error') {
            bannerEl.classList.add('bg-rose-500/15', 'border', 'border-rose-500/40', 'text-rose-300');
            bannerEl.innerHTML = `<i class="fa-solid fa-triangle-exclamation mr-1.5"></i> ${message}`;
        } else {
            bannerEl.classList.add('bg-emerald-500/15', 'border', 'border-emerald-500/40', 'text-emerald-300');
            bannerEl.innerHTML = `<i class="fa-solid fa-circle-check mr-1.5"></i> ${message}`;
        }
    }

    function clearFeedback() {
        if (bannerEl) bannerEl.classList.add('hidden');
    }

    function triggerShake(containerId) {
        const container = document.getElementById(containerId);
        if (!container) return;
        container.classList.remove('animate-pin-shake');
        void container.offsetWidth; // Force DOM reflow
        container.classList.add('animate-pin-shake');
        setTimeout(() => container.classList.remove('animate-pin-shake'), 500);
    }

    function focusFirstInput(containerId) {
        setTimeout(() => {
            const container = document.getElementById(containerId);
            if (container) {
                const first = container.querySelector('input');
                if (first) {
                    first.focus();
                    first.select();
                }
            }
        }, 80);
    }

    window.clearPinInputs = function(containerId) {
        const container = document.getElementById(containerId);
        if (!container) return;
        const inputs = container.querySelectorAll('input');
        inputs.forEach(inp => inp.value = '');
        focusFirstInput(containerId);
        clearFeedback();
    };

    // Auto-Verification Pipeline
    function setVerifyingState(verifying, text = 'Verifying credentials...') {
        isVerifying = verifying;
        if (verifying) {
            clearFeedback();
            if (loaderEl) {
                loaderText.innerText = text;
                loaderEl.classList.remove('hidden');
            }
            // Disable inputs
            document.querySelectorAll('.digit-cell').forEach(inp => {
                inp.disabled = true;
                inp.classList.add('opacity-50');
            });
        } else {
            if (loaderEl) loaderEl.classList.add('hidden');
            // Re-enable inputs
            document.querySelectorAll('.digit-cell').forEach(inp => {
                inp.disabled = false;
                inp.classList.remove('opacity-50');
            });
        }
    }

    function showPassTransition(message = 'Identity Verified') {
        if (loaderEl) loaderEl.classList.add('hidden');
        if (panePinVerify) panePinVerify.classList.add('hidden');
        if (panePinSetup) panePinSetup.classList.add('hidden');
        if (paneOtp) paneOtp.classList.add('hidden');
        if (bannerEl) bannerEl.classList.add('hidden');

        if (successEl) {
            successEl.querySelector('p.font-extrabold').innerText = message;
            successEl.classList.remove('hidden');
        }

        // Brief delay for visual satisfaction, then reload
        setTimeout(() => {
            window.location.reload();
        }, 550);
    }

    // Attach Smart Auto-Advancing Digit Handlers
    function setupDigitDeck(containerId, onCompleteCallback) {
        const container = document.getElementById(containerId);
        if (!container) return;
        const inputs = Array.from(container.querySelectorAll('input'));

        inputs.forEach((input, idx) => {
            // Typing input
            input.addEventListener('input', (e) => {
                const val = input.value.replace(/[^0-9]/g, '');
                input.value = val ? val.slice(-1) : '';

                if (input.value && idx < inputs.length - 1) {
                    inputs[idx + 1].focus();
                    inputs[idx + 1].select();
                }

                // Check if all filled
                const fullPin = inputs.map(i => i.value).join('');
                if (fullPin.length === inputs.length) {
                    onCompleteCallback(fullPin);
                }
            });

            // Backspace & Left/Right navigation
            input.addEventListener('keydown', (e) => {
                if (e.key === 'Backspace') {
                    if (!input.value && idx > 0) {
                        inputs[idx - 1].value = '';
                        inputs[idx - 1].focus();
                        e.preventDefault();
                    } else {
                        input.value = '';
                    }
                } else if (e.key === 'ArrowLeft' && idx > 0) {
                    inputs[idx - 1].focus();
                    inputs[idx - 1].select();
                } else if (e.key === 'ArrowRight' && idx < inputs.length - 1) {
                    inputs[idx + 1].focus();
                    inputs[idx + 1].select();
                }
            });

            // Clipboard Paste Support
            input.addEventListener('paste', (e) => {
                e.preventDefault();
                const pasteData = (e.clipboardData || window.clipboardData)
                    .getData('text')
                    .replace(/[^0-9]/g, '')
                    .slice(0, inputs.length);

                if (pasteData) {
                    pasteData.split('').forEach((char, i) => {
                        if (inputs[i]) inputs[i].value = char;
                    });
                    const targetIdx = Math.min(pasteData.length, inputs.length - 1);
                    inputs[targetIdx].focus();

                    const fullPin = inputs.map(i => i.value).join('');
                    if (fullPin.length === inputs.length) {
                        onCompleteCallback(fullPin);
                    }
                }
            });
        });
    }

    // 1. PIN VERIFY AUTO-SUBMISSION
    if (hasPin) {
        setupDigitDeck('pin-verify-boxes', function(pin) {
            setVerifyingState(true, 'Verifying 6-digit PIN...');

            fetch('{{ route('pin.verify') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ pin: pin })
            })
            .then(res => res.json().then(data => ({ status: res.status, body: data })))
            .then(res => {
                if (res.status === 200) {
                    showPassTransition('PIN Verified');
                } else {
                    setVerifyingState(false);
                    triggerShake('pin-verify-boxes');
                    showFeedback(res.body.message || 'Incorrect security PIN. Please try again.', 'error');
                    window.clearPinInputs('pin-verify-boxes');

                    if (res.status === 423) {
                        // Account locked out
                        setTimeout(() => {
                            window.location.href = '{{ route('home') }}';
                        }, 2500);
                    }
                }
            })
            .catch(() => {
                setVerifyingState(false);
                triggerShake('pin-verify-boxes');
                showFeedback('Network error during verification. Please try again.', 'error');
                focusFirstInput('pin-verify-boxes');
            });
        });
    }

    // 2. PIN SETUP AUTO-SUBMISSION
    if (!hasPin && panePinSetup) {
        let createdPin = '';

        setupDigitDeck('pin-create-boxes', function(pin) {
            createdPin = pin;
            // Advance automatically to confirm boxes
            focusFirstInput('pin-confirm-boxes');
        });

        setupDigitDeck('pin-confirm-boxes', function(confirmPin) {
            const createInputs = Array.from(document.querySelectorAll('.pin-create-digit'));
            createdPin = createInputs.map(i => i.value).join('');

            if (createdPin.length !== 6) {
                showFeedback('Please fill in the 6 digits of Create PIN first.', 'error');
                focusFirstInput('pin-create-boxes');
                return;
            }

            if (createdPin !== confirmPin) {
                triggerShake('pin-confirm-boxes');
                showFeedback('PIN confirmation does not match. Please re-enter.', 'error');
                window.clearPinInputs('pin-confirm-boxes');
                return;
            }

            // Both match - Auto submit setup
            setVerifyingState(true, 'Configuring security PIN...');

            fetch('{{ route('pin.setup') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    pin: createdPin,
                    pin_confirmation: confirmPin
                })
            })
            .then(res => res.json().then(data => ({ status: res.status, body: data })))
            .then(res => {
                if (res.status === 200) {
                    showPassTransition('Security PIN Configured');
                } else {
                    setVerifyingState(false);
                    triggerShake('pin-confirm-boxes');
                    showFeedback(res.body.message || 'Failed to configure PIN.', 'error');
                    window.clearPinInputs('pin-confirm-boxes');
                }
            })
            .catch(() => {
                setVerifyingState(false);
                showFeedback('Network error configuring PIN.', 'error');
            });
        });
    }

    // 3. OTP REQUEST ACTION
    window.requestOtpCode = function() {
        const btn = document.getElementById('btn-request-otp');
        const resendBtn = document.getElementById('btn-resend-otp');
        
        clearFeedback();
        if (btn) btn.disabled = true;
        if (resendBtn) resendBtn.classList.add('hidden');
        setVerifyingState(true, 'Dispatching secure verification code...');

        fetch('{{ route('otp.send') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({})
        })
        .then(res => res.json().then(data => ({ status: res.status, body: data })))
        .then(res => {
            setVerifyingState(false);
            if (btn) btn.disabled = false;

            if (res.status === 200) {
                // Transition subpanes
                if (otpSendSubpane) otpSendSubpane.classList.add('hidden');
                if (otpVerifySubpane) otpVerifySubpane.classList.remove('hidden');

                titleEl.innerText = "Enter Verification Code";
                descEl.innerText = "Please enter the 6-digit code sent to your registered email address.";

                showFeedback(res.body.message || 'Verification code sent to your email.', 'success');
                startOtpCountdown(60);
                focusFirstInput('otp-verify-boxes');
            } else {
                showFeedback(res.body.message || 'Unable to send verification code.', 'error');
            }
        })
        .catch(() => {
            setVerifyingState(false);
            if (btn) btn.disabled = false;
            showFeedback('Network error while requesting code.', 'error');
        });
    };

    // 4. OTP VERIFY AUTO-SUBMISSION
    if (hasEmail) {
        setupDigitDeck('otp-verify-boxes', function(otpCode) {
            setVerifyingState(true, 'Verifying one-time passcode...');

            fetch('{{ route('otp.verify') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ otp: otpCode })
            })
            .then(res => res.json().then(data => ({ status: res.status, body: data })))
            .then(res => {
                if (res.status === 200) {
                    showPassTransition('Email Code Verified');
                } else {
                    setVerifyingState(false);
                    triggerShake('otp-verify-boxes');
                    showFeedback(res.body.message || 'Incorrect verification code. Please try again.', 'error');
                    window.clearPinInputs('otp-verify-boxes');

                    if (res.status === 423) {
                        setTimeout(() => {
                            window.location.href = '{{ route('home') }}';
                        }, 2500);
                    }
                }
            })
            .catch(() => {
                setVerifyingState(false);
                triggerShake('otp-verify-boxes');
                showFeedback('Network error during OTP verification.', 'error');
                focusFirstInput('otp-verify-boxes');
            });
        });
    }

    // OTP Countdown Helper
    function startOtpCountdown(seconds) {
        clearInterval(otpCountdownInterval);
        const timerLabel = document.getElementById('otp-timer-label');
        const countdownText = document.getElementById('otp-countdown-text');
        const resendBtn = document.getElementById('btn-resend-otp');

        if (timerLabel) timerLabel.classList.remove('hidden');
        if (resendBtn) resendBtn.classList.add('hidden');
        if (countdownText) countdownText.innerText = `Resend in ${seconds}s`;

        otpCountdownInterval = setInterval(() => {
            seconds--;
            if (countdownText) countdownText.innerText = `Resend in ${seconds}s`;

            if (seconds <= 0) {
                clearInterval(otpCountdownInterval);
                if (timerLabel) timerLabel.classList.add('hidden');
                if (resendBtn) resendBtn.classList.remove('hidden');
            }
        }, 1000);
    }

    // Initialize with the starting tab & focus
    switchVerificationTab(currentTab);

    // If OTP was already active from a previous session, start the countdown
    @if ($hasActiveOtp && session()->has('login_otp_sent_at'))
        const sentAt = {{ session('login_otp_sent_at')->timestamp }} * 1000;
        const timePassed = Date.now() - sentAt;
        const remaining = Math.max(0, 60 - Math.floor(timePassed / 1000));
        if (remaining > 0) {
            startOtpCountdown(remaining);
        } else {
            const timerLabel = document.getElementById('otp-timer-label');
            const resendBtn = document.getElementById('btn-resend-otp');
            if (timerLabel) timerLabel.classList.add('hidden');
            if (resendBtn) resendBtn.classList.remove('hidden');
        }
    @endif
});
</script>
@endif
