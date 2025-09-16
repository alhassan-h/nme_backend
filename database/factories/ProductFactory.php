<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $units = ['kg', 'ton', 'carat', 'pieces'];

        return [
            'title' => $this->faker->words(3, true),
            'description' => $this->faker->paragraph(),
            'mineral_category_id' => \App\Models\MineralCategory::inRandomOrder()->first()?->id ?? null,
            'price' => $this->faker->randomFloat(2, 10, 10000),
            'quantity' => $this->faker->numberBetween(1, 1000),
            'unit' => $this->faker->randomElement($units),
            'location' => $this->faker->city(),
            'images' => [
                'products/sample1.jpg',
                'products/sample2.jpg'
            ],
            'seller_id' => User::factory()->create()->id,
            'status' => Product::STATUS_ACTIVE,
            'views' => $this->faker->numberBetween(0, 1000),
            'created_at' => $this->faker->dateTimeBetween('-1 years'),
        ];
    }
}
