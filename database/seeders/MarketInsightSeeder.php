<?php

namespace Database\Seeders;

use App\Models\MarketInsight;
use Illuminate\Database\Seeder;

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
                'category' => 'Gold',
                'featured' => true,
                'created_at' => now()->subDays(5),
                'updated_at' => now()->subDays(5),
            ],
            [
                'title' => 'Limestone Industry Growth Outlook',
                'content' => 'The limestone industry is experiencing unprecedented growth driven by Nigeria\'s construction boom. Major cities like Lagos and Abuja are leading consumption, with infrastructure projects requiring massive amounts of construction materials. Ogun State limestone producers are reporting 40% increase in orders from cement manufacturers. The industry is expected to grow by 25% annually over the next three years.',
                'category' => 'Limestone',
                'featured' => true,
                'created_at' => now()->subDays(8),
                'updated_at' => now()->subDays(8),
            ],
            [
                'title' => 'Tin Mining Regulations Update',
                'content' => 'New government policies aim to boost sustainable tin mining practices while ensuring environmental compliance. The Nigerian Minerals and Mining Act amendments focus on reducing environmental impact while increasing production efficiency. Plateau State tin mines are implementing new technologies for better ore extraction and waste management. The regulations also include incentives for companies adopting green mining practices.',
                'category' => 'Tin',
                'featured' => false,
                'created_at' => now()->subDays(10),
                'updated_at' => now()->subDays(10),
            ],
            [
                'title' => 'Coal Market Faces Challenges',
                'content' => 'Environmental concerns and renewable energy push create headwinds for coal sector, prices down 8% this month. The global shift towards clean energy is impacting Nigerian coal exports, with major international buyers reducing orders. However, domestic demand from manufacturing and power sectors remains strong. Industry analysts suggest diversification into cleaner coal technologies could help maintain market position.',
                'category' => 'Coal',
                'featured' => false,
                'created_at' => now()->subDays(12),
                'updated_at' => now()->subDays(12),
            ],
            [
                'title' => 'Iron Ore Export Opportunities',
                'content' => 'Rising global steel demand creates new export opportunities for Nigerian iron ore producers. International steel manufacturers are seeking reliable suppliers as global supply chains face disruptions. Nigerian iron ore with its high-grade quality is gaining attention from Asian markets. Kogi and Nasarawa states are positioning themselves as key suppliers to meet growing international demand.',
                'category' => 'Iron Ore',
                'featured' => true,
                'created_at' => now()->subDays(15),
                'updated_at' => now()->subDays(15),
            ],
            [
                'title' => 'Copper Market Analysis Q4',
                'content' => 'Copper prices remain stable despite global economic uncertainties. Nigerian copper deposits in Zamfara State show promising exploration results. The metal\'s importance in renewable energy technologies is driving steady demand. Local mining companies are investing in exploration and extraction technologies to capitalize on growing market opportunities.',
                'category' => 'Copper',
                'featured' => false,
                'created_at' => now()->subDays(18),
                'updated_at' => now()->subDays(18),
            ],
            [
                'title' => 'Zinc Industry Trends',
                'content' => 'Zinc concentrate production increases as demand from galvanizing industry grows. Ebonyi State zinc mines are expanding operations to meet rising domestic and international demand. The metal\'s corrosion-resistant properties make it essential for construction and manufacturing sectors. New processing facilities are being established to improve product quality and meet international standards.',
                'category' => 'Zinc',
                'featured' => false,
                'created_at' => now()->subDays(20),
                'updated_at' => now()->subDays(20),
            ],
            [
                'title' => 'Lead Market Dynamics',
                'content' => 'Lead ore prices stabilize after recent volatility. Taraba State lead deposits continue to attract investment from battery manufacturers. The growing electric vehicle market is creating new demand streams for lead-acid batteries. Mining companies are focusing on sustainable extraction methods to meet environmental standards while maximizing production efficiency.',
                'category' => 'Lead',
                'featured' => false,
                'created_at' => now()->subDays(22),
                'updated_at' => now()->subDays(22),
            ],
            [
                'title' => 'Bauxite Production Forecast',
                'content' => 'Bauxite production expected to increase 30% in 2025. Ogun State bauxite mines are ramping up operations to meet growing aluminum industry demand. New processing facilities are being commissioned to improve ore quality and extraction efficiency. The industry is attracting significant foreign investment as global aluminum demand continues to rise.',
                'category' => 'Bauxite',
                'featured' => true,
                'created_at' => now()->subDays(25),
                'updated_at' => now()->subDays(25),
            ],
            [
                'title' => 'Mineral Export Strategy',
                'content' => 'Government launches comprehensive mineral export strategy to boost foreign exchange earnings. The initiative focuses on value addition, quality improvement, and market diversification. Key minerals like gold, tin, and iron ore are prioritized for export promotion. The strategy includes incentives for exporters and infrastructure development to support increased mineral trade.',
                'category' => 'General',
                'featured' => true,
                'created_at' => now()->subDays(28),
                'updated_at' => now()->subDays(28),
            ],
        ];

        foreach ($insights as $insightData) {
            MarketInsight::create($insightData);
        }
    }
}
