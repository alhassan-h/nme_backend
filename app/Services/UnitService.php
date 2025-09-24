<?php

namespace App\Services;

use App\Models\Unit;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class UnitService
{
    /**
     * Get all units with pagination for admin
     */
    public function getUnits(array $filters = []): LengthAwarePaginator
    {
        $query = Unit::query();

        if (!empty($filters['search'])) {
            $query->where('name', 'like', '%' . $filters['search'] . '%');
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        $perPage = $filters['per_page'] ?? 15;
        $page = $filters['page'] ?? 1;

        return $query->withCount('products')->orderBy('name')->paginate($perPage, ['*'], 'page', $page);
    }

    /**
     * Get all active units for dropdowns
     */
    public function getActiveUnits(): array
    {
        return Unit::active()->ordered()->get()->toArray();
    }

    /**
     * Get a specific unit by ID
     */
    public function getUnit(int $id): Unit
    {
        return Unit::findOrFail($id);
    }

    /**
     * Create a new unit
     */
    public function createUnit(array $data): Unit
    {
        $unit = Unit::create($data);
        event(new \App\Events\UnitCreated($unit));
        return $unit;
    }

    /**
     * Update an existing unit
     */
    public function updateUnit(int $id, array $data): Unit
    {
        $unit = Unit::findOrFail($id);
        $unit->update($data);
        return $unit->fresh();
    }

    /**
     * Delete a unit
     */
    public function deleteUnit(int $id): bool
    {
        $unit = Unit::findOrFail($id);
        return $unit->delete();
    }

    /**
     * Toggle unit active status
     */
    public function toggleActive(int $id): Unit
    {
        $unit = Unit::findOrFail($id);
        $unit->update(['is_active' => !$unit->is_active]);
        return $unit->fresh();
    }
}