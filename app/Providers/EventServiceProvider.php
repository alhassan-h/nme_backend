<?php

namespace App\Providers;

use App\Events\ForumPostCreated;
use App\Events\InsightCreated;
use App\Events\ProductCreated;
use App\Events\UserRegistered;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        ForumPostCreated::class => [
            //
        ],
        InsightCreated::class => [
            //
        ],
        ProductCreated::class => [
            //
        ],
        UserRegistered::class => [
            //
        ],
    ];

    public function boot(): void
    {
        parent::boot();
    }
}
