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
        $query = ForumPost::with('author')
            ->select(['forum_posts.*'])
            ->selectRaw('(SELECT COUNT(*) FROM forum_replies WHERE forum_replies.post_id = forum_posts.id) as replies')
            ->selectRaw('views as views')
            ->orderByDesc('created_at');

        if ($category) {
            $query->where('category', $category);
        }

        $paginated = $query->paginate($perPage, ['*'], 'page', $page);

        // Transform the data to match frontend expectations
        $paginated->getCollection()->transform(function ($post) {
            return [
                'id' => $post->id,
                'title' => $post->title,
                'content' => $post->content,
                'category' => $post->category,
                'tags' => $post->tags,
                'user_id' => $post->user_id,
                'views' => $post->views,
                'replies' => $post->replies,
                'author' => $post->author,
                'created_at' => $post->created_at,
                'updated_at' => $post->updated_at,
                'lastActivity' => $post->updated_at->diffForHumans(),
                'isPinned' => false, // You can add a pinned field later if needed
                'avatar' => $post->author ? "/avatars/{$post->author->name}.jpg" : null,
            ];
        });

        return $paginated;
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

    public function getStats(): array
    {
        return [
            'total_posts' => ForumPost::count(),
            'active_members' => User::whereHas('forumPosts')->count(),
            'posts_this_week' => ForumPost::where('created_at', '>=', now()->startOfWeek())->count(),
        ];
    }

    public function getCategories(): array
    {
        return ForumPost::select('category')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('category')
            ->orderByDesc('count')
            ->get()
            ->map(function ($item) {
                return [
                    'name' => $item->category,
                    'count' => $item->count,
                ];
            })
            ->toArray();
    }

    public function getTopContributors(int $limit = 10): array
    {
        return User::select('users.id', 'users.name')
            ->selectRaw('COUNT(forum_posts.id) as posts_count')
            ->join('forum_posts', 'users.id', '=', 'forum_posts.user_id')
            ->groupBy('users.id', 'users.name')
            ->orderByDesc('posts_count')
            ->limit($limit)
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'posts' => $user->posts_count,
                ];
            })
            ->toArray();
    }

    public function incrementViewCount(ForumPost $post): void
    {
        $post->increment('views');
    }
}
