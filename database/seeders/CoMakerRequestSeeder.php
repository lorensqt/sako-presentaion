<?php

namespace Database\Seeders;

use App\Models\Loan;
use App\Models\LoanActivity;
use App\Models\LoanApplication;
use App\Models\LoanApproval;
use App\Models\LoanComaker;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CoMakerRequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Identify target co-maker test users (Super Admin & Standard Member)
        $targetMembers = User::whereIn('email', [
            'member@mlsako.com',
            'castillojohnlaurence0@gmail.com',
        ])->orWhere('company_id', '20248216')->get();

        if ($targetMembers->isEmpty()) {
            $this->command->warn('No target test member found. Please seed users first.');
            return;
        }

        $targetMemberIds = $targetMembers->pluck('id')->toArray();

        // 2. Ensure borrower accounts exist
        $borrowerJane = User::updateOrCreate(
            ['email' => 'jane.comaker@mlsako.com'],
            [
                'name' => 'Jane Doe',
                'company_id' => '20248217',
                'role' => 'member',
                'address' => 'Pahina Central, Cebu City, 6000',
                'contact_number' => '09479992493',
                'password' => Hash::make('password'),
            ]
        );

        $borrowerJohn = User::updateOrCreate(
            ['email' => 'john.comaker@mlsako.com'],
            [
                'name' => 'John Smith',
                'company_id' => '20248218',
                'role' => 'member',
                'address' => 'Capitol Site, Cebu City, 6000',
                'contact_number' => '09479992494',
                'password' => Hash::make('password'),
            ]
        );

        $borrowerMaria = User::updateOrCreate(
            ['email' => 'maria.borrower@mlsako.com'],
            [
                'name' => 'Maria Santos',
                'company_id' => '20248219',
                'role' => 'member',
                'address' => 'Banilad, Cebu City, 6000',
                'contact_number' => '09479992495',
                'password' => Hash::make('password'),
            ]
        );

        // Fetch loan template models if available
        $regularLoan = Loan::where('type_key', 'maxi')->first();
        $emergencyLoan = Loan::where('type_key', 'emergency_loan')->first();
        $commodityLoan = Loan::where('type_key', 'appliance_gadget')->first();
        $pettyCashLoan = Loan::where('type_key', 'petty_cash')->first();
        $travelLoan = Loan::where('type_key', 'travel_loan')->first();

        // -------------------------------------------------------------
        // APPLICATION 1: PENDING CO-MAKER SIGN-OFF (Jane Doe - Maxi Loan)
        // -------------------------------------------------------------
        $app1 = LoanApplication::updateOrCreate(
            [
                'user_id' => $borrowerJane->id,
                'loan_type' => 'maxi',
                'status' => 'pending',
                'current_stage' => 'comakers',
            ],
            [
                'loan_id' => $regularLoan ? $regularLoan->id : null,
                'loan_category' => 'regular',
                'requested_amount' => 45000.00,
                'approved_amount' => null,
                'interest_rate' => 1.50,
                'term_months' => 12,
                'total_interest' => 8100.00,
                'total_payable' => 53100.00,
                'monthly_amortization' => 4425.00,
                'service_charge' => 500.00,
                'net_proceeds' => 44500.00,
                'form_data' => [
                    'comakers' => $targetMemberIds,
                    'term_months' => 12,
                    'member_remarks' => 'Tuition fees and textbook expenses for children first semester enrollment.',
                ],
                'created_at' => Carbon::now()->subDays(2),
                'updated_at' => Carbon::now()->subDays(2),
            ]
        );

        // Ensure LoanComaker relational records exist
        foreach ($targetMemberIds as $mid) {
            LoanComaker::updateOrCreate(
                ['loan_application_id' => $app1->id, 'user_id' => $mid],
                ['status' => 'pending', 'remarks' => null, 'actioned_at' => null]
            );
        }

        // Clean out any approvals so it remains pending
        LoanApproval::where('loan_application_id', $app1->id)->where('stage_role_slug', 'comakers')->delete();

        LoanActivity::firstOrCreate(
            ['loan_application_id' => $app1->id, 'action' => 'submitted'],
            [
                'user_id' => $borrowerJane->id,
                'description' => "Loan application submitted by member {$borrowerJane->name}.",
                'created_at' => Carbon::now()->subDays(2),
            ]
        );

        // -------------------------------------------------------------
        // APPLICATION 2: PENDING CO-MAKER SIGN-OFF (John Smith - Emergency Loan)
        // -------------------------------------------------------------
        $app2 = LoanApplication::updateOrCreate(
            [
                'user_id' => $borrowerJohn->id,
                'loan_type' => 'emergency_loan',
                'status' => 'pending',
                'current_stage' => 'comakers',
            ],
            [
                'loan_id' => $emergencyLoan ? $emergencyLoan->id : null,
                'loan_category' => 'emergency',
                'requested_amount' => 25000.00,
                'approved_amount' => null,
                'interest_rate' => 1.00,
                'term_months' => 6,
                'total_interest' => 1500.00,
                'total_payable' => 26500.00,
                'monthly_amortization' => 4416.67,
                'service_charge' => 300.00,
                'net_proceeds' => 24700.00,
                'form_data' => [
                    'comakers' => $targetMemberIds,
                    'term_months' => 6,
                    'member_remarks' => 'Urgent medical and dental hospitalization copay.',
                ],
                'created_at' => Carbon::now()->subHours(18),
                'updated_at' => Carbon::now()->subHours(18),
            ]
        );

        foreach ($targetMemberIds as $mid) {
            LoanComaker::updateOrCreate(
                ['loan_application_id' => $app2->id, 'user_id' => $mid],
                ['status' => 'pending', 'remarks' => null, 'actioned_at' => null]
            );
        }

        LoanApproval::where('loan_application_id', $app2->id)->where('stage_role_slug', 'comakers')->delete();

        LoanActivity::firstOrCreate(
            ['loan_application_id' => $app2->id, 'action' => 'submitted'],
            [
                'user_id' => $borrowerJohn->id,
                'description' => "Loan application submitted by member {$borrowerJohn->name}.",
                'created_at' => Carbon::now()->subHours(18),
            ]
        );

        // -------------------------------------------------------------
        // APPLICATION 3: PENDING CO-MAKER SIGN-OFF (Maria Santos - Commodity)
        // -------------------------------------------------------------
        $app3 = LoanApplication::updateOrCreate(
            [
                'user_id' => $borrowerMaria->id,
                'loan_type' => 'appliance_gadget',
                'status' => 'pending',
                'current_stage' => 'comakers',
            ],
            [
                'loan_id' => $commodityLoan ? $commodityLoan->id : null,
                'loan_category' => 'commodity',
                'requested_amount' => 32000.00,
                'approved_amount' => null,
                'interest_rate' => 1.25,
                'term_months' => 12,
                'total_interest' => 4800.00,
                'total_payable' => 36800.00,
                'monthly_amortization' => 3066.67,
                'service_charge' => 350.00,
                'net_proceeds' => 31650.00,
                'form_data' => [
                    'comakers' => $targetMemberIds,
                    'term_months' => 12,
                    'member_remarks' => 'Workstation laptop and inverter air conditioner for home office.',
                ],
                'created_at' => Carbon::now()->subHours(6),
                'updated_at' => Carbon::now()->subHours(6),
            ]
        );

        foreach ($targetMemberIds as $mid) {
            LoanComaker::updateOrCreate(
                ['loan_application_id' => $app3->id, 'user_id' => $mid],
                ['status' => 'pending', 'remarks' => null, 'actioned_at' => null]
            );
        }

        LoanApproval::where('loan_application_id', $app3->id)->where('stage_role_slug', 'comakers')->delete();

        LoanActivity::firstOrCreate(
            ['loan_application_id' => $app3->id, 'action' => 'submitted'],
            [
                'user_id' => $borrowerMaria->id,
                'description' => "Loan application submitted by member {$borrowerMaria->name}.",
                'created_at' => Carbon::now()->subHours(6),
            ]
        );

        // -------------------------------------------------------------
        // APPLICATION 4: HISTORICAL CO-MAKER SIGNED & APPROVED (Jane Doe)
        // -------------------------------------------------------------
        $app4 = LoanApplication::updateOrCreate(
            [
                'user_id' => $borrowerJane->id,
                'loan_type' => 'petty_cash',
                'status' => 'approved',
            ],
            [
                'loan_id' => $pettyCashLoan ? $pettyCashLoan->id : null,
                'loan_category' => 'regular',
                'requested_amount' => 10000.00,
                'approved_amount' => 10000.00,
                'interest_rate' => 1.00,
                'term_months' => 3,
                'total_interest' => 300.00,
                'total_payable' => 10300.00,
                'monthly_amortization' => 3433.33,
                'service_charge' => 200.00,
                'net_proceeds' => 9800.00,
                'current_stage' => 'releasing_officer',
                'form_data' => [
                    'comakers' => $targetMemberIds,
                    'term_months' => 3,
                    'member_remarks' => 'Urgent office project supplies reimbursement.',
                ],
                'created_at' => Carbon::now()->subWeeks(3),
                'updated_at' => Carbon::now()->subWeeks(2),
            ]
        );

        foreach ($targetMemberIds as $mid) {
            LoanComaker::updateOrCreate(
                ['loan_application_id' => $app4->id, 'user_id' => $mid],
                [
                    'status' => 'approved',
                    'remarks' => 'Verified member capacity to pay and authorized co-guarantee.',
                    'actioned_at' => Carbon::now()->subWeeks(2),
                ]
            );

            LoanApproval::updateOrCreate(
                [
                    'loan_application_id' => $app4->id,
                    'stage_role_slug' => 'comakers',
                    'actioned_by_user_id' => $mid,
                ],
                [
                    'decision' => 'approved',
                    'remarks' => 'Verified member capacity to pay and authorized co-guarantee.',
                    'created_at' => Carbon::now()->subWeeks(2),
                    'updated_at' => Carbon::now()->subWeeks(2),
                ]
            );
        }

        // -------------------------------------------------------------
        // APPLICATION 5: HISTORICAL CO-MAKER SIGNED & REJECTED (John Smith)
        // -------------------------------------------------------------
        $app5 = LoanApplication::updateOrCreate(
            [
                'user_id' => $borrowerJohn->id,
                'loan_type' => 'travel_loan',
                'status' => 'returned',
            ],
            [
                'loan_id' => $travelLoan ? $travelLoan->id : null,
                'loan_category' => 'travel',
                'requested_amount' => 50000.00,
                'approved_amount' => null,
                'interest_rate' => 2.00,
                'term_months' => 12,
                'total_interest' => 12000.00,
                'total_payable' => 62000.00,
                'monthly_amortization' => 5166.67,
                'service_charge' => 500.00,
                'net_proceeds' => 49500.00,
                'current_stage' => 'comakers',
                'form_data' => [
                    'comakers' => $targetMemberIds,
                    'term_months' => 12,
                    'member_remarks' => 'Personal vacation package loan.',
                ],
                'created_at' => Carbon::now()->subMonth(),
                'updated_at' => Carbon::now()->subWeeks(3),
            ]
        );

        foreach ($targetMemberIds as $mid) {
            LoanComaker::updateOrCreate(
                ['loan_application_id' => $app5->id, 'user_id' => $mid],
                [
                    'status' => 'rejected',
                    'remarks' => 'Borrower has other active loan commitments; unable to act as guarantor at this time.',
                    'actioned_at' => Carbon::now()->subWeeks(3),
                ]
            );

            LoanApproval::updateOrCreate(
                [
                    'loan_application_id' => $app5->id,
                    'stage_role_slug' => 'comakers',
                    'actioned_by_user_id' => $mid,
                ],
                [
                    'decision' => 'rejected',
                    'remarks' => 'Borrower has other active loan commitments; unable to act as guarantor at this time.',
                    'created_at' => Carbon::now()->subWeeks(3),
                    'updated_at' => Carbon::now()->subWeeks(3),
                ]
            );
        }

        $this->command->info('Co-maker request test dataset successfully seeded!');
        $this->command->info("- 3 Pending Requests (Jane Doe ₱45k, John Smith ₱25k, Maria Santos ₱32k)");
        $this->command->info("- 2 Historical Endorsements (Jane Doe ₱10k [Approved], John Smith ₱50k [Declined])");
        $this->command->info("- Designated Co-maker ID(s): " . implode(', ', $targetMemberIds));
    }
}
