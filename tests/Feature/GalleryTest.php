<?php

namespace Tests\Feature;

use App\Models\GalleryImage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class GalleryTest extends TestCase
{
    use RefreshDatabase;

    public function test_list_gallery_images()
    {
        GalleryImage::factory()->count(5)->create();

        $response = $this->getJson('/api/gallery');

        $response->assertStatus(200)
            ->assertJsonStructure(['data', 'links', 'meta']);
    }

    public function test_authenticated_user_can_upload_image()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $file = UploadedFile::fake()->image('sample.jpg');

        $data = [
            'image' => $file,
            'category' => 'Events',
            'location' => 'Abuja',
            'description' => 'Sample image description',
        ];

        $response = $this->postJson('/api/gallery', $data);

        $response->assertStatus(201)
            ->assertJsonStructure(['id', 'file_path', 'uploader']);
    }

    public function test_toggle_like_requires_auth()
    {
        $image = GalleryImage::factory()->create();

        $response = $this->postJson("/api/gallery/{$image->id}/like");

        $response->assertStatus(401);
    }

    public function test_authenticated_user_can_toggle_like()
    {
        $user = User::factory()->create();
        $image = GalleryImage::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->postJson("/api/gallery/{$image->id}/like");

        $response->assertStatus(204);
    }

    public function test_increment_view_increments_views()
    {
        $user = User::factory()->create();
        $image = GalleryImage::factory()->create();
        Sanctum::actingAs($user);

        $viewsBefore = $image->views;

        $response = $this->postJson("/api/gallery/{$image->id}/view");

        $response->assertStatus(204);

        $image->refresh();

        $this->assertEquals($viewsBefore + 1, $image->views);
    }
}
