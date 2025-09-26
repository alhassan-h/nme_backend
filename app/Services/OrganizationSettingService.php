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

    /**
     * Check if a setting is enabled (boolean check)
     */
    public function isEnabled(string $key): bool
    {
        $value = $this->getSettingValue($key, 'false');
        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * Check access to a feature and throw exception if disabled
     */
    public function checkAccess(string $key, string $operation = null): void
    {
        if (!$this->isEnabled($key)) {
            $message = $operation ?
                "The {$operation} feature is currently disabled" :
                "This feature is currently unavailable";
            throw new \Exception($message, 403);
        }
    }

    /**
     * Get setting value with type casting
     */
    public function get(string $key, $default = null)
    {
        return $this->getSettingValue($key, $default);
    }

    /**
     * Get integer setting value
     */
    public function getInt(string $key, int $default = 0): int
    {
        $value = $this->getSettingValue($key, $default);
        return (int) $value;
    }

    /**
     * Get float setting value
     */
    public function getFloat(string $key, float $default = 0.0): float
    {
        $value = $this->getSettingValue($key, $default);
        return (float) $value;
    }

    /**
     * Get JSON setting value as array
     */
    public function getJson(string $key, array $default = []): array
    {
        $value = $this->getSettingValue($key, json_encode($default));
        $decoded = json_decode($value, true);
        return is_array($decoded) ? $decoded : $default;
    }

    /**
     * Check if maintenance mode is enabled
     */
    public function isMaintenanceMode(): bool
    {
        return $this->isEnabled('maintenance_mode');
    }

    /**
     * Get password minimum length requirement
     */
    public function getPasswordMinLength(): int
    {
        return $this->getInt('password_min_length', 8);
    }

    /**
     * Get maximum login attempts
     */
    public function getMaxLoginAttempts(): int
    {
        return $this->getInt('login_attempts', 5);
    }

    /**
     * Get maximum file size in MB
     */
    public function getMaxFileSize(): int
    {
        return $this->getInt('max_file_size', 10);
    }
}