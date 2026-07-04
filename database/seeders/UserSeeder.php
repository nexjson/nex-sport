<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $superAdminRole = Role::where('name', 'super-admin')->first();
        $adminRole = Role::where('name', 'admin')->first();
        $organizerRole = Role::where('name', 'organizer')->first();
        $playerRole = Role::where('name', 'player')->first();

        // 1. Super Admin
        User::firstOrCreate(
            ['email' => 'superadmin@nexsport.com'],
            [
                'username' => 'superadmin',
                'name' => 'Super Admin NEX-Sport',
                'password' => Hash::make('password'),
                'phone' => '081234567890',
                'role_id' => $superAdminRole->id,
                'status' => true,
                'email_verified' => true,
                'email_verified_at' => now(),
            ]
        );

        // 2. Admins
        User::firstOrCreate(
            ['email' => 'admin1@nexsport.com'],
            [
                'username' => 'admin1',
                'name' => 'Operational Admin 1',
                'password' => Hash::make('password'),
                'phone' => '081234567891',
                'role_id' => $adminRole->id,
                'status' => true,
                'email_verified' => true,
                'email_verified_at' => now(),
            ]
        );

        User::firstOrCreate(
            ['email' => 'admin2@nexsport.com'],
            [
                'username' => 'admin2',
                'name' => 'Operational Admin 2',
                'password' => Hash::make('password'),
                'phone' => '081234567892',
                'role_id' => $adminRole->id,
                'status' => true,
                'email_verified' => true,
                'email_verified_at' => now(),
            ]
        );

        // 3. Organizers
        for ($i = 1; $i <= 3; $i++) {
            User::firstOrCreate(
                ['email' => "organizer{$i}@nexsport.com"],
                [
                    'username' => "organizer{$i}",
                    'name' => "Tournament Organizer {$i}",
                    'password' => Hash::make('password'),
                    'phone' => '08123456789'.($i + 2),
                    'role_id' => $organizerRole->id,
                    'status' => true,
                    'email_verified' => true,
                    'email_verified_at' => now(),
                ]
            );
        }

        // 4. Players
        for ($i = 1; $i <= 20; $i++) {
            User::firstOrCreate(
                ['email' => "player{$i}@nexsport.com"],
                [
                    'username' => "player{$i}",
                    'name' => "Esport/Sport Player {$i}",
                    'password' => Hash::make('password'),
                    'phone' => '0812987654'.sprintf('%02d', $i),
                    'role_id' => $playerRole->id,
                    'status' => true,
                    'email_verified' => true,
                    'email_verified_at' => now(),
                ]
            );
        }
    }
}
