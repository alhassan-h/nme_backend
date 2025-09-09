<?php

namespace App\Http\Controllers;

use App\Http\Requests\MarketInsightRequest;
use App\Models\MarketInsight;
use App\Services\MarketInsightService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MarketInsightController extends Controller
{
    protected MarketInsightService $insightService;

    public function __construct(MarketInsightService $insightService)
    {
        $this->insightService = $insightService;
    }

    public function index(Request $request): JsonResponse
    {
        $filters = [
            'category' => $request->get('category'),
            'exclude' => $request->get('exclude'),
            'search' => $request->get('search'),
        ];
        $perPage = (int) $request->get('per_page', 15);
        $page = (int) $request->get('page', 1);
        $userId = auth()->check() ? auth()->id() : null;

        $paginated = $this->insightService->paginateInsights($filters, $userId, $perPage, $page);

        foreach ($paginated->items() as $insight) {
            $insight->likes_count = $insight->likes->count();
            $insight->is_liked = $userId && $insight->likes->contains('user_id', $userId);
        }

        return response()->json($paginated);
    }

    public function show(int $id): JsonResponse
    {
        $insight = $this->insightService->getById($id);

        if (!$insight) {
            return response()->json(['message' => 'Market Insight not found'], Response::HTTP_NOT_FOUND);
        }

        $insight->load('likes');
        $userId = auth()->check() ? auth()->id() : null;
        $insight->likes_count = $insight->likes->count();
        $insight->is_liked = $userId && $insight->likes->contains('user_id', $userId);
        $insight->related = $this->insightService->getRelated($id, 5);
        $insight->author = $insight->getAuthorAttribute();

        return response()->json($insight);
    }

    public function store(MarketInsightRequest $request): JsonResponse
    {
        $insight = $this->insightService->create($request->validated());

        return response()->json($insight, Response::HTTP_CREATED);
    }

    public function update(MarketInsightRequest $request, MarketInsight $insight): JsonResponse
    {
        $updated = $this->insightService->update($insight, $request->validated());

        return response()->json($updated);
    }

    public function destroy(MarketInsight $insight): JsonResponse
    {
        $this->insightService->delete($insight);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    public function toggleLike(int $id): JsonResponse
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], Response::HTTP_UNAUTHORIZED);
        }

        $result = $this->insightService->toggleLike($id, $user->id);

        return response()->json($result);
    }
}
