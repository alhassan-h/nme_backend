<?php

namespace Database\Seeders;

use App\Models\GalleryImage;
use App\Models\User;
use Illuminate\Database\Seeder;

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
                'file_path' => 'images/gallery/placeholder-ke6hd.png',
                'category' => 'Gold',
                'location_id' => $locations['Zamfara State'],
                'description' => 'Gold Nuggets from Zamfara',
                'views' => 1234,
                'user_id' => $johnAdebayo->id,
                'created_at' => now()->subDays(5),
                'updated_at' => now()->subDays(5),
            ],
            [
                'file_path' => 'images/gallery/placeholder-ke6hd.png',
                'category' => 'Limestone',
                'location_id' => $locations['Ogun State'],
                'description' => 'Limestone Quarry Operations',
                'views' => 856,
                'user_id' => $sarahOkafor->id,
                'created_at' => now()->subDays(6),
                'updated_at' => now()->subDays(6),
            ],
            [
                'file_path' => 'images/gallery/tin-ore-samples.png',
                'category' => 'Tin',
                'location_id' => $locations['Plateau State'],
                'description' => 'Tin Ore Samples',
                'views' => 642,
                'user_id' => $michaelAdamu->id,
                'created_at' => now()->subDays(7),
                'updated_at' => now()->subDays(7),
            ],
            [
                'file_path' => 'images/gallery/placeholder-ke6hd.png',
                'category' => 'Coal',
                'location_id' => $locations['Enugu State'],
                'description' => 'Coal Mining Site',
                'views' => 789,
                'user_id' => $emekaNwachukwu->id,
                'created_at' => now()->subDays(8),
                'updated_at' => now()->subDays(8),
            ],
            [
                'file_path' => 'images/gallery/iron-ore-deposits.png',
                'category' => 'Iron Ore',
                'location_id' => $locations['Kogi State'],
                'description' => 'Iron Ore Deposits',
                'views' => 923,
                'user_id' => $fatimaBello->id,
                'created_at' => now()->subDays(9),
                'updated_at' => now()->subDays(9),
            ],
            [
                'file_path' => 'images/gallery/barite-crystal.png',
                'category' => 'Barite',
                'location_id' => $locations['Cross River State'],
                'description' => 'Barite Crystal Formation',
                'views' => 567,
                'user_id' => $aminaYusuf->id,
                'created_at' => now()->subDays(10),
                'updated_at' => now()->subDays(10),
            ],
            [
                'file_path' => 'images/gallery/placeholder-ke6hd.png',
                'category' => 'Mining',
                'location_id' => $locations['Kaduna State'],
                'description' => 'Mining Operations in Kaduna',
                'views' => 345,
                'user_id' => $johnAdebayo->id,
                'created_at' => now()->subDays(11),
                'updated_at' => now()->subDays(11),
            ],
            [
                'file_path' => 'images/gallery/placeholder-ke6hd.png',
                'category' => 'Products',
                'location_id' => $locations['Lagos State'],
                'description' => 'Mineral Products Display',
                'views' => 678,
                'user_id' => $sarahOkafor->id,
                'created_at' => now()->subDays(12),
                'updated_at' => now()->subDays(12),
            ],
            [
                'file_path' => 'images/gallery/placeholder-ke6hd.png',
                'category' => 'Market',
                'location_id' => $locations['FCT Abuja'],
                'description' => 'Mineral Market Activities',
                'views' => 432,
                'user_id' => $michaelAdamu->id,
                'created_at' => now()->subDays(13),
                'updated_at' => now()->subDays(13),
            ],
            [
                'file_path' => 'images/gallery/placeholder-ke6hd.png',
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
}
