<?php

namespace Tests\Feature;

use App\Models\ForumPost;
use App\Models\ForumReply;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ForumTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_forum_posts()
    {
        ForumPost::factory()->count(5)->create();

        $response = $this->getJson('/api/forum/posts');

        $response->assertStatus(200)
            ->assertJsonStructure(['data', 'links', 'meta']);
    }

    public function test_get_single_forum_post()
    {
        $post = ForumPost::factory()->create();

        $response = $this->getJson("/api/forum/posts/{$post->id}");

        $response->assertStatus(200)
            ->assertJson(['id' => $post->id]);
    }

    public function test_create_forum_post_requires_auth()
    {
        $response = $this->postJson('/api/forum/posts', []);

        $response->assertStatus(401);
    }

    public function test_authenticated_user_can_create_forum_post()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $data = [
            'title' => 'Test Post',
            'content' => 'Content here',
            'category' => 'General',
            'tags' => ['tag1', 'tag2'],
        ];

        $response = $this->postJson('/api/forum/posts', $data);

        $response->assertStatus(201)
            ->assertJson(['title' => 'Test Post']);
    }

    public function test_get_replies_for_post()
    {
        $post = ForumPost::factory()->create();
        ForumReply::factory()->count(3)->create(['post_id' => $post->id]);

        $response = $this->getJson("/api/forum/posts/{$post->id}/replies");

        $response->assertStatus(200)
            ->assertJsonCount(3);
    }

    public function test_create_reply_requires_auth()
    {
        $post = ForumPost::factory()->create();

        $response = $this->postJson("/api/forum/posts/{$post->id}/replies", [
            'content' => 'A reply',
        ]);

        $response->assertStatus(401);
    }

    public function test_authenticated_user_can_create_reply()
    {
        $post = ForumPost::factory()->create();
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->postJson("/api/forum/posts/{$post->id}/replies", [
            'content' => 'A reply',
        ]);

        $response->assertStatus(201)
            ->assertJson(['content' => 'A reply']);
    }
}
