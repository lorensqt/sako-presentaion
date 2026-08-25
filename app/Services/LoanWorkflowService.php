<?php

namespace App\Services;

use App\Models\LoanApplication;

class LoanWorkflowService
{
    /**
     * The standard sequential chain of approval stages.
     * These slugs correspond to standard dynamic role slugs in the roles table.
     */
    protected array $standardFlow = [
        'comakers',
        'sako_staff',
        'hrmd_staff',
        'credit_committee',
        'accounting',
        'releasing_officer'
    ];

    /**
     * Get the active approval flow for a given loan application.
     */
    public function getFlowFor(LoanApplication $application): array
    {
        if ($application->loan && is_array($application->loan->approval_flow)) {
            return $application->loan->approval_flow;
        }
        return $this->standardFlow;
    }

    /**
     * Retrieve the complete list of workflow steps for a loan application,
     * showing skipped, completed, current, pending, or cancelled status for each.
     */
    public function getWorkflowDetails(LoanApplication $application): array
    {
        $steps = [];
        // Key approvals by stage slug
        $approvals = $application->approvals->keyBy('stage_role_slug');
        $isRejected = $application->status === 'rejected';

        $flow = $this->getFlowFor($application);

        // Find index of current stage
        $currentStageIndex = array_search($application->current_stage, $flow);

        foreach ($flow as $index => $stage) {
            $skipped = $this->shouldSkipStage($stage, $application);
            $approval = $approvals->get($stage);

            $status = 'pending'; // default
            $actor = null;
            $decision = null;
            $remarks = null;
            $date = null;

            if ($skipped) {
                $status = 'skipped';
            } elseif ($approval) {
                $status = $approval->decision; // 'approved' or 'rejected'
                $actor = $approval->actor ? $approval->actor->name : 'System/Staff';
                $decision = $approval->decision;
                $remarks = $approval->remarks;
                $date = $approval->created_at->format('M d, Y h:i A');
            } else {
                if ($isRejected) {
                    $status = 'cancelled';
                } elseif ($application->status === 'approved' || $application->status === 'released') {
                    $status = 'completed';
                } elseif ($stage === $application->current_stage) {
                    $status = 'current';
                } elseif ($currentStageIndex !== false && $index < $currentStageIndex) {
                    $status = 'skipped';
                } else {
                    $status = 'pending';
                }
            }

            $steps[] = [
                'stage' => $stage,
                'label' => ucwords(str_replace('_', ' ', $stage)),
                'status' => $status,
                'skipped' => $skipped,
                'actor' => $actor,
                'decision' => $decision,
                'remarks' => $remarks,
                'date' => $date,
            ];
        }

        return $steps;
    }

    /**
     * Compute the initial stage of a loan application.
     */
    public function getInitialStage(LoanApplication $application): string
    {
        $flow = $this->getFlowFor($application);
        $firstStage = $flow[0] ?? 'completed';
        if ($firstStage === 'completed') {
            return 'completed';
        }
        if ($this->shouldSkipStage($firstStage, $application)) {
            $application->current_stage = $firstStage;
            return $this->getNextStage($application) ?? 'completed';
        }
        return $firstStage;
    }

    /**
     * Determine what the next stage is for a given loan application.
     * Returns the slug of the next Role stage, or null if complete.
     */
    public function getNextStage(LoanApplication $application): ?string
    {
        $flow = $this->getFlowFor($application);
        $current = $application->current_stage;
        $currentIndex = array_search($current, $flow);

        if ($currentIndex === false) {
            return null; // Invalid or untracked stage
        }

        // Search through subsequent stages to find the next active one (handling conditional skips)
        for ($i = $currentIndex + 1; $i < count($flow); $i++) {
            $nextStage = $flow[$i];

            if ($this->shouldSkipStage($nextStage, $application)) {
                continue; // Skip this stage, evaluate next
            }

            return $nextStage;
        }

        return null; // End of approval chain
    }

    /**
     * Evaluates dynamic rules configured in config/loans.php or database loans to determine if a stage is skipped.
     */
    protected function shouldSkipStage(string $stage, LoanApplication $application): bool
    {
        $category = $application->loan_category;
        $type = $application->loan_type;
        $config = config("loans.{$category}.{$type}");

        // If the loan product has a custom approval flow, we don't skip stages that are explicitly included!
        if ($application->loan && is_array($application->loan->approval_flow)) {
            // Note: 'comakers' should still be skipped if the required co-makers count is 0
            if ($stage === 'comakers') {
                $requiredComakers = $this->getRequiredComakersCount($category, $type, (float) $application->requested_amount, $application);
                return $requiredComakers === 0;
            }
            // For other stages in the custom flow, we DO NOT skip them since they were explicitly selected!
            return false;
        }

        // Rule 0: Comakers Check
        if ($stage === 'comakers') {
            $requiredComakers = $this->getRequiredComakersCount($category, $type, (float) $application->requested_amount, $application);
            return $requiredComakers === 0;
        }

        // Rule 1: HRMD Approval Check
        if ($stage === 'hrmd_staff') {
            $requiresHrmd = $application->loan ? $application->loan->hrmd_approval : ($config['hrmd_approval'] ?? false);
            return !$requiresHrmd;
        }

        // Rule 2: Credit Committee Check (can expand or adjust as needed)
        if ($stage === 'credit_committee') {
            // E.g., skip credit committee if a loan doesn't specify comakers or is extremely small
            // but for safety, default to requiring credit committee unless explicitly configured.
            return false;
        }

        return false;
    }

    /**
     * Compute the required co-makers count based on configuration and dynamic thresholds.
     */
    public function getRequiredComakersCount(string $category, string $type, float $amount, ?LoanApplication $application = null): int
    {
        // 1. First attempt to get the comakers configuration from the database Loan product
        $loanProduct = null;
        if ($application && $application->loan) {
            $loanProduct = $application->loan;
        } else {
            $loanProduct = \App\Models\Loan::where('category', $category)
                ->where('type_key', $type)
                ->first();
        }

        if ($loanProduct) {
            $comakersConfig = $loanProduct->comakers;
        } else {
            // Fallback to static configuration file
            $config = config("loans.{$category}.{$type}");
            if (!$config) {
                return 0;
            }
            $comakersConfig = $config['comakers'] ?? 0;
        }

        if (is_numeric($comakersConfig)) {
            return (int) $comakersConfig;
        }

        if (is_array($comakersConfig)) {
            foreach ($comakersConfig as $condition => $count) {
                // Clean the condition string to get numeric value
                $numericValue = (float) preg_replace('/[^\d\.]/', '', $condition);
                
                // If condition contains '≤' or '<='
                if (str_contains($condition, '≤') || str_contains($condition, '<=')) {
                    if ($amount <= $numericValue) {
                        return (int) $count;
                    }
                }
                // If condition contains '>'
                elseif (str_contains($condition, '>')) {
                    if ($amount > $numericValue) {
                        return (int) $count;
                    }
                }
            }
        }

        return 0;
    }
}
