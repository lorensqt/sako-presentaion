<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\LoanApplication;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class LoanApprovalTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);
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

        $response = $this->actingAs($borrower)->post('/loans/apply', [
            'category' => 'travel',
            'type' => 'travel_loan',
            'amount' => 30000,
            'term' => 12,
            'remarks' => 'Vacation trip',
            'pin' => '123456',
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

        $response = $this->actingAs($borrower)->post('/loans/apply', [
            'category' => 'special',
            'type' => 'birthday', // requires 1 comaker
            'amount' => 5000,
            'term' => 5,
            'comakers' => [$comaker1->id],
            'remarks' => 'Birthday cash',
            'pin' => '123456',
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

        // Submit resubmission with corrected details
        $response = $this->actingAs($borrower)->post('/loans/apply', [
            'resubmit_id' => $application->id,
            'category' => 'travel',
            'type' => 'travel_loan',
            'amount' => 25000, // corrected amount
            'term' => 12,
            'remarks' => 'Vacation trip - updated with attachment description',
            'pin' => '123456',
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
}
