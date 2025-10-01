<?php

namespace Database\Seeders;

use App\Models\GalleryImage;
use App\Models\User;
use App\Services\CloudinaryService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class GallerySeeder extends Seeder
{
    public function run(): void
    {
        // Get users from database (ensuring they exist from UserSeeder)
        $johnAdebayo = User::where('email', 'john.adebayo@nme.com')->first();
        $sarahOkafor = User::where('email', 'sarah.okafor@nme.com')->first();
        $michaelAdamu = User::where('email', 'michael.adamu@nme.com')->first();
        $fatimaBello = User::where('email', 'fatima.bello@nme.com')->first();
        $emekaNwachukwu = User::where('email', 'emeka.nwachukwu@nme.com')->first();
        $aminaYusuf = User::where('email', 'amina.yusuf@nme.com')->first();

        // Ensure required users exist
        if (!$johnAdebayo || !$sarahOkafor || !$michaelAdamu || !$fatimaBello || !$emekaNwachukwu || !$aminaYusuf) {
            throw new \Exception('Required users not found. Please run UserSeeder first.');
        }

        // Get locations from database (ensuring they exist from LocationSeeder), pluck their IDs and names
        $locations = \App\Models\Location::pluck('id', 'name')->toArray();

        // Sample gallery images data with explicit data creation
        $galleryImages = [
            [
                'filename' => 'gold-nuggets-from-zamfara.png',
                'category' => 'Gold',
                'location_id' => $locations['Zamfara State'],
                'description' => 'Gold Nuggets from Zamfara',
                'views' => 1234,
                'user_id' => $johnAdebayo->id,
                'status' => 'published',
                'created_at' => now()->subDays(5),
                'updated_at' => now()->subDays(5),
            ],
            [
                'filename' => 'limestone-quarry-operations.png',
                'category' => 'Limestone',
                'location_id' => $locations['Ogun State'],
                'description' => 'Limestone Quarry Operations',
                'views' => 856,
                'user_id' => $sarahOkafor->id,
                'created_at' => now()->subDays(6),
                'updated_at' => now()->subDays(6),
            ],
            [
                'filename' => 'tin-ore-samples.png',
                'category' => 'Tin',
                'location_id' => $locations['Plateau State'],
                'description' => 'Tin Ore Samples',
                'views' => 642,
                'user_id' => $michaelAdamu->id,
                'created_at' => now()->subDays(7),
                'updated_at' => now()->subDays(7),
            ],
            [
                'filename' => 'coal-mining-site.png',
                'category' => 'Coal',
                'location_id' => $locations['Enugu State'],
                'description' => 'Coal Mining Site',
                'views' => 789,
                'user_id' => $emekaNwachukwu->id,
                'created_at' => now()->subDays(8),
                'updated_at' => now()->subDays(8),
            ],
            [
                'filename' => 'iron-ore-deposits.png',
                'category' => 'Iron Ore',
                'location_id' => $locations['Kogi State'],
                'description' => 'Iron Ore Deposits',
                'views' => 923,
                'user_id' => $fatimaBello->id,
                'created_at' => now()->subDays(9),
                'updated_at' => now()->subDays(9),
            ],
            [
                'filename' => 'barite-crystal-formation.png',
                'category' => 'Barite',
                'location_id' => $locations['Cross River State'],
                'description' => 'Barite Crystal Formation',
                'views' => 567,
                'user_id' => $aminaYusuf->id,
                'status' => 'published',
                'created_at' => now()->subDays(10),
                'updated_at' => now()->subDays(10),
            ],
            [
                'filename' => 'mining-operations-in-kaduna.png',
                'category' => 'Mining',
                'location_id' => $locations['Kaduna State'],
                'description' => 'Mining Operations in Kaduna',
                'views' => 345,
                'user_id' => $johnAdebayo->id,
                'status' => 'published',
                'created_at' => now()->subDays(11),
                'updated_at' => now()->subDays(11),
            ],
            [
                'filename' => 'mineral-products-display.png',
                'category' => 'Products',
                'location_id' => $locations['Lagos State'],
                'description' => 'Mineral Products Display',
                'views' => 678,
                'user_id' => $sarahOkafor->id,
                'status' => 'published',
                'created_at' => now()->subDays(12),
                'updated_at' => now()->subDays(12),
            ],
            [
                'filename' => 'mineral-market-activities.png',
                'category' => 'Market',
                'location_id' => $locations['FCT Abuja'],
                'description' => 'Mineral Market Activities',
                'views' => 432,
                'user_id' => $michaelAdamu->id,
                'created_at' => now()->subDays(13),
                'updated_at' => now()->subDays(13),
            ],
            [
                'filename' => 'mining-industry-conference.png',
                'category' => 'Events',
                'location_id' => $locations['Rivers State'],
                'description' => 'Mining Industry Conference',
                'views' => 789,
                'user_id' => $fatimaBello->id,
                'created_at' => now()->subDays(14),
                'updated_at' => now()->subDays(14),
            ],
        ];

        // Create gallery images
        foreach ($galleryImages as $imageData) {
            $filename = $imageData['filename'];
            unset($imageData['filename']);
            $imageData['file_path'] = $this->uploadGalleryImage($filename);
            GalleryImage::create($imageData);
        }

        // Add some sample likes to demonstrate the relationship
        $galleryImages = GalleryImage::all();
        $users = User::all();

        // Add likes to some images (for demonstration)
        if ($galleryImages->count() > 0 && $users->count() > 0) {
            // Add likes to first few images
            $sampleImages = $galleryImages->take(5);
            $sampleUsers = $users->take(3);

            foreach ($sampleImages as $image) {
                foreach ($sampleUsers as $user) {
                    // Add like with 50% probability
                    if (rand(0, 1)) {
                        $image->likes()->create([
                            'user_id' => $user->id,
                            'created_at' => now()->subDays(rand(1, 30)),
                        ]);
                    }
                }
            }
        }
    }

    private function uploadGalleryImage($filename)
    {
        $cloudinary = app(CloudinaryService::class);
        try {
            $imagePath = resource_path('defaults/images/gallery/' . $filename);
            if (file_exists($imagePath)) {
                $result = $cloudinary->upload($imagePath, ['folder' => 'gallery']);
                return $result['secure_url'];
            } else {
                Log::warning('Gallery image not found: ' . $imagePath);
                return null;
            }
        } catch (\Exception $e) {
            Log::error('Failed to upload gallery image ' . $filename . ': ' . $e->getMessage());
            return null;
        }
    }
}
