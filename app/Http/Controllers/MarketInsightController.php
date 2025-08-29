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
        $this->middleware('auth:sanctum')->only(['store', 'update', 'destroy']);
        $this->middleware('can:admin-access')->only(['store', 'update', 'destroy']);
    }

    public function index(Request $request): JsonResponse
    {
        $paginated = $this->insightService->paginateInsights($request->get('per_page', 15), $request->get('page', 1));

        return response()->json($paginated);
    }

    public function show(int $id): JsonResponse
    {
        $insight = $this->insightService->getById($id);

        if (!$insight) {
            return response()->json(['message' => 'Market Insight not found'], Response::HTTP_NOT_FOUND);
        }

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
}
