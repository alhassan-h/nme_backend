<?php

namespace Tests\Feature;

use App\Models\MarketInsight;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class MarketInsightTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_fetch_paginated_market_insights()
    {
        MarketInsight::factory()->count(10)->create();

        $response = $this->getJson('/api/insights');

        $response->assertStatus(200)
            ->assertJsonStructure(['data', 'links', 'meta']);
    }

    public function test_can_fetch_single_insight()
    {
        $insight = MarketInsight::factory()->create();

        $response = $this->getJson("/api/insights/{$insight->id}");

        $response->assertStatus(200)
            ->assertJson(['id' => $insight->id]);
    }

    public function test_non_admin_cannot_create_insight()
    {
        $user = User::factory()->create(['role' => 'user']);
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/insights', [
            'title' => 'New Insight',
            'content' => 'Insight content',
            'category' => 'Market Trends',
        ]);

        $response->assertStatus(403);
    }

    public function test_admin_can_create_insight()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Sanctum::actingAs($admin);

        $response = $this->postJson('/api/insights', [
            'title' => 'New Insight',
            'content' => 'Insight content',
            'category' => 'Market Trends',
        ]);

        $response->assertStatus(201)
            ->assertJson(['title' => 'New Insight']);
    }
}
