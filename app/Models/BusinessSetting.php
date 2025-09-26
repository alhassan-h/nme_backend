<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class BusinessSetting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'type',
        'description',
        'is_sensitive',
    ];

    protected $casts = [
        'is_sensitive' => 'boolean',
    ];

    /**
     * Get the decrypted value if sensitive
     */
    public function getDecryptedValueAttribute()
    {
        if ($this->is_sensitive && $this->value) {
            try {
                return Crypt::decryptString($this->value);
            } catch (\Exception $e) {
                return null;
            }
        }
        return $this->value;
    }

    /**
     * Set the value, encrypting if sensitive
     */
    public function setValueAttribute($value)
    {
        if ($this->is_sensitive && $value) {
            $this->attributes['value'] = Crypt::encryptString($value);
        } else {
            $this->attributes['value'] = $value;
        }
    }

    /**
     * Get a setting by key
     */
    public static function getValue(string $key, $default = null)
    {
        $setting = static::where('key', $key)->first();
        return $setting ? $setting->decrypted_value : $default;
    }

    /**
     * Set a setting value
     */
    public static function setValue(string $key, $value, string $type = 'string', bool $isSensitive = false, string $description = null)
    {
        return static::updateOrCreate(
            ['key' => $key],
            [
                'value' => $value,
                'type' => $type,
                'is_sensitive' => $isSensitive,
                'description' => $description,
            ]
        );
    }

    /**
     * Get all settings as key-value array
     */
    public static function getAllSettings()
    {
        return static::all()->mapWithKeys(function ($setting) {
            return [$setting->key => $setting->decrypted_value];
        })->toArray();
    }
}
