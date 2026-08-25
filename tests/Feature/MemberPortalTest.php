<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MemberPortalTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that guest users are redirected to login when trying to access member routes.
     */
    public function test_guests_are_redirected_to_home_or_login(): void
    {
        $protectedRoutes = [
            '/dashboard',
            '/savings',
            '/myloans',
            '/withdrawals',
            '/deductions',
            '/loans',
            '/settings',
        ];

        foreach ($protectedRoutes as $route) {
            $response = $this->get($route);
            // In standard Laravel, unauthenticated routes redirect to home or login page.
            $response->assertRedirect('/');
        }
    }

    /**
     * Test that requesting the dashboard redirects to the savings page (dashboard disabled).
     */
    public function test_requesting_dashboard_redirects_to_savings(): void
    {
        $user = User::create([
            'name' => 'John Member',
            'email' => 'john.member@example.com',
            'password' => bcrypt('password'),
            'company_id' => '12345678',
            'role' => 'member',
        ]);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertRedirect('/savings');
    }

    /**
     * Test that authenticated members can access the savings page and view accurate balances.
     */
    public function test_authenticated_members_can_access_savings_page_and_see_details(): void
    {
        $user = User::create([
            'name' => 'John Member',
            'email' => 'john.member@example.com',
            'password' => bcrypt('password'),
            'company_id' => '12345678',
            'role' => 'member',
        ]);

        $response = $this->actingAs($user)->get('/savings');

        $response->assertStatus(200);
        $response->assertSee('My Savings');
        $response->assertSee('Share Capital');
        $response->assertSee('Savings Deposit');
        $response->assertSee('50,000.00'); // Contributed Share Capital
        $response->assertSee('142,490.00'); // Cooperative Savings Deposit Balance
        $response->assertSee('141,990.00'); // Calculated withdrawable amount (142490 - 500)
        $response->assertSee('500.00'); // Maintaining balance
        $response->assertSee('strictly non-withdrawable'); // Non-withdrawable warning notice
    }

    /**
     * Test that all other member portal pages load successfully.
     */
    public function test_all_other_member_pages_render_successfully(): void
    {
        $user = User::create([
            'name' => 'John Member',
            'email' => 'john.member@example.com',
            'password' => bcrypt('password'),
            'company_id' => '12345678',
            'role' => 'member',
        ]);

        $routesToVerify = [
            '/myloans',
            '/withdrawals',
            '/deductions',
            '/loans',
            '/settings',
        ];

        foreach ($routesToVerify as $route) {
            $response = $this->actingAs($user)->get($route);
            $response->assertStatus(200);
        }
    }

    /**
     * Test that filing a withdrawal request without a reason fails validation.
     */
    public function test_withdrawal_request_requires_reason(): void
    {
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);

        $user = User::create([
            'name' => 'John Member',
            'email' => 'john.member@example.com',
            'password' => bcrypt('password'),
            'company_id' => '12345678',
            'role' => 'member',
        ]);

        $response = $this->actingAs($user)->post('/withdrawals', [
            'amount' => 1000,
            'channel' => 'MCash E-Wallet',
            'reason' => '', // empty reason
        ]);

        $response->assertSessionHasErrors('reason');
        $this->assertDatabaseCount('withdrawal_requests', 0);
    }

    /**
     * Test that filing a withdrawal request with a reason is successful.
     */
    public function test_withdrawal_request_with_reason_succeeds(): void
    {
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);

        $user = User::create([
            'name' => 'John Member',
            'email' => 'john.member@example.com',
            'password' => bcrypt('password'),
            'pin' => bcrypt('123456'),
            'company_id' => '12345678',
            'role' => 'member',
        ]);

        $response = $this->actingAs($user)->post('/withdrawals', [
            'amount' => 1000,
            'channel' => 'MCash E-Wallet',
            'reason' => 'Emergency medical expenses',
            'pin' => '123456',
        ]);

        $response->assertRedirect('/withdrawals');
        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('withdrawal_requests', [
            'user_id' => $user->id,
            'amount' => 1000,
            'channel' => 'MCash E-Wallet',
            'reason' => 'Emergency medical expenses',
            'status' => 'pending',
        ]);
    }

    /**
     * Test that pending withdrawal request can be cancelled.
     */
    public function test_pending_withdrawal_request_can_be_cancelled(): void
    {
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);

        $user = User::create([
            'name' => 'John Member',
            'email' => 'john.member@example.com',
            'password' => bcrypt('password'),
            'company_id' => '12345678',
            'role' => 'member',
        ]);

        $withdrawal = \App\Models\WithdrawalRequest::create([
            'user_id' => $user->id,
            'amount' => 1000,
            'channel' => 'MCash E-Wallet',
            'reason' => 'School tuition fees',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($user)->post("/withdrawals/{$withdrawal->id}/cancel");

        $response->assertRedirect('/withdrawals');
        $response->assertSessionHas('success_title', 'Request Cancelled');
        $this->assertDatabaseHas('withdrawal_requests', [
            'id' => $withdrawal->id,
            'status' => 'cancelled',
        ]);
    }

    /**
     * Test that non-pending withdrawal request cannot be cancelled.
     */
    public function test_non_pending_withdrawal_request_cannot_be_cancelled(): void
    {
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);

        $user = User::create([
            'name' => 'John Member',
            'email' => 'john.member@example.com',
            'password' => bcrypt('password'),
            'company_id' => '12345678',
            'role' => 'member',
        ]);

        $withdrawal = \App\Models\WithdrawalRequest::create([
            'user_id' => $user->id,
            'amount' => 1000,
            'channel' => 'MCash E-Wallet',
            'reason' => 'School tuition fees',
            'status' => 'processing',
        ]);

        $response = $this->actingAs($user)->post("/withdrawals/{$withdrawal->id}/cancel");

        $response->assertRedirect('/withdrawals');
        $response->assertSessionHas('error', 'Only pending withdrawal requests can be cancelled.');
        $this->assertDatabaseHas('withdrawal_requests', [
            'id' => $withdrawal->id,
            'status' => 'processing',
        ]);
    }

    /**
     * Test that authenticated members can successfully update their profile details and signature.
     */
    public function test_members_can_update_profile_and_signature(): void
    {
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);
        \Illuminate\Support\Facades\Storage::fake('public');

        $user = User::create([
            'name' => 'John Member',
            'email' => 'john.member@example.com',
            'password' => bcrypt('password'),
            'company_id' => '12345678',
            'role' => 'member',
        ]);

        $fakeImage = \Illuminate\Http\UploadedFile::fake()->create('my-signature.png', 100, 'image/png');

        $response = $this->actingAs($user)->post('/settings', [
            'contact_number' => '09123456789',
            'address' => 'Cebu City, Cebu',
            'signature' => $fakeImage,
        ]);

        $response->assertRedirect('/settings');
        $response->assertSessionHas('success');

        $user->refresh();
        $this->assertEquals('09123456789', $user->contact_number);
        $this->assertEquals('Cebu City, Cebu', $user->address);
        $this->assertNotNull($user->signature);

        // Assert file exists in faked public storage
        \Illuminate\Support\Facades\Storage::disk('public')->assertExists($user->signature);
    }

    /**
     * Test that authenticated members can successfully file a payroll deduction adjustment request.
     */
    public function test_members_can_submit_deduction_request(): void
    {
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);

        $user = User::create([
            'name' => 'John Member',
            'email' => 'john.member@example.com',
            'password' => bcrypt('password'),
            'pin' => bcrypt('123456'),
            'company_id' => '12345678',
            'role' => 'member',
        ]);

        $response = $this->actingAs($user)->post('/deductions', [
            'savings_amount' => 3000.00,
            'fixed_amount' => 1500.00,
            'effectivity_date' => now()->addDays(5)->format('Y-m-d'),
            'remarks' => 'Please adjust my monthly deductions.',
            'terms_agreed' => '1',
            'pin' => '123456',
        ]);

        $response->assertRedirect('/deductions');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('deduction_requests', [
            'user_id' => $user->id,
            'savings_amount' => 3000.00,
            'fixed_amount' => 1500.00,
            'remarks' => 'Please adjust my monthly deductions.',
            'status' => 'pending',
        ]);
    }
}
