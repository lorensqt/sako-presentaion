<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AuditLog;
use App\Models\User;
use App\Models\LoanApplication;
use App\Models\WithdrawalRequest;
use Carbon\Carbon;

class AuditLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Truncate existing logs to start fresh
        AuditLog::truncate();

        // Retrieve seeded users for relational integrity
        $superAdmin = User::where('email', 'castillojohnlaurence0@gmail.com')->first();
        $admin = User::where('email', 'admin@mlsako.com')->first();
        $member = User::where('email', 'member@mlsako.com')->first();
        $jane = User::where('email', 'jane.comaker@mlsako.com')->first();
        $john = User::where('email', 'john.comaker@mlsako.com')->first();

        // Retrieve seeded loans if any exist, or make mock auditable links
        $loanApp = LoanApplication::first();
        $withdrawalApp = WithdrawalRequest::first();

        $now = Carbon::now();

        // --- Day 1 (7 Days Ago) ---
        $date = $now->copy()->subDays(7);

        // 1. Unauthenticated Bruteforce Warning
        AuditLog::create([
            'user_id' => null,
            'action' => 'auth_login_failed',
            'severity' => 'warning',
            'auditable_type' => null,
            'auditable_id' => null,
            'ip_address' => '192.168.1.155',
            'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0.0.0 Safari/537.36',
            'old_values' => null,
            'new_values' => null,
            'description' => "Failed login attempt for identifier: 'root_admin@mlsako.com'.",
            'created_at' => $date->copy()->setHour(8)->setMinute(15),
            'updated_at' => $date->copy()->setHour(8)->setMinute(15),
        ]);

        // 2. Successful Admin Login
        if ($admin) {
            AuditLog::create([
                'user_id' => $admin->id,
                'action' => 'auth_login_success',
                'severity' => 'info',
                'auditable_type' => User::class,
                'auditable_id' => $admin->id,
                'ip_address' => '112.198.84.15',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0.0.0 Safari/537.36',
                'old_values' => null,
                'new_values' => null,
                'description' => "User {$admin->name} logged in successfully.",
                'created_at' => $date->copy()->setHour(8)->setMinute(22),
                'updated_at' => $date->copy()->setHour(8)->setMinute(22),
            ]);
        }

        // 3. Admin registers a new member (Jane)
        if ($admin && $jane) {
            AuditLog::create([
                'user_id' => $admin->id,
                'action' => 'member_created',
                'severity' => 'info',
                'auditable_type' => User::class,
                'auditable_id' => $jane->id,
                'ip_address' => '112.198.84.15',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0.0.0 Safari/537.36',
                'old_values' => null,
                'new_values' => $jane->only(['name', 'email', 'company_id', 'role']),
                'description' => "Admin {$admin->name} registered a new member: {$jane->name} ({$jane->email}).",
                'created_at' => $date->copy()->setHour(9)->setMinute(10),
                'updated_at' => $date->copy()->setHour(9)->setMinute(10),
            ]);
        }

        // --- Day 2 (6 Days Ago) ---
        $date = $now->copy()->subDays(6);

        // 4. Backdoor Bypass Warning
        if ($superAdmin) {
            AuditLog::create([
                'user_id' => $superAdmin->id,
                'action' => 'auth_login_backdoor',
                'severity' => 'warning',
                'auditable_type' => User::class,
                'auditable_id' => $superAdmin->id,
                'ip_address' => '120.28.122.9',
                'user_agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0.0.0 Safari/537.36',
                'old_values' => null,
                'new_values' => null,
                'description' => "User {$superAdmin->name} logged in via developer backdoor bypass.",
                'created_at' => $date->copy()->setHour(14)->setMinute(45),
                'updated_at' => $date->copy()->setHour(14)->setMinute(45),
            ]);
        }

        // 5. Member Configuration of Security PIN
        if ($member) {
            AuditLog::create([
                'user_id' => $member->id,
                'action' => 'security_pin_setup',
                'severity' => 'info',
                'auditable_type' => User::class,
                'auditable_id' => $member->id,
                'ip_address' => '49.145.10.82',
                'user_agent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_5 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Mobile/15E148',
                'old_values' => null,
                'new_values' => null,
                'description' => "User {$member->name} configured their 6-digit security PIN.",
                'created_at' => $date->copy()->setHour(15)->setMinute(02),
                'updated_at' => $date->copy()->setHour(15)->setMinute(02),
            ]);
        }

        // --- Day 3 (5 Days Ago) ---
        $date = $now->copy()->subDays(5);

        // 6. Security PIN Verification Failure (Wrong PIN)
        if ($member) {
            AuditLog::create([
                'user_id' => $member->id,
                'action' => 'security_pin_failed',
                'severity' => 'warning',
                'auditable_type' => User::class,
                'auditable_id' => $member->id,
                'ip_address' => '49.145.10.82',
                'user_agent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_5 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Mobile/15E148',
                'old_values' => null,
                'new_values' => null,
                'description' => "User {$member->name} entered an incorrect security PIN. Remaining attempts: 2.",
                'created_at' => $date->copy()->setHour(10)->setMinute(30),
                'updated_at' => $date->copy()->setHour(10)->setMinute(30),
            ]);
        }

        // 7. Security PIN Verification Success
        if ($member) {
            AuditLog::create([
                'user_id' => $member->id,
                'action' => 'security_pin_success',
                'severity' => 'info',
                'auditable_type' => User::class,
                'auditable_id' => $member->id,
                'ip_address' => '49.145.10.82',
                'user_agent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_5 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Mobile/15E148',
                'old_values' => null,
                'new_values' => null,
                'description' => "User {$member->name} successfully verified their security PIN.",
                'created_at' => $date->copy()->setHour(10)->setMinute(31),
                'updated_at' => $date->copy()->setHour(10)->setMinute(31),
            ]);
        }

        // 8. Member files a Savings Withdrawal
        if ($member) {
            AuditLog::create([
                'user_id' => $member->id,
                'action' => 'withdrawal_requested',
                'severity' => 'info',
                'auditable_type' => $withdrawalApp ? get_class($withdrawalApp) : null,
                'auditable_id' => $withdrawalApp ? $withdrawalApp->id : null,
                'ip_address' => '49.145.10.82',
                'user_agent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_5 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Mobile/15E148',
                'old_values' => null,
                'new_values' => null,
                'description' => "Member {$member->name} filed a withdrawal request for ₱25,000.00 via GCASH.",
                'created_at' => $date->copy()->setHour(10)->setMinute(35),
                'updated_at' => $date->copy()->setHour(10)->setMinute(35),
            ]);
        }

        // --- Day 4 (4 Days Ago) ---
        $date = $now->copy()->subDays(4);

        // 9. Admin profile edit (Address and Contact change)
        if ($admin && $member) {
            $oldVals = [
                'contact_number' => '09479992492',
                'address' => 'Room 201, ML Borromeo Bldg. Borromeo St. Pahina Central, Cebu City, 6000'
            ];
            $newVals = [
                'contact_number' => '09173010543',
                'address' => 'Unit 12B, Horizons 101, General Maxilom Ave, Cebu City, 6000'
            ];

            AuditLog::create([
                'user_id' => $admin->id,
                'action' => 'member_updated',
                'severity' => 'info',
                'auditable_type' => User::class,
                'auditable_id' => $member->id,
                'ip_address' => '112.198.84.15',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0.0.0 Safari/537.36',
                'old_values' => $oldVals,
                'new_values' => $newVals,
                'description' => "Admin {$admin->name} updated member details for {$member->name}.",
                'created_at' => $date->copy()->setHour(11)->setMinute(20),
                'updated_at' => $date->copy()->setHour(11)->setMinute(20),
            ]);
        }

        // 10. Admin exports directory report PDF
        if ($admin && $member) {
            AuditLog::create([
                'user_id' => $admin->id,
                'action' => 'compliance_report_exported',
                'severity' => 'warning',
                'auditable_type' => User::class,
                'auditable_id' => $member->id,
                'ip_address' => '112.198.84.15',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0.0.0 Safari/537.36',
                'old_values' => null,
                'new_values' => null,
                'description' => "Admin {$admin->name} exported member profile PDF for {$member->name}.",
                'created_at' => $date->copy()->setHour(11)->setMinute(25),
                'updated_at' => $date->copy()->setHour(11)->setMinute(25),
            ]);
        }

        // --- Day 5 (3 Days Ago) ---
        $date = $now->copy()->subDays(3);

        // 11. Admin Acknowledges Withdrawal
        if ($admin && $withdrawalApp) {
            AuditLog::create([
                'user_id' => $admin->id,
                'action' => 'withdrawal_status_updated',
                'severity' => 'info',
                'auditable_type' => get_class($withdrawalApp),
                'auditable_id' => $withdrawalApp->id,
                'ip_address' => '112.198.84.15',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0.0.0 Safari/537.36',
                'old_values' => ['status' => 'pending', 'transaction_id' => null],
                'new_values' => ['status' => 'processing', 'transaction_id' => 'TXN-994020A'],
                'description' => "Admin {$admin->name} acknowledged withdrawal request REF #WD-" . str_pad($withdrawalApp->id, 5, '0', STR_PAD_LEFT) . " for user {$withdrawalApp->user->name}.",
                'created_at' => $date->copy()->setHour(14)->setMinute(10),
                'updated_at' => $date->copy()->setHour(14)->setMinute(10),
            ]);
        }

        // 12. Admin Releases Withdrawal Funds
        if ($admin && $withdrawalApp) {
            AuditLog::create([
                'user_id' => $admin->id,
                'action' => 'withdrawal_status_updated',
                'severity' => 'info',
                'auditable_type' => get_class($withdrawalApp),
                'auditable_id' => $withdrawalApp->id,
                'ip_address' => '112.198.84.15',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0.0.0 Safari/537.36',
                'old_values' => ['status' => 'processing'],
                'new_values' => ['status' => 'released'],
                'description' => "Admin {$admin->name} released funds for withdrawal request REF #WD-" . str_pad($withdrawalApp->id, 5, '0', STR_PAD_LEFT) . " for user {$withdrawalApp->user->name}.",
                'created_at' => $date->copy()->setHour(14)->setMinute(40),
                'updated_at' => $date->copy()->setHour(14)->setMinute(40),
            ]);
        }

        // --- Day 6 (2 Days Ago) ---
        $date = $now->copy()->subDays(2);

        // 13. Critical Security Lockout Alert (3 failed PIN attempts)
        if ($member) {
            AuditLog::create([
                'user_id' => $member->id,
                'action' => 'security_lockout',
                'severity' => 'danger',
                'auditable_type' => User::class,
                'auditable_id' => $member->id,
                'ip_address' => '180.252.92.11',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:128.0) Gecko/20100101 Firefox/128.0',
                'old_values' => null,
                'new_values' => null,
                'description' => "User {$member->name} was locked out due to 3 consecutive failed PIN attempts.",
                'created_at' => $date->copy()->setHour(22)->setMinute(15),
                'updated_at' => $date->copy()->setHour(22)->setMinute(15),
            ]);
        }

        // --- Day 7 (Yesterday) ---
        $date = $now->copy()->subDays(1);

        // 14. Member files a Loan Application
        if ($member && $loanApp) {
            AuditLog::create([
                'user_id' => $member->id,
                'action' => 'loan_applied',
                'severity' => 'info',
                'auditable_type' => get_class($loanApp),
                'auditable_id' => $loanApp->id,
                'ip_address' => '49.145.10.82',
                'user_agent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_5 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Mobile/15E148',
                'old_values' => null,
                'new_values' => null,
                'description' => "Member {$member->name} filed a new loan application for ₱50,000.00 (LN-" . str_pad($loanApp->id, 5, '0', STR_PAD_LEFT) . ").",
                'created_at' => $date->copy()->setHour(9)->setMinute(40),
                'updated_at' => $date->copy()->setHour(9)->setMinute(40),
            ]);
        }

        // 15. Co-maker Endorses the Loan
        if ($jane && $loanApp) {
            AuditLog::create([
                'user_id' => $jane->id,
                'action' => 'loan_comaker_endorsed',
                'severity' => 'info',
                'auditable_type' => get_class($loanApp),
                'auditable_id' => $loanApp->id,
                'ip_address' => '120.29.34.22',
                'user_agent' => 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0.0.0 Mobile Safari/537.36',
                'old_values' => null,
                'new_values' => null,
                'description' => "Co-maker {$jane->name} endorsed loan application LN-" . str_pad($loanApp->id, 5, '0', STR_PAD_LEFT) . " for member {$member->name}.",
                'created_at' => $date->copy()->setHour(13)->setMinute(15),
                'updated_at' => $date->copy()->setHour(13)->setMinute(15),
            ]);
        }

        // 16. Stage Approvals (Credit Committee stage passed)
        if ($admin && $loanApp) {
            AuditLog::create([
                'user_id' => $admin->id,
                'action' => 'loan_stage_approved',
                'severity' => 'info',
                'auditable_type' => get_class($loanApp),
                'auditable_id' => $loanApp->id,
                'ip_address' => '112.198.84.15',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0.0.0 Safari/537.36',
                'old_values' => null,
                'new_values' => null,
                'description' => "Stage 'Credit Committee' approved by {$admin->name} for loan application LN-" . str_pad($loanApp->id, 5, '0', STR_PAD_LEFT) . ".",
                'created_at' => $date->copy()->setHour(16)->setMinute(00),
                'updated_at' => $date->copy()->setHour(16)->setMinute(00),
            ]);

            AuditLog::create([
                'user_id' => null,
                'action' => 'loan_stage_passed',
                'severity' => 'info',
                'auditable_type' => get_class($loanApp),
                'auditable_id' => $loanApp->id,
                'ip_address' => '112.198.84.15',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0.0.0 Safari/537.36',
                'old_values' => null,
                'new_values' => null,
                'description' => "Loan application LN-" . str_pad($loanApp->id, 5, '0', STR_PAD_LEFT) . " moved to 'Board of Directors' stage.",
                'created_at' => $date->copy()->setHour(16)->setMinute(01),
                'updated_at' => $date->copy()->setHour(16)->setMinute(01),
            ]);
        }

        // --- Day 8 (Today!) ---
        $date = $now;

        // 17. Board of Directors full approval
        if ($superAdmin && $loanApp) {
            AuditLog::create([
                'user_id' => $superAdmin->id,
                'action' => 'loan_stage_approved',
                'severity' => 'info',
                'auditable_type' => get_class($loanApp),
                'auditable_id' => $loanApp->id,
                'ip_address' => '120.28.122.9',
                'user_agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0.0.0 Safari/537.36',
                'old_values' => ['status' => 'pending', 'current_stage' => 'board_of_directors'],
                'new_values' => ['status' => 'approved', 'current_stage' => 'completed'],
                'description' => "Loan application LN-" . str_pad($loanApp->id, 5, '0', STR_PAD_LEFT) . " has been fully approved.",
                'created_at' => $date->copy()->setHour(10)->setMinute(30),
                'updated_at' => $date->copy()->setHour(10)->setMinute(30),
            ]);
        }

        // 18. Admin deletes a member account simulation (Danger Audit)
        if ($admin) {
            $mockDeletedUser = [
                'id' => 999,
                'name' => 'Bad Actor',
                'email' => 'bad.actor@example.com',
                'company_id' => '99999999',
                'role' => 'member'
            ];

            AuditLog::create([
                'user_id' => $admin->id,
                'action' => 'member_deleted',
                'severity' => 'danger',
                'auditable_type' => null,
                'auditable_id' => null,
                'ip_address' => '112.198.84.15',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0.0.0 Safari/537.36',
                'old_values' => $mockDeletedUser,
                'new_values' => null,
                'description' => "Admin {$admin->name} deleted member account {$mockDeletedUser['name']}.",
                'created_at' => $date->copy()->setHour(11)->setMinute(15),
                'updated_at' => $date->copy()->setHour(11)->setMinute(15),
            ]);
        }

        // 19. Admin exports batch withdrawals
        if ($admin) {
            AuditLog::create([
                'user_id' => $admin->id,
                'action' => 'compliance_report_exported',
                'severity' => 'warning',
                'auditable_type' => null,
                'auditable_id' => null,
                'ip_address' => '112.198.84.15',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0.0.0 Safari/537.36',
                'old_values' => null,
                'new_values' => null,
                'description' => "Admin {$admin->name} exported withdrawals batch manifest PDF (Count: 15).",
                'created_at' => $date->copy()->setHour(11)->setMinute(30),
                'updated_at' => $date->copy()->setHour(11)->setMinute(30),
            ]);
        }
    }
}
