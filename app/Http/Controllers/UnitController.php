<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUnitRequest;
use App\Http\Requests\UpdateUnitRequest;
use App\Services\UnitService;
use Illuminate\Http\JsonResponse;

class UnitController extends Controller
{
    protected $unitService;

    public function __construct(UnitService $unitService)
    {
        $this->unitService = $unitService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $units = $this->unitService->getActiveUnits();
        return response()->json($units);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUnitRequest $request): JsonResponse
    {
        $unit = $this->unitService->createUnit($request->validated());
        return response()->json($unit, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): JsonResponse
    {
        $unit = $this->unitService->getUnit($id);
        return response()->json($unit);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUnitRequest $request, int $id): JsonResponse
    {
        $unit = $this->unitService->updateUnit($id, $request->validated());
        return response()->json($unit);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        $this->unitService->deleteUnit($id);
        return response()->json(['message' => 'Unit deleted successfully']);
    }

    /**
     * Get units for admin with pagination
     */
    public function adminIndex(): JsonResponse
    {
        $filters = request()->only(['search', 'per_page', 'page']);
        $units = $this->unitService->getUnits($filters);
        return response()->json($units);
    }

    /**
     * Toggle unit active status
     */
    public function toggleActive(int $id): JsonResponse
    {
        $unit = $this->unitService->toggleActive($id);
        return response()->json($unit);
    }
}
