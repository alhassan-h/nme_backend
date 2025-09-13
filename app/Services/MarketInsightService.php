<?php

namespace App\Services;

use App\Events\InsightCreated;
use App\Models\MarketInsightLike;
use App\Models\MarketInsight;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class MarketInsightService
{
    public function paginateInsights(array $filters = [], ?int $userId = null, int $perPage = 15, int $page = 1): LengthAwarePaginator
    {
        $query = MarketInsight::with(['user', 'likes', 'category']);

        if (!empty($filters['category'])) {
            // Filter by category name through relationship
            $query->whereHas('category', function ($q) use ($filters) {
                $q->where('name', $filters['category']);
            });
        }

        if (isset($filters['exclude'])) {
            $query->whereNotIn('id', (array) $filters['exclude']);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%")
                  ->orWhere('summary', 'like', "%{$search}%");
            });
        }

        return $query->orderByDesc('created_at')->paginate($perPage, ['*'], 'page', $page);
    }

    public function getById(int $id): ?MarketInsight
    {
        return MarketInsight::with('user')->find($id);
    }

    public function getRelated(int $id, int $limit = 5): Collection
    {
        $insight = $this->getById($id);
        if (!$insight) {
            return collect();
        }

        return MarketInsight::where('category_id', $insight->category_id)
            ->where('id', '!=', $id)
            ->with(['user', 'category'])
            ->latest()
            ->limit($limit)
            ->get();
    }

    public function create(array $attributes): MarketInsight
    {
        $insight = MarketInsight::create($attributes);
        InsightCreated::dispatch($insight);

        return $insight;
    }

    public function update(MarketInsight $insight, array $attributes): MarketInsight
    {
        $insight->update($attributes);

        return $insight;
    }

    public function toggleLike(int $insightId, int $userId): array
    {
        $like = MarketInsightLike::where('market_insight_id', $insightId)
            ->where('user_id', $userId)
            ->first();

        if ($like) {
            $like->delete();
            $isLiked = false;
        } else {
            MarketInsightLike::create([
                'market_insight_id' => $insightId,
                'user_id' => $userId,
            ]);
            $isLiked = true;
        }

        $insight = MarketInsight::find($insightId);
        $likesCount = $insight ? $insight->likes()->count() : 0;

        return [
            'is_liked' => $isLiked,
            'likes_count' => $likesCount,
        ];
    }

    public function delete(MarketInsight $insight): void
    {
        $insight->delete();
    }
}
