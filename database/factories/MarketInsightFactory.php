<?php

namespace Database\Factories;

use App\Models\MarketInsight;
use Illuminate\Database\Eloquent\Factories\Factory;

class MarketInsightFactory extends Factory
{
    protected $model = MarketInsight::class;

    public function definition(): array
    {
        $categories = ['Mining', 'Market Trends', 'Regulations', 'Investments'];

        return [
            'title' => $this->faker->sentence(),
            'content' => $this->faker->paragraphs(3, true),
            'category' => $this->faker->randomElement($categories),
            'featured' => $this->faker->boolean(20),
            'created_at' => $this->faker->dateTimeBetween('-6 months'),
        ];
    }
}
