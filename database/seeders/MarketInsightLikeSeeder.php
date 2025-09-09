<?php

namespace Database\Seeders;

use App\Models\MarketInsightLike;
use App\Models\MarketInsight;
use App\Models\User;
use Illuminate\Database\Seeder;

class MarketInsightLikeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $insights = MarketInsight::all();
        if ($insights->isEmpty()) {
            return;
        }

        $users = User::all();
        if ($users->isEmpty()) {
            return;
        }

        $totalLikes = 0;
        $maxLikes = 500;

        foreach ($insights as $insight) {
            if ($totalLikes >= $maxLikes) {
                break;
            }

            $authorId = $insight->user_id;
            $availableUsers = $users->where('id', '!=', $authorId);

            if ($availableUsers->isEmpty()) {
                continue;
            }

            $numLikes = rand(5, 20);
            $numAvailable = $availableUsers->count();
            $actualLikes = min($numLikes, $numAvailable, $maxLikes - $totalLikes);

            $selectedUsers = $availableUsers->random($actualLikes);

            foreach ($selectedUsers as $user) {
                MarketInsightLike::create([
                    'market_insight_id' => $insight->id,
                    'user_id' => $user->id,
                    'created_at' => now()->subMinutes(rand(1, 1440)),
                    'updated_at' => now()->subMinutes(rand(1, 1440)),
                ]);

                $totalLikes++;
            }
        }
    }
}
