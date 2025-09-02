<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get pre-seeded users from UserSeeder
        $johnAdebayo = User::where('email', 'john.adebayo@nme.com')->first();
        $sarahOkafor = User::where('email', 'sarah.okafor@nme.com')->first();
        $michaelAdamu = User::where('email', 'michael.adamu@nme.com')->first();
        $fatimaBello = User::where('email', 'fatima.bello@nme.com')->first();
        $emekaNwachukwu = User::where('email', 'emeka.nwachukwu@nme.com')->first();
        $aminaYusuf = User::where('email', 'amina.yusuf@nme.com')->first();

        // Ensure all required users exist
        if (!$johnAdebayo || !$sarahOkafor || !$michaelAdamu || !$fatimaBello || !$emekaNwachukwu || !$aminaYusuf) {
            throw new \Exception('Required users not found. Please run UserSeeder first.');
        }

        // Get mineral categories for mapping
        $categories = \App\Models\MineralCategory::pluck('id', 'name')->toArray();

        // Sample products data
        $products = [
            [
                'title' => 'Premium Gold Nuggets - 2kg',
                'description' => 'High-grade gold nuggets from our Kaduna mining operations. 99.9% purity guaranteed with full certification.',
                'mineral_category_id' => $categories['Gold'] ?? null,
                'price' => 2850000,
                'quantity' => 2,
                'unit' => 'kg',
                'location' => 'Kaduna State',
                'seller_id' => $johnAdebayo->id,
                'status' => Product::STATUS_ACTIVE,
                'views' => 234,
                'images' => ['/products/gold-nuggets-1.jpg', '/products/gold-nuggets-2.jpg'],
                'created_at' => now()->subDays(5),
                'updated_at' => now()->subDays(5),
            ],
            [
                'title' => 'Limestone Blocks - Bulk Supply',
                'description' => 'High-quality limestone blocks suitable for construction. Available in various sizes with consistent quality.',
                'mineral_category_id' => $categories['Limestone'] ?? null,
                'price' => 45000,
                'quantity' => 500,
                'unit' => 'ton',
                'location' => 'Ogun State',
                'seller_id' => $sarahOkafor->id,
                'status' => Product::STATUS_ACTIVE,
                'views' => 189,
                'images' => ['/products/limestone-blocks-1.jpg', '/products/limestone-blocks-2.jpg'],
                'created_at' => now()->subDays(6),
                'updated_at' => now()->subDays(6),
            ],
            [
                'title' => 'Tin Ore Concentrate - 95% Purity',
                'description' => 'Premium tin ore concentrate from Jos plateau. Ideal for industrial applications with 95% purity level.',
                'mineral_category_id' => $categories['Tin'] ?? null,
                'price' => 1200000,
                'quantity' => 10,
                'unit' => 'ton',
                'location' => 'Plateau State',
                'seller_id' => $michaelAdamu->id,
                'status' => Product::STATUS_PENDING,
                'views' => 0,
                'images' => ['/products/tin-ore-1.jpg', '/products/tin-ore-2.jpg'],
                'created_at' => now()->subDays(7),
                'updated_at' => now()->subDays(7),
            ],
            [
                'title' => 'Coal - Industrial Grade',
                'description' => 'High-quality industrial grade coal suitable for manufacturing and power generation.',
                'mineral_category_id' => $categories['Coal'] ?? null,
                'price' => 85000,
                'quantity' => 200,
                'unit' => 'ton',
                'location' => 'Enugu State',
                'seller_id' => $emekaNwachukwu->id,
                'status' => Product::STATUS_SOLD,
                'views' => 98,
                'images' => ['/products/coal-industrial-1.jpg', '/products/coal-industrial-2.jpg'],
                'created_at' => now()->subDays(8),
                'updated_at' => now()->subDays(8),
            ],
            [
                'title' => 'Iron Ore - High Grade',
                'description' => 'Premium iron ore with 65% iron content. Perfect for steel production and manufacturing.',
                'mineral_category_id' => $categories['Iron Ore'] ?? null,
                'price' => 95000,
                'quantity' => 150,
                'unit' => 'ton',
                'location' => 'Nasarawa State',
                'seller_id' => $aminaYusuf->id,
                'status' => Product::STATUS_ACTIVE,
                'views' => 156,
                'images' => ['/products/iron-ore-1.jpg', '/products/iron-ore-2.jpg'],
                'created_at' => now()->subDays(9),
                'updated_at' => now()->subDays(9),
            ],
            [
                'title' => 'Copper Ore - Rich Deposit',
                'description' => 'High-grade copper ore with excellent extraction potential. Located in mineral-rich zones.',
                'mineral_category_id' => $categories['Copper'] ?? null,
                'price' => 750000,
                'quantity' => 25,
                'unit' => 'ton',
                'location' => 'Zamfara State',
                'seller_id' => $johnAdebayo->id,
                'status' => Product::STATUS_ACTIVE,
                'views' => 87,
                'images' => ['/products/copper-ore-1.jpg', '/products/copper-ore-2.jpg'],
                'created_at' => now()->subDays(10),
                'updated_at' => now()->subDays(10),
            ],
            [
                'title' => 'Zinc Concentrate - Premium Quality',
                'description' => 'Zinc concentrate with 55% zinc content. Ideal for metallurgical processes and manufacturing.',
                'price' => 450000,
                'quantity' => 30,
                'unit' => 'ton',
                'location' => 'Ebonyi State',
                'seller_id' => $sarahOkafor->id,
                'status' => Product::STATUS_ACTIVE,
                'views' => 123,
                'images' => ['/products/zinc-concentrate-1.jpg', '/products/zinc-concentrate-2.jpg'],
                'created_at' => now()->subDays(11),
                'updated_at' => now()->subDays(11),
            ],
            [
                'title' => 'Lead Ore - High Concentration',
                'description' => 'Lead ore with 75% lead content. Suitable for battery manufacturing and industrial applications.',
                'price' => 380000,
                'quantity' => 20,
                'unit' => 'ton',
                'location' => 'Taraba State',
                'seller_id' => $michaelAdamu->id,
                'status' => Product::STATUS_ACTIVE,
                'views' => 67,
                'images' => ['/products/lead-ore-1.jpg', '/products/lead-ore-2.jpg'],
                'created_at' => now()->subDays(12),
                'updated_at' => now()->subDays(12),
            ],
            [
                'title' => 'Bauxite - Aluminum Ore',
                'description' => 'High-quality bauxite ore for aluminum production. Excellent alumina content for processing.',
                'price' => 120000,
                'quantity' => 100,
                'unit' => 'ton',
                'location' => 'Ogun State',
                'seller_id' => $fatimaBello->id,
                'status' => Product::STATUS_ACTIVE,
                'views' => 145,
                'images' => ['/products/bauxite-1.jpg', '/products/bauxite-2.jpg'],
                'created_at' => now()->subDays(13),
                'updated_at' => now()->subDays(13),
            ],
            [
                'title' => 'Uranium Ore - Low Grade',
                'description' => 'Uranium ore deposit with potential for nuclear energy applications. Requires specialized processing.',
                'price' => 2500000,
                'quantity' => 5,
                'unit' => 'ton',
                'location' => 'Katsina State',
                'seller_id' => $emekaNwachukwu->id,
                'status' => Product::STATUS_PENDING,
                'views' => 23,
                'images' => ['/products/uranium-ore-1.jpg', '/products/uranium-ore-2.jpg'],
                'created_at' => now()->subDays(14),
                'updated_at' => now()->subDays(14),
            ],
        ];

        foreach ($products as $productData) {
            Product::create($productData);
        }
    }
}
