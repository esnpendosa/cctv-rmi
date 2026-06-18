<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Class UserSeeder
 * 
 * Seeds default users with specific roles.
 * 
 * @package Database\Seeders
 */
class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Super Admin
        $superAdmin = User::firstOrCreate(
            ['email' => 'superadmin@cctvmanager.com'],
            [
                'name' => 'Super Admin User',
                'password' => Hash::make('password'),
            ]
        );
        $superAdmin->assignRole('Super Admin');

        // 2. Admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@cctvmanager.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
            ]
        );
        $admin->assignRole('Admin');

        // 3. Technician
        $technician = User::firstOrCreate(
            ['email' => 'teknisi@cctvmanager.com'],
            [
                'name' => 'Technician User',
                'password' => Hash::make('password'),
            ]
        );
        $technician->assignRole('Technician');

        // 4. Finance
        $finance = User::firstOrCreate(
            ['email' => 'finance@cctvmanager.com'],
            [
                'name' => 'Finance User',
                'password' => Hash::make('password'),
            ]
        );
        $finance->assignRole('Finance');

        // 5. Viewer
        $viewer = User::firstOrCreate(
            ['email' => 'viewer@cctvmanager.com'],
            [
                'name' => 'Viewer User',
                'password' => Hash::make('password'),
            ]
        );
        $viewer->assignRole('Viewer');
    }
}
