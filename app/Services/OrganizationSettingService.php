<?php

namespace App\Services;

use App\Models\OrganizationSetting;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class OrganizationSettingService
{
    /**
     * Get paginated settings with optional filters
     */
    public function getPaginatedSettings(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = OrganizationSetting::query();

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

        if (isset($filters['is_sensitive'])) {
            $query->where('is_sensitive', (bool) $filters['is_sensitive']);
        }

        return $query->orderBy('type')->orderBy('key')->paginate($perPage);
    }

    /**
     * Get settings by type
     */
    public function getSettingsByType(string $type): Collection
    {
        return OrganizationSetting::where('type', $type)->get();
    }

    /**
     * Create a new setting
     */
    public function createSetting(array $data): OrganizationSetting
    {
        return OrganizationSetting::create($data);
    }

    /**
     * Update an existing setting
     */
    public function updateSetting(OrganizationSetting $setting, array $data): OrganizationSetting
    {
        $setting->update($data);
        return $setting->fresh();
    }

    /**
     * Delete a setting
     */
    public function deleteSetting(OrganizationSetting $setting): bool
    {
        return $setting->delete();
    }

    /**
     * Get a setting value by key
     */
    public function getSettingValue(string $key, $default = null)
    {
        return OrganizationSetting::getValue($key, $default);
    }

    /**
     * Set a setting value
     */
    public function setSettingValue(string $key, $value, string $type = 'platform', bool $isSensitive = false, string $description = null): OrganizationSetting
    {
        return OrganizationSetting::setValue($key, $value, $type, $isSensitive, $description);
    }

    /**
     * Get all settings as key-value array
     */
    public function getAllSettings(): array
    {
        return OrganizationSetting::getAllSettings();
    }

    /**
     * Get settings grouped by type
     */
    public function getGroupedSettings(): array
    {
        return OrganizationSetting::getGroupedByType();
    }

    /**
     * Bulk update settings
     */
    public function bulkUpdateSettings(array $settings): array
    {
        $results = [];
        foreach ($settings as $data) {
            if (is_array($data) && isset($data['key'])) {
                $results[$data['key']] = $this->setSettingValue(
                    $data['key'],
                    $data['value'] ?? null,
                    $data['type'] ?? 'platform',
                    $data['is_sensitive'] ?? false,
                    $data['description'] ?? null
                );
            }
        }
        return $results;
    }

    /**
     * Validate setting data
     */
    public function validateSettingData(array $data): array
    {
        $errors = [];

        if (empty($data['key'])) {
            $errors['key'] = 'Key is required';
        } elseif (!preg_match('/^[a-z_][a-z0-9_]*$/', $data['key'])) {
            $errors['key'] = 'Key must contain only lowercase letters, numbers, and underscores, starting with a letter or underscore';
        }

        if (empty($data['description'])) {
            $errors['description'] = 'Description is required';
        }

        $validTypes = ['security', 'email', 'platform', 'content', 'payment', 'organization', 'business'];
        if (!in_array($data['type'] ?? 'platform', $validTypes)) {
            $errors['type'] = 'Invalid type specified';
        }

        return $errors;
    }
}