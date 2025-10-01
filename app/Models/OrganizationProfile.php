<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class OrganizationProfile extends Model
{
    protected $fillable = [
        'key',
        'value',
        'type',
        'description',
        'is_public',
        'sort_order',
    ];

    protected $casts = [
        'is_public' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Cast the value based on type
     */
    protected function value(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                if ($value === null) {
                    return null;
                }

                return match ($this->type) {
                    'json' => json_decode($value, true),
                    'integer' => (int) $value,
                    'boolean' => (bool) $value,
                    'float' => (float) $value,
                    'image' => $value, // Store image URL as string
                    default => $value,
                };
            },
            set: function ($value) {
                if ($value === null) {
                    return null;
                }

                return match ($this->type) {
                    'json' => is_array($value) ? json_encode($value) : $value,
                    'boolean' => $value ? '1' : '0',
                    'image' => (string) $value, // Store image URL as string
                    default => (string) $value,
                };
            }
        );
    }

    /**
     * Scope for public fields only
     */
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    /**
     * Get all profile data as a key-value array
     */
    public static function getAllProfileData($publicOnly = false)
    {
        $query = static::orderBy('sort_order')->orderBy('key');

        if ($publicOnly) {
            $query->public();
        }

        return $query->get()->mapWithKeys(function ($item) {
            return [$item->key => $item->value];
        })->toArray();
    }

    /**
     * Get a specific profile value by key
     */
    public static function getProfileValue(string $key, $default = null)
    {
        $profile = static::where('key', $key)->first();
        return $profile ? $profile->value : $default;
    }

    /**
     * Set a profile value
     */
    public static function setProfileValue(string $key, $value, string $type = 'string', ?string $description = null, bool $isPublic = true, int $sortOrder = 0)
    {
        return static::updateOrCreate(
            ['key' => $key],
            [
                'value' => $value,
                'type' => $type,
                'description' => $description,
                'is_public' => $isPublic,
                'sort_order' => $sortOrder,
            ]
        );
    }

    /**
     * Delete a profile field by key
     */
    public static function deleteProfileValue(string $key)
    {
        return static::where('key', $key)->delete();
    }
}
