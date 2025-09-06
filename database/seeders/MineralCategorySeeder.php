<?php

namespace Database\Seeders;

use App\Models\MineralCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MineralCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Gold',
                'icon' => '✨',
            ],
            [
                'name' => 'Limestone',
                'icon' => '🏔️',
            ],
            [
                'name' => 'Tin',
                'icon' => '⚡',
            ],
            [
                'name' => 'Coal',
                'icon' => '⚫',
            ],
            [
                'name' => 'Iron Ore',
                'icon' => '🔩',
            ],
            [
                'name' => 'Lead',
                'icon' => '🔘',
            ],
            [
                'name' => 'Zinc',
                'icon' => '⚪',
            ],
            [
                'name' => 'Copper',
                'icon' => '🟤',
            ],
            [
                'name' => 'Diamond',
                'icon' => '💎',
            ],
            [
                'name' => 'Gemstones',
                'icon' => '💍',
            ],
            [
                'name' => 'Salt',
                'icon' => '🧂',
            ],
            [
                'name' => 'Gypsum',
                'icon' => '🏗️',
            ],
            [
                'name' => 'Bauxite',
                'icon' => '⚙️',
            ],
            [
                'name' => 'Uranium',
                'icon' => '☢️',
            ],
            [
                'name' => 'Oil',
                'icon' => '🛢️',
            ],
            [
                'name' => 'Gas',
                'icon' => '🔥',
            ],
        ];

        foreach ($categories as $category) {
            MineralCategory::create($category);
        }
    }
}
