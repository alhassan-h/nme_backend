<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UserDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_get_own_products()
    {
        $user = User::factory()->create();
        Product::factory()->count(3)->create(['seller_id' => $user->id]);
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/user/products');

        $response->assertStatus(200)
            ->assertJsonStructure(['data', 'links', 'meta']);
    }

    public function test_authenticated_user_can_get_favorites()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();
        $user->favoriteProducts()->attach($product->id);
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/user/favorites');

        $response->assertStatus(200)
            ->assertJsonStructure(['data', 'links', 'meta']);
    }
}
