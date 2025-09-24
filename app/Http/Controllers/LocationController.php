<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLocationRequest;
use App\Http\Requests\UpdateLocationRequest;
use App\Services\LocationService;
use Illuminate\Http\JsonResponse;

class LocationController extends Controller
{
    protected $locationService;

    public function __construct(LocationService $locationService)
    {
        $this->locationService = $locationService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $locations = $this->locationService->getActiveLocations();
        return response()->json($locations);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreLocationRequest $request): JsonResponse
    {
        $location = $this->locationService->createLocation($request->validated());
        return response()->json($location, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): JsonResponse
    {
        $location = $this->locationService->getLocation($id);
        return response()->json($location);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateLocationRequest $request, int $id): JsonResponse
    {
        $location = $this->locationService->updateLocation($id, $request->validated());
        return response()->json($location);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        $this->locationService->deleteLocation($id);
        return response()->json(['message' => 'Location deleted successfully']);
    }

    /**
     * Get locations for admin with pagination
     */
    public function adminIndex(): JsonResponse
    {
        $filters = request()->only(['search', 'per_page', 'page']);
        $locations = $this->locationService->getLocations($filters);
        return response()->json($locations);
    }

    /**
     * Toggle location active status
     */
    public function toggleActive(int $id): JsonResponse
    {
        $location = $this->locationService->toggleActive($id);
        return response()->json($location);
    }
}
