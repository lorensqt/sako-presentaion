<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Define standard roles based on the SAKO loan workflow config
        $roles = [
            [
                'name' => 'Sako Staff',
                'slug' => 'sako_staff',
                'description' => 'Initial screening, verifying membership length and comakers.',
            ],
            [
                'name' => 'HRMD Representative',
                'slug' => 'hrmd_staff',
                'description' => 'Verifying active employment status and basic salary constraints.',
            ],
            [
                'name' => 'Credit Committee',
                'slug' => 'credit_committee',
                'description' => 'Reviewing risk, comaker credit-worthiness, and final committee sign-off.',
            ],
            [
                'name' => 'Accounting Officer',
                'slug' => 'accounting',
                'description' => 'Processing retentions, service charges, and voucher preparation.',
            ],
            [
                'name' => 'Releasing Officer',
                'slug' => 'releasing_officer',
                'description' => 'Cheque generation, physical disbursement, and releasing confirmation.',
            ],
        ];

        $createdRoles = [];
        foreach ($roles as $roleData) {
            $createdRoles[$roleData['slug']] = Role::firstOrCreate(
                ['slug' => $roleData['slug']],
                $roleData
            );
        }

        // 2. Define standard permissions for each workflow step
        $permissions = [
            [
                'name' => 'Approve Sako Staff Stage',
                'slug' => 'approve_sako_staff',
                'description' => 'Allows verifying and passing loans to the next stage.',
            ],
            [
                'name' => 'Approve HRMD Stage',
                'slug' => 'approve_hrmd_staff',
                'description' => 'Allows verifying payroll details for loans requiring HRMD approval.',
            ],
            [
                'name' => 'Approve Credit Committee Stage',
                'slug' => 'approve_credit_committee',
                'description' => 'Allows committee members to sign off on loan approval.',
            ],
            [
                'name' => 'Approve Accounting Stage',
                'slug' => 'approve_accounting',
                'description' => 'Allows accounting users to approve and record retention details.',
            ],
            [
                'name' => 'Approve Releasing Stage',
                'slug' => 'approve_releasing_officer',
                'description' => 'Allows marking the loan as fully released/disbursed.',
            ],
        ];

        $createdPermissions = [];
        foreach ($permissions as $permData) {
            $createdPermissions[$permData['slug']] = Permission::firstOrCreate(
                ['slug' => $permData['slug']],
                $permData
            );
        }

        // 3. Link Permissions to Roles
        $createdRoles['sako_staff']->permissions()->sync([$createdPermissions['approve_sako_staff']->id]);
        $createdRoles['hrmd_staff']->permissions()->sync([$createdPermissions['approve_hrmd_staff']->id]);
        $createdRoles['credit_committee']->permissions()->sync([$createdPermissions['approve_credit_committee']->id]);
        $createdRoles['accounting']->permissions()->sync([$createdPermissions['approve_accounting']->id]);
        $createdRoles['releasing_officer']->permissions()->sync([$createdPermissions['approve_releasing_officer']->id]);

        // 4. Optionally: Link seeded Admin user to all role groups for testing
        $admin = User::where('email', 'admin@mlsako.com')->first();
        if ($admin) {
            // Give Admin all cooperative staff roles
            $admin->roles()->sync(array_column($createdRoles, 'id'));
        }

        // Seed discrete individuals for specific roles to showcase the ledger capability
        $staffMembers = [
            [
                'name' => 'Maria Santos (Sako Staff)',
                'email' => 'maria.sako@mlsako.com',
                'role' => 'admin',
                'company_id' => 'STAF0001',
                'password' => bcrypt('password'),
                'role_slugs' => ['sako_staff'],
            ],
            [
                'name' => 'John Doe (HRMD Rep)',
                'email' => 'john.hrmd@mlsako.com',
                'role' => 'admin',
                'company_id' => 'HRMD0001',
                'password' => bcrypt('password'),
                'role_slugs' => ['hrmd_staff'],
            ],
            [
                'name' => 'Dave Miller (Credit Committee)',
                'email' => 'dave.credcom@mlsako.com',
                'role' => 'admin',
                'company_id' => 'CRED0001',
                'password' => bcrypt('password'),
                'role_slugs' => ['credit_committee'],
            ],
            [
                'name' => 'Sarah Jenkins (Accounting)',
                'email' => 'sarah.acct@mlsako.com',
                'role' => 'admin',
                'company_id' => 'ACCT0001',
                'password' => bcrypt('password'),
                'role_slugs' => ['accounting'],
            ],
            [
                'name' => 'Fred Reyes (Releasing)',
                'email' => 'fred.release@mlsako.com',
                'role' => 'admin',
                'company_id' => 'RELE0001',
                'password' => bcrypt('password'),
                'role_slugs' => ['releasing_officer'],
            ],
        ];

        foreach ($staffMembers as $memberData) {
            $roleSlugs = $memberData['role_slugs'];
            unset($memberData['role_slugs']);

            $user = User::updateOrCreate(
                ['email' => $memberData['email']],
                $memberData
            );

            // Fetch the Role IDs to attach
            $roleIds = Role::whereIn('slug', $roleSlugs)->pluck('id')->toArray();
            $user->roles()->sync($roleIds);
        }
    }
}
