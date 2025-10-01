<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\User;
use App\Services\CloudinaryService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

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

        // Get units for mapping
        $units = \App\Models\Unit::pluck('id', 'name')->toArray();

        // Get locations for mapping
        $locations = \App\Models\Location::pluck('id', 'name')->toArray();

        // Sample products data
        $products = [
            [
                'title' => 'Premium Gold Nuggets - 2kg',
                'description' => 'High-grade gold nuggets from our Kaduna mining operations. 99.9% purity guaranteed with full certification.',
                'mineral_category_id' => $categories['Gold'] ?? null,
                'price' => 2850000,
                'quantity' => 2,
                'unit_id' => $units['kg'] ?? null,
                'location_id' => $locations['Kaduna State'] ?? null,
                'seller_id' => $johnAdebayo->id,
                'status' => Product::STATUS_ACTIVE,
                'views' => 234,
                'slug' => 'gold-nuggets',
                'created_at' => now()->subDays(5),
                'updated_at' => now()->subDays(5),
            ],
            [
                'title' => 'Limestone Blocks - Bulk Supply',
                'description' => 'High-quality limestone blocks suitable for construction. Available in various sizes with consistent quality.',
                'mineral_category_id' => $categories['Limestone'] ?? null,
                'price' => 45000,
                'quantity' => 500,
                'unit_id' => $units['ton'],
                'location_id' => $locations['Ogun State'],
                'seller_id' => $sarahOkafor->id,
                'status' => Product::STATUS_ACTIVE,
                'views' => 189,
                'slug' => 'limestone-blocks',
                'created_at' => now()->subDays(6),
                'updated_at' => now()->subDays(6),
            ],
            [
                'title' => 'Tin Ore Concentrate - 95% Purity',
                'description' => 'Premium tin ore concentrate from Jos plateau. Ideal for industrial applications with 95% purity level.',
                'mineral_category_id' => $categories['Tin'] ?? null,
                'price' => 1200000,
                'quantity' => 10,
                'unit_id' => $units['ton'],
                'location_id' => $locations['Plateau State'],
                'seller_id' => $michaelAdamu->id,
                'status' => Product::STATUS_PENDING,
                'views' => 0,
                'slug' => 'tin-ore',
                'created_at' => now()->subDays(7),
                'updated_at' => now()->subDays(7),
            ],
            [
                'title' => 'Coal - Industrial Grade',
                'description' => 'High-quality industrial grade coal suitable for manufacturing and power generation.',
                'mineral_category_id' => $categories['Coal'] ?? null,
                'price' => 85000,
                'quantity' => 200,
                'unit_id' => $units['ton'],
                'location_id' => $locations['Enugu State'],
                'seller_id' => $emekaNwachukwu->id,
                'status' => Product::STATUS_SOLD,
                'views' => 98,
                'slug' => 'coal-industrial',
                'created_at' => now()->subDays(8),
                'updated_at' => now()->subDays(8),
            ],
            [
                'title' => 'Iron Ore - High Grade',
                'description' => 'Premium iron ore with 65% iron content. Perfect for steel production and manufacturing.',
                'mineral_category_id' => $categories['Iron Ore'] ?? null,
                'price' => 95000,
                'quantity' => 150,
                'unit_id' => $units['ton'],
                'location_id' => $locations['Nasarawa State'],
                'seller_id' => $aminaYusuf->id,
                'status' => Product::STATUS_ACTIVE,
                'views' => 156,
                'slug' => 'iron-ore',
                'created_at' => now()->subDays(9),
                'updated_at' => now()->subDays(9),
            ],
            [
                'title' => 'Copper Ore - Rich Deposit',
                'description' => 'High-grade copper ore with excellent extraction potential. Located in mineral-rich zones.',
                'mineral_category_id' => $categories['Copper'] ?? null,
                'price' => 750000,
                'quantity' => 25,
                'unit_id' => $units['ton'],
                'location_id' => $locations['Zamfara State'],
                'seller_id' => $johnAdebayo->id,
                'status' => Product::STATUS_ACTIVE,
                'views' => 87,
                'slug' => 'copper-ore',
                'created_at' => now()->subDays(10),
                'updated_at' => now()->subDays(10),
            ],
            [
                'title' => 'Zinc Concentrate - Premium Quality',
                'description' => 'Zinc concentrate with 55% zinc content. Ideal for metallurgical processes and manufacturing.',
                'price' => 450000,
                'quantity' => 30,
                'unit_id' => $units['ton'],
                'location_id' => $locations['Ebonyi State'],
                'seller_id' => $sarahOkafor->id,
                'status' => Product::STATUS_ACTIVE,
                'views' => 123,
                'slug' => 'zinc-concentrate',
                'created_at' => now()->subDays(11),
                'updated_at' => now()->subDays(11),
            ],
            [
                'title' => 'Lead Ore - High Concentration',
                'description' => 'Lead ore with 75% lead content. Suitable for battery manufacturing and industrial applications.',
                'price' => 380000,
                'quantity' => 20,
                'unit_id' => $units['ton'],
                'location_id' => $locations['Taraba State'],
                'seller_id' => $michaelAdamu->id,
                'status' => Product::STATUS_ACTIVE,
                'views' => 67,
                'slug' => 'lead-ore',
                'created_at' => now()->subDays(12),
                'updated_at' => now()->subDays(12),
            ],
            [
                'title' => 'Bauxite - Aluminum Ore',
                'description' => 'High-quality bauxite ore for aluminum production. Excellent alumina content for processing.',
                'price' => 120000,
                'quantity' => 100,
                'unit_id' => $units['ton'],
                'location_id' => $locations['Ogun State'],
                'seller_id' => $fatimaBello->id,
                'status' => Product::STATUS_ACTIVE,
                'views' => 145,
                'slug' => 'bauxite',
                'created_at' => now()->subDays(13),
                'updated_at' => now()->subDays(13),
            ],
            [
                'title' => 'Uranium Ore - Low Grade',
                'description' => 'Uranium ore deposit with potential for nuclear energy applications. Requires specialized processing.',
                'price' => 2500000,
                'quantity' => 5,
                'unit_id' => $units['ton'],
                'location_id' => $locations['Katsina State'],
                'seller_id' => $emekaNwachukwu->id,
                'status' => Product::STATUS_PENDING,
                'views' => 23,
                'slug' => 'uranium-ore',
                'created_at' => now()->subDays(14),
                'updated_at' => now()->subDays(14),
            ],
        ];

        foreach ($products as $productData) {
            $images = [];
            if (isset($productData['slug'])) {
                $slug = $productData['slug'];
                // Find matching image files
                $imageFiles = glob(resource_path('defaults/images/products/' . $slug . '-*.jpg'));
                foreach ($imageFiles as $imagePath) {
                    $url = $this->uploadProductImage(basename($imagePath));
                    if ($url) {
                        $images[] = $url;
                    }
                }
            }
            $productData['images'] = $images;
            unset($productData['slug']);  // Remove slug before creating
            Product::create($productData);
        }
    }

    private function uploadProductImage($filename)
    {
        $cloudinary = app(CloudinaryService::class);
        try {
            $imagePath = resource_path('defaults/images/products/' . $filename);
            if (file_exists($imagePath)) {
                $result = $cloudinary->upload($imagePath, ['folder' => 'products']);
                return $result['secure_url'];
            } else {
                Log::warning('Product image not found: ' . $imagePath);
                return null;
            }
        } catch (\Exception $e) {
            Log::error('Failed to upload product image ' . $filename . ': ' . $e->getMessage());
            return null;
        }
    }
}
