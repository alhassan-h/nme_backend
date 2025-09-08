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
        $user = User::factory();
        $product = Product::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->postJson("/api/products/{$product->id}/view");

        $response->assertStatus(204);
    }

    public function test_seller_can_mark_product_as_sold()
    {
        $user = User::factory()->seller()->create();
        $product = Product::factory()->create(['seller_id' => $user->id, 'status' => Product::STATUS_ACTIVE]);
        Sanctum::actingAs($user);

        $response = $this->putJson("/api/products/{$product->id}", ['status' => Product::STATUS_SOLD]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('products', ['id' => $product->id, 'status' => Product::STATUS_SOLD]);
    }

    public function test_seller_cannot_mark_product_as_active()
    {
        $user = User::factory()->seller()->create();
        $product = Product::factory()->create(['seller_id' => $user->id, 'status' => Product::STATUS_PENDING]);
        Sanctum::actingAs($user);

        $response = $this->putJson("/api/products/{$product->id}", ['status' => Product::STATUS_ACTIVE]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['status']);
    }

    public function test_non_owner_cannot_change_product_status()
    {
        $owner = User::factory()->seller()->create();
        $otherUser = User::factory()->seller()->create();
        $product = Product::factory()->create(['seller_id' => $owner->id, 'status' => Product::STATUS_ACTIVE]);
        Sanctum::actingAs($otherUser);

        $response = $this->putJson("/api/products/{$product->id}", ['status' => Product::STATUS_SOLD]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['status']);
    }

    public function test_admin_can_mark_product_as_active()
    {
        $admin = User::factory()->admin()->create();
        $product = Product::factory()->create(['status' => Product::STATUS_PENDING]);
        Sanctum::actingAs($admin);

        $response = $this->putJson("/api/products/{$product->id}", ['status' => Product::STATUS_ACTIVE]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('products', ['id' => $product->id, 'status' => Product::STATUS_ACTIVE]);
    }

    public function test_buyer_cannot_change_product_status()
    {
        $buyer = User::factory()->buyer()->create();
        $product = Product::factory()->create(['status' => Product::STATUS_ACTIVE]);
        Sanctum::actingAs($buyer);

        $response = $this->putJson("/api/products/{$product->id}", ['status' => Product::STATUS_SOLD]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['status']);
    }
}
