<?php

namespace Database\Seeders;

use App\Models\MarketInsight;
use App\Models\MarketInsightCategory;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class MarketInsightSeeder extends Seeder
{
    /**
     * Run the database seeds.
    */
    public function run(): void
    {
        $insights = [
            [
                'title' => 'Gold Prices Surge in Q4 2024',
                'content' => 'Nigerian gold market shows strong performance with increased international demand driving prices up by 15% this quarter. The surge is attributed to global economic uncertainties and increased investment in precious metals. Local miners are reporting higher profit margins, with Kaduna and Zamfara states leading production increases. Industry experts predict continued growth through 2025 as international buyers seek stable investment options.',
                'category_id' => MarketInsightCategory::where('name', 'Gold')->first()->id,
                'featured' => true,
                'tags' => json_encode(['market', 'gold']),
                'price_trend' => '+15%',
                'market_volume' => 'High',
                'investor_confidence' => 'Strong',
                'created_at' => now()->subDays(5),
                'updated_at' => now()->subDays(5),
            ],
            [
                'title' => 'Limestone Industry Growth Outlook',
                'content' => 'The limestone industry is experiencing unprecedented growth driven by Nigeria\'s construction boom. Major cities like Lagos and Abuja are leading consumption, with infrastructure projects requiring massive amounts of construction materials. Ogun State limestone producers are reporting 40% increase in orders from cement manufacturers. The industry is expected to grow by 25% annually over the next three years.',
                'category_id' => MarketInsightCategory::where('name', 'Limestone')->first()->id,
                'featured' => true,
                'tags' => json_encode(['market', 'industry', 'limestone', 'construction']),
                'created_at' => now()->subDays(8),
                'updated_at' => now()->subDays(8),
            ],
            [
                'title' => 'Tin Mining Regulations Update',
                'content' => 'New government policies aim to boost sustainable tin mining practices while ensuring environmental compliance. The Nigerian Minerals and Mining Act amendments focus on reducing environmental impact while increasing production efficiency. Plateau State tin mines are implementing new technologies for better ore extraction and waste management. The regulations also include incentives for companies adopting green mining practices.',
                'category_id' => MarketInsightCategory::where('name', 'Tin')->first()->id,
                'featured' => false,
                'tags' => json_encode(['regulations', 'mining', 'tin', 'environment']),
                'created_at' => now()->subDays(10),
                'updated_at' => now()->subDays(10),
            ],
            [
                'title' => 'Coal Market Faces Challenges',
                'content' => 'Environmental concerns and renewable energy push create headwinds for coal sector, prices down 8% this month. The global shift towards clean energy is impacting Nigerian coal exports, with major international buyers reducing orders. However, domestic demand from manufacturing and power sectors remains strong. Industry analysts suggest diversification into cleaner coal technologies could help maintain market position.',
                'category_id' => MarketInsightCategory::where('name', 'Coal')->first()->id,
                'featured' => false,
                'tags' => json_encode(['analysis', 'coal', 'commodities']),
                'price_trend' => '-8%',
                'market_volume' => 'Medium',
                'investor_confidence' => 'Moderate',
                'created_at' => now()->subDays(12),
                'updated_at' => now()->subDays(12),
            ],
            [
                'title' => 'Iron Ore Export Opportunities',
                'content' => 'Rising global steel demand creates new export opportunities for Nigerian iron ore producers. International steel manufacturers are seeking reliable suppliers as global supply chains face disruptions. Nigerian iron ore with its high-grade quality is gaining attention from Asian markets. Kogi and Nasarawa states are positioning themselves as key suppliers to meet growing international demand.',
                'category_id' => MarketInsightCategory::where('name', 'Iron Ore')->first()->id,
                'featured' => true,
                'tags' => json_encode(['news', 'export', 'iron ore', 'commodities']),
                'created_at' => now()->subDays(15),
                'updated_at' => now()->subDays(15),
            ],
            [
                'title' => 'Copper Market Analysis Q4',
                'content' => 'Copper prices remain stable despite global economic uncertainties. Nigerian copper deposits in Zamfara State show promising exploration results. The metal\'s importance in renewable energy technologies is driving steady demand. Local mining companies are investing in exploration and extraction technologies to capitalize on growing market opportunities.',
                'category_id' => MarketInsightCategory::where('name', 'Copper')->first()->id,
                'featured' => false,
                'tags' => json_encode(['market', 'analysis', 'copper', 'commodities']),
                'created_at' => now()->subDays(18),
                'updated_at' => now()->subDays(18),
            ],
            [
                'title' => 'Zinc Industry Trends',
                'content' => 'Zinc concentrate production increases as demand from galvanizing industry grows. Ebonyi State zinc mines are expanding operations to meet rising domestic and international demand. The metal\'s corrosion-resistant properties make it essential for construction and manufacturing sectors. New processing facilities are being established to improve product quality and meet international standards.',
                'category_id' => MarketInsightCategory::where('name', 'Zinc')->first()->id,
                'featured' => false,
                'tags' => json_encode(['market', 'trends', 'zinc', 'commodities']),
                'created_at' => now()->subDays(20),
                'updated_at' => now()->subDays(20),
            ],
            [
                'title' => 'Lead Market Dynamics',
                'content' => 'Lead ore prices stabilize after recent volatility. Taraba State lead deposits continue to attract investment from battery manufacturers. The growing electric vehicle market is creating new demand streams for lead-acid batteries. Mining companies are focusing on sustainable extraction methods to meet environmental standards while maximizing production efficiency.',
                'category_id' => MarketInsightCategory::where('name', 'Lead')->first()->id,
                'featured' => false,
                'tags' => json_encode(['market', 'news', 'lead', 'analysis']),
                'created_at' => now()->subDays(22),
                'updated_at' => now()->subDays(22),
            ],
            [
                'title' => 'Bauxite Production Forecast',
                'content' => 'Bauxite production expected to increase 30% in 2025. Ogun State bauxite mines are ramping up operations to meet growing aluminum industry demand. New processing facilities are being commissioned to improve ore quality and extraction efficiency. The industry is attracting significant foreign investment as global aluminum demand continues to rise.',
                'category_id' => MarketInsightCategory::where('name', 'Bauxite')->first()->id,
                'featured' => true,
                'tags' => json_encode(['market', 'forecast', 'bauxite', 'analysis']),
                'created_at' => now()->subDays(25),
                'updated_at' => now()->subDays(25),
            ],
            [
                'title' => 'Mineral Export Strategy',
                'content' => 'Government launches comprehensive mineral export strategy to boost foreign exchange earnings. The initiative focuses on value addition, quality improvement, and market diversification. Key minerals like gold, tin, and iron ore are prioritized for export promotion. The strategy includes incentives for exporters and infrastructure development to support increased mineral trade.',
                'category_id' => MarketInsightCategory::where('name', 'News')->first()->id,
                'featured' => true,
                'tags' => json_encode(['market', 'finance', 'gold', 'commodities']),
                'created_at' => now()->subDays(28),
                'updated_at' => now()->subDays(28),
            ],
        ];

        foreach ($insights as $insightData) {
            MarketInsight::factory()->create($insightData);
        }

        $this->random();

    }

    public function random(): void
    {
        $users = User::all();
        if ($users->isEmpty()) {
            return;
        }

        // get the name of market insight categories
        $marketInsightCategories = MarketInsightCategory::pluck('name')->toArray();
        $tagOptions = ['market', 'finance', 'crypto', 'real-estate', 'commodities', 'analysis', 'economy', 'stocks', 'inflation', 'tech'];
        $numInsights = 5;

        for ($i = 0; $i < $numInsights; $i++) {
            $marketInsightCategoryIndex = array_rand($marketInsightCategories);
            $categoryName = $marketInsightCategories[$marketInsightCategoryIndex];
            $title = Str::title($categoryName . ' Market Update ' . now()->format('M Y'));
            $content = 'The ' . strtolower($categoryName) . ' market is experiencing ' . ['growth', 'stability', 'challenges', 'opportunities'][array_rand(['growth', 'stability', 'challenges', 'opportunities'])] . ' due to recent economic factors. Analysis shows ' . ['positive', 'neutral', 'negative'][array_rand(['positive', 'neutral', 'negative'])] . ' trends with implications for investors.';
            $featured = rand(0, 1);
            $userId = $users->random()->id;
            $numTags = rand(3, 6);
            $categoryId = MarketInsightCategory::where('name', $categoryName)->first()->id;
            shuffle($tagOptions);
            $selected = array_slice($tagOptions, 0, $numTags);
            $tags = json_encode($selected);

            MarketInsight::create([
                'title' => $title,
                'content' => $content,
                'category_id' => $categoryId,
                'featured' => $featured,
                'user_id' => $userId,
                'price_trend' => ['+15%', '-8%', 'Stable'][rand(0, 2)],
                'market_volume' => ['High', 'Medium', 'Low'][rand(0, 2)],
                'investor_confidence' => ['Strong', 'Moderate', 'Weak'][rand(0, 2)],
                'tags' => $tags,
                'created_at' => now()->subDays(rand(1, 30)),
                'updated_at' => now()->subDays(rand(1, 30)),
            ]);
        }
    }
}
