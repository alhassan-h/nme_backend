<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->admin()->create([
            'name' => 'Admin User',
            'email' => 'admin@nme.com',
            'password' => bcrypt('password'),
            'verified' => true,
        ]);

        User::factory()->count(10)->create([
            'verified' => true,
        ]);
    }
}
