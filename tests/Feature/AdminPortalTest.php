<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Loan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminPortalTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that guests and non-admin members are blocked from accessing the admin panel.
     */
    public function test_guests_and_members_are_redirected_away_from_admin_routes(): void
    {
        $adminRoutes = [
            '/admin/dashboard',
            '/admin/members',
        ];

        // 1. Verify guests are redirected
        foreach ($adminRoutes as $route) {
            $response = $this->get($route);
            $response->assertRedirect('/');
        }

        // 2. Verify normal member is redirected with denial error
        $member = User::create([
            'name' => 'Laurence Castillo (Member)',
            'email' => 'member@mlsako.com',
            'role' => 'member',
            'company_id' => '20248216',
            'password' => Hash::make('password'),
        ]);

        foreach ($adminRoutes as $route) {
            $response = $this->actingAs($member)->get($route);
            $response->assertRedirect('/');
            $response->assertSessionHasErrors('login_identifier');
        }
    }

    /**
     * Test that authorized administrators can access the dashboard and members list.
     */
    public function test_admins_can_access_admin_dashboard_and_members_list(): void
    {
        $admin = User::create([
            'name' => 'Sako Admin',
            'email' => 'admin@mlsako.com',
            'role' => 'admin',
            'company_id' => '10001000',
            'password' => Hash::make('password'),
        ]);

        // Dashboard
        $response = $this->actingAs($admin)->get('/admin/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Overview Panel');
        $response->assertSee('Ledger Pool');

        // Members Directory
        $response = $this->actingAs($admin)->get('/admin/members');
        $response->assertStatus(200);
        $response->assertSee('Members Directory');
        $response->assertSee('Sako Admin');
    }

    /**
     * Test that administrators can successfully create a new member.
     */
    public function test_admins_can_store_new_member(): void
    {
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);

        $admin = User::create([
            'name' => 'Sako Admin',
            'email' => 'admin@mlsako.com',
            'role' => 'admin',
            'company_id' => '10001000',
            'password' => Hash::make('password'),
        ]);

        $response = $this->actingAs($admin)->post('/admin/members', [
            'name' => 'Jane Doe',
            'email' => 'jane.doe@example.com',
            'company_id' => '20241002',
            'role' => 'member',
            'contact_number' => '09123456789',
            'address' => 'Cebu City, Cebu',
            'password' => 'secret123',
        ]);

        $response->assertRedirect('/admin/members');
        $response->assertSessionHas('success');
        
        $this->assertDatabaseHas('users', [
            'name' => 'Jane Doe',
            'email' => 'jane.doe@example.com',
            'company_id' => '20241002',
            'role' => 'member',
        ]);
    }

    /**
     * Test that administrators can successfully update member details.
     */
    public function test_admins_can_update_existing_member(): void
    {
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);

        $admin = User::create([
            'name' => 'Sako Admin',
            'email' => 'admin@mlsako.com',
            'role' => 'admin',
            'company_id' => '10001000',
            'password' => Hash::make('password'),
        ]);

        $member = User::create([
            'name' => 'Old Name',
            'email' => 'old.email@example.com',
            'company_id' => '20241111',
            'role' => 'member',
            'password' => Hash::make('password'),
        ]);

        $response = $this->actingAs($admin)->put("/admin/members/{$member->id}", [
            'name' => 'Updated Name',
            'email' => 'updated.email@example.com',
            'company_id' => '20241111', // Unchanged ID but needs validation check bypass
            'contact_number' => '09171112222',
            'address' => 'Mandaue City, Cebu',
        ]);

        $response->assertRedirect('/admin/members');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'id' => $member->id,
            'name' => 'Updated Name',
            'email' => 'updated.email@example.com',
            'role' => 'member',
        ]);
    }

    /**
     * Test that administrators can successfully delete members, but are blocked from self-deletion.
     */
    public function test_admins_can_delete_members_but_not_self(): void
    {
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);

        $admin = User::create([
            'name' => 'Sako Admin',
            'email' => 'admin@mlsako.com',
            'role' => 'admin',
            'company_id' => '10001000',
            'password' => Hash::make('password'),
        ]);

        $memberToDelete = User::create([
            'name' => 'Disposable Member',
            'email' => 'delete.me@example.com',
            'company_id' => '20249999',
            'role' => 'member',
            'password' => Hash::make('password'),
        ]);

        // 1. Delete other member (Succeeds)
        $response = $this->actingAs($admin)->delete("/admin/members/{$memberToDelete->id}");
        $response->assertRedirect('/admin/members');
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('users', ['id' => $memberToDelete->id]);

        // 2. Self-deletion (Blocked)
        $response = $this->actingAs($admin)->delete("/admin/members/{$admin->id}");
        $response->assertRedirect('/admin/members');
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('users', ['id' => $admin->id]);
    }

    /**
     * Test that administrators can successfully download a member's PDF document.
     */
    public function test_admins_can_export_member_pdf(): void
    {
        $admin = User::create([
            'name' => 'Sako Admin',
            'email' => 'admin@mlsako.com',
            'role' => 'admin',
            'company_id' => '10001000',
            'password' => Hash::make('password'),
        ]);

        $member = User::create([
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'company_id' => '20241112',
            'role' => 'member',
            'password' => Hash::make('password'),
        ]);

        $response = $this->actingAs($admin)->get("/admin/members/{$member->id}/pdf");

        // Verify PDF download starts or streams
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
    }

    /**
     * Test that administrators can successfully view the withdrawals page.
     */
    public function test_admins_can_access_withdrawals_queue(): void
    {
        $admin = User::create([
            'name' => 'Sako Admin',
            'email' => 'admin@mlsako.com',
            'role' => 'admin',
            'company_id' => '10001000',
            'password' => Hash::make('password'),
        ]);

        $response = $this->actingAs($admin)->get('/admin/withdrawals');
        $response->assertStatus(200);
        $response->assertSee('Withdrawals Decision Board');
    }

    /**
     * Test that administrators can acknowledge a pending withdrawal request by supplying a transaction ID.
     */
    public function test_admins_can_acknowledge_withdrawal_with_transaction_id(): void
    {
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);

        $admin = User::create([
            'name' => 'Sako Admin',
            'email' => 'admin@mlsako.com',
            'role' => 'admin',
            'company_id' => '10001000',
            'password' => Hash::make('password'),
        ]);

        $member = User::create([
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'company_id' => '20241112',
            'role' => 'member',
            'password' => Hash::make('password'),
        ]);

        $withdrawal = \App\Models\WithdrawalRequest::create([
            'user_id' => $member->id,
            'amount' => 5000.00,
            'channel' => 'GCash',
            'reason' => 'Emergency expenses',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($admin)->post("/admin/withdrawals/{$withdrawal->id}/status", [
            'action' => 'acknowledge',
            'transaction_id' => 'TXN-987654321',
        ]);

        $response->assertRedirect('/admin/withdrawals');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('withdrawal_requests', [
            'id' => $withdrawal->id,
            'status' => 'processing',
            'transaction_id' => 'TXN-987654321',
        ]);
    }

    /**
     * Test that acknowledging a withdrawal fails without supplying a transaction ID.
     */
    public function test_admins_cannot_acknowledge_withdrawal_without_transaction_id(): void
    {
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);

        $admin = User::create([
            'name' => 'Sako Admin',
            'email' => 'admin@mlsako.com',
            'role' => 'admin',
            'company_id' => '10001000',
            'password' => Hash::make('password'),
        ]);

        $member = User::create([
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'company_id' => '20241112',
            'role' => 'member',
            'password' => Hash::make('password'),
        ]);

        $withdrawal = \App\Models\WithdrawalRequest::create([
            'user_id' => $member->id,
            'amount' => 5000.00,
            'channel' => 'GCash',
            'reason' => 'Emergency expenses',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($admin)->post("/admin/withdrawals/{$withdrawal->id}/status", [
            'action' => 'acknowledge',
        ]);

        $response->assertSessionHasErrors('transaction_id');
        $this->assertDatabaseHas('withdrawal_requests', [
            'id' => $withdrawal->id,
            'status' => 'pending',
        ]);
    }

    /**
     * Test that administrators can successfully release/complete an in-processing withdrawal request.
     */
    public function test_admins_can_release_withdrawal(): void
    {
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);

        $admin = User::create([
            'name' => 'Sako Admin',
            'email' => 'admin@mlsako.com',
            'role' => 'admin',
            'company_id' => '10001000',
            'password' => Hash::make('password'),
        ]);

        $member = User::create([
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'company_id' => '20241112',
            'role' => 'member',
            'password' => Hash::make('password'),
        ]);

        $withdrawal = \App\Models\WithdrawalRequest::create([
            'user_id' => $member->id,
            'amount' => 5000.00,
            'channel' => 'GCash',
            'reason' => 'Emergency expenses',
            'status' => 'processing',
            'transaction_id' => 'TXN-987654321',
        ]);

        $response = $this->actingAs($admin)->post("/admin/withdrawals/{$withdrawal->id}/status", [
            'action' => 'release',
        ]);

        $response->assertRedirect('/admin/withdrawals');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('withdrawal_requests', [
            'id' => $withdrawal->id,
            'status' => 'released',
        ]);
    }

    /**
     * Test that administrators can successfully download selected withdrawals as a PDF manifest.
     */
    public function test_admins_can_export_selected_withdrawals_pdf(): void
    {
        $admin = User::create([
            'name' => 'Sako Admin',
            'email' => 'admin@mlsako.com',
            'role' => 'admin',
            'company_id' => '10001000',
            'password' => Hash::make('password'),
        ]);

        $member = User::create([
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'company_id' => '20241112',
            'role' => 'member',
            'password' => Hash::make('password'),
        ]);

        $withdrawal1 = \App\Models\WithdrawalRequest::create([
            'user_id' => $member->id,
            'amount' => 5000.00,
            'channel' => 'GCash',
            'reason' => 'Emergency expenses',
            'status' => 'processing',
            'transaction_id' => 'TXN-001',
        ]);

        $withdrawal2 = \App\Models\WithdrawalRequest::create([
            'user_id' => $member->id,
            'amount' => 3000.00,
            'channel' => 'GCash',
            'reason' => 'Medical bill',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($admin)->get("/admin/withdrawals/export-pdf?ids[]={$withdrawal1->id}&ids[]={$withdrawal2->id}");

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
    }

    /**
     * Test that administrators can successfully view the deductions adjustments page.
     */
    public function test_admins_can_access_deductions_queue(): void
    {
        $admin = User::create([
            'name' => 'Sako Admin',
            'email' => 'admin@mlsako.com',
            'role' => 'admin',
            'company_id' => '10001000',
            'password' => Hash::make('password'),
        ]);

        $response = $this->actingAs($admin)->get('/admin/deductions');
        $response->assertStatus(200);
        $response->assertSee('Deduction Adjustments');
    }

    /**
     * Test that administrators can successfully approve a member's deduction adjustment request.
     */
    public function test_admins_can_approve_deduction_request(): void
    {
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);

        $admin = User::create([
            'name' => 'Sako Admin',
            'email' => 'admin@mlsako.com',
            'role' => 'admin',
            'company_id' => '10001000',
            'password' => Hash::make('password'),
        ]);

        $member = User::create([
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'company_id' => '20241112',
            'role' => 'member',
            'password' => Hash::make('password'),
        ]);

        $deductionRequest = \App\Models\DeductionRequest::create([
            'user_id' => $member->id,
            'savings_amount' => 3000.00,
            'fixed_amount' => 1500.00,
            'effectivity_date' => now()->addDays(5)->format('Y-m-d'),
            'remarks' => 'Adjustments',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($admin)->post("/admin/deductions/{$deductionRequest->id}/status", [
            'action' => 'approve',
        ]);

        $response->assertRedirect('/admin/deductions');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('deduction_requests', [
            'id' => $deductionRequest->id,
            'status' => 'approved',
        ]);
    }

    /**
     * Test that administrators can successfully reject a member's deduction adjustment request.
     */
    public function test_admins_can_reject_deduction_request(): void
    {
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);

        $admin = User::create([
            'name' => 'Sako Admin',
            'email' => 'admin@mlsako.com',
            'role' => 'admin',
            'company_id' => '10001000',
            'password' => Hash::make('password'),
        ]);

        $member = User::create([
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'company_id' => '20241112',
            'role' => 'member',
            'password' => Hash::make('password'),
        ]);

        $deductionRequest = \App\Models\DeductionRequest::create([
            'user_id' => $member->id,
            'savings_amount' => 3000.00,
            'fixed_amount' => 1500.00,
            'effectivity_date' => now()->addDays(5)->format('Y-m-d'),
            'remarks' => 'Adjustments',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($admin)->post("/admin/deductions/{$deductionRequest->id}/status", [
            'action' => 'reject',
        ]);

        $response->assertRedirect('/admin/deductions');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('deduction_requests', [
            'id' => $deductionRequest->id,
            'status' => 'rejected',
        ]);
    }

    /**
     * Test that administrators can create a loan product with custom available terms and tiered rates.
     */
    public function test_admins_can_create_loan_facility_with_custom_available_terms(): void
    {
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);

        $admin = User::create([
            'name' => 'Sako Admin',
            'email' => 'admin_terms@mlsako.com',
            'role' => 'admin',
            'company_id' => '10001009',
            'password' => Hash::make('password'),
        ]);

        $terms = [
            ['months' => 2, 'interest_rate' => 2.00],
            ['months' => 4, 'interest_rate' => 3.50],
            ['months' => 6, 'interest_rate' => 5.00],
        ];

        $response = $this->actingAs($admin)->post('/admin/loans/products', [
            'category' => 'regular',
            'name' => 'Custom Tiered Multi-Term Loan',
            'fixed_deposit' => 2000,
            'comakers' => '0',
            'interest_rate' => 5.00,
            'available_terms' => json_encode($terms),
            'minimum_membership_months' => 3,
        ]);

        $response->assertRedirect('/admin/loans/management');
        $response->assertSessionHas('success');

        $loan = Loan::where('name', 'Custom Tiered Multi-Term Loan')->first();
        $this->assertNotNull($loan);
        $this->assertTrue($loan->hasCustomTerms());
        $this->assertEquals(6, $loan->max_term_months);
        $this->assertEquals(2.00, $loan->getInterestRateForTerm(2));
        $this->assertEquals(3.50, $loan->getInterestRateForTerm(4));
        $this->assertEquals(5.00, $loan->getInterestRateForTerm(6));

        // Test rendering on management page
        $pageResponse = $this->actingAs($admin)->get('/admin/loans/management');
        $pageResponse->assertStatus(200);
        $pageResponse->assertSee('Tiered Rates');
        $pageResponse->assertSee('3 Tenures');
    }

    /**
     * Test that administrators can update an existing loan product with new available terms.
     */
    public function test_admins_can_update_loan_facility_terms(): void
    {
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);

        $admin = User::create([
            'name' => 'Sako Admin',
            'email' => 'admin_update_terms@mlsako.com',
            'role' => 'admin',
            'company_id' => '10001010',
            'password' => Hash::make('password'),
        ]);

        $loan = Loan::create([
            'category' => 'regular',
            'type_key' => 'initial_term_loan',
            'name' => 'Initial Term Loan',
            'fixed_deposit' => 1000,
            'comakers' => 0,
            'interest_rate' => 3.00,
            'max_term_months' => 12,
            'is_active' => true,
        ]);

        $newTerms = [
            ['months' => 3, 'interest_rate' => 3.00],
            ['months' => 6, 'interest_rate' => 5.00],
            ['months' => 12, 'interest_rate' => 7.50],
        ];

        $response = $this->actingAs($admin)->put("/admin/loans/products/{$loan->id}", [
            'category' => 'regular',
            'name' => 'Updated Term Loan',
            'fixed_deposit' => 1500,
            'comakers' => '0',
            'interest_rate' => 4.00,
            'available_terms' => json_encode($newTerms),
            'minimum_membership_months' => 3,
            'is_active' => 1,
        ]);

        $response->assertRedirect('/admin/loans/management');
        $response->assertSessionHas('success');

        $loan->refresh();
        $this->assertEquals('Updated Term Loan', $loan->name);
        $this->assertTrue($loan->hasCustomTerms());
        $this->assertEquals(12, $loan->max_term_months);
        $this->assertEquals(3.00, $loan->getInterestRateForTerm(3));
        $this->assertEquals(5.00, $loan->getInterestRateForTerm(6));
        $this->assertEquals(7.50, $loan->getInterestRateForTerm(12));
    }

    /**
     * Test that an admin can force reset a member's password and clear their security PIN.
     */
    public function test_admin_can_force_reset_member_password_and_clear_pin(): void
    {
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);

        $admin = User::factory()->create([
            'role' => 'admin',
            'password' => Hash::make('password'),
        ]);

        $member = User::factory()->create([
            'role' => 'member',
            'password' => Hash::make('oldpassword'),
            'pin' => Hash::make('112233'),
            'pin_attempts' => 2,
        ]);

        $response = $this->actingAs($admin)->post("/admin/members/{$member->id}/reset-credentials", [
            'reset_password' => 1,
            'password' => 'NewSecur3P@ss',
            'reset_pin' => 1,
            'pin_mode' => 'clear',
        ]);

        $response->assertRedirect('/admin/members');
        $response->assertSessionHas('success');

        $member->refresh();
        $this->assertTrue(Hash::check('NewSecur3P@ss', $member->password));
        $this->assertNull($member->pin);
        $this->assertEquals(0, $member->pin_attempts);

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'member_credential_reset',
            'user_id' => $admin->id,
            'auditable_type' => User::class,
            'auditable_id' => $member->id,
            'severity' => 'warning',
        ]);
    }

    /**
     * Test that an admin can manually assign a 6-digit security PIN to a member.
     */
    public function test_admin_can_manually_assign_member_pin(): void
    {
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);

        $admin = User::factory()->create([
            'role' => 'admin',
            'password' => Hash::make('password'),
        ]);

        $member = User::factory()->create([
            'role' => 'member',
            'password' => Hash::make('password'),
            'pin' => null,
            'pin_attempts' => 0,
        ]);

        $response = $this->actingAs($admin)->post("/admin/members/{$member->id}/reset-credentials", [
            'reset_pin' => 1,
            'pin_mode' => 'manual',
            'pin' => '987654',
        ]);

        $response->assertRedirect('/admin/members');
        $response->assertSessionHas('success');

        $member->refresh();
        $this->assertNotNull($member->pin);
        $this->assertTrue(Hash::check('987654', $member->pin));
        $this->assertEquals(0, $member->pin_attempts);
    }

    /**
     * Test that resetting credentials clears existing account lockout attempts.
     */
    public function test_force_reset_clears_member_account_lockout(): void
    {
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);

        $admin = User::factory()->create([
            'role' => 'admin',
            'password' => Hash::make('password'),
        ]);

        $member = User::factory()->create([
            'role' => 'member',
            'password' => Hash::make('password'),
            'pin' => Hash::make('123456'),
            'pin_attempts' => 3, // Locked out
        ]);

        $response = $this->actingAs($admin)->post("/admin/members/{$member->id}/reset-credentials", [
            'reset_password' => 1,
            'password' => 'UnlockedPassword123',
        ]);

        $response->assertRedirect('/admin/members');
        $member->refresh();
        $this->assertEquals(0, $member->pin_attempts);
        $this->assertTrue(Hash::check('UnlockedPassword123', $member->password));
    }

    /**
     * Test that a super admin can force reset an administrator's credentials.
     */
    public function test_super_admin_can_force_reset_administrator_credentials(): void
    {
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);

        $superAdmin = User::factory()->create([
            'role' => 'super_admin',
            'password' => Hash::make('password'),
        ]);

        $targetAdmin = User::factory()->create([
            'role' => 'admin',
            'password' => Hash::make('adminoldpass'),
            'pin' => Hash::make('111222'),
            'pin_attempts' => 3,
        ]);

        $response = $this->actingAs($superAdmin)->post("/admin/administrators/{$targetAdmin->id}/reset-credentials", [
            'reset_password' => 1,
            'password' => 'AdminNewPass2026',
            'reset_pin' => 1,
            'pin_mode' => 'clear',
        ]);

        $response->assertRedirect('/admin/administrators');
        $response->assertSessionHas('success');

        $targetAdmin->refresh();
        $this->assertTrue(Hash::check('AdminNewPass2026', $targetAdmin->password));
        $this->assertNull($targetAdmin->pin);
        $this->assertEquals(0, $targetAdmin->pin_attempts);

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'admin_credential_reset',
            'user_id' => $superAdmin->id,
            'auditable_type' => User::class,
            'auditable_id' => $targetAdmin->id,
            'severity' => 'warning',
        ]);
    }

    /**
     * Test that a regular admin cannot reset another administrator's credentials.
     */
    public function test_regular_admin_cannot_reset_administrator_credentials(): void
    {
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);

        $regularAdmin = User::factory()->create([
            'role' => 'admin',
            'password' => Hash::make('password'),
        ]);

        $targetAdmin = User::factory()->create([
            'role' => 'admin',
            'password' => Hash::make('adminoldpass'),
        ]);

        $response = $this->actingAs($regularAdmin)->post("/admin/administrators/{$targetAdmin->id}/reset-credentials", [
            'reset_password' => 1,
            'password' => 'HackedPassword123',
        ]);

        $response->assertRedirect('/admin/dashboard');
        $targetAdmin->refresh();
        $this->assertTrue(Hash::check('adminoldpass', $targetAdmin->password));
    }

    /**
     * Test that an administrator cannot reset their own credentials through this panel.
     */
    public function test_admin_cannot_reset_own_credentials_through_panel(): void
    {
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);

        $superAdmin = User::factory()->create([
            'role' => 'super_admin',
            'password' => Hash::make('superpassword'),
        ]);

        $response = $this->actingAs($superAdmin)->post("/admin/administrators/{$superAdmin->id}/reset-credentials", [
            'reset_password' => 1,
            'password' => 'ShouldNotWork123',
        ]);

        $response->assertRedirect('/admin/administrators');
        $response->assertSessionHas('error');
        $superAdmin->refresh();
        $this->assertTrue(Hash::check('superpassword', $superAdmin->password));
    }
}
