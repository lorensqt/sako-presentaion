<?php

namespace App\Http\Controllers;

use App\Models\LoanApplication;
use App\Models\LoanComaker;
use App\Services\LoanWorkflowService;
use App\Services\AuditLogger;
use App\Mail\CoMakerDeclinedMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        }

        DB::transaction(function () use ($application, $user, $currentStageRole, $request) {
            // 1. Record individual action in approval ledger
            $application->approvals()->create([
                'stage_role_slug' => $currentStageRole,
                'actioned_by_user_id' => $user->id,
                'decision' => 'approved',
                'remarks' => $request->input('remarks'),
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
            } else {
                $nextStage = $this->workflowService->getNextStage($application);

                if ($nextStage) {
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
            $loanTypeName = config("loans.{$application->loan_category}.{$application->loan_type}.name", ucwords(str_replace('_', ' ', $application->loan_type)));
            $termMonths = $application->form_data['term_months'] ?? $application->term_months ?? 12;

            try {
                \Illuminate\Support\Facades\Mail::to($borrower->email)->send(
                    new \App\Mail\LoanReleasedMail(
                        $borrower->name,
                        $loanTypeName,
                        (float) $application->requested_amount,
                        (int) $termMonths,
                        (string) $application->id
                    )
                );
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::warning("Failed to send loan released email to {$borrower->email}: " . $e->getMessage());
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

        // Fetch interest rate (from application columns, or fallback to the product, or fallback to default 5%)
        $rate = $application->interest_rate ?? 5.00;
        if ($application->loan) {
            $rate = $application->loan->interest_rate;
        }
        $application->interest_rate = $rate;

        $termMonths = (int) ($application->form_data['term_months'] ?? 12);
        $application->term_months = $termMonths;

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
}
