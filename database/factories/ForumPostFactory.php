<?php

namespace Database\Factories;

use App\Models\ForumPost;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ForumPostFactory extends Factory
{
    protected $model = ForumPost::class;

    public function definition(): array
    {
        $user = User::inRandomOrder()->first();
        $categories = ['Mining', 'Products', 'Market', 'Regulations', 'General'];

        return [
            'title' => $this->faker->sentence(),
            'content' => $this->faker->paragraphs(2, true),
            'category' => $this->faker->randomElement($categories),
            'tags' => $this->faker->words(3),
            'user_id' => $user->id,
            'views' => $this->faker->numberBetween(0, 1000),
            'replies_count' => $this->faker->numberBetween(0, 50),
            'created_at' => $this->faker->dateTimeBetween('-3 months'),
        ];
    }
}
