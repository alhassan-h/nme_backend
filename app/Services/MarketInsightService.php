<?php

namespace App\Services;

use App\Events\InsightCreated;
use App\Models\MarketInsight;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class MarketInsightService
{
    public function paginateInsights(int $perPage, int $page): LengthAwarePaginator
    {
        return MarketInsight::orderByDesc('created_at')->paginate($perPage, ['*'], 'page', $page);
    }

    public function getById(int $id): ?MarketInsight
    {
        return MarketInsight::find($id);
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

    public function delete(MarketInsight $insight): void
    {
        $insight->delete();
    }
}
