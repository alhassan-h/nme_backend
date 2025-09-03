<?php

namespace Database\Seeders;

use App\Models\ForumPost;
use App\Models\ForumReply;
use App\Models\User;
use Illuminate\Database\Seeder;

class ForumSeeder extends Seeder
{
    public function run(): void
    {
        // convert this array to seeder
        $data = [
            [
            'title' => "Best practices for gold mining in Zamfara State",
            'content' => "In Zamfara State, gold mining has become a significant economic activity. However, it is essential to adopt best practices to ensure the safety of miners and the environment. This includes proper training for miners, the use of protective equipment, and adherence to environmental regulations.",
            'category' => "Mining",
            ],
            [
            'title' => "Market trends for limestone in Q4 2024",
            'content' => "As the demand for limestone continues to grow, industry experts predict a shift in market dynamics by the end of 2024. Key factors include increased construction activities and a focus on sustainable mining practices.",
            'category' => "Market",
            ],
            [
            'title' => "Environmental compliance for mineral extraction",
            'content' => "As environmental concerns continue to rise, mining companies are urged to adopt more sustainable practices. This includes regular environmental impact assessments and community engagement.",
            'category' => "Regulations",
            ],
            [
            'title' => "New tin deposits discovered in Plateau State",
            'content' => "Recent geological surveys have indicated the presence of significant tin deposits in Plateau State, potentially boosting local mining activities. Government officials are optimistic about the economic impact of these findings.",
            'category' => "News",
            ],
        ];

        foreach ($data as $item) {
            $post = ForumPost::factory()->create([
                'title' => $item['title'],
                'content' => $item['content'],
                'category' => $item['category'],
            ]);

            $post->replies()->createMany(
                ForumReply::factory()->count(mt_rand(1, 3))->make([
                    'post_id' => $post->id,
                ])->toArray()
            );
        }

        ForumPost::factory()->count(5)->create()->each(function ($post) {
            $post->replies()->createMany(
                ForumReply::factory()->count(mt_rand(0, 5))->make([
                    'post_id' => $post->id,
                ])->toArray()
            );
        });
    }
}

