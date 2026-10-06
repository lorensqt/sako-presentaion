<!-- PWA Install Prompt Banner & iOS Instructions Modal -->
<div id="pwa-install-container" class="hidden fixed bottom-4 left-4 right-4 sm:left-auto sm:right-6 sm:max-w-md z-50 transform transition-all duration-300 translate-y-8 opacity-0">
    <div class="bg-white/95 dark:bg-slate-900/95 backdrop-blur-md border border-slate-200 dark:border-slate-800 rounded-2xl p-4 shadow-2xl shadow-emerald-950/20 dark:shadow-black/50 flex items-center gap-3.5">
        <!-- App Logo -->
        <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-slate-950 border border-slate-800 p-1.5 flex items-center justify-center shadow-inner">
            <img src="{{ asset('img/sako-logo-nobg.png') }}" alt="ML Sako" class="w-full h-full object-contain">
        </div>

        <!-- Info Text -->
        <div class="flex-1 min-w-0">
            <h4 class="text-xs sm:text-sm font-bold text-slate-900 dark:text-white truncate">Install ML Sako App</h4>
            <p class="text-[11px] text-slate-500 dark:text-slate-400 leading-snug line-clamp-2">Add to home screen for faster 1-tap access and full-screen view.</p>
        </div>

        <!-- Actions -->
        <div class="flex items-center gap-1.5 flex-shrink-0">
            <button id="pwa-install-btn" type="button" class="inline-flex items-center justify-center bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs px-3 py-2 rounded-xl transition-all shadow-md shadow-emerald-700/30 cursor-pointer">
                Install
            </button>
            <button id="pwa-dismiss-btn" type="button" aria-label="Dismiss install prompt" class="p-2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </div>
</div>

<!-- iOS Safari Step-by-Step Instructions Modal -->
<div id="pwa-ios-modal" class="hidden fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-slate-950/70 backdrop-blur-xs p-4 transition-opacity duration-200">
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl max-w-sm w-full p-5 shadow-2xl space-y-4 text-center transform transition-transform">
        <div class="mx-auto w-14 h-14 rounded-2xl bg-slate-950 border border-slate-800 p-2 flex items-center justify-center shadow-md">
            <img src="{{ asset('img/sako-logo-nobg.png') }}" alt="ML Sako" class="w-full h-full object-contain">
        </div>

        <div class="space-y-1">
            <h3 class="text-base font-bold text-slate-900 dark:text-white">Add ML Sako to Home Screen</h3>
            <p class="text-xs text-slate-500 dark:text-slate-400">Install shortcut on your iPhone or iPad in 2 easy steps:</p>
        </div>

        <div class="bg-slate-50 dark:bg-slate-950/70 border border-slate-200/80 dark:border-slate-800/80 rounded-2xl p-3.5 space-y-3 text-left text-xs">
            <!-- Step 1 -->
            <div class="flex items-center gap-3">
                <div class="w-7 h-7 rounded-lg bg-emerald-500/10 dark:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 flex items-center justify-center flex-shrink-0 font-bold text-xs">
                    1
                </div>
                <div class="flex-1 text-slate-700 dark:text-slate-300">
                    Tap the <span class="font-bold text-slate-900 dark:text-white">Share</span> button below in Safari toolbar
                    <svg class="w-4 h-4 inline-block text-emerald-500 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
                    </svg>
                </div>
            </div>

            <!-- Step 2 -->
            <div class="flex items-center gap-3">
                <div class="w-7 h-7 rounded-lg bg-emerald-500/10 dark:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 flex items-center justify-center flex-shrink-0 font-bold text-xs">
                    2
                </div>
                <div class="flex-1 text-slate-700 dark:text-slate-300">
                    Scroll down and select <span class="font-bold text-slate-900 dark:text-white">"Add to Home Screen"</span>
                    <svg class="w-4 h-4 inline-block text-emerald-500 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                </div>
            </div>
        </div>

        <button id="pwa-ios-close-btn" type="button" class="w-full bg-slate-900 hover:bg-slate-800 dark:bg-emerald-600 dark:hover:bg-emerald-500 text-white font-bold text-xs py-2.5 px-4 rounded-xl transition-all cursor-pointer">
            Got It
        </button>
    </div>
