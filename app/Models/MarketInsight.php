<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MarketInsight extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'category',
        'featured',
        'price_trend',
        'market_volume',
        'investor_confidence',
    ];

    protected $casts = [
        'featured' => 'boolean',
    ];
}
