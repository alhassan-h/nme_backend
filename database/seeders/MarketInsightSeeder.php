<?php

namespace Database\Seeders;

use App\Models\MarketInsight;
use Illuminate\Database\Seeder;

class MarketInsightSeeder extends Seeder
{
    public function run(): void
    {
        MarketInsight::factory()->count(20)->create();
    }
}
