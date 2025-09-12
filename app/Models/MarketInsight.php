<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MarketInsight extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'category_id',
        'featured',
        'user_id',
        'price_trend',
        'market_volume',
        'investor_confidence',
        'tags',
        'status',
        'published_at',
    ];

    protected $casts = [
        'featured' => 'boolean',
        'tags' => 'array',
    ];

    public function setTagsAttribute($value)
    {
        // Ensure tags are stored as proper JSON array
        if (is_array($value)) {
            $this->attributes['tags'] = json_encode($value);
        } elseif (is_string($value)) {
            // If it's already a JSON string, decode and re-encode to ensure proper format
            $decoded = json_decode($value, true);
            if (is_array($decoded)) {
                $this->attributes['tags'] = json_encode($decoded);
            } else {
                $this->attributes['tags'] = json_encode([]);
            }
        } else {
            $this->attributes['tags'] = json_encode([]);
        }
    }

    public function getTagsAttribute($value)
    {
        if (is_null($value)) {
            return [];
        }

        if (is_array($value)) {
            return $value;
        }

        if (is_string($value)) {
            $decoded = json_decode($value, true);
            return is_array($decoded) ? $decoded : [];
        }

        return [];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(MarketInsightCategory::class, 'category_id');
    }

    public function likes(): HasMany
    {
        return $this->hasMany(MarketInsightLike::class);
    }

    public function getAuthorAttribute(): string
    {
        return $this->user ? trim($this->user->first_name . ' ' . $this->user->last_name) : 'Anonymous';
    }
}
