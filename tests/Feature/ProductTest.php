<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_list_products_with_filters()
    {
        Product::factory()->count(10)->create();

        $response = $this->getJson('/api/products?category=Gold');

        $response->assertStatus(200)
            ->assertJsonStructure(['data', 'links', 'meta']);
    }

    public function test_show_single_product()
    {
        $product = Product::factory()->create();

        $response = $this->getJson("/api/products/{$product->id}");

        $response->assertStatus(200)
            ->assertJson(['id' => $product->id]);
    }

    public function test_create_product_requires_authentication()
    {
        $response = $this->postJson('/api/products', []);

        $response->assertStatus(401);
    }

    public function test_authenticated_user_can_create_product()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $file = UploadedFile::fake()->image('product.jpg');

        $data = [
            'title' => 'New Product',
            'description' => 'Description here',
            'category' => 'Gold',
            'price' => 1000,
            'quantity' => 10,
            'unit' => 'kg',
            'location' => 'Lagos',
            'images' => [$file],
        ];

        $response = $this->postJson('/api/products', $data);

        $response->assertStatus(201)
            ->assertJsonStructure(['id', 'title', 'seller']);

        $this->assertDatabaseHas('products', ['title' => 'New Product']);
    }

    public function test_authenticated_user_can_update_product()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['seller_id' => $user->id]);
        Sanctum::actingAs($user);

        $updateData = [
            'title' => 'Updated Product Title',
        ];

        $response = $this->putJson("/api/products/{$product->id}", $updateData);

        $response->assertStatus(200)
            ->assertJsonFragment(['title' => 'Updated Product Title']);
    }

    public function test_authenticated_user_can_delete_product()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['seller_id' => $user->id]);
        Sanctum::actingAs($user);

        $response = $this->deleteJson("/api/products/{$product->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }

    public function test_toggle_favorite_endpoint()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->postJson("/api/products/{$product->id}/favorite");

        $response->assertStatus(204);
    }

    public function test_increment_view_endpoint()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->postJson("/api/products/{$product->id}/view");

        $response->assertStatus(204);
    }
}
