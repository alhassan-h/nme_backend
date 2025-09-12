<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\MarketInsightCategory;

class MarketInsightCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Technology',
                'description' => 'Insights related to technology trends, innovations, and digital transformation in the mining industry.'
            ],
            [
                'name' => 'Finance',
                'description' => 'Financial analysis, investment opportunities, and economic trends affecting mineral markets.'
            ],
            [
                'name' => 'Healthcare',
                'description' => 'Medical and healthcare applications of minerals and mining industry health & safety.'
            ],
            [
                'name' => 'Energy',
                'description' => 'Energy sector insights, renewable energy minerals, and traditional energy markets.'
            ],
            [
                'name' => 'Construction',
                'description' => 'Construction materials, infrastructure development, and building industry trends.'
            ],
            [
                'name' => 'Manufacturing',
                'description' => 'Manufacturing processes, industrial applications, and production trends.'
            ],
            [
                'name' => 'Agriculture',
                'description' => 'Agricultural applications, fertilizers, and farming industry insights.'
            ],
            [
                'name' => 'Environment',
                'description' => 'Environmental impact, sustainability, and green mining practices.'
            ],
            [
                'name' => 'Gold',
                'description' => 'Gold market analysis, mining trends, and investment opportunities.'
            ],
            [
                'name' => 'Coal',
                'description' => 'Coal industry insights, market dynamics, and energy transition impacts.'
            ],
            [
                'name' => 'Tin',
                'description' => 'Tin market analysis, applications, and global supply chain insights.'
            ],
            [
                'name' => 'News',
                'description' => 'Latest news and updates from the mining industry and mineral markets.'
            ],
            [
                'name' => 'Limestone',
                'description' => 'Limestone market analysis, construction applications, and cement industry insights.'
            ],
            [
                'name' => 'Iron Ore',
                'description' => 'Iron ore market dynamics, steel industry trends, and global supply chain analysis.'
            ],
            [
                'name' => 'Copper',
                'description' => 'Copper market analysis, electrical applications, and industrial demand trends.'
            ],
            [
                'name' => 'Zinc',
                'description' => 'Zinc market insights, galvanizing industry, and corrosion protection applications.'
            ],
            [
                'name' => 'Lead',
                'description' => 'Lead market analysis, battery manufacturing, and industrial applications.'
            ],
            [
                'name' => 'Bauxite',
                'description' => 'Bauxite production trends, aluminum industry, and global market dynamics.'
            ],
            [
                'name' => 'Nickel',
                'description' => 'Nickel market insights, stainless steel production, and battery applications.'
            ],
            [
                'name' => 'Cobalt',
                'description' => 'Cobalt market analysis, battery technology, and supply chain considerations.'
            ],
            [
                'name' => 'Manganese',
                'description' => 'Manganese market trends, steel production, and industrial applications.'
            ],
            [
                'name' => 'Phosphate',
                'description' => 'Phosphate market insights, fertilizer industry, and agricultural applications.'
            ],
            [
                'name' => 'Sulphur',
                'description' => 'Sulphur market analysis, chemical industry, and agricultural uses.'
            ],
            [
                'name' => 'Salt',
                'description' => 'Salt market dynamics, industrial applications, and food industry trends.'
            ],
            [
                'name' => 'Gypsum',
                'description' => 'Gypsum market insights, construction applications, and agricultural uses.'
            ],
            [
                'name' => 'Clays',
                'description' => 'Clays market analysis, ceramics industry, and industrial applications.'
            ],
            [
                'name' => 'Silica',
                'description' => 'Silica market trends, glass manufacturing, and industrial uses.'
            ],
            [
                'name' => 'Mining',
                'description' => 'Mining industry insights, trends, and market analysis.'
            ],
            [
                'name' => 'Market Trends',
                'description' => 'Current trends and future outlook in various markets.'
            ],
            [
                'name' => 'Regulations',
                'description' => 'Regulatory landscape and compliance issues affecting industries.'
            ],
            [
                'name' => 'Investments',
                'description' => 'Investment opportunities and market analysis for investors.'
            ],
            [
                'name' => 'Other',
                'description' => 'Miscellaneous insights and general market analysis.'
            ],
        ];

        foreach ($categories as $category) {
            MarketInsightCategory::create($category);
        }
    }
}
