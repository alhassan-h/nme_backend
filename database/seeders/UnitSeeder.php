<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $units = [
            "kg", "ton", "metric ton", "pound", "ounce", "gram", "carat", "piece",
            "cubic meter", "square meter", "liter", "gallon", "barrel", "bag", "sack"
        ];

        foreach ($units as $unit) {
            \App\Models\Unit::create([
                'name' => $unit,
                'is_active' => true,
            ]);
        }
    }
}
