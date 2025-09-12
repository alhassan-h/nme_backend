<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            MineralCategorySeeder::class,
            LocationSeeder::class,
            UnitSeeder::class,
            ProductSeeder::class,
            MarketInsightCategorySeeder::class,
            MarketInsightSeeder::class,
            MarketInsightLikeSeeder::class,
            ForumSeeder::class,
            GallerySeeder::class,
        ]);
    }
}
