@if (auth()->check() && !session('pin_verified'))
@php
    $email = auth()->user()->email;
    $parts = explode('@', $email);
    $name = $parts[0];
    $domain = isset($parts[1]) ? $parts[1] : '';
    
    if (strlen($name) > 3) {
        $obfuscatedName = substr($name, 0, 1) . str_repeat('*', min(strlen($name) - 2, 8)) . substr($name, -1);
    } else {
        $obfuscatedName = substr($name, 0, 1) . str_repeat('*', strlen($name) - 1);
    }
    
    $obfuscatedEmail = $obfuscatedName . '@' . $domain;
    
    // Determine initial state based on active OTP session
    $initialState = 'choice';
    if (session()->has('login_otp') && session('login_otp_expires_at') > now()) {
        $initialState = 'otp-verify';
    }
@endphp

<div class="fixed inset-0 z-[9999] bg-slate-900/98 backdrop-blur-lg flex flex-col items-center justify-center p-4 overflow-y-auto">
    <div class="w-full max-w-md bg-white dark:bg-slate-800 rounded-3xl shadow-2xl border border-slate-100 dark:border-slate-700/50 p-5 md:p-6 space-y-4 text-center animate-fade-in transition-all duration-300">
        
        <!-- Logo and Branding -->
        <div class="space-y-1">
            <img src="{{ asset('img/sako-logo-nobg.png') }}" alt="ML Sako Logo" class="h-10 mx-auto object-contain">
            <h2 class="text-[10px] font-bold uppercase tracking-widest text-emerald-600 dark:text-emerald-400">
                Security Verification
            </h2>
            <h1 id="view-title" class="text-lg md:text-xl font-extrabold text-slate-900 dark:text-white serif-font tracking-tight">
                Choose Verification Method
            </h1>
            <p id="view-desc" class="text-[11px] md:text-xs text-slate-500 dark:text-slate-400 max-w-sm mx-auto leading-relaxed">
                For your security, please verify your identity to access your cooperative account.
            </p>
        </div>

        <!-- Global Error / Validation Messages Banner -->
        <div id="error-banner" class="hidden p-2.5 bg-rose-50 dark:bg-rose-950/20 border border-rose-100 dark:border-rose-900/30 rounded-xl text-rose-600 dark:text-rose-400 text-[11px] font-semibold text-center leading-relaxed">
        </div>
        
        <!-- Dynamic Views -->

        <!-- 1. CHOICE VIEW -->
        <div id="view-choice" class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 mt-2">
                <!-- PIN Card -->
                <button type="button" onclick="showView('pin')" class="flex flex-col items-center justify-center p-4 bg-slate-50 dark:bg-slate-900/30 hover:bg-emerald-50/50 dark:hover:bg-emerald-950/10 border border-slate-200 dark:border-slate-700 hover:border-emerald-500 dark:hover:border-emerald-500 rounded-2xl transition-all duration-200 group text-center cursor-pointer">
                    <div class="w-10 h-10 rounded-full bg-emerald-50 dark:bg-emerald-950/40 flex items-center justify-center mb-2.5 group-hover:scale-110 transition-transform duration-200 text-emerald-600 dark:text-emerald-400">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                    <span class="text-xs font-bold text-slate-800 dark:text-slate-200 group-hover:text-emerald-600 dark:group-hover:text-emerald-400">
                        @if (is_null(auth()->user()->pin))
                            Set Security PIN
                        @else
                            Security PIN
                        @endif
                    </span>
                    <span class="text-[10px] text-slate-500 dark:text-slate-400 mt-1 leading-normal max-w-[130px]">
                        @if (is_null(auth()->user()->pin))
                            Create your 6-digit access PIN.
                        @else
                            Enter your personal 6-digit PIN.
                        @endif
                    </span>
                </button>

                <!-- OTP Card -->
                <button type="button" onclick="showView('otp-request')" class="flex flex-col items-center justify-center p-4 bg-slate-50 dark:bg-slate-900/30 hover:bg-emerald-50/50 dark:hover:bg-emerald-950/10 border border-slate-200 dark:border-slate-700 hover:border-emerald-500 dark:hover:border-emerald-500 rounded-2xl transition-all duration-200 group text-center cursor-pointer">
                    <div class="w-10 h-10 rounded-full bg-emerald-50 dark:bg-emerald-950/40 flex items-center justify-center mb-2.5 group-hover:scale-110 transition-transform duration-200 text-emerald-600 dark:text-emerald-400">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 19v-8.93a2 2 0 01.89-1.664l8-5.333a2 2 0 012.22 0l8 5.333A2 2 0 0121 10.07V19M3 19a2 2 0 002 2h14a2 2 0 002-2M3 19l6.75-4.5M21 19l-6.75-4.5M3 10l6.75 4.5M21 10l-6.75 4.5m0 0l-2.25-1.5a2 2 0 00-2.22 0l-2.25 1.5" />
                        </svg>
                    </div>
                    <span class="text-xs font-bold text-slate-800 dark:text-slate-200 group-hover:text-emerald-600 dark:group-hover:text-emerald-400">
                        Email OTP Code
                    </span>
                    <span class="text-[10px] text-slate-500 dark:text-slate-400 mt-1 leading-normal max-w-[130px]">
                        Receive a 6-digit code via email.
                    </span>
                </button>
            </div>
        </div>

        <!-- 2. PIN SETUP FORM VIEW -->
        @if (is_null(auth()->user()->pin))
            <div id="view-pin-setup" class="hidden">
                <form id="pin-setup-form" onsubmit="handleAjaxSubmit(event, '{{ route('pin.setup') }}')" class="space-y-4">
                    @csrf
                    
                    <!-- Create PIN Box -->
                    <div class="space-y-1.5">
                        <label class="block text-[10px] font-bold uppercase text-slate-400 dark:text-slate-500 tracking-wider">
                            Create PIN
                        </label>
                        <div class="flex justify-center gap-1.5" id="pin-setup-inputs-container">
                            @for ($i = 0; $i < 6; $i++)
                                <input type="password" maxlength="1" pattern="[0-9]" inputmode="numeric" 
                                    class="w-9 h-11 text-center text-lg font-bold bg-slate-50 dark:bg-slate-900/40 border border-slate-200 dark:border-slate-700 rounded-lg focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500 transition-all duration-150" required>
                            @endfor
                        </div>
                        <input type="hidden" name="pin" id="hidden-pin-setup-input">
                    </div>

                    <!-- Confirm PIN Box -->
                    <div class="space-y-1.5">
                        <label class="block text-[10px] font-bold uppercase text-slate-400 dark:text-slate-500 tracking-wider">
                            Confirm PIN
                        </label>
                        <div class="flex justify-center gap-1.5" id="pin-confirm-inputs-container">
                            @for ($i = 0; $i < 6; $i++)
                                <input type="password" maxlength="1" pattern="[0-9]" inputmode="numeric" 
                                    class="w-9 h-11 text-center text-lg font-bold bg-slate-50 dark:bg-slate-900/40 border border-slate-200 dark:border-slate-700 rounded-lg focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500 transition-all duration-150" required>
                            @endfor
                        </div>
                        <input type="hidden" name="pin_confirmation" id="hidden-pin-confirm-input">
                    </div>

                    <div class="pt-2 flex flex-col gap-2">
                        <button type="submit" class="w-full py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-lg shadow-emerald-600/10 hover:shadow-emerald-600/20 active:scale-[0.98] transition-all duration-200 cursor-pointer flex items-center justify-center gap-1.5">
                            <span class="btn-text">Set Security PIN</span>
                            <span class="btn-spinner hidden w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
                        </button>
                        <button type="button" onclick="showView('choice')" class="w-full py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-600 dark:text-slate-200 font-bold text-xs rounded-xl transition-all duration-200 cursor-pointer">
                            Back
                        </button>
                    </div>
                </form>
            </div>
        @else
            <!-- 3. PIN VERIFY FORM VIEW -->
            <div id="view-pin-verify" class="hidden">
                <form id="pin-verify-form" onsubmit="handleAjaxSubmit(event, '{{ route('pin.verify') }}')" class="space-y-4">
                    @csrf
                    
                    <div class="space-y-2">
                        <div class="flex justify-center gap-1.5" id="pin-verify-inputs-container">
                            @for ($i = 0; $i < 6; $i++)
                                <input type="password" maxlength="1" pattern="[0-9]" inputmode="numeric" 
                                    class="w-10 h-12 text-center text-xl font-bold bg-slate-50 dark:bg-slate-900/40 border border-slate-200 dark:border-slate-700 rounded-lg focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500 transition-all duration-150" required>
                            @endfor
                        </div>
                        <input type="hidden" name="pin" id="hidden-pin-verify-input">
                    </div>

                    <div class="pt-2 flex flex-col gap-2">
                        <button type="submit" class="w-full py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-lg shadow-emerald-600/10 hover:shadow-emerald-600/20 active:scale-[0.98] transition-all duration-200 cursor-pointer flex items-center justify-center gap-1.5">
                            <span class="btn-text">Verify PIN</span>
                            <span class="btn-spinner hidden w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
                        </button>
                        <button type="button" onclick="showView('choice')" class="w-full py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-600 dark:text-slate-200 font-bold text-xs rounded-xl transition-all duration-200 cursor-pointer">
                            Back
                        </button>
                    </div>
                </form>
            </div>
        @endif

        <!-- 4. OTP REQUEST VIEW -->
        <div id="view-otp-request" class="hidden space-y-4">
            <div class="p-4 bg-emerald-50/50 dark:bg-emerald-950/10 border border-emerald-100/50 dark:border-emerald-900/30 rounded-2xl space-y-1">
                <span class="text-[10px] font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-wide">Registered Email Address</span>
                <p class="text-sm font-extrabold text-slate-800 dark:text-slate-100 tracking-tight">{{ $obfuscatedEmail }}</p>
            </div>

            <div class="flex flex-col gap-2 pt-1">
                <button type="button" id="send-otp-btn" onclick="sendOtpCode()" class="w-full py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-lg shadow-emerald-600/10 hover:shadow-emerald-600/20 active:scale-[0.98] transition-all duration-200 cursor-pointer flex items-center justify-center gap-1.5">
                    <span class="btn-text">Send Code via Email</span>
                    <span class="btn-spinner hidden w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
                </button>
                <button type="button" onclick="showView('choice')" class="w-full py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-600 dark:text-slate-200 font-bold text-xs rounded-xl transition-all duration-200 cursor-pointer">
                    Back
                </button>
            </div>
        </div>

        <!-- 5. OTP VERIFY FORM VIEW -->
        <div id="view-otp-verify" class="hidden">
            <form id="otp-verify-form" onsubmit="handleAjaxSubmit(event, '{{ route('otp.verify') }}')" class="space-y-4">
                @csrf
                
                <div class="space-y-2">
                    <div class="flex justify-center gap-1.5" id="otp-verify-inputs-container">
                        @for ($i = 0; $i < 6; $i++)
                            <input type="text" maxlength="1" pattern="[0-9]" inputmode="numeric" 
                                class="w-10 h-12 text-center text-xl font-bold bg-slate-50 dark:bg-slate-900/40 border border-slate-200 dark:border-slate-700 rounded-lg focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500 transition-all duration-150" required>
                        @endfor
                    </div>
                    <input type="hidden" name="otp" id="hidden-otp-verify-input">
                </div>

                <p id="otp-countdown" class="text-[10px] font-bold text-slate-400 dark:text-slate-500 tracking-wide uppercase">
                    Resend code in 59s
                </p>

                <div class="pt-2 flex flex-col gap-2">
                    <button type="submit" class="w-full py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-lg shadow-emerald-600/10 hover:shadow-emerald-600/20 active:scale-[0.98] transition-all duration-200 cursor-pointer flex items-center justify-center gap-1.5">
                        <span class="btn-text">Verify Code</span>
                        <span class="btn-spinner hidden w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
                    </button>
                    <button type="button" id="resend-otp-btn" onclick="sendOtpCode()" class="hidden w-full py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-600 dark:text-slate-200 font-bold text-xs rounded-xl transition-all duration-200 cursor-pointer">
                        Resend Code
                    </button>
                    <button type="button" onclick="cancelOtpVerify()" class="w-full py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-600 dark:text-slate-200 font-bold text-xs rounded-xl transition-all duration-200 cursor-pointer">
                        Back to Methods
                    </button>
                </div>
            </form>
        </div>

        <!-- Logout Action -->
        <div class="pt-2 border-t border-slate-100 dark:border-slate-700/50">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="inline-flex items-center gap-1 text-[10px] font-bold text-slate-400 hover:text-rose-500 dark:text-slate-500 dark:hover:text-rose-400 transition-colors duration-200 cursor-pointer">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    Logout and abort
                </button>
            </form>
        </div>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Current active view state
    let activeState = '{{ $initialState }}';
    let countdownInterval = null;

    // View descriptions & titles mapping
    const viewsMeta = {
        'choice': {
            title: 'Choose Verification Method',
            desc: 'For your security, please verify your identity to access your cooperative account.'
        },
        'pin': {
            title: @if (is_null(auth()->user()->pin)) 'Set Your Security PIN' @else 'Enter Security PIN' @endif,
            desc: @if (is_null(auth()->user()->pin)) 'Set a secure 6-digit PIN to protect your cooperative account.' @else 'Confirm your identity by entering your 6-digit security PIN below.' @endif
        },
        'otp-request': {
            title: 'Email Verification',
            desc: 'We will send a secure 6-digit verification code to your registered email address.'
        },
        'otp-verify': {
            title: 'Enter Verification Code',
            desc: 'Please enter the 6-digit verification code we sent to your email.'
        }
    };

    // Helper to change view and update texts
    window.showView = function(viewName) {
        activeState = viewName;
        hideBanner();

        // Map alias 'pin' to specific setup/verify views
        let actualViewId = 'view-choice';
        if (viewName === 'pin') {
            actualViewId = @if (is_null(auth()->user()->pin)) 'view-pin-setup' @else 'view-pin-verify' @endif;
        } else if (viewName === 'otp-request') {
            actualViewId = 'view-otp-request';
        } else if (viewName === 'otp-verify') {
            actualViewId = 'view-otp-verify';
        }

        // Hide all views
        ['view-choice', 'view-pin-setup', 'view-pin-verify', 'view-otp-request', 'view-otp-verify'].forEach(id => {
            const el = document.getElementById(id);
            if (el) el.classList.add('hidden');
        });

        // Show matching view
        const currentEl = document.getElementById(actualViewId);
        if (currentEl) currentEl.classList.remove('hidden');

        // Update titles and descriptions
        const meta = viewsMeta[viewName] || viewsMeta['choice'];
        document.getElementById('view-title').innerText = meta.title;
        document.getElementById('view-desc').innerText = meta.desc;

        // Auto focus first input in the shown view
        setTimeout(() => {
            if (currentEl) {
                const firstInput = currentEl.querySelector('input');
                if (firstInput) firstInput.focus();
            }
        }, 150);

        // Start countdown timer if we transitioned into otp-verify
        if (viewName === 'otp-verify') {
            @if (session()->has('login_otp_sent_at'))
                const sentAt = {{ session('login_otp_sent_at')->timestamp }} * 1000;
                const timePassed = Date.now() - sentAt;
                const remaining = Math.max(0, 60 - Math.floor(timePassed / 1000));
                startCountdown(remaining);
            @else
                startCountdown(60);
            @endif
        } else {
            clearInterval(countdownInterval);
        }
    };

    // Error and Success Banner helpers
    window.showBanner = function(message, isError = true) {
        const banner = document.getElementById('error-banner');
        if (!banner) return;
        banner.className = isError 
            ? "p-2.5 bg-rose-50 dark:bg-rose-950/20 border border-rose-100 dark:border-rose-900/30 rounded-xl text-rose-600 dark:text-rose-400 text-[11px] font-semibold text-center leading-relaxed"
            : "p-2.5 bg-emerald-50 dark:bg-emerald-950/20 border border-emerald-100 dark:border-emerald-900/30 rounded-xl text-emerald-600 dark:text-emerald-400 text-[11px] font-semibold text-center leading-relaxed";
        banner.innerText = message;
        banner.classList.remove('hidden');
    };

    window.hideBanner = function() {
        const banner = document.getElementById('error-banner');
        if (banner) banner.classList.add('hidden');
    };

    // Send OTP AJAX action
    window.sendOtpCode = function() {
        const btn = document.getElementById('send-otp-btn');
        const resendBtn = document.getElementById('resend-otp-btn');
        
        // Show spinner inside button
        setLoading(btn, true);
        setLoading(resendBtn, true);
        hideBanner();

        fetch('{{ route('otp.send') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({})
        })
        .then(response => response.json().then(data => ({ status: response.status, body: data })))
        .then(res => {
            setLoading(btn, false);
            setLoading(resendBtn, false);

            if (res.status === 200) {
                // Code sent successfully
                showView('otp-verify');
                showBanner(res.body.message, false);
                startCountdown(60);
            } else {
                showBanner(res.body.message || 'Failed to send OTP code.', true);
            }
        })
        .catch(err => {
            setLoading(btn, false);
            setLoading(resendBtn, false);
            showBanner('A network error occurred. Please try again.', true);
        });
    };

    // AJAX Form submit handler
    window.handleAjaxSubmit = function(event, url) {
        event.preventDefault();
        const form = event.target;
        const btn = form.querySelector('button[type="submit"]');
        
        setLoading(btn, true);
        hideBanner();

        const formData = new FormData(form);
        const data = {};
        formData.forEach((value, key) => { data[key] = value; });

        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json().then(data => ({ status: response.status, body: data })))
        .then(res => {
            setLoading(btn, false);

            if (res.status === 200) {
                // Success - reload the page to clear the overlay
                window.location.reload();
            } else {
                showBanner(res.body.message || 'Verification failed. Please try again.', true);
                
                // Clear the digit inputs for a fresh retry
                const inputs = form.querySelectorAll('input[type="password"], input[type="text"]');
                inputs.forEach(inp => { if (inp.maxLength === 1) inp.value = ''; });
                const hiddenInput = form.querySelector('input[type="hidden"]');
                if (hiddenInput) hiddenInput.value = '';
                
                // Refocus first box
                if (inputs[0]) inputs[0].focus();
                
                // If account locked out, reload page or redirect
                if (res.status === 423) {
                    setTimeout(() => {
                        window.location.href = '{{ route('home') }}';
                    }, 3000);
                }
            }
        })
        .catch(err => {
            setLoading(btn, false);
            showBanner('A network error occurred. Please try again.', true);
        });
    };

    // Cancel dynamic OTP view state
    window.cancelOtpVerify = function() {
        showView('choice');
    };

    // Helper to toggle spinners/buttons
    function setLoading(button, isLoading) {
        if (!button) return;
        const textSpan = button.querySelector('.btn-text');
        const spinnerSpan = button.querySelector('.btn-spinner');
        
        if (isLoading) {
            button.disabled = true;
            button.classList.add('opacity-80', 'cursor-not-allowed');
            if (textSpan) textSpan.classList.add('opacity-50');
            if (spinnerSpan) spinnerSpan.classList.remove('hidden');
        } else {
            button.disabled = false;
            button.classList.remove('opacity-80', 'cursor-not-allowed');
            if (textSpan) textSpan.classList.remove('opacity-50');
            if (spinnerSpan) spinnerSpan.classList.add('hidden');
        }
    }

    // OTP Timer countdown helper
    function startCountdown(seconds) {
        clearInterval(countdownInterval);
        const countdownText = document.getElementById('otp-countdown');
        const resendBtn = document.getElementById('resend-otp-btn');
        
        if (!countdownText) return;

        countdownText.classList.remove('hidden');
        if (resendBtn) resendBtn.classList.add('hidden');
        
        countdownText.innerText = `Resend code in ${seconds}s`;

        countdownInterval = setInterval(() => {
            seconds--;
            countdownText.innerText = `Resend code in ${seconds}s`;

            if (seconds <= 0) {
                clearInterval(countdownInterval);
                countdownText.classList.add('hidden');
                if (resendBtn) resendBtn.classList.remove('hidden');
            }
        }, 1000);
    }

    // Digit Input Box Auto-Tabbing
    const setupInputs = (containerId, hiddenInputId) => {
        const container = document.getElementById(containerId);
        if (!container) return;
        
        const inputs = container.querySelectorAll('input');
        const hiddenInput = document.getElementById(hiddenInputId);
        
        inputs.forEach((input, index) => {
            // Filter numbers only
            input.addEventListener('input', (e) => {
                input.value = input.value.replace(/[^0-9]/g, '');
                
                if (input.value.length === 1 && index < inputs.length - 1) {
                    inputs[index + 1].focus();
                }
                updateHidden();
            });
            
            // Backspace navigation
            input.addEventListener('keydown', (e) => {
                if (e.key === 'Backspace') {
                    if (input.value.length === 0 && index > 0) {
                        inputs[index - 1].value = '';
                        inputs[index - 1].focus();
                        e.preventDefault();
                    } else {
                        input.value = '';
                    }
                    updateHidden();
                }
            });

            // Clipboard Paste support
            input.addEventListener('paste', (e) => {
                e.preventDefault();
                const pasteData = (e.clipboardData || window.clipboardData).getData('text').replace(/[^0-9]/g, '').slice(0, inputs.length);
                if (pasteData) {
                    for (let i = 0; i < pasteData.length; i++) {
                        if (inputs[i]) {
                            inputs[i].value = pasteData[i];
                        }
                    }
                    const nextFocus = Math.min(pasteData.length, inputs.length - 1);
                    inputs[nextFocus].focus();
                    updateHidden();
                }
            });
        });
        
        const updateHidden = () => {
            let fullVal = '';
            inputs.forEach(inp => {
                fullVal += inp.value;
            });
            if (hiddenInput) hiddenInput.value = fullVal;
        };
    };
    
    // Initialize digit inputs for all forms
    setupInputs('pin-setup-inputs-container', 'hidden-pin-setup-input');
    setupInputs('pin-confirm-inputs-container', 'hidden-pin-confirm-input');
    setupInputs('pin-verify-inputs-container', 'hidden-pin-verify-input');
    setupInputs('otp-verify-inputs-container', 'hidden-otp-verify-input');

    // Run view router initializer
    showView(activeState);
});
</script>
@endif
