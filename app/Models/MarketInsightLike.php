<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MarketInsightLike extends Model
{
    use HasFactory;

    protected $fillable = [
        'market_insight_id',
        'user_id',
    ];

    public function insight(): BelongsTo
    {
        return $this->belongsTo(MarketInsight::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
