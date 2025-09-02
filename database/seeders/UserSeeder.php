<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin user
        User::firstOrCreate(
            ['email' => 'admin@nme.com'],
            [
                'name' => 'Admin User',
                'password' => bcrypt('password'),
                'role' => 'admin',
                'user_type' => 'both',
                'company' => 'NME Administration',
                'phone' => '+234 800 000 0000',
                'location' => 'Abuja',
                'verified' => true,
            ]
        );

        // Create sample users for products
        $users = [
            [
                'name' => 'John Adebayo',
                'email' => 'john.adebayo@nme.com',
                'password' => bcrypt('password'),
                'user_type' => 'both',
                'company' => 'Adebayo Mining Co.',
                'phone' => '+234 800 123 4567',
                'location' => 'Kaduna State',
                'role' => 'user',
                'verified' => true,
            ],
            [
                'name' => 'Sarah Okafor',
                'email' => 'sarah.okafor@nme.com',
                'password' => bcrypt('password'),
                'user_type' => 'both',
                'company' => 'Okafor Limestone Ltd.',
                'phone' => '+234 800 234 5678',
                'location' => 'Ogun State',
                'role' => 'user',
                'verified' => true,
            ],
            [
                'name' => 'Michael Adamu',
                'email' => 'michael.adamu@nme.com',
                'password' => bcrypt('password'),
                'user_type' => 'both',
                'company' => 'Plateau Tin Mining',
                'phone' => '+234 800 345 6789',
                'location' => 'Plateau State',
                'role' => 'user',
                'verified' => true,
            ],
            [
                'name' => 'Fatima Bello',
                'email' => 'fatima.bello@nme.com',
                'password' => bcrypt('password'),
                'user_type' => 'both',
                'company' => 'Bello Minerals',
                'phone' => '+234 800 456 7890',
                'location' => 'Kogi State',
                'role' => 'user',
                'verified' => true,
            ],
            [
                'name' => 'Emeka Nwachukwu',
                'email' => 'emeka.nwachukwu@nme.com',
                'password' => bcrypt('password'),
                'user_type' => 'both',
                'company' => 'Nwachukwu Coal Ltd.',
                'phone' => '+234 800 567 8901',
                'location' => 'Enugu State',
                'role' => 'user',
                'verified' => true,
            ],
            [
                'name' => 'Amina Yusuf',
                'email' => 'amina.yusuf@nme.com',
                'password' => bcrypt('password'),
                'user_type' => 'both',
                'company' => 'Yusuf Iron Works',
                'phone' => '+234 800 678 9012',
                'location' => 'Nasarawa State',
                'role' => 'user',
                'verified' => true,
            ],
        ];

        foreach ($users as $userData) {
            User::firstOrCreate(
                ['email' => $userData['email']],
                $userData
            );
        }

        // Create additional random users using factory
        User::factory()->count(5)->create([
            'verified' => true,
        ]);
    }
}
