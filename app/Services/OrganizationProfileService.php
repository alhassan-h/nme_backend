<?php

namespace App\Services;

use App\Models\OrganizationProfile;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class OrganizationProfileService
{
    /**
     * Get all organization profile entries with optional pagination
     */
    public function getAllProfiles(?int $perPage = null, array $filters = []): Collection|LengthAwarePaginator
    {
        $query = OrganizationProfile::query()
            ->orderBy('sort_order')
            ->orderBy('key');

        // Apply filters
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('key', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (isset($filters['is_public'])) {
            $query->where('is_public', (bool) $filters['is_public']);
        }

        if ($perPage) {
            return $query->paginate($perPage);
        }

        return $query->get();
    }

    /**
     * Get a single profile entry by key
     */
    public function getProfile(string $key): ?OrganizationProfile
    {
        return OrganizationProfile::where('key', $key)->first();
    }

    /**
     * Get profile value by key with automatic type casting
     */
    public function getProfileValue(string $key, $default = null)
    {
        return OrganizationProfile::getProfileValue($key, $default);
    }

    /**
     * Create a new profile entry
     */
    public function createProfile(array $data): OrganizationProfile
    {
        return OrganizationProfile::create([
            'key' => $data['key'],
            'value' => $data['value'],
            'type' => $data['type'] ?? 'string',
            'description' => $data['description'] ?? null,
            'is_public' => $data['is_public'] ?? true,
            'sort_order' => $data['sort_order'] ?? 0,
        ]);
    }

    /**
     * Update an existing profile entry
     */
    public function updateProfile(OrganizationProfile $profile, array $data): OrganizationProfile
    {
        $profile->update([
            'value' => $data['value'] ?? $profile->value,
            'type' => $data['type'] ?? $profile->type,
            'description' => $data['description'] ?? $profile->description,
            'is_public' => $data['is_public'] ?? $profile->is_public,
            'sort_order' => $data['sort_order'] ?? $profile->sort_order,
        ]);

        return $profile->fresh();
    }

    /**
     * Set or update a profile value by key
     */
    public function setProfileValue(string $key, $value, string $type = 'string', ?string $description = null, bool $isPublic = true, int $sortOrder = 0): OrganizationProfile
    {
        return OrganizationProfile::setProfileValue($key, $value, $type, $description, $isPublic, $sortOrder);
    }

    /**
     * Delete a profile entry
     */
    public function deleteProfile(OrganizationProfile $profile): bool
    {
        return $profile->delete();
    }

    /**
     * Delete a profile entry by key
     */
    public function deleteProfileByKey(string $key): bool
    {
        return OrganizationProfile::deleteProfileValue($key);
    }

    /**
     * Get all profile data as a key-value array
     */
    public function getAllProfileData(bool $publicOnly = false): array
    {
        return OrganizationProfile::getAllProfileData($publicOnly);
    }

    /**
     * Bulk update profile entries
     */
    public function bulkUpdate(array $updates): array
    {
        $results = [];

        foreach ($updates as $key => $data) {
            try {
                if (is_array($data) && isset($data['value'])) {
                    $profile = $this->setProfileValue(
                        $key,
                        $data['value'],
                        $data['type'] ?? 'string',
                        $data['description'] ?? null,
                        $data['is_public'] ?? true,
                        $data['sort_order'] ?? 0
                    );
                    $results[$key] = ['success' => true, 'data' => $profile];
                } else {
                    // Simple key-value update
                    $profile = $this->setProfileValue($key, $data);
                    $results[$key] = ['success' => true, 'data' => $profile];
                }
            } catch (\Exception $e) {
                $results[$key] = ['success' => false, 'error' => $e->getMessage()];
            }
        }

        return $results;
    }

    /**
     * Get profile statistics
     */
    public function getStats(): array
    {
        return [
            'total_entries' => OrganizationProfile::count(),
            'public_entries' => OrganizationProfile::public()->count(),
            'private_entries' => OrganizationProfile::where('is_public', false)->count(),
            'types_breakdown' => OrganizationProfile::selectRaw('type, COUNT(*) as count')
                ->groupBy('type')
                ->pluck('count', 'type')
                ->toArray(),
        ];
    }
}