</div>

<script>
    (function () {
        // 1. Register Service Worker
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function () {
                navigator.serviceWorker.register('/sw.js')
                    .then(function (reg) {
                        // Registration successful
                    })
                    .catch(function (err) {
                        console.warn('[PWA] ServiceWorker registration failed: ', err);
                    });
            });
        }

        // 2. Check if already installed or in standalone mode
        const isStandalone = window.matchMedia('(display-mode: standalone)').matches ||
                             window.navigator.standalone === true ||
                             document.referrer.includes('android-app://');

        if (isStandalone) {
            return; // App is already running as installed shortcut
        }

        // Check if recently dismissed by user (within 7 days)
        const dismissedTimestamp = localStorage.getItem('pwa_prompt_dismissed_until');
        if (dismissedTimestamp && Date.now() < parseInt(dismissedTimestamp, 10)) {
            return;
        }

        const container = document.getElementById('pwa-install-container');
        const installBtn = document.getElementById('pwa-install-btn');
        const dismissBtn = document.getElementById('pwa-dismiss-btn');
        const iosModal = document.getElementById('pwa-ios-modal');
        const iosCloseBtn = document.getElementById('pwa-ios-close-btn');

        let deferredPrompt = null;
        const isIos = /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;

        function showPromptBanner() {
            if (!container) return;
            container.classList.remove('hidden');
            setTimeout(() => {
                container.classList.remove('translate-y-8', 'opacity-0');
            }, 100);
        }

        function hidePromptBanner() {
            if (!container) return;
            container.classList.add('translate-y-8', 'opacity-0');
            setTimeout(() => {
                container.classList.add('hidden');
            }, 300);
        }

        function dismissPrompt() {
            hidePromptBanner();
            // Silence for 7 days
            localStorage.setItem('pwa_prompt_dismissed_until', (Date.now() + 7 * 86400000).toString());
        }

        // 3. Android / Chrome: Capture 'beforeinstallprompt'
        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            deferredPrompt = e;
            // Delay slightly so the user sees page content first
            setTimeout(showPromptBanner, 2500);
        });

        // 4. iOS Safari Detection
        if (isIos) {
            const isSafari = /Safari/i.test(navigator.userAgent) && !/CriOS|FxiOS|OPiOS|mercury/i.test(navigator.userAgent);
            if (isSafari) {
                // Show after 3.5 seconds
                setTimeout(showPromptBanner, 3500);
            }
        }

        // 5. Button Actions
        if (installBtn) {
            installBtn.addEventListener('click', async () => {
                if (deferredPrompt) {
                    deferredPrompt.prompt();
                    const { outcome } = await deferredPrompt.userChoice;
                    if (outcome === 'accepted') {
                        hidePromptBanner();
                    }
                    deferredPrompt = null;
                } else if (isIos) {
                    // Open iOS step-by-step guidance modal
                    if (iosModal) {
                        iosModal.classList.remove('hidden');
                    }
                } else {
                    // General instruction if browser doesn't expose native event
                    alert('To add this app to your phone, tap your browser menu (⋮ or Share) and select "Add to Home Screen" or "Install app".');
                }
            });
        }

        if (dismissBtn) {
            dismissBtn.addEventListener('click', dismissPrompt);
        }

        if (iosCloseBtn && iosModal) {
            iosCloseBtn.addEventListener('click', () => {
                iosModal.classList.add('hidden');
                dismissPrompt();
            });
            iosModal.addEventListener('click', (e) => {
                if (e.target === iosModal) {
                    iosModal.classList.add('hidden');
                    dismissPrompt();
                }
            });
        }

        // Listen for app installed event
        window.addEventListener('appinstalled', () => {
            hidePromptBanner();
            deferredPrompt = null;
        });
    })();
</script>
