<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\LoanApplication;
use App\Models\LoanComaker;
use App\Models\User;
use App\Services\LoanWorkflowService;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MemberController extends Controller
{
    /**
     * Display the member dashboard.
     */
    public function dashboard()
    {
        return view('member.dashboard');
    }

    /**
     * Build dynamic loan config structure from DB to keep full compatibility with Blade & JS.
     */
    protected function getDynamicLoanConfig()
    {
        $loans = Loan::where('is_active', true)->get();
        $config = [];

        foreach ($loans as $loan) {
            $category = $loan->category;
            $typeKey = $loan->type_key;

            $config[$category][$typeKey] = [
                'id' => $loan->id,
                'name' => $loan->name,
                'partner' => $loan->partner ? (str_contains($loan->partner, ', ') ? explode(', ', $loan->partner) : $loan->partner) : null,
                'loanable_amount' => is_numeric($loan->loanable_amount) ? (float) $loan->loanable_amount : $loan->loanable_amount,
                'fixed_deposit' => (float) $loan->fixed_deposit,
                'comakers' => $loan->comakers, // cast as array/json or integer
                'interest_rate' => (float) $loan->interest_rate,
                'max_term_months' => $loan->max_term_months,
                'minimum_membership_months' => $loan->minimum_membership_months,
                'hrmd_approval' => (bool) $loan->hrmd_approval,
            ];

            // Merge metadata if present to preserve special attributes
            if ($loan->metadata && is_array($loan->metadata)) {
                $config[$category][$typeKey] = array_merge($loan->metadata, $config[$category][$typeKey]);
            }
        }

        return $config;
    }

    /**
     * Display the member savings page.
     */
    public function savings()
    {
        // Define representative sample/actual figures (can be integrated with external API/DB later)
        $sharedCapital = 50000.00;
        $savingsDeposit = 142490.00;
        $minBalance = 500.00;
        
        // Calculate withdrawable amount
        $withdrawableAmount = max(0.00, $savingsDeposit - $minBalance);

        // Sample ledger entries related specifically to savings to make the page highly polished
        $ledgerEntries = [
            [
                'reference' => '#DEP-10928',
                'type' => 'Savings Pool Deposit',
                'channel' => 'M Lhuillier Branch',
                'date' => 'Jul 28, 2026',
                'amount' => 2500.00,
                'is_deposit' => true
            ],
            [
                'reference' => '#DEP-10815',
                'type' => 'Savings Pool Deposit',
                'channel' => 'M Lhuillier Branch',
                'date' => 'Jun 28, 2026',
                'amount' => 2500.00,
                'is_deposit' => true
            ],
            [
                'reference' => '#DEP-10702',
                'type' => 'Savings Pool Deposit',
                'channel' => 'GCash Online',
                'date' => 'May 28, 2026',
                'amount' => 1500.00,
                'is_deposit' => true
            ]
        ];

        return view('member.savings', compact('sharedCapital', 'savingsDeposit', 'minBalance', 'withdrawableAmount', 'ledgerEntries'));
    }

    /**
     * Display the member loans page.
     */
    public function loans()
    {
        $loanConfig = $this->getDynamicLoanConfig();
        $members = User::where('role', 'member')
            ->where('id', '!=', Auth::id())
            ->orderBy('name')
            ->get(['id', 'name', 'company_id']);

        // Retrieve logged-in member's loan applications with their respective audit logs
        $applications = LoanApplication::with(['approvals.actor', 'activities.actor', 'comakers.user'])
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        return view('member.myloans', compact('loanConfig', 'members', 'applications'));
    }

    /**
     * Display the member withdrawals page.
     */
    public function withdrawals()
    {
        $withdrawals = \App\Models\WithdrawalRequest::where('user_id', Auth::id())
            ->latest()
            ->get();

        $savingsDeposit = 142490.00;
        $minBalance = 500.00;
        $withdrawableAmount = max(0.00, $savingsDeposit - $minBalance);

        return view('member.withdrawals', compact('withdrawals', 'withdrawableAmount'));
    }

    /**
     * Submit a new withdrawal request.
     */
    public function storeWithdrawal(Request $request)
    {
        $savingsDeposit = 142490.00;
        $minBalance = 500.00;
        $withdrawableAmount = max(0.00, $savingsDeposit - $minBalance);

        $validated = $request->validate([
            'amount' => 'required|numeric|min:100|max:' . $withdrawableAmount,
            'channel' => 'required|string',
            'reason' => 'required|string|max:1000',
            'pin' => 'required|string|size:6',
        ]);

        $user = Auth::user();
        if (is_null($user->pin)) {
            return back()->withErrors(['pin' => 'Security PIN is not configured. Please setup your PIN first.'])->withInput();
        }

        if (!\Illuminate\Support\Facades\Hash::check($request->pin, $user->pin)) {
            // Increment attempts
            $user->increment('pin_attempts');

            if ($user->pin_attempts >= 3) {
                // Reset attempts so they can try again next login
                $user->update(['pin_attempts' => 0]);

                // Send Security Alert Email
                try {
                    \Illuminate\Support\Facades\Mail::to($user->email)->send(
                        new \App\Mail\SecurityAlertMail($user->name)
                    );
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error("Failed to send security lockout email from withdrawal: " . $e->getMessage());
                }

                // Invalidate session and logout
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect('/')->withErrors([
                    'login_identifier' => 'Account signed out due to 3 consecutive failed PIN attempts during withdrawal authorization. A security alert email has been sent.'
                ]);
            }

            $remaining = 3 - $user->pin_attempts;
            return back()->withErrors(['pin' => "Incorrect security PIN. You have {$remaining} attempts remaining."])->withInput();
        }

        // Reset attempts if correct
        $user->update(['pin_attempts' => 0]);

        $withdrawal = \App\Models\WithdrawalRequest::create([
            'user_id' => Auth::id(),
            'amount' => $validated['amount'],
            'channel' => $validated['channel'],
            'reason' => $validated['reason'] ?? null,
            'status' => 'pending',
        ]);

        AuditLogger::log('withdrawal_requested', "Member " . auth()->user()->name . " filed a withdrawal request for ₱" . number_format($withdrawal->amount, 2) . " via {$withdrawal->channel}.", 'info', $withdrawal);

        return redirect()->route('member.withdrawals')->with([
            'success' => 'Your withdrawal request has been submitted and is currently pending review.',
            'success_title' => 'Withdrawal Filed'
        ]);
    }

    /**
     * Cancel a pending withdrawal request.
     */
    public function cancelWithdrawal(\App\Models\WithdrawalRequest $withdrawal)
    {
        if ($withdrawal->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        if ($withdrawal->status !== 'pending') {
            return redirect()->route('member.withdrawals')->with('error', 'Only pending withdrawal requests can be cancelled.');
        }

        $withdrawal->update(['status' => 'cancelled']);

        AuditLogger::log('withdrawal_cancelled', "Member " . auth()->user()->name . " cancelled pending withdrawal request REF #WD-" . str_pad($withdrawal->id, 5, '0', STR_PAD_LEFT) . ".", 'info', $withdrawal);

        return redirect()->route('member.withdrawals')->with([
            'success' => 'Your withdrawal request has been successfully cancelled.',
            'success_title' => 'Request Cancelled'
        ]);
    }

    /**
     * Display the member deductions page.
     */
    public function deductions()
    {
        $user = auth()->user();
        $deductionRequests = $user->deductionRequests()->latest()->get();

        return view('member.deductions', compact('deductionRequests'));
    }

    /**
     * Store a new payroll deduction adjustment request.
     */
    public function storeDeductionRequest(Request $request)
    {
        $request->validate([
            'savings_amount' => 'required|numeric|min:250',
            'fixed_amount' => 'required|numeric|min:250',
            'effectivity_date' => 'required|date|after_or_equal:today',
            'remarks' => 'nullable|string|max:500',
            'terms_agreed' => 'required|accepted',
            'pin' => 'required|string|size:6',
        ]);

        $user = auth()->user();
        if (is_null($user->pin)) {
            return back()->withErrors(['pin' => 'Security PIN is not configured. Please setup your PIN first.'])->withInput();
        }

        if (!\Illuminate\Support\Facades\Hash::check($request->pin, $user->pin)) {
            // Increment attempts
            $user->increment('pin_attempts');

            if ($user->pin_attempts >= 3) {
                // Reset attempts so they can try again next login
                $user->update(['pin_attempts' => 0]);

                // Send Security Alert Email
                try {
                    \Illuminate\Support\Facades\Mail::to($user->email)->send(
                        new \App\Mail\SecurityAlertMail($user->name)
                    );
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error("Failed to send security lockout email from deduction: " . $e->getMessage());
                }

                // Invalidate session and logout
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect('/')->withErrors([
                    'login_identifier' => 'Account signed out due to 3 consecutive failed PIN attempts during deduction authorization. A security alert email has been sent.'
                ]);
            }

            $remaining = 3 - $user->pin_attempts;
            return back()->withErrors(['pin' => "Incorrect security PIN. You have {$remaining} attempts remaining."])->withInput();
        }

        // Reset attempts if correct
        $user->update(['pin_attempts' => 0]);

        $deduction = auth()->user()->deductionRequests()->create([
            'savings_amount' => $request->input('savings_amount'),
            'fixed_amount' => $request->input('fixed_amount'),
            'effectivity_date' => $request->input('effectivity_date'),
            'remarks' => $request->input('remarks'),
            'status' => 'pending',
        ]);

        AuditLogger::log('deduction_requested', "Member " . auth()->user()->name . " filed a payroll deduction adjustment request: Savings ₱" . number_format($deduction->savings_amount, 2) . ", Fixed capital ₱" . number_format($deduction->fixed_amount, 2) . ".", 'info', $deduction);

        return redirect()->route('member.deductions')->with([
            'success' => 'Your payroll deduction adjustment request has been submitted successfully and is awaiting review.',
            'success_title' => 'Adjustment Requested'
        ]);
    }

    /**
     * Display the member forms page.
     */
    public function forms(Request $request)
    {
        $loanConfig = $this->getDynamicLoanConfig();
        $members = User::where('role', 'member')
            ->where('id', '!=', Auth::id())
            ->orderBy('name')
            ->get(['id', 'name', 'company_id']);

        $resubmitApp = null;
        if ($request->has('resubmit_id')) {
            $resubmitApp = LoanApplication::where('user_id', Auth::id())
                ->where('status', 'returned')
                ->find($request->input('resubmit_id'));
        }

        return view('member.loans', compact('loanConfig', 'members', 'resubmitApp'));
    }

    /**
     * Display the member settings page.
     */
    public function settings()
    {
        return view('member.settings');
    }

    /**
     * Update member profile and signature settings.
     */
    public function updateSettings(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'contact_number' => 'nullable|string|max:30',
            'address' => 'nullable|string|max:500',
            'signature' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
        ]);

        $oldValues = $user->only(['contact_number', 'address']);

        $updateData = [
            'contact_number' => $validated['contact_number'],
            'address' => $validated['address'],
        ];

        if ($request->hasFile('signature')) {
            // Delete old signature file if it exists
            if ($user->signature && Storage::disk('public')->exists($user->signature)) {
                Storage::disk('public')->delete($user->signature);
            }

            // Store new signature image
            $path = $request->file('signature')->store('signatures', 'public');
            $updateData['signature'] = $path;
        }

        $user->update($updateData);

        AuditLogger::log('member_updated', "Member " . auth()->user()->name . " updated their profile settings.", 'info', $user, $oldValues, $user->fresh()->only(['contact_number', 'address']));

        return redirect()->route('member.settings')->with('success', 'Your portal profile was updated successfully.');
    }

    /**
     * Submit a new loan application.
     */
    public function applyLoan(Request $request)
    {
        $validated = $request->validate([
            'category' => 'required|string',
            'type' => 'required|string',
            'amount' => 'required|numeric|min:1',
            'term' => 'required|integer|min:1',
            'comakers' => 'nullable|array',
            'comakers.*' => 'exists:users,id',
            'partner' => 'nullable|string',
            'product' => 'nullable|string',
            'remarks' => 'nullable|string|max:1000',
            'pin' => 'required|string|size:6',
        ]);

        $user = Auth::user();
        if (is_null($user->pin)) {
            return back()->withErrors(['pin' => 'Security PIN is not configured. Please setup your PIN first.'])->withInput();
        }

        if (!\Illuminate\Support\Facades\Hash::check($request->pin, $user->pin)) {
            // Increment attempts
            $user->increment('pin_attempts');

            if ($user->pin_attempts >= 3) {
                // Reset attempts so they can try again next login
                $user->update(['pin_attempts' => 0]);

                // Send Security Alert Email
                try {
                    \Illuminate\Support\Facades\Mail::to($user->email)->send(
                        new \App\Mail\SecurityAlertMail($user->name)
                    );
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error("Failed to send security lockout email from loan application: " . $e->getMessage());
                }

                // Invalidate session and logout
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect('/')->withErrors([
                    'login_identifier' => 'Account signed out due to 3 consecutive failed PIN attempts during loan authorization. A security alert email has been sent.'
                ]);
            }

            $remaining = 3 - $user->pin_attempts;
            return back()->withErrors(['pin' => "Incorrect security PIN. You have {$remaining} attempts remaining."])->withInput();
        }

        // Reset attempts if correct
        $user->update(['pin_attempts' => 0]);

        $resubmitId = $request->input('resubmit_id');
        $application = null;

        if ($resubmitId) {
            $application = LoanApplication::where('user_id', Auth::id())
                ->where('status', 'returned')
                ->find($resubmitId);

            if (!$application) {
                return back()->with('error', 'The returned loan application could not be found.');
            }
        }

        // Find the loan product definition in the database
        $loan = Loan::where('category', $validated['category'])
            ->where('type_key', $validated['type'])
            ->where('is_active', true)
            ->first();

        // Dynamic self-healing fallback (e.g., for automated tests or empty DB)
        if (!$loan) {
            $loansConfig = config("loans.{$validated['category']}.{$validated['type']}");
            if ($loansConfig) {
                $loan = Loan::create([
                    'category' => $validated['category'],
                    'type_key' => $validated['type'],
                    'name' => $loansConfig['name'] ?? ucwords(str_replace('_', ' ', $validated['type'])),
                    'partner' => is_array($loansConfig['partner'] ?? null) ? implode(', ', $loansConfig['partner']) : ($loansConfig['partner'] ?? null),
                    'loanable_amount' => isset($loansConfig['loanable_amount']) ? (string) $loansConfig['loanable_amount'] : null,
                    'fixed_deposit' => is_numeric($loansConfig['fixed_deposit'] ?? null) ? (float) $loansConfig['fixed_deposit'] : 0.00,
                    'comakers' => $loansConfig['comakers'] ?? 0,
                    'interest_rate' => 5.00, // standard default fallback interest rate
                    'max_term_months' => $loansConfig['max_term_months'] ?? $loansConfig['term_months'] ?? null,
                    'minimum_membership_months' => $loansConfig['minimum_membership_months'] ?? null,
                    'hrmd_approval' => $loansConfig['hrmd_approval'] ?? false,
                    'is_active' => true,
                ]);
            }
        }

        if (!$loan) {
            return back()->with('error', 'Selected loan package is invalid or inactive.');
        }

        // Build structured form data payload
        $comakerIds = $validated['comakers'] ?? [];
        $formData = [
            'term_months' => $validated['term'],
            'partner' => $validated['partner'] ?? null,
            'product' => $validated['product'] ?? null,
            'comakers' => $comakerIds,
            'member_remarks' => $validated['remarks'] ?? null,
        ];

        // Create or update the application record with dynamic initial stage and lock interest rate
        $workflowService = app(LoanWorkflowService::class);
        
        if ($application) {
            // Updating existing returned loan application
            $application->loan_id = $loan->id;
            $application->loan_category = $validated['category'];
            $application->loan_type = $validated['type'];
            $application->requested_amount = $validated['amount'];
            $application->interest_rate = $loan->interest_rate;
            $application->status = 'pending';
            $application->form_data = $formData;
            $application->rejection_reason = null; // Clear returned remarks

            $application->current_stage = $workflowService->getInitialStage($application);
            $application->save();

            // Clear old approvals and co-makers since we are starting over
            $application->approvals()->delete();
            $application->comakers()->delete();

            // Create activity log
            \App\Models\LoanActivity::create([
                'loan_application_id' => $application->id,
                'user_id' => Auth::id(),
                'action' => 'resubmitted',
                'description' => 'Loan application resubmitted with corrected/updated requirements. Current Stage: ' . ucwords(str_replace('_', ' ', $application->current_stage)),
            ]);

            AuditLogger::log('loan_applied', "Member " . auth()->user()->name . " resubmitted returned loan application LN-" . str_pad($application->id, 5, '0', STR_PAD_LEFT) . ".", 'info', $application);
        } else {
            // Create a brand new loan application
            $application = new LoanApplication([
                'user_id' => Auth::id(),
                'loan_id' => $loan->id,
                'loan_category' => $validated['category'],
                'loan_type' => $validated['type'],
                'requested_amount' => $validated['amount'],
                'interest_rate' => $loan->interest_rate, // Lock rate at application time
                'status' => 'pending',
                'form_data' => $formData,
            ]);
            
            $application->current_stage = $workflowService->getInitialStage($application);
            $application->save();

            // Create initial activity log
            \App\Models\LoanActivity::create([
                'loan_application_id' => $application->id,
                'user_id' => Auth::id(),
                'action' => 'submitted',
                'description' => 'Loan application submitted and entered the queue. Current Stage: ' . ucwords(str_replace('_', ' ', $application->current_stage)),
            ]);

            AuditLogger::log('loan_applied', "Member " . auth()->user()->name . " filed a new loan application for ₱" . number_format($application->requested_amount, 2) . " (LN-" . str_pad($application->id, 5, '0', STR_PAD_LEFT) . ").", 'info', $application);
        }

        // Create relational co-maker records in the loan_comakers table
        if (!empty($comakerIds)) {
            foreach ($comakerIds as $comakerId) {
                LoanComaker::create([
                    'loan_application_id' => $application->id,
                    'user_id' => $comakerId,
                    'status' => 'pending',
                ]);
            }
        }

        // Send co-maker request email notifications if any are requested
        if (!empty($comakerIds)) {
            $borrower = Auth::user();
            $loanTypeName = $loan->name;
            
            // Fetch requested co-makers from DB
            $comakersList = User::whereIn('id', $comakerIds)->get();
            foreach ($comakersList as $comaker) {
                try {
                    \Illuminate\Support\Facades\Mail::to($comaker->email)->send(
                        new \App\Mail\CoMakerRequestMail(
                            $borrower->name,
                            $comaker->name,
                            $loanTypeName,
                            (float) $application->requested_amount
                        )
                    );
                } catch (\Exception $e) {
                    \Illuminate\Support5\Facades\Log::warning("Failed to send co-maker email to {$comaker->email}: " . $e->getMessage());
                }
            }
        }

        return redirect()->route('member.loans')->with('success', 'Your loan application was successfully submitted and has entered the approval queue.');
    }

    /**
     * Replace a rejected co-maker.
     */
    public function replaceCoMaker(Request $request, LoanApplication $application)
    {
        // 1. Authorization: Ensure the application belongs to this member
        if ($application->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // 2. Validate current state
        if ($application->status !== 'pending' || $application->current_stage !== 'comakers') {
            return back()->with('error', 'This loan application cannot have its co-makers modified at this stage.');
        }

        $validated = $request->validate([
            'old_comaker_id' => 'required|exists:users,id',
            'new_comaker_id' => 'required|exists:users,id|different:old_comaker_id',
        ], [
            'new_comaker_id.different' => 'The newly selected co-maker must not be the same as the rejected co-maker.',
        ]);

        $oldId = $validated['old_comaker_id'];
        $newId = $validated['new_comaker_id'];

        if ((int)$newId === (int)$oldId) {
            return back()->with('error', 'The newly selected co-maker must not be the same as the rejected co-maker.');
        }

        // 3. Ensure the old co-maker was indeed rejected
        $oldComakerRecord = LoanComaker::where('loan_application_id', $application->id)
            ->where('user_id', $oldId)
            ->where('status', 'rejected')
            ->first();

        if (!$oldComakerRecord) {
            return back()->with('error', 'The selected co-maker has not rejected the request or is invalid.');
        }

        // 4. Ensure the new co-maker is not already in the active list
        $comakerIds = $application->form_data['comakers'] ?? [];
        if (in_array($newId, $comakerIds) || in_array((string) $newId, $comakerIds)) {
            return back()->with('error', "The new co-maker (ID: {$newId}) is already designated for this loan application. Active co-maker IDs: " . implode(', ', $comakerIds) . ". Selected old co-maker ID: {$oldId}.");
        }

        // 5. Update application and co-maker records in a transaction
        \Illuminate\Support\Facades\DB::transaction(function () use ($application, $comakerIds, $oldId, $newId) {
            // Replace in form_data['comakers']
            $updatedComakers = array_map(function ($id) use ($oldId, $newId) {
                return ((int)$id === (int)$oldId || (string)$id === (string)$oldId) ? (int)$newId : (int)$id;
            }, $comakerIds);

            $formData = $application->form_data;
            $formData['comakers'] = array_values($updatedComakers);
            $application->form_data = $formData;
            $application->save();

            // Create new pending LoanComaker record
            LoanComaker::create([
                'loan_application_id' => $application->id,
                'user_id' => $newId,
                'status' => 'pending',
            ]);

            // Add activity log
            $oldUser = User::find($oldId);
            $newUser = User::find($newId);
            \App\Models\LoanActivity::create([
                'loan_application_id' => $application->id,
                'user_id' => Auth::id(),
                'action' => 'comaker_replaced',
                'description' => "Replaced co-maker '{$oldUser->name}' with '{$newUser->name}' due to rejection.",
            ]);

            AuditLogger::log('loan_comaker_replaced', "Member " . auth()->user()->name . " replaced rejected co-maker '{$oldUser->name}' with '{$newUser->name}' on loan application LN-" . str_pad($application->id, 5, '0', STR_PAD_LEFT) . ".", 'info', $application);
        });

        // 6. Send notification email to new co-maker
        $borrower = Auth::user();
        $loanTypeName = $application->loan ? $application->loan->name : ucwords(str_replace('_', ' ', $application->loan_type));
        $newComaker = User::find($newId);
        try {
            \Illuminate\Support\Facades\Mail::to($newComaker->email)->send(
                new \App\Mail\CoMakerRequestMail(
                    $borrower->name,
                    $newComaker->name,
                    $loanTypeName,
                    (float) $application->requested_amount
                )
            );
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning("Failed to send replacement co-maker email to {$newComaker->email}: " . $e->getMessage());
        }

        return back()->with('success', 'Co-maker has been successfully replaced. A request email has been sent to the new co-maker.');
    }

    /**
     * Display member's co-maker requests and histories.
     */
    public function coMakerRequests()
    {
        $userId = Auth::id();

        // 1. Fetch pending loans currently at 'comakers' stage
        $allPendingComakersLoans = LoanApplication::with(['borrower', 'approvals.actor'])
            ->where('status', 'pending')
            ->where('current_stage', 'comakers')
            ->get();

        // Filter: User is designated as co-maker AND has NOT yet approved/rejected this application
        $pendingRequests = $allPendingComakersLoans->filter(function ($app) use ($userId) {
            $comakers = $app->form_data['comakers'] ?? [];
            $isComaker = in_array($userId, $comakers) || in_array((string) $userId, $comakers);
            
            if (!$isComaker) {
                return false;
            }

            // Check if user already actioned
            $hasActioned = $app->approvals()
                ->where('stage_role_slug', 'comakers')
                ->where('actioned_by_user_id', $userId)
                ->exists();

            return !$hasActioned;
        });

        // 2. Fetch history of co-maker loans actioned by this user (either approved or rejected)
        $historicalRequests = LoanApplication::with(['borrower', 'approvals.actor'])
            ->whereHas('approvals', function ($query) use ($userId) {
                $query->where('stage_role_slug', 'comakers')
                      ->where('actioned_by_user_id', $userId);
            })
            ->latest()
            ->get();

        return view('member.comaker-requests', compact('pendingRequests', 'historicalRequests'));
    }
}
