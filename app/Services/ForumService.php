<?php

namespace App\Services;

use App\Events\ForumPostCreated;
use App\Models\ForumPost;
use App\Models\ForumReply;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ForumService
{
    public function getPosts(?string $category, int $perPage, int $page): LengthAwarePaginator
    {
        $query = ForumPost::with('author')->orderByDesc('created_at');

        if ($category) {
            $query->where('category', $category);
        }

        return $query->paginate($perPage, ['*'], 'page', $page);
    }

    public function createPost(array $attributes, User $user): ForumPost
    {
        $post = new ForumPost();
        $post->title = $attributes['title'];
        $post->content = $attributes['content'];
        $post->category = $attributes['category'];
        $post->tags = $attributes['tags'] ?? [];
        $post->user_id = $user->id;
        $post->save();

        ForumPostCreated::dispatch($post);

        return $post->load('author');
    }

    /**
     * Get replies for a forum post
     * @param ForumPost $post
     * @return ForumReply[]
     */
    public function getReplies(ForumPost $post): array
    {
        // Load all top-level replies with nested replies eager loaded
        return $post->replies()->with('user', 'replies')->get()->toArray();
    }

    public function createReply(ForumPost $post, array $attributes, User $user): ForumReply
    {
        $reply = new ForumReply();
        $reply->content = $attributes['content'];
        $reply->parent_id = $attributes['parent_id'] ?? null;
        $reply->post_id = $post->id;
        $reply->user_id = $user->id;
        $reply->save();

        return $reply->load('user', 'parent');
    }
}
