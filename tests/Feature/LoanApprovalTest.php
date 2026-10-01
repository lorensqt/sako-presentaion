<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Loan;
use App\Models\LoanApplication;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class LoanApprovalTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);
        Storage::fake('public');
    }

    /**
     * Helper to create a user.
     */
    protected function createUser(string $name, string $role = 'member'): User
    {
        return User::create([
            'name' => $name,
            'email' => strtolower(str_replace(' ', '.', $name)) . '@example.com',
            'password' => bcrypt('password'),
            'pin' => bcrypt('123456'),
            'company_id' => (string) rand(10000000, 99999999),
            'role' => $role,
        ]);
    }

    /**
     * Test applying for a loan with no co-makers.
     */
    public function test_apply_loan_without_comakers_routes_straight_to_sako_staff(): void
    {
        $borrower = $this->createUser('John Borrower');
        $file = UploadedFile::fake()->create('compliance_docs.pdf', 100, 'application/pdf');

        $response = $this->actingAs($borrower)->post('/loans/apply', [
            'category' => 'travel',
            'type' => 'travel_loan',
            'amount' => 30000,
            'term' => 12,
            'remarks' => 'Vacation trip',
            'pin' => '123456',
            'documents' => [$file],
        ]);

        $response->assertRedirect('/myloans');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('loan_applications', [
            'user_id' => $borrower->id,
            'loan_category' => 'travel',
            'loan_type' => 'travel_loan',
            'current_stage' => 'sako_staff',
            'status' => 'pending',
        ]);
    }

    /**
     * Test applying for a loan with co-makers.
     */
    public function test_apply_loan_with_comakers_pauses_at_comakers_stage(): void
    {
        $borrower = $this->createUser('John Borrower');
        $comaker1 = $this->createUser('Comaker One');
        $file = UploadedFile::fake()->create('compliance_docs.pdf', 100, 'application/pdf');

        $response = $this->actingAs($borrower)->post('/loans/apply', [
            'category' => 'special',
            'type' => 'birthday', // requires 1 comaker
            'amount' => 5000,
            'term' => 5,
            'comakers' => [$comaker1->id],
            'remarks' => 'Birthday cash',
            'pin' => '123456',
            'documents' => [$file],
        ]);

        $response->assertRedirect('/myloans');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('loan_applications', [
            'user_id' => $borrower->id,
            'loan_category' => 'special',
            'loan_type' => 'birthday',
            'current_stage' => 'comakers',
            'status' => 'pending',
        ]);
    }

    /**
     * Test sequential approvals of multiple co-makers.
     */
    public function test_sequential_comaker_approvals_advances_stage(): void
    {
        $borrower = $this->createUser('John Borrower');
        $comaker1 = $this->createUser('Comaker One');
        $comaker2 = $this->createUser('Comaker Two');

        // Create loan with 2 comakers (using sako_care which configures comakers = 2)
        $application = LoanApplication::create([
            'user_id' => $borrower->id,
            'loan_category' => 'health',
            'loan_type' => 'sako_care',
            'requested_amount' => 15000,
            'current_stage' => 'comakers',
            'status' => 'pending',
            'form_data' => [
                'term_months' => 12,
                'comakers' => [$comaker1->id, $comaker2->id],
            ]
        ]);

        // 1. First comaker approves. Should record approval but stage remains 'comakers'
        $response = $this->actingAs($comaker1)->post("/loans/{$application->id}/approve", [
            'remarks' => 'Approved by comaker 1',
        ]);
        $response->assertRedirect();
        $response->assertSessionHas('success');

        $application->refresh();
        $this->assertEquals('comakers', $application->current_stage);

        // 2. Try to approve again with same comaker. Should be blocked.
        $response2 = $this->actingAs($comaker1)->post("/loans/{$application->id}/approve", [
            'remarks' => 'Approving again',
        ]);
        $response2->assertSessionHas('error');

        // 3. Second comaker approves. Should advance stage to 'sako_staff'
        $response3 = $this->actingAs($comaker2)->post("/loans/{$application->id}/approve", [
            'remarks' => 'Approved by comaker 2',
        ]);
        $response3->assertRedirect();
        $response3->assertSessionHas('success');

        $application->refresh();
        $this->assertEquals('sako_staff', $application->current_stage);
    }

    /**
     * Test co-maker rejection keeps loan pending and updates comaker status.
     */
    public function test_comaker_rejection_keeps_loan_pending_for_replacement(): void
    {
        $borrower = $this->createUser('John Borrower');
        $comaker1 = $this->createUser('Comaker One');

        $application = LoanApplication::create([
            'user_id' => $borrower->id,
            'loan_category' => 'health',
            'loan_type' => 'sako_care',
            'requested_amount' => 15000,
            'current_stage' => 'comakers',
            'status' => 'pending',
            'form_data' => [
                'term_months' => 12,
                'comakers' => [$comaker1->id],
            ]
        ]);

        \App\Models\LoanComaker::create([
            'loan_application_id' => $application->id,
            'user_id' => $comaker1->id,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($comaker1)->post("/loans/{$application->id}/reject", [
            'remarks' => 'No budget to co-sign right now',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $application->refresh();
        $this->assertEquals('pending', $application->status);
        $this->assertEquals('comakers', $application->current_stage);

        $comakerRecord = \App\Models\LoanComaker::where('loan_application_id', $application->id)
            ->where('user_id', $comaker1->id)
            ->first();
        $this->assertEquals('rejected', $comakerRecord->status);
        $this->assertEquals('No budget to co-sign right now', $comakerRecord->remarks);

        // Verify activity was logged
        $activity = \App\Models\LoanActivity::where('loan_application_id', $application->id)
            ->where('action', 'comaker_rejected')
            ->first();
        $this->assertNotNull($activity);
        $this->assertStringContainsString('Comaker One', $activity->description);
    }

    /**
     * Test replacing a rejected co-maker.
     */
    public function test_member_can_replace_rejected_comaker(): void
    {
        $borrower = $this->createUser('John Borrower');
        $comaker1 = $this->createUser('Comaker One');
        $comaker2 = $this->createUser('Comaker Two');

        $application = LoanApplication::create([
            'user_id' => $borrower->id,
            'loan_category' => 'health',
            'loan_type' => 'sako_care',
            'requested_amount' => 15000,
            'current_stage' => 'comakers',
            'status' => 'pending',
            'form_data' => [
                'term_months' => 12,
                'comakers' => [$comaker1->id],
            ]
        ]);

        \App\Models\LoanComaker::create([
            'loan_application_id' => $application->id,
            'user_id' => $comaker1->id,
            'status' => 'rejected',
        ]);

        $response = $this->actingAs($borrower)->patch("/loans/{$application->id}/replace-comaker", [
            'old_comaker_id' => $comaker1->id,
            'new_comaker_id' => $comaker2->id,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $application->refresh();
        $comakerIds = $application->form_data['comakers'];
        $this->assertContains($comaker2->id, $comakerIds);
        $this->assertNotContains($comaker1->id, $comakerIds);

        // Verify historical co-maker record is still there
        $oldRecord = \App\Models\LoanComaker::where('loan_application_id', $application->id)
            ->where('user_id', $comaker1->id)
            ->first();
        $this->assertEquals('rejected', $oldRecord->status);

        // Verify new pending co-maker record was created
        $newRecord = \App\Models\LoanComaker::where('loan_application_id', $application->id)
            ->where('user_id', $comaker2->id)
            ->first();
        $this->assertEquals('pending', $newRecord->status);

        // Verify activity log
        $activity = \App\Models\LoanActivity::where('loan_application_id', $application->id)
            ->where('action', 'comaker_replaced')
            ->first();
        $this->assertNotNull($activity);
        $this->assertStringContainsString('Comaker One', $activity->description);
        $this->assertStringContainsString('Comaker Two', $activity->description);
    }

    /**
     * Test that non-assigned co-makers cannot endorse/reject a loan.
     */
    public function test_non_assigned_members_cannot_interact_with_comaker_stage(): void
    {
        $borrower = $this->createUser('John Borrower');
        $comaker1 = $this->createUser('Comaker One');
        $intruder = $this->createUser('Intruder Member');

        $application = LoanApplication::create([
            'user_id' => $borrower->id,
            'loan_category' => 'health',
            'loan_type' => 'sako_care',
            'requested_amount' => 15000,
            'current_stage' => 'comakers',
            'status' => 'pending',
            'form_data' => [
                'term_months' => 12,
                'comakers' => [$comaker1->id],
            ]
        ]);

        // Attempt approve
        $response = $this->actingAs($intruder)->post("/loans/{$application->id}/approve", [
            'remarks' => 'Intruding',
        ]);
        $response->assertSessionHas('error', 'You are not listed as a co-maker for this loan application.');

        // Attempt reject
        $response2 = $this->actingAs($intruder)->post("/loans/{$application->id}/reject", [
            'remarks' => 'Intruding',
        ]);
        $response2->assertSessionHas('error', 'You are not listed as a co-maker for this loan application.');
    }

    /**
     * Test that administrators can successfully download a loan's PDF contract document.
     */
    public function test_admins_can_export_loan_pdf(): void
    {
        $admin = $this->createUser('Sako Admin', 'admin');
        $borrower = $this->createUser('John Borrower');

        $application = LoanApplication::create([
            'user_id' => $borrower->id,
            'loan_category' => 'travel',
            'loan_type' => 'travel_loan',
            'requested_amount' => 30000,
            'current_stage' => 'sako_staff',
            'status' => 'pending',
            'form_data' => [
                'term_months' => 12,
                'member_remarks' => 'Vacation trip',
            ]
        ]);

        $response = $this->actingAs($admin)->get("/admin/loans/{$application->id}/pdf");

        // Verify PDF download starts or streams
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
    }

    /**
     * Test that a sako staff member can return a loan application due to lack of requirements.
     */
    public function test_sako_staff_can_return_loan_for_lack_of_requirements(): void
    {
        Mail::fake();

        $borrower = $this->createUser('John Borrower');
        $officer = $this->createUser('Sako Officer', 'sako_staff');
        
        $role = \App\Models\Role::firstOrCreate(['slug' => 'sako_staff'], ['name' => 'Sako Staff']);
        $officer->roles()->attach($role);

        $application = LoanApplication::create([
            'user_id' => $borrower->id,
            'loan_category' => 'travel',
            'loan_type' => 'travel_loan',
            'requested_amount' => 30000,
            'current_stage' => 'sako_staff',
            'status' => 'pending',
            'form_data' => [
                'term_months' => 12,
                'member_remarks' => 'Vacation trip',
            ]
        ]);

        $response = $this->actingAs($officer)->post("/loans/{$application->id}/return", [
            'remarks' => 'Lacking latest payslip document.',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Loan application successfully returned to the member.');

        $this->assertDatabaseHas('loan_applications', [
            'id' => $application->id,
            'status' => 'returned',
            'rejection_reason' => 'Lacking latest payslip document.',
        ]);

        $this->assertDatabaseHas('loan_activities', [
            'loan_application_id' => $application->id,
            'action' => 'returned',
            'description' => "Loan application was returned to the member by Sako Officer due to: Lacking latest payslip document.",
        ]);

        Mail::assertSent(\App\Mail\LoanReturnedMail::class, function ($mail) use ($borrower, $application) {
            return $mail->hasTo($borrower->email) &&
                   $mail->borrowerName === $borrower->name &&
                   $mail->loanId === (string) $application->id &&
                   $mail->remarks === 'Lacking latest payslip document.';
        });
    }

    /**
     * Test that a member can modify and resubmit a returned loan.
     */
    public function test_member_can_resubmit_returned_loan(): void
    {
        $borrower = $this->createUser('John Borrower');

        $application = LoanApplication::create([
            'user_id' => $borrower->id,
            'loan_category' => 'travel',
            'loan_type' => 'travel_loan',
            'requested_amount' => 30000,
            'current_stage' => 'sako_staff',
            'status' => 'returned',
            'form_data' => [
                'term_months' => 12,
                'member_remarks' => 'Vacation trip',
            ]
        ]);

        $file = UploadedFile::fake()->create('compliance_docs.pdf', 100, 'application/pdf');

        // Submit resubmission with corrected details
        $response = $this->actingAs($borrower)->post('/loans/apply', [
            'resubmit_id' => $application->id,
            'category' => 'travel',
            'type' => 'travel_loan',
            'amount' => 25000, // corrected amount
            'term' => 12,
            'remarks' => 'Vacation trip - updated with attachment description',
            'pin' => '123456',
            'documents' => [$file],
        ]);

        $response->assertRedirect('/myloans');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('loan_applications', [
            'id' => $application->id,
            'requested_amount' => 25000,
            'status' => 'pending',
            'current_stage' => 'sako_staff', // back in queue
        ]);

        $this->assertDatabaseHas('loan_activities', [
            'loan_application_id' => $application->id,
            'action' => 'resubmitted',
            'description' => 'Loan application resubmitted with corrected/updated requirements. Current Stage: Sako Staff',
        ]);
    }

    public function test_member_loan_submission_locks_tiered_interest_rate(): void
    {
        $borrower = User::factory()->create([
            'role' => 'member',
            'pin' => Hash::make('123456'),
        ]);

        $loan = Loan::create([
            'category' => 'special',
            'type_key' => 'tiered_special_loan',
            'name' => 'Special Tiered Loan Facility',
            'loanable_amount' => 50000.00,
            'fixed_deposit' => 0.00,
            'comakers' => 0,
            'interest_rate' => 10.00, // base rate
            'max_term_months' => 24,
            'available_terms' => [
                ['months' => 6, 'interest_rate' => 3.50],
                ['months' => 12, 'interest_rate' => 4.50],
                ['months' => 24, 'interest_rate' => 6.00],
            ],
            'is_active' => true,
        ]);

        $file = UploadedFile::fake()->create('id_doc.pdf', 100, 'application/pdf');

        $response = $this->actingAs($borrower)->post('/loans/apply', [
            'category' => 'special',
            'type' => 'tiered_special_loan',
            'amount' => 20000,
            'term' => 6, // 6 months matches 3.50%
            'pin' => '123456',
            'documents' => [$file],
        ]);

        $response->assertRedirect('/myloans');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('loan_applications', [
            'user_id' => $borrower->id,
            'loan_id' => $loan->id,
            'loan_category' => 'special',
            'loan_type' => 'tiered_special_loan',
            'requested_amount' => 20000,
            'interest_rate' => 3.50, // Confirms the exact tiered rate is locked!
            'status' => 'pending',
        ]);
    }

    public function test_member_loan_submission_rejects_unauthorized_term(): void
    {
        $borrower = User::factory()->create([
            'role' => 'member',
            'pin' => Hash::make('123456'),
        ]);

        Loan::create([
            'category' => 'special',
            'type_key' => 'tiered_fixed_terms',
            'name' => 'Fixed Terms Loan',
            'loanable_amount' => 50000.00,
            'fixed_deposit' => 0.00,
            'comakers' => 0,
            'interest_rate' => 5.00,
            'max_term_months' => 24,
            'available_terms' => [
                ['months' => 6, 'interest_rate' => 3.00],
                ['months' => 12, 'interest_rate' => 5.00],
            ],
            'is_active' => true,
        ]);

        $file = UploadedFile::fake()->create('id_doc.pdf', 100, 'application/pdf');

        // Submitting with term 9 (which is not allowed in available_terms)
        $response = $this->actingAs($borrower)->post('/loans/apply', [
            'category' => 'special',
            'type' => 'tiered_fixed_terms',
            'amount' => 15000,
            'term' => 9,
            'pin' => '123456',
            'documents' => [$file],
        ]);

        $response->assertSessionHasErrors(['term']);
    }

    public function test_member_loan_submission_rejects_amount_exceeding_limit(): void
    {
        $borrower = User::factory()->create([
            'role' => 'member',
            'pin' => Hash::make('123456'),
        ]);

        Loan::create([
            'category' => 'special',
            'type_key' => 'capped_loan',
            'name' => 'Capped Loan',
            'loanable_amount' => 30000.00,
            'fixed_deposit' => 0.00,
            'comakers' => 0,
            'interest_rate' => 5.00,
            'max_term_months' => 12,
            'is_active' => true,
        ]);

        $file = UploadedFile::fake()->create('id_doc.pdf', 100, 'application/pdf');

        // Submitting with amount 40000 (exceeds 30000 cap)
        $response = $this->actingAs($borrower)->post('/loans/apply', [
            'category' => 'special',
            'type' => 'capped_loan',
            'amount' => 40000,
            'term' => 12,
            'pin' => '123456',
            'documents' => [$file],
        ]);

        $response->assertSessionHasErrors(['amount']);
    }

    /**
     * Test that sequential HRMD approvals advance from 1 to the last sequence before moving to Credit Committee.
     */
    public function test_sequential_hrmd_approvals_advance_from_1_to_last_then_to_next_stage(): void
    {
        $borrower = $this->createUser('Borrower Account');

        $roleHrmd = \App\Models\Role::firstOrCreate(['slug' => 'hrmd_staff'], ['name' => 'HRMD Staff']);

        // Create 3 HRMD users with sequences 1, 2, and 3
        $hrUser1 = $this->createUser('HR Sequence 1', 'admin');
        $hrUser1->hrmd_sequence = 1;
        $hrUser1->save();
        $hrUser1->roles()->attach($roleHrmd);

        $hrUser2 = $this->createUser('HR Sequence 2', 'admin');
        $hrUser2->hrmd_sequence = 2;
        $hrUser2->save();
        $hrUser2->roles()->attach($roleHrmd);

        $hrUser3 = $this->createUser('HR Sequence 3', 'admin');
        $hrUser3->hrmd_sequence = 3;
        $hrUser3->save();
        $hrUser3->roles()->attach($roleHrmd);

        $application = LoanApplication::create([
            'user_id' => $borrower->id,
            'loan_category' => 'travel',
            'loan_type' => 'travel_loan',
            'requested_amount' => 30000,
            'current_stage' => 'hrmd_staff',
            'current_hrmd_sequence' => 1,
            'status' => 'pending',
            'form_data' => [
                'term_months' => 12,
            ]
        ]);

        // 1. HR User 2 attempts to approve out of order while at sequence 1 -> should fail
        $outOfOrderResponse = $this->actingAs($hrUser2)->post("/loans/{$application->id}/approve", [
            'remarks' => 'Approving early',
        ]);
        $outOfOrderResponse->assertSessionHas('error');
        $this->assertEquals(1, $application->fresh()->current_hrmd_sequence);
        $this->assertEquals('hrmd_staff', $application->fresh()->current_stage);

        // 2. HR User 1 approves -> moves to sequence 2
        $resp1 = $this->actingAs($hrUser1)->post("/loans/{$application->id}/approve", [
            'remarks' => 'HR Level 1 Approved',
        ]);
        $resp1->assertSessionHas('success');
        $this->assertEquals(2, $application->fresh()->current_hrmd_sequence);
        $this->assertEquals('hrmd_staff', $application->fresh()->current_stage);

        $this->assertDatabaseHas('loan_approvals', [
            'loan_application_id' => $application->id,
            'stage_role_slug' => 'hrmd_staff',
            'hrmd_sequence' => 1,
            'actioned_by_user_id' => $hrUser1->id,
        ]);

        // 3. HR User 2 approves -> moves to sequence 3
        $resp2 = $this->actingAs($hrUser2)->post("/loans/{$application->id}/approve", [
            'remarks' => 'HR Level 2 Approved',
        ]);
        $resp2->assertSessionHas('success');
        $this->assertEquals(3, $application->fresh()->current_hrmd_sequence);
        $this->assertEquals('hrmd_staff', $application->fresh()->current_stage);

        $this->assertDatabaseHas('loan_approvals', [
            'loan_application_id' => $application->id,
            'stage_role_slug' => 'hrmd_staff',
            'hrmd_sequence' => 2,
            'actioned_by_user_id' => $hrUser2->id,
        ]);

        // 4. HR User 3 (the last sequence) approves -> all HR sequences done, moves to next stage (credit_committee)
        $resp3 = $this->actingAs($hrUser3)->post("/loans/{$application->id}/approve", [
            'remarks' => 'HR Final Level Approved',
        ]);
        $resp3->assertSessionHas('success');
        $application->refresh();
        $this->assertEquals('credit_committee', $application->current_stage);
        $this->assertNull($application->current_hrmd_sequence);

        $this->assertDatabaseHas('loan_approvals', [
            'loan_application_id' => $application->id,
            'stage_role_slug' => 'hrmd_staff',
            'hrmd_sequence' => 3,
            'actioned_by_user_id' => $hrUser3->id,
        ]);
    }

    /**
     * Test that accounting does not require files, but releasing officer requires General Ledger & Schedule PDFs.
     */
    public function test_releasing_officer_requires_general_ledger_and_schedule_pdfs_to_disburse(): void
    {
        Mail::fake();

        $borrower = $this->createUser('Borrower Account');

        $roleAccounting = \App\Models\Role::firstOrCreate(['slug' => 'accounting'], ['name' => 'Accounting']);
        $roleReleasing = \App\Models\Role::firstOrCreate(['slug' => 'releasing_officer'], ['name' => 'Releasing Officer']);

        $accountingOfficer = $this->createUser('Accounting Officer', 'admin');
        $accountingOfficer->roles()->attach($roleAccounting);

        $releasingOfficer = $this->createUser('Releasing Officer User', 'admin');
        $releasingOfficer->roles()->attach($roleReleasing);

        $application = LoanApplication::create([
            'user_id' => $borrower->id,
            'loan_category' => 'travel',
            'loan_type' => 'travel_loan',
            'requested_amount' => 30000,
            'current_stage' => 'accounting',
            'status' => 'pending',
            'form_data' => [
                'term_months' => 12,
            ]
        ]);

        // 1. Accounting approves WITHOUT ledger/schedule -> succeeds and moves to releasing_officer
        $respAcct = $this->actingAs($accountingOfficer)->post("/loans/{$application->id}/approve", [
            'remarks' => 'Computations verified.',
        ]);
        $respAcct->assertSessionHas('success');
        $this->assertEquals('releasing_officer', $application->fresh()->current_stage);

        // 2. Releasing officer attempts to approve WITHOUT ledger and schedule -> fails validation
        $failResp = $this->actingAs($releasingOfficer)->post("/loans/{$application->id}/approve", [
            'remarks' => 'Disbursing now without files',
        ]);
        $failResp->assertSessionHasErrors(['ledger', 'schedule']);
        $this->assertEquals('releasing_officer', $application->fresh()->current_stage);

        // 3. Releasing officer uploads General Ledger and Payment Schedule PDFs -> succeeds and completes disbursement
        $ledgerPdf = UploadedFile::fake()->create('General_Ledger.pdf', 200, 'application/pdf');
        $schedulePdf = UploadedFile::fake()->create('Amortization_Schedule.pdf', 200, 'application/pdf');

        $successResp = $this->actingAs($releasingOfficer)->post("/loans/{$application->id}/approve", [
            'ledger' => $ledgerPdf,
            'schedule' => $schedulePdf,
            'remarks' => 'All vouchers prepared and disbursements issued.',
        ]);

        $successResp->assertSessionHas('success');
        $application->refresh();
        $this->assertEquals('completed', $application->current_stage);
        $this->assertEquals('approved', $application->status);
        $this->assertNotNull($application->ledger_path);
        $this->assertNotNull($application->schedule_path);

        // 4. Verify LoanReleasedMail was sent to the borrower with releasing remarks and officer identity
        Mail::assertSent(\App\Mail\LoanReleasedMail::class, function ($mail) use ($borrower, $application, $releasingOfficer) {
            return $mail->hasTo($borrower->email) &&
                   $mail->borrowerName === $borrower->name &&
                   $mail->loanId === (string) $application->id &&
                   $mail->remarks === 'All vouchers prepared and disbursements issued.' &&
                   $mail->releasingOfficerName === $releasingOfficer->name;
        });
    }
}
