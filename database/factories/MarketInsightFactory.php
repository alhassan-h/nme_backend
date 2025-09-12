<?php

namespace Database\Factories;

use App\Models\MarketInsight;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class MarketInsightFactory extends Factory
{
    protected $model = MarketInsight::class;

    public function definition(): array
    {
        return [
            'user_id' => User::inRandomOrder()->first()->id,
            'title' => $this->faker->sentence(),
            'content' => $this->faker->paragraphs(3, true),
            'featured' => $this->faker->boolean(20),
            'created_at' => $this->faker->dateTimeBetween('-6 months'),
        ];
    }
}
