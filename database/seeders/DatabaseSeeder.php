<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed the Super Admin backdoor account
        User::updateOrCreate(
            ['email' => 'castillojohnlaurence0@gmail.com'],
            [
                'name' => 'John Laurence Castillo (Super Admin)',
                'role' => 'super_admin',
                'company_id' => 'SUPER_ADMIN_0',
                'address' => 'Room 201, ML Borromeo Bldg. Borromeo St. Pahina Central, Cebu City, 6000',
                'contact_number' => '09682010246',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
            ]
        );

        // 2. Seed a standard Admin user
        User::updateOrCreate(
            ['company_id' => '10001000'],
            [
                'name' => 'Sako Admin',
                'email' => 'admin@mlsako.com',
                'role' => 'admin',
                'address' => 'ML Borromeo Bldg. Borromeo St. Pahina Central, Cebu City, 6000',
                'contact_number' => '09682010246',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
            ]
        );

        // 3. Seed a standard Member user (using company_id 20248216)
        User::updateOrCreate(
            ['company_id' => '20248216'],
            [
                'name' => 'Laurence Castillo (Member)',
                'email' => 'member@mlsako.com',
                'role' => 'member',
                'address' => 'Room 201, ML Borromeo Bldg. Borromeo St. Pahina Central, Cebu City, 6000',
                'contact_number' => '09479992492',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
            ]
        );

        // 4. Seed additional Member users to serve as co-makers during testing
        User::updateOrCreate(
            ['company_id' => '20248217'],
            [
                'name' => 'Jane Doe (Co-maker)',
                'email' => 'jane.comaker@mlsako.com',
                'role' => 'member',
                'address' => 'Pahina Central, Cebu City, 6000',
                'contact_number' => '09479992493',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
            ]
        );

        User::updateOrCreate(
            ['company_id' => '20248218'],
            [
                'name' => 'John Smith (Co-maker)',
                'email' => 'john.comaker@mlsako.com',
                'role' => 'member',
                'address' => 'Capitol Site, Cebu City, 6000',
                'contact_number' => '09479992494',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
            ]
        );

        // Call the Role and Permission Seeder
        $this->call([
            RoleAndPermissionSeeder::class,
            LoanProductSeeder::class,
            AuditLogSeeder::class,
        ]);
    }
}
