<?php

namespace Database\Factories;

use App\Models\GalleryImage;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class GalleryImageFactory extends Factory
{
    protected $model = GalleryImage::class;

    public function definition(): array
    {
        $categories = ['Mining', 'Products', 'Market', 'Events'];

        $imageUrls = [
            'images/gallery/sample1.jpg',
            'images/gallery/sample2.jpg',
            'images/gallery/sample3.jpg',
            'images/gallery/sample4.jpg',
        ];

        return [
            'file_path' => $this->faker->randomElement($imageUrls),
            'category' => $this->faker->randomElement($categories),
            'location_id' => $this->faker->numberBetween(1, 37),
            'description' => $this->faker->sentence(),
            'views' => $this->faker->numberBetween(0, 1000),
            'user_id' => User::factory()->create()->id,
            'created_at' => $this->faker->dateTimeBetween('-4 months'),
        ];
    }
}
