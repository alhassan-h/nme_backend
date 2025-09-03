<?php

namespace Database\Factories;

use App\Models\ForumPost;
use App\Models\ForumReply;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ForumReplyFactory extends Factory
{
    protected $model = ForumReply::class;

    public function definition(): array
    {
        // get a random existing user
        $user = User::inRandomOrder()->first();

        return [
            'content' => $this->faker->sentence(),
            'parent_id' => null, // We'll handle nested replies separately if needed.
            // 'post_id' => ForumPost::factory()->create()->id,
            'user_id' => $user->id,
            'created_at' => $this->faker->dateTimeBetween('-2 months'),
        ];
    }
}
