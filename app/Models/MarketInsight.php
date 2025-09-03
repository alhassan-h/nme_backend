<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MarketInsight extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'category',
        'featured',
        'user_id',
        'price_trend',
        'market_volume',
        'investor_confidence',
    ];

    protected $casts = [
        'featured' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
