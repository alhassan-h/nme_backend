<?php

namespace App\Services;

use App\Models\Location;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class LocationService
{
    /**
     * Get all locations with pagination for admin
     */
    public function getLocations(array $filters = []): LengthAwarePaginator
    {
        $query = Location::query();

        if (!empty($filters['search'])) {
            $query->where('name', 'like', '%' . $filters['search'] . '%');
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        $perPage = $filters['per_page'] ?? 15;
        $page = $filters['page'] ?? 1;

        return $query->withCount(['products', 'galleryImages'])->orderBy('name')->paginate($perPage, ['*'], 'page', $page);
    }

    /**
     * Get all active locations for dropdowns
     */
    public function getActiveLocations(): array
    {
        return Location::active()->ordered()->get()->toArray();
    }

    /**
     * Get a specific location by ID
     */
    public function getLocation(int $id): Location
    {
        return Location::findOrFail($id);
    }

    /**
     * Create a new location
     */
    public function createLocation(array $data): Location
    {
        return Location::create($data);
    }

    /**
     * Update an existing location
     */
    public function updateLocation(int $id, array $data): Location
    {
        $location = Location::findOrFail($id);
        $location->update($data);
        return $location->fresh();
    }

    /**
     * Delete a location
     */
    public function deleteLocation(int $id): bool
    {
        $location = Location::findOrFail($id);
        return $location->delete();
    }

    /**
     * Toggle location active status
     */
    public function toggleActive(int $id): Location
    {
        $location = Location::findOrFail($id);
        $location->update(['is_active' => !$location->is_active]);
        return $location->fresh();
    }
}