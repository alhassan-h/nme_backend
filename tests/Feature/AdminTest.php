<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_non_admin_cannot_access_admin_routes()
    {
        $user = User::factory()->create(['role' => 'user']);
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/admin/dashboard/stats');
        $response->assertStatus(403);
    }

    public function test_admin_can_get_dashboard_stats()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Sanctum::actingAs($admin);

        $response = $this->getJson('/api/admin/dashboard/stats');
        $response->assertStatus(200)->assertJsonStructure(['total_users', 'active_users', 'total_products', 'pending_products']);
    }

    public function test_admin_can_update_user_status()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['role' => 'user', 'status' => 'active']);
        Sanctum::actingAs($admin);

        $response = $this->putJson("/api/admin/users/{$user->id}/status", ['status' => 'inactive']);
        $response->assertStatus(200);
        $this->assertDatabaseHas('users', ['id' => $user->id, 'status' => 'inactive']);
    }

    public function test_admin_can_approve_product()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $product = Product::factory()->create(['status' => Product::STATUS_PENDING]);
        Sanctum::actingAs($admin);

        $response = $this->putJson("/api/admin/products/{$product->id}/approve");
        $response->assertStatus(200);
        $this->assertDatabaseHas('products', ['id' => $product->id, 'status' => Product::STATUS_ACTIVE]);
    }
}
