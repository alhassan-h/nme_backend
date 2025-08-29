<?php

namespace App\Events;

use App\Models\MarketInsight;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class InsightCreated
{
    use Dispatchable, SerializesModels;

    public MarketInsight $insight;

    public function __construct(MarketInsight $insight)
    {
        $this->insight = $insight;
    }
}
