<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Define admin gate based on user_type
        Gate::define('admin', function (User $user) {
            return $user->user_type === 'admin';
        });

        // Define gates for user types
        Gate::define('seller', function (User $user) {
            return in_array($user->user_type, ['seller', 'both']);
        });

        Gate::define('buyer', function (User $user) {
            return in_array($user->user_type, ['buyer', 'both']);
        });
    }
}
