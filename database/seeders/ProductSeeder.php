<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::where('role', 'user')->get();

        foreach ($users as $user) {
            Product::factory()->count(5)->create([
                'seller_id' => $user->id,
                'status' => Product::STATUS_ACTIVE,
            ]);
        }
    }
}
