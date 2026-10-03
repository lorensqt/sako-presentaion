<?php

namespace App\Http\Controllers;

use App\Models\LoanApplication;
use App\Models\LoanComaker;
use App\Models\LoanDocument;
use App\Models\User;
use App\Services\LoanWorkflowService;
use App\Services\AuditLogger;
use App\Mail\CoMakerDeclinedMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class LoanApprovalController extends Controller
{
    protected LoanWorkflowService $workflowService;

    public function __construct(LoanWorkflowService $workflowService)
    {
        $this->workflowService = $workflowService;
    }

    /**
     * Approve the loan for the current active stage/group.
     */
    public function approve(Request $request, LoanApplication $application)
    {
        $user = $request->user();
        $currentStageRole = $application->current_stage;

        // Check if the application is still pending
        if ($application->status !== 'pending') {
            return back()->with('error', 'This loan application has already been processed.');
        }

        // Authorization: Check if user belongs to the active group stage or is a valid co-maker
        if ($currentStageRole === 'comakers') {
            $comakers = $application->form_data['comakers'] ?? [];
            $isComaker = in_array($user->id, $comakers) || in_array((string) $user->id, $comakers);

            if (!$isComaker) {
                return back()->with('error', "You are not listed as a co-maker for this loan application.");
            }

            $hasAlreadyApproved = $application->approvals()
                ->where('stage_role_slug', 'comakers')
                ->where('actioned_by_user_id', $user->id)
                ->exists();

            if ($hasAlreadyApproved) {
                return back()->with('error', "You have already actioned this endorsement request.");
            }
        } else {
            if (!$user->hasRole($currentStageRole)) {
                return back()->with('error', "You do not have permission to approve loans at the '{$currentStageRole}' stage.");
            }

            // Granular HRMD Sequence Verification
            if ($currentStageRole === 'hrmd_staff') {
                $currentSeq = $application->current_hrmd_sequence ?? $this->workflowService->getInitialHrmdSequence();
                $application->current_hrmd_sequence = $currentSeq;

                if ($user->role !== 'super_admin' && (int) $user->hrmd_sequence !== (int) $currentSeq) {
                    return back()->with('error', "This loan application is currently awaiting HRMD Sequence #{$currentSeq} approval.");
                }

                $alreadyApproved = $application->approvals()
                    ->where('stage_role_slug', 'hrmd_staff')
                    ->where('hrmd_sequence', $currentSeq)
                    ->where('decision', 'approved')
                    ->exists();

                if ($alreadyApproved) {
                    return back()->with('error', "HRMD Sequence #{$currentSeq} has already been approved.");
                }
            }
        }

        // Releasing Officer Stage Compliance: Ledger & Payment Schedule files and remarks are mandatory
        if ($currentStageRole === 'releasing_officer') {
            $request->validate([
                'ledger' => 'required|file|mimes:pdf|max:10240',
                'schedule' => 'required|file|mimes:pdf|max:10240',
                'remarks' => 'required|string|min:3|max:1000',
            ], [
                'ledger.required' => 'The General Ledger PDF file is required before releasing the loan.',
                'ledger.mimes' => 'The General Ledger must be a PDF document.',
                'ledger.max' => 'The General Ledger PDF must not exceed 10MB.',
                'schedule.required' => 'The Amortization Payment Schedule PDF file is required before releasing the loan.',
                'schedule.mimes' => 'The Payment Schedule must be a PDF document.',
                'schedule.max' => 'The Payment Schedule PDF must not exceed 10MB.',
                'remarks.required' => 'Official releasing remarks are required before finalizing disbursement.',
            ]);
        }

        DB::transaction(function () use ($application, $user, $currentStageRole, $request) {
            // If in releasing_officer stage, store uploaded Ledger and Schedule files
            if ($currentStageRole === 'releasing_officer') {
                $disk = User::storageDisk();
                if ($request->hasFile('ledger')) {
                    $ledgerPath = $request->file('ledger')->store("loans/releasing/{$application->id}", $disk);
                    $application->ledger_path = $ledgerPath;
                }
                if ($request->hasFile('schedule')) {
                    $schedulePath = $request->file('schedule')->store("loans/releasing/{$application->id}", $disk);
                    $application->schedule_path = $schedulePath;
                }
            }

            $currentHrmdSeq = ($currentStageRole === 'hrmd_staff')
                ? ($application->current_hrmd_sequence ?? $this->workflowService->getInitialHrmdSequence())
                : null;

            $actionRemarks = $request->input('remarks');
            if (empty($actionRemarks) && $currentStageRole === 'releasing_officer') {
                $actionRemarks = 'Loan funds and official disbursement schedules have been successfully released.';
            }

            // 1. Record individual action in approval ledger
            $application->approvals()->create([
                'stage_role_slug' => $currentStageRole,
                'hrmd_sequence' => $currentHrmdSeq,
                'actioned_by_user_id' => $user->id,
                'decision' => 'approved',
                'remarks' => $actionRemarks,
            ]);

            // Update relational co-maker status if in co-makers stage
            if ($currentStageRole === 'comakers') {
                LoanComaker::where('loan_application_id', $application->id)
                    ->where('user_id', $user->id)
                    ->update([
                        'status' => 'approved',
                        'remarks' => $request->input('remarks'),
                        'actioned_at' => now(),
                    ]);

                \App\Models\LoanActivity::create([
                    'loan_application_id' => $application->id,
                    'user_id' => $user->id,
                    'action' => 'comaker_approved',
                    'description' => "Co-maker {$user->name} endorsed the loan application." . ($request->input('remarks') ? " Remarks: " . $request->input('remarks') : ""),
                ]);

                AuditLogger::log('loan_comaker_endorsed', "Co-maker {$user->name} endorsed loan application LN-" . str_pad($application->id, 5, '0', STR_PAD_LEFT) . " for member {$application->borrower->name}.", 'info', $application);
            } elseif ($currentStageRole === 'hrmd_staff') {
                \App\Models\LoanActivity::create([
                    'loan_application_id' => $application->id,
                    'user_id' => $user->id,
                    'action' => 'stage_approved',
                    'description' => "HRMD Sequence #{$currentHrmdSeq} approved by {$user->name}." . ($request->input('remarks') ? " Remarks: " . $request->input('remarks') : ""),
                ]);

                AuditLogger::log('loan_stage_approved', "HRMD Sequence #{$currentHrmdSeq} approved by {$user->name} for loan application LN-" . str_pad($application->id, 5, '0', STR_PAD_LEFT) . ".", 'info', $application);
            } else {
                \App\Models\LoanActivity::create([
                    'loan_application_id' => $application->id,
                    'user_id' => $user->id,
                    'action' => 'stage_approved',
                    'description' => "Stage '" . ucwords(str_replace('_', ' ', $currentStageRole)) . "' approved by {$user->name}." . ($request->input('remarks') ? " Remarks: " . $request->input('remarks') : ""),
                ]);

                AuditLogger::log('loan_stage_approved', "Stage '" . ucwords(str_replace('_', ' ', $currentStageRole)) . "' approved by {$user->name} for loan application LN-" . str_pad($application->id, 5, '0', STR_PAD_LEFT) . ".", 'info', $application);
            }

            // 2. Advance to next stage using workflow engine
            if ($currentStageRole === 'comakers') {
                $requiredCount = $this->workflowService->getRequiredComakersCount(
                    $application->loan_category,
                    $application->loan_type,
                    (float) $application->requested_amount
                );

                $distinctApprovals = $application->approvals()
                    ->where('stage_role_slug', 'comakers')
                    ->where('decision', 'approved')
                    ->count();

                if ($distinctApprovals >= $requiredCount) {
                    $nextStage = $this->workflowService->getNextStage($application);
                    if ($nextStage) {
                        if ($nextStage === 'hrmd_staff') {
                            $application->current_hrmd_sequence = $this->workflowService->getInitialHrmdSequence();
                        }
                        $application->current_stage = $nextStage;
                        \App\Models\LoanActivity::create([
                            'loan_application_id' => $application->id,
                            'user_id' => null,
                            'action' => 'stage_passed',
                            'description' => "All required co-maker endorsements received. Moved to '" . ucwords(str_replace('_', ' ', $nextStage)) . "' stage.",
                        ]);

                        AuditLogger::log('loan_stage_passed', "Loan application LN-" . str_pad($application->id, 5, '0', STR_PAD_LEFT) . " moved to '" . ucwords(str_replace('_', ' ', $nextStage)) . "' stage after endorsements.", 'info', $application);
                    } else {
                        $application->current_stage = 'completed';
                        $application->status = 'approved';
                        $this->calculateLoanDisbursementDetails($application);
                        \App\Models\LoanActivity::create([
                            'loan_application_id' => $application->id,
                            'user_id' => null,
                            'action' => 'approved',
                            'description' => "Loan application has been fully approved.",
                        ]);

                        AuditLogger::log('loan_stage_approved', "Loan application LN-" . str_pad($application->id, 5, '0', STR_PAD_LEFT) . " has been fully approved.", 'info', $application);
                    }
                }
                // If distinct approvals < requiredCount, do not advance! It stays in 'comakers' stage.
            } elseif ($currentStageRole === 'hrmd_staff') {
                // Check if another HRMD sequence is pending
                $nextSeq = User::whereHas('roles', fn($q) => $q->where('slug', 'hrmd_staff'))
                    ->whereNotNull('hrmd_sequence')
                    ->where('hrmd_sequence', '>', $currentHrmdSeq)
                    ->orderBy('hrmd_sequence', 'asc')
                    ->value('hrmd_sequence');

                if ($nextSeq) {
                    // Stays at hrmd_staff stage, but moves to next sequence
                    $application->current_hrmd_sequence = (int) $nextSeq;
                    \App\Models\LoanActivity::create([
                        'loan_application_id' => $application->id,
                        'user_id' => null,
                        'action' => 'stage_passed',
                        'description' => "Advanced to HRMD Sequence #{$nextSeq} approval.",
                    ]);
                    AuditLogger::log('loan_stage_passed', "Loan application LN-" . str_pad($application->id, 5, '0', STR_PAD_LEFT) . " advanced to HRMD Sequence #{$nextSeq}.", 'info', $application);
                } else {
                    // All HRMD sequences are complete; move to next workflow stage
                    $application->current_hrmd_sequence = null;
                    $nextStage = $this->workflowService->getNextStage($application);

                    if ($nextStage) {
                        $application->current_stage = $nextStage;
                        \App\Models\LoanActivity::create([
                            'loan_application_id' => $application->id,
                            'user_id' => null,
                            'action' => 'stage_passed',
                            'description' => "All HRMD approval sequences completed. Moved to '" . ucwords(str_replace('_', ' ', $nextStage)) . "' stage.",
                        ]);
                        AuditLogger::log('loan_stage_passed', "Loan application LN-" . str_pad($application->id, 5, '0', STR_PAD_LEFT) . " completed HRMD verification and moved to '" . ucwords(str_replace('_', ' ', $nextStage)) . "' stage.", 'info', $application);
                    } else {
                        $application->current_stage = 'completed';
                        $application->status = 'approved';
                        $this->calculateLoanDisbursementDetails($application);
                        \App\Models\LoanActivity::create([
                            'loan_application_id' => $application->id,
                            'user_id' => null,
                            'action' => 'approved',
                            'description' => "Loan application has been fully approved.",
                        ]);
                        AuditLogger::log('loan_stage_approved', "Loan application LN-" . str_pad($application->id, 5, '0', STR_PAD_LEFT) . " has been fully approved.", 'info', $application);
                    }
                }
            } else {
                $nextStage = $this->workflowService->getNextStage($application);

                if ($nextStage) {
                    if ($nextStage === 'hrmd_staff') {
                        $application->current_hrmd_sequence = $this->workflowService->getInitialHrmdSequence();
                    }
                    $application->current_stage = $nextStage;
                    \App\Models\LoanActivity::create([
                        'loan_application_id' => $application->id,
                        'user_id' => null,
                        'action' => 'stage_passed',
                        'description' => "Moved to '" . ucwords(str_replace('_', ' ', $nextStage)) . "' stage.",
                    ]);

                    AuditLogger::log('loan_stage_passed', "Loan application LN-" . str_pad($application->id, 5, '0', STR_PAD_LEFT) . " moved to '" . ucwords(str_replace('_', ' ', $nextStage)) . "' stage.", 'info', $application);
                } else {
                    // Completed all approval stages! Mark loan as approved
                    $application->current_stage = 'completed';
                    $application->status = 'approved';
                    $this->calculateLoanDisbursementDetails($application);
                    \App\Models\LoanActivity::create([
                        'loan_application_id' => $application->id,
                        'user_id' => null,
                        'action' => 'approved',
                        'description' => "Loan application has been fully approved.",
                    ]);

                    AuditLogger::log('loan_stage_approved', "Loan application LN-" . str_pad($application->id, 5, '0', STR_PAD_LEFT) . " has been fully approved.", 'info', $application);
                }
            }

            $application->save();
        });

        // Send release notification email if the loan has been completely approved
        if ($application->status === 'approved') {
            $application->load('borrower');
            $borrower = $application->borrower;

            if ($borrower && !empty($borrower->email)) {
                $loanTypeName = config("loans.{$application->loan_category}.{$application->loan_type}.name", ucwords(str_replace('_', ' ', $application->loan_type)));
                $termMonths = $application->form_data['term_months'] ?? $application->term_months ?? 12;

                // Retrieve releasing officer approval record and remarks
                $releasingApproval = $application->approvals()
                    ->with('actor')
                    ->where('stage_role_slug', 'releasing_officer')
                    ->where('decision', 'approved')
                    ->latest()
                    ->first();

                $remarksForEmail = $releasingApproval?->remarks 
                    ?: ($request->input('remarks') ?: 'Loan funds and official disbursement schedules have been successfully released.');
                $releasingOfficerName = $releasingApproval?->actor?->name ?? $user->name;

                try {
                    \Illuminate\Support\Facades\Mail::to($borrower->email)->send(
                        new \App\Mail\LoanReleasedMail(
                            $borrower->name,
                            $loanTypeName,
                            (float) $application->requested_amount,
                            (int) $termMonths,
                            (string) $application->id,
                            $remarksForEmail,
                            $releasingOfficerName
                        )
                    );
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::warning("Failed to send loan released email to {$borrower->email}: " . $e->getMessage());
                }
            }
        }

        return back()->with('success', 'Loan application successfully approved and completed.');
    }

    /**
     * Reject the loan from the current active stage/group.
     */
    public function reject(Request $request, LoanApplication $application)
    {
        $user = $request->user();
        $currentStageRole = $application->current_stage;

        $request->validate([
            'remarks' => 'required|string|max:1000',
        ]);

        if ($application->status !== 'pending') {
            return back()->with('error', 'This loan application has already been processed.');
        }

        // Authorization: Check if user belongs to the active group stage or is a valid co-maker
        if ($currentStageRole === 'comakers') {
            $comakers = $application->form_data['comakers'] ?? [];
            $isComaker = in_array($user->id, $comakers) || in_array((string) $user->id, $comakers);

            if (!$isComaker) {
                return back()->with('error', "You are not listed as a co-maker for this loan application.");
            }

            $hasAlreadyApproved = $application->approvals()
                ->where('stage_role_slug', 'comakers')
                ->where('actioned_by_user_id', $user->id)
                ->exists();

            if ($hasAlreadyApproved) {
                return back()->with('error', "You have already actioned this endorsement request.");
            }
        } else {
            if (!$user->hasRole($currentStageRole)) {
                return back()->with('error', "You do not have permission to reject loans at the '{$currentStageRole}' stage.");
            }
        }

        DB::transaction(function () use ($application, $user, $currentStageRole, $request) {
            // 1. Record individual action in approval ledger
            $application->approvals()->create([
                'stage_role_slug' => $currentStageRole,
                'actioned_by_user_id' => $user->id,
                'decision' => 'rejected',
                'remarks' => $request->input('remarks'),
            ]);

            // Update relational co-maker status if in co-makers stage
            if ($currentStageRole === 'comakers') {
                LoanComaker::where('loan_application_id', $application->id)
                    ->where('user_id', $user->id)
                    ->update([
                        'status' => 'rejected',
                        'remarks' => $request->input('remarks'),
                        'actioned_at' => now(),
                    ]);

                \App\Models\LoanActivity::create([
                    'loan_application_id' => $application->id,
                    'user_id' => $user->id,
                    'action' => 'comaker_rejected',
                    'description' => "Co-maker {$user->name} declined the endorsement request. Remarks: " . $request->input('remarks'),
                ]);

                AuditLogger::log('loan_comaker_declined', "Co-maker {$user->name} declined to endorse loan application LN-" . str_pad($application->id, 5, '0', STR_PAD_LEFT) . " for member {$application->borrower->name}.", 'warning', $application);

                // Send email notification to borrower about the co-maker declining
                $borrower = $application->borrower;
                if ($borrower && $borrower->email) {
                    $loanProduct = $application->loan;
                    $loanTypeName = $loanProduct ? $loanProduct->name : ucwords(str_replace('_', ' ', $application->loan_type));
                    
                    try {
                        \Illuminate\Support\Facades\Mail::to($borrower->email)->send(
                            new \App\Mail\CoMakerDeclinedMail(
                                $borrower->name,
                                $user->name,
                                $loanTypeName,
                                (float) $application->requested_amount,
                                $request->input('remarks')
                            )
                        );
                    } catch (\Exception $e) {
                        \Illuminate\Support\Facades\Log::warning("Failed to send co-maker decline email to {$borrower->email}: " . $e->getMessage());
                    }
                }
            } else {
                // 2. Terminate workflow and set application status to rejected
                $application->status = 'rejected';
                $application->rejection_reason = $request->input('remarks');
                $application->save();

                \App\Models\LoanActivity::create([
                    'loan_application_id' => $application->id,
                    'user_id' => $user->id,
                    'action' => 'rejected',
                    'description' => "Stage '" . ucwords(str_replace('_', ' ', $currentStageRole)) . "' rejected by {$user->name}. Reason: " . $request->input('remarks'),
                ]);

                AuditLogger::log('loan_stage_rejected', "Stage '" . ucwords(str_replace('_', ' ', $currentStageRole)) . "' rejected by {$user->name} for loan application LN-" . str_pad($application->id, 5, '0', STR_PAD_LEFT) . ".", 'danger', $application);
            }
        });

        return back()->with('success', 'Loan application has been rejected.');
    }

    /**
     * Return the loan application to the member for lack of requirements.
     */
    public function returnLoan(Request $request, LoanApplication $application)
    {
        $user = $request->user();
        $currentStageRole = $application->current_stage;

        $request->validate([
            'remarks' => 'required|string|max:1000',
        ]);

        if ($application->status !== 'pending') {
            return back()->with('error', 'This loan application has already been processed.');
        }

        // Authorization: Check if user belongs to the active group stage (either 'sako_staff' or 'hrmd_staff')
        if (!in_array($currentStageRole, ['sako_staff', 'hrmd_staff'])) {
            return back()->with('error', "Loan applications can only be returned at the Sako Staff or HRMD Staff stages.");
        }

        if (!$user->hasRole($currentStageRole)) {
            return back()->with('error', "You do not have permission to return loans at the '{$currentStageRole}' stage.");
        }

        DB::transaction(function () use ($application, $user, $currentStageRole, $request) {
            // Update application status to returned
            $application->status = 'returned';
            $application->rejection_reason = $request->input('remarks'); // Store return reasons here
            $application->save();

            // Record action in approvals ledger
            $application->approvals()->create([
                'stage_role_slug' => $currentStageRole,
                'actioned_by_user_id' => $user->id,
                'decision' => 'rejected', // Keeps schema compatibility
                'remarks' => '[RETURNED] ' . $request->input('remarks'),
            ]);

            // Create LoanActivity log
            \App\Models\LoanActivity::create([
                'loan_application_id' => $application->id,
                'user_id' => $user->id,
                'action' => 'returned',
                'description' => "Loan application was returned to the member by {$user->name} due to: " . $request->input('remarks'),
            ]);

            AuditLogger::log('loan_stage_returned', "Loan application LN-" . str_pad($application->id, 5, '0', STR_PAD_LEFT) . " was returned to the member by {$user->name}.", 'warning', $application);
        });

        // Send email notification to borrower about the loan application being returned for corrections
        $application->load('borrower');
        $borrower = $application->borrower;
        if ($borrower && $borrower->email) {
            $loanTypeName = config("loans.{$application->loan_category}.{$application->loan_type}.name", ucwords(str_replace('_', ' ', $application->loan_type)));
            
            try {
                \Illuminate\Support\Facades\Mail::to($borrower->email)->send(
                    new \App\Mail\LoanReturnedMail(
                        $borrower->name,
                        $loanTypeName,
                        (float) $application->requested_amount,
                        $request->input('remarks'),
                        (string) $application->id
                    )
                );
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::warning("Failed to send loan returned email to {$borrower->email}: " . $e->getMessage());
            }
        }

        return back()->with('success', 'Loan application successfully returned to the member.');
    }

    /**
     * Compute and lock financial summary details for HR/Audit on final release.
     */
    protected function calculateLoanDisbursementDetails(LoanApplication $application): void
    {
        $approvedAmount = (float) $application->requested_amount;
        $application->approved_amount = $approvedAmount;

        $termMonths = (int) ($application->form_data['term_months'] ?? 12);
        $application->term_months = $termMonths;

        // Fetch interest rate (prioritize locked application rate, or loan product rate for the term, or fallback 5%)
        $rate = $application->interest_rate;
        if ($rate === null || (float)$rate === 0.0) {
            if ($application->loan) {
                $rate = $application->loan->getInterestRateForTerm($termMonths);
            } else {
                $rate = 5.00;
            }
        }
        $application->interest_rate = (float) $rate;

        // Flat Rate Interest Formula: Total Interest = Principal * (Rate/100) * (Months / 12)
        $totalInterest = $approvedAmount * ($rate / 100) * ($termMonths / 12);
        $application->total_interest = $totalInterest;

        $totalPayable = $approvedAmount + $totalInterest;
        $application->total_payable = $totalPayable;

        $application->monthly_amortization = $totalPayable / $termMonths;

        // Service charge from config or 0.00
        $serviceCharge = 0.00;
        $category = $application->loan_category;
        $type = $application->loan_type;
        $config = config("loans.{$category}.{$type}");
        if ($config && isset($config['service_charge']) && is_numeric($config['service_charge'])) {
            $serviceCharge = (float) $config['service_charge'];
        }
        $application->service_charge = $serviceCharge;
        $application->net_proceeds = $approvedAmount - $serviceCharge;

        $application->release_date = now();
        $application->maturity_date = now()->addMonths($termMonths);
    }

    /**
     * Securely stream an attached loan compliance document.
     */
    public function viewDocument(LoanDocument $document)
    {
        $application = $document->loanApplication;
        if (!$application) {
            return $this->renderFrameError('Loan application not found.', 404);
        }

        $this->authorizeDocumentAccess($application);

        $disk = User::storageDisk();
        if (Storage::disk($disk)->exists($document->file_path)) {
            return Storage::disk($disk)->response($document->file_path, $document->original_name, [
                'Content-Type' => $document->mime_type ?: 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . addslashes($document->original_name) . '"',
                'X-Frame-Options' => 'SAMEORIGIN',
                'Content-Security-Policy' => "frame-ancestors 'self' https://loans.sako-central.org https://sako-central.org https://*.sako-central.org http://localhost:* http://127.0.0.1:*",
            ]);
        }

        return $this->renderFrameError("The document file '{$document->original_name}' could not be located in cloud storage. If this loan was filed before cloud storage was enabled, the document may need to be re-uploaded.", 404);
    }

    /**
     * Securely stream an attached accounting ledger document.
     */
    public function viewLedger(LoanApplication $application)
    {
        $this->authorizeDocumentAccess($application);

        if (!$application->ledger_path) {
            return $this->renderFrameError('Accounting ledger has not been uploaded for this loan.', 404);
        }

        $disk = User::storageDisk();
        if (Storage::disk($disk)->exists($application->ledger_path)) {
            return Storage::disk($disk)->response($application->ledger_path, 'Loan_Ledger_LN-' . str_pad($application->id, 5, '0', STR_PAD_LEFT) . '.pdf', [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="Loan_Ledger_LN-' . str_pad($application->id, 5, '0', STR_PAD_LEFT) . '.pdf"',
                'X-Frame-Options' => 'SAMEORIGIN',
                'Content-Security-Policy' => "frame-ancestors 'self' https://loans.sako-central.org https://sako-central.org https://*.sako-central.org http://localhost:* http://127.0.0.1:*",
            ]);
        }

        return $this->renderFrameError('Accounting ledger file could not be located in cloud storage.', 404);
    }

    /**
     * Securely stream an attached payment schedule document.
     */
    public function viewSchedule(LoanApplication $application)
    {
        $this->authorizeDocumentAccess($application);

        if (!$application->schedule_path) {
            return $this->renderFrameError('Payment schedule has not been uploaded for this loan.', 404);
        }

        $disk = User::storageDisk();
        if (Storage::disk($disk)->exists($application->schedule_path)) {
            return Storage::disk($disk)->response($application->schedule_path, 'Amortization_Schedule_LN-' . str_pad($application->id, 5, '0', STR_PAD_LEFT) . '.pdf', [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="Amortization_Schedule_LN-' . str_pad($application->id, 5, '0', STR_PAD_LEFT) . '.pdf"',
                'X-Frame-Options' => 'SAMEORIGIN',
                'Content-Security-Policy' => "frame-ancestors 'self' https://loans.sako-central.org https://sako-central.org https://*.sako-central.org http://localhost:* http://127.0.0.1:*",
            ]);
        }

        return $this->renderFrameError('Payment schedule file could not be located in cloud storage.', 404);
    }

    /**
     * Helper to render an elegant in-frame message if a document cannot be streamed.
     */
    protected function renderFrameError(string $message, int $statusCode = 404)
    {
        $html = '<!DOCTYPE html><html><head><meta charset="utf-8"><title>Document Unavailable</title></head>' .
            '<body style="margin:0;font-family:system-ui,-apple-system,sans-serif;background:#0b1120;color:#e2e8f0;display:flex;flex-direction:column;align-items:center;justify-content:center;height:100vh;padding:24px;text-align:center;box-sizing:border-box;">' .
            '<div style="width:48px;height:48px;background:rgba(239,68,68,0.15);border:1px solid rgba(239,68,68,0.3);border-radius:12px;display:flex;align-items:center;justify-content:center;margin-bottom:16px;color:#f87171;font-weight:bold;font-size:20px;">!</div>' .
            '<h2 style="color:#f87171;margin:0 0 8px 0;font-size:18px;font-weight:700;">Document Unavailable</h2>' .
            '<p style="color:#94a3b8;max-width:480px;font-size:13px;line-height:1.6;margin:0 0 20px 0;">' . htmlspecialchars($message) . '</p>' .
            '<a href="javascript:window.location.reload()" style="display:inline-block;padding:8px 16px;background:#1e293b;color:#cbd5e1;text-decoration:none;border-radius:8px;font-size:12px;font-weight:600;border:1px solid #334155;">Retry</a>' .
            '</body></html>';

        return response($html, $statusCode, [
            'Content-Type' => 'text/html; charset=UTF-8',
            'X-Frame-Options' => 'SAMEORIGIN',
            'Content-Security-Policy' => "frame-ancestors 'self' https://loans.sako-central.org https://sako-central.org https://*.sako-central.org http://localhost:* http://127.0.0.1:*",
        ]);
    }

    /**
     * Helper to verify if the authenticated user has rights to view loan compliance files.
     */
    protected function authorizeDocumentAccess(LoanApplication $application): void
    {
        $user = auth()->user();
        if (!$user) {
            abort(403, 'Unauthorized.');
        }

        $isBorrower = ($application->user_id === $user->id);
        $isAdmin = in_array($user->role, ['admin', 'super_admin']);
        $isApprover = method_exists($user, 'roles') && $user->roles()->exists();
        $isComaker = false;
        if (!empty($application->form_data['comakers'])) {
            $isComaker = in_array($user->id, $application->form_data['comakers']) || in_array((string)$user->id, $application->form_data['comakers']);
        }

        if (!$isBorrower && !$isAdmin && !$isApprover && !$isComaker) {
            abort(403, 'You do not have permission to view this document.');
        }
    }
}
