<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@nme.com'],
            [
                'first_name' => 'Admin',
                'last_name' => 'User',
                'password' => bcrypt('password'),
                'user_type' => 'admin',
                'company' => 'NME Administration',
                'phone' => '+234 800 000 0000',
                'location' => 'Abuja',
                'verified' => true,
                ]
            );

        $admin->avatar = 'images/avatar/' . $admin->id . '.jpg';
        $admin->save();

        // Create sample users for products
        $users = [
            [
                'first_name' => 'John',
                'last_name' => 'Adebayo',
                'email' => 'john.adebayo@nme.com',
                'password' => bcrypt('password'),
                'user_type' => 'both',
                'company' => 'Adebayo Mining Co.',
                'phone' => '+234 800 123 4567',
                'location' => 'Kaduna State',
                'verified' => true,
            ],
            [
                'first_name' => 'Sarah',
                'last_name' => 'Okafor',
                'email' => 'sarah.okafor@nme.com',
                'password' => bcrypt('password'),
                'user_type' => 'both',
                'company' => 'Okafor Limestone Ltd.',
                'phone' => '+234 800 234 5678',
                'location' => 'Ogun State',
                'verified' => true,
            ],
            [
                'first_name' => 'Michael',
                'last_name' => 'Adamu',
                'email' => 'michael.adamu@nme.com',
                'password' => bcrypt('password'),
                'user_type' => 'both',
                'company' => 'Plateau Tin Mining',
                'phone' => '+234 800 345 6789',
                'location' => 'Plateau State',
                'verified' => true,
            ],
            [
                'first_name' => 'Fatima',
                'last_name' => 'Bello',
                'email' => 'fatima.bello@nme.com',
                'password' => bcrypt('password'),
                'user_type' => 'both',
                'company' => 'Bello Minerals',
                'phone' => '+234 800 456 7890',
                'location' => 'Kogi State',
                'verified' => true,
            ],
            [
                'first_name' => 'Emeka',
                'last_name' => 'Nwachukwu',
                'email' => 'emeka.nwachukwu@nme.com',
                'password' => bcrypt('password'),
                'user_type' => 'both',
                'company' => 'Nwachukwu Coal Ltd.',
                'phone' => '+234 800 567 8901',
                'location' => 'Enugu State',
                'verified' => true,
            ],
            [
                'first_name' => 'Amina',
                'last_name' => 'Yusuf',
                'email' => 'amina.yusuf@nme.com',
                'password' => bcrypt('password'),
                'user_type' => 'both',
                'company' => 'Yusuf Iron Works',
                'phone' => '+234 800 678 9012',
                'location' => 'Nasarawa State',
                'verified' => true,
            ],
        ];

        foreach ($users as $userData) {
            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                $userData
            );
            $user->avatar = 'images/avatar/' . $user->id . '.jpg';
            $user->save();
        }

        // Create additional random users using factory
        User::factory()->count(5)->create([
            'verified' => true,
        ]);
    }

    private function downloadAvatar($userId)
    {
        $apiUrl = 'https://randomuser.me/api/?inc=picture&noinfo';
        $path = storage_path('app/public/avatar/' . $userId . '.jpg');
        File::ensureDirectoryExists(dirname($path));
        try {
            $response = Http::get($apiUrl);
            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['results'][0]['picture']['large'])) {
                    $photoUrl = $data['results'][0]['picture']['large'];
                    $photoResponse = Http::get($photoUrl);
                    if ($photoResponse->successful()) {
                        File::put($path, $photoResponse->body());
                    } else {
                        Log::error('Failed to download photo for user ' . $userId);
                    }
                } else {
                    Log::error('Invalid API response for user ' . $userId);
                }
            } else {
                Log::error('Failed to fetch random user data for user ' . $userId);
            }
        } catch (\Exception $e) {
            Log::error('Exception downloading avatar for user ' . $userId . ': ' . $e->getMessage());
        }
    }
}
