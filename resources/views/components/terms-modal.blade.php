<!-- MLSAKO Cooperative Terms & Conditions Modal Component -->
<div id="terms-modal-wrapper" class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm hidden transition-opacity duration-300 opacity-0">
    <div id="terms-modal-card" class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-[2rem] shadow-2xl max-w-2xl w-full flex flex-col max-h-[90vh] overflow-hidden transform scale-95 transition-transform duration-300">
        
        <!-- Header -->
        <div class="px-6 py-5 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between bg-slate-50/50 dark:bg-slate-950/30">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-emerald-50 dark:bg-emerald-950/40 rounded-xl border border-emerald-100 dark:border-emerald-900/50">
                    <img src="{{ asset('img/sako-logo-nobg.png') }}" alt="MLSAKO Logo" class="w-6 h-6 object-contain" onerror="this.outerHTML='📜'">
                </div>
                <div>
                    <h3 class="text-xs font-black uppercase tracking-widest text-emerald-600 dark:text-emerald-400">MLSAKO Cooperative</h3>
                    <h4 class="text-[11px] font-bold text-slate-500 dark:text-slate-400 mt-0.5">Loan Agreement & Disclosure Terms</h4>
                </div>
            </div>
            <button type="button" id="btn-close-terms-modal" class="p-1.5 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <!-- Scrollable Content Area -->
        <div id="terms-scroll-box" class="px-8 py-6 overflow-y-auto max-h-[50vh] space-y-5 text-slate-600 dark:text-slate-400 border-b border-slate-100 dark:border-slate-800 leading-relaxed scrollbar-thin scrollbar-thumb-slate-200 dark:scrollbar-thumb-slate-800">
            
            <div class="text-center pb-4 border-b border-slate-100 dark:border-slate-800/60">
                <h5 class="text-xs font-extrabold text-slate-800 dark:text-white uppercase tracking-wider">MEMBERSHIP LOAN CONTRACT &amp; OBLIGATION DEED</h5>
                <p class="text-[10px] text-slate-400 dark:text-slate-500 mt-1 font-semibold">Please scroll to the very bottom of this document to enable agreement.</p>
            </div>

            <!-- Section 1 -->
            <div class="space-y-1.5">
                <h6 class="text-[11px] font-black text-slate-800 dark:text-slate-200 uppercase tracking-wide">1. Covenant of Good Faith and Membership Obligations</h6>
                <p class="text-[11px] font-medium text-justify">
                    By submitting this Digital Loan Application, the undersigned borrower ("Borrower") acknowledges and affirms their active, good-standing membership in the MLSAKO Savings and Credit Cooperative ("MLSAKO"). The Borrower covenants to abide by the Articles of Cooperation, the By-Laws, and all policy guidelines promulgated by the Board of Directors, the Credit Committee, and authorized officers.
                </p>
            </div>

            <!-- Section 2 -->
            <div class="space-y-1.5">
                <h6 class="text-[11px] font-black text-slate-800 dark:text-slate-200 uppercase tracking-wide">2. Interest Rates, Service Fees, and Retention Reserves</h6>
                <p class="text-[11px] font-medium text-justify">
                    The Borrower agrees to pay interest, fees, and other charges as defined by the specific loan facility. A fixed service charge or retention reserve may be deducted upfront from the gross loan principal in accordance with MLSAKO's established lending policies (such as fixed share deposits, mutual aid reserves, or insurance policies). Any simulation, calculations, and amortization schedule provided in Step 2 of the application are high-fidelity estimations and are subject to final audit, verification, and adjustments by the Credit Committee and Treasury releasing office.
                </p>
            </div>

            <!-- Section 3 -->
            <div class="space-y-1.5">
                <h6 class="text-[11px] font-black text-slate-800 dark:text-slate-200 uppercase tracking-wide">3. Irrevocable Authorization for Salary Payroll Deduction</h6>
                <p class="text-[11px] font-medium text-justify">
                    <strong>CRITICAL DEDUCTION CONSENT:</strong> The Borrower hereby grants MLSAKO, and its partner payroll administrative systems, absolute, unconditional, and irrevocable authority to deduct all amortization payments, monthly interest, outstanding principal, penalties, and related charges directly from the Borrower's regular payroll, salary, bonuses, retirement claims, separation benefits, or any monetary credits due to the Borrower from their employer. This authorization shall remain in full force and effect until the loan is fully paid and extinguished. No revocation of this authority shall be valid without the written consent of MLSAKO's Treasurer.
                </p>
            </div>

            <!-- Section 4 -->
            <div class="space-y-1.5">
                <h6 class="text-[11px] font-black text-slate-800 dark:text-slate-200 uppercase tracking-wide">4. Solidary Liability of Appointed Co-Makers</h6>
                <p class="text-[11px] font-medium text-justify">
                    The Borrower acknowledges that certain loan facilities require co-makers as security guarantors. Under the principle of joint and several (solidary) liability, each designated co-maker who approves this loan application is bound as a co-debtor. In the event of default, delinquency, or separation of the primary Borrower from the company, MLSAKO reserves the absolute right to collect any and all remaining balances, surcharges, and interests from the salary deductions or credits of the appointed co-makers, collectively or individually, without needing to exhaust first the properties or resources of the primary Borrower.
                </p>
            </div>

            <!-- Section 5 -->
            <div class="space-y-1.5">
                <h6 class="text-[11px] font-black text-slate-800 dark:text-slate-200 uppercase tracking-wide">5. Delinquency, Default Acceleration, and Recovery Penalties</h6>
                <p class="text-[11px] font-medium text-justify">
                    Any failure by the Borrower to settle their obligations on the scheduled due dates, or any misrepresentation in the application parameters, shall constitute an Event of Default. Upon default:
                </p>
                <ul class="list-disc list-inside text-[11px] font-medium space-y-1 text-slate-500 dark:text-slate-400 pl-2">
                    <li>The entire outstanding balance of the loan (principal, interest, and surcharges) shall automatically become immediately due, payable, and demandable without prior notice or demand.</li>
                    <li>MLSAKO is authorized to charge a monthly penalty of 2.0% (or the rate set by current cooperative board policies) on any overdue amortization amount.</li>
                    <li>The default state will be reported to appropriate credit registries, databases, and may impact future cooperative credit privileges.</li>
                </ul>
            </div>

            <!-- Section 6 -->
            <div class="space-y-1.5">
                <h6 class="text-[11px] font-black text-slate-800 dark:text-slate-200 uppercase tracking-wide">6. Regulatory Compliance and Data Privacy Consent</h6>
                <p class="text-[11px] font-medium text-justify">
                    Pursuant to the Data Privacy Act of 2012 (Republic Act No. 10173) and cooperative regulations, the Borrower consents to the collection, storage, inquiry, validation, transfer, and sharing of their personal information, employment data, and financial transactions with MLSAKO credit investigators, partner employers, government institutions, and credit registries.
                </p>
            </div>

            <!-- Section 7 -->
            <div class="space-y-1.5">
                <h6 class="text-[11px] font-black text-slate-800 dark:text-slate-200 uppercase tracking-wide">7. Legal Venue and Attestation of Completeness</h6>
                <p class="text-[11px] font-medium text-justify">
                    Any legal action arising from this agreement shall be brought exclusively in the proper courts of Cebu City, Philippines. The Borrower hereby certifies that all entries, uploaded documents, and declared details in this application are true, accurate, and complete, and agrees that any falsification constitutes a ground for immediate cancellation, default, and potential administrative or civil liability.
                </p>
            </div>

            <!-- Bottom Target Indicator to verify scroll depth -->
            <div id="terms-scroll-trigger" class="h-1"></div>
        </div>

        <!-- Warning / Status Indicator Panel -->
        <div class="px-6 pt-4 pb-1.5">
            <div id="terms-unlock-alert" class="p-3 bg-amber-50 dark:bg-amber-950/20 border border-amber-250 dark:border-amber-900/50 rounded-xl text-amber-800 dark:text-amber-400 text-2xs font-bold flex items-start gap-2.5 transition-all duration-300">
                <svg class="w-4 h-4 text-amber-600 dark:text-amber-400 flex-shrink-0 animate-bounce" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 15v2m0-6h.01M5.071 19h13.858c1.41 0 2.29-1.53 1.58-2.66l-6.93-11.13c-.71-1.13-2.45-1.13-3.16 0L3.49 16.34c-.71 1.13.17 2.66 1.58 2.66z"/></svg>
                <span>Please scroll to the very bottom of the Terms &amp; Conditions text inside the box to unlock the agreement button.</span>
            </div>
        </div>

        <!-- Footer Actions -->
        <div class="px-6 py-4 bg-slate-50/50 dark:bg-slate-950/30 border-t border-slate-100 dark:border-slate-800 flex items-center justify-end gap-2.5">
            <button type="button" id="btn-decline-terms" class="bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-extrabold text-xs px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 hover:-translate-y-0.5 transition-all shadow-xs">
                Decline &amp; Cancel
            </button>
            <button type="button" id="modal-agree-btn" disabled class="bg-slate-300 dark:bg-slate-800 text-slate-500 dark:text-slate-400 font-black text-xs px-5 py-2.5 rounded-xl cursor-not-allowed opacity-50 transition-all">
                ✓ I Agree &amp; Accept
            </button>
        </div>

    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const modalWrapper = document.getElementById("terms-modal-wrapper");
        const modalCard = document.getElementById("terms-modal-card");
        const scrollBox = document.getElementById("terms-scroll-box");
        const closeBtn = document.getElementById("btn-close-terms-modal");
        const declineBtn = document.getElementById("btn-decline-terms");
        const agreeBtn = document.getElementById("modal-agree-btn");
        const unlockAlert = document.getElementById("terms-unlock-alert");
        const mainTermsCheckbox = document.getElementById("main-terms-agree");
        const submitLoanBtn = document.getElementById("btn-submit-loan");
        const openTermsBtn = document.getElementById("btn-open-terms");

        let hasScrolledToBottom = false;

        // Open modal function
        window.openTermsAndConditionsModal = function() {
            modalWrapper.classList.remove("hidden");
            // Trigger animation frame for fade-in effect
            setTimeout(() => {
                modalWrapper.classList.remove("opacity-0");
                modalWrapper.classList.add("opacity-100");
                modalCard.classList.remove("scale-95");
                modalCard.classList.add("scale-100");
            }, 50);

            // If already scrolled previously, make sure state is kept
            if (hasScrolledToBottom) {
                enableAgreementState();
            }
        };

        // Close modal function
        window.closeTermsAndConditionsModal = function() {
            modalWrapper.classList.remove("opacity-100");
            modalWrapper.classList.add("opacity-0");
            modalCard.classList.remove("scale-100");
            modalCard.classList.add("scale-95");
            setTimeout(() => {
                modalWrapper.classList.add("hidden");
            }, 300);
        };

        // Unlock agreement state
        function enableAgreementState() {
            hasScrolledToBottom = true;
            agreeBtn.removeAttribute("disabled");
            agreeBtn.className = "bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs px-5 py-2.5 rounded-xl hover:-translate-y-0.5 transition-all shadow-md shadow-emerald-600/10 cursor-pointer";
            
            unlockAlert.className = "p-3 bg-emerald-50 dark:bg-emerald-950/20 border border-emerald-250 dark:border-emerald-900/50 rounded-xl text-emerald-800 dark:text-emerald-400 text-2xs font-bold flex items-start gap-2.5 transition-all duration-300";
            unlockAlert.innerHTML = `
                <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>Terms fully read and unlocked! You are now authorized to accept and agree.</span>
            `;
        }

        // Scroll listener to trace reading progress
        scrollBox.addEventListener("scroll", function() {
            if (hasScrolledToBottom) return;

            // Check if user scrolled close to the bottom
            const offset = 15; // 15px threshold from the bottom
            const isBottom = (scrollBox.scrollHeight - scrollBox.scrollTop - scrollBox.clientHeight) <= offset;

            if (isBottom) {
                enableAgreementState();
            }
        });

        // Event hooks
        if (openTermsBtn) {
            openTermsBtn.addEventListener("click", function(e) {
                e.preventDefault();
                openTermsAndConditionsModal();
            });
        }

        // If user clicks the disabled checkbox, guide them to open terms
        if (mainTermsCheckbox) {
            mainTermsCheckbox.parentElement.addEventListener("click", function(e) {
                if (mainTermsCheckbox.disabled) {
                    e.preventDefault();
                    openTermsAndConditionsModal();
                }
            });
        }

        closeBtn.addEventListener("click", closeTermsAndConditionsModal);
        declineBtn.addEventListener("click", function() {
            // User decline: make sure checkbox is unchecked
            if (mainTermsCheckbox) {
                mainTermsCheckbox.checked = false;
                mainTermsCheckbox.disabled = true;
                mainTermsCheckbox.classList.add("opacity-50", "cursor-not-allowed");
            }
            if (submitLoanBtn) {
                submitLoanBtn.setAttribute("disabled", "true");
                submitLoanBtn.className = "bg-slate-400 text-slate-100 font-black text-xs px-6 py-3 rounded-xl cursor-not-allowed opacity-50 transition-all";
            }
            const termsBadge = document.getElementById("terms-status-badge");
            if (termsBadge) {
                termsBadge.textContent = "⚠️ Review required before submission";
                termsBadge.className = "text-[10px] text-amber-600 dark:text-amber-400 font-bold flex items-center gap-1.5";
            }
            closeTermsAndConditionsModal();
        });

        agreeBtn.addEventListener("click", function() {
            // User accepts!
            if (mainTermsCheckbox) {
                mainTermsCheckbox.disabled = false;
                mainTermsCheckbox.checked = true;
                mainTermsCheckbox.classList.remove("opacity-50", "cursor-not-allowed");
            }
            if (submitLoanBtn) {
                submitLoanBtn.removeAttribute("disabled");
                submitLoanBtn.className = "bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs px-6 py-3 rounded-xl shadow-lg shadow-emerald-600/10 hover:-translate-y-0.5 transition-all cursor-pointer";
            }
            const termsBadge = document.getElementById("terms-status-badge");
            if (termsBadge) {
                termsBadge.textContent = "✓ Terms and Conditions Agreed";
                termsBadge.className = "text-[10px] text-emerald-600 dark:text-emerald-400 font-bold flex items-center gap-1.5";
            }
            closeTermsAndConditionsModal();
        });

        // Close on escape key
        document.addEventListener("keydown", function(e) {
            if (e.key === "Escape" && !modalWrapper.classList.contains("hidden")) {
                closeTermsAndConditionsModal();
            }
        });

        // Close on clicking backdrop wrapper (only outside card)
        modalWrapper.addEventListener("click", function(e) {
            if (e.target === modalWrapper) {
                closeTermsAndConditionsModal();
            }
        });
    });
</script>
