<?php

namespace Database\Seeders;

use App\Models\Loan;
use Illuminate\Database\Seeder;

class LoanProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $loansConfig = config('loans');

        if (!$loansConfig || !is_array($loansConfig)) {
            return;
        }

        foreach ($loansConfig as $category => $types) {
            if (!is_array($types)) {
                continue;
            }

            foreach ($types as $typeKey => $config) {
                if (!is_array($config)) {
                    continue;
                }

                // Handle partner (can be string or array)
                $partner = null;
                if (isset($config['partner'])) {
                    $partner = is_array($config['partner']) 
                        ? implode(', ', $config['partner']) 
                        : $config['partner'];
                }

                // Handle comakers (can be string, integer, or conditional array)
                $comakers = $config['comakers'] ?? 0;

                // Handle max term months
                $maxTerm = null;
                if (isset($config['max_term_months'])) {
                    $maxTerm = (int) $config['max_term_months'];
                } elseif (isset($config['term_months'])) {
                    $maxTerm = (int) $config['term_months'];
                } elseif (isset($config['max_term_years'])) {
                    $maxTerm = (int) ($config['max_term_years'] * 12);
                }

                // Generate random interest rate between 3.00% and 12.00% (with 0.25% increments for realism)
                $randomInterest = rand(12, 48) * 0.25; 

                Loan::updateOrCreate(
                    [
                        'category' => $category,
                        'type_key' => $typeKey,
                    ],
                    [
                        'name' => $config['name'] ?? ucwords(str_replace('_', ' ', $typeKey)),
                        'partner' => $partner,
                        'loanable_amount' => isset($config['loanable_amount']) ? (string) $config['loanable_amount'] : null,
                        'fixed_deposit' => is_numeric($config['fixed_deposit'] ?? null) ? (float) $config['fixed_deposit'] : 0.00,
                        'comakers' => $comakers,
                        'interest_rate' => $randomInterest,
                        'max_term_months' => $maxTerm,
                        'minimum_membership_months' => $config['minimum_membership_months'] ?? null,
                        'hrmd_approval' => $config['hrmd_approval'] ?? false,
                        'is_active' => true,
                        'metadata' => $config, // store whole config array as flexible backup metadata
                    ]
                );
            }
        }
    }
}
