<?php

namespace Database\Seeders;

use App\Models\ForumPost;
use App\Models\ForumReply;
use Illuminate\Database\Seeder;

class ForumSeeder extends Seeder
{
    public function run(): void
    {
        ForumPost::factory()->count(10)->create()->each(function ($post) {
            ForumReply::factory()->count(3)->create([
                'post_id' => $post->id,
            ]);
        });
    }
}
