<?php

namespace App\Http\Middleware;

use App\Services\OrganizationSettingService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckMaintenanceMode
{
    protected OrganizationSettingService $settingService;

    public function __construct(OrganizationSettingService $settingService)
    {
        $this->settingService = $settingService;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($this->settingService->isEnabled('maintenance_mode')) {
            // Allow admin routes to be accessible during maintenance
            if ($this->isAdminRoute($request)) {
                return $next($request);
            }

            // Allow maintenance status check to pass through
            if ($this->isMaintenanceStatusRoute($request)) {
                return $next($request);
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'The platform is currently under maintenance. Please try again later.',
                    'code' => 'MAINTENANCE_MODE'
                ], 503);
            }
            return response()->view('maintenance', [], 503);
        }

        return $next($request);
    }

    /**
     * Check if the current request is for an admin route
     */
    private function isAdminRoute(Request $request): bool
    {
        // Check if the route name starts with 'admin'
        $routeName = $request->route()?->getName();
        if ($routeName && str_starts_with($routeName, 'admin.')) {
            return true;
        }

        // Check if the path starts with 'admin'
        if (str_starts_with($request->path(), 'admin')) {
            return true;
        }

        // Check if user is authenticated and has admin role
        if (auth()->check() && auth()->user()->user_type === 'admin') {
            return true;
        }

        return false;
    }

    /**
     * Check if the current request is for the maintenance status route
     */
    private function isMaintenanceStatusRoute(Request $request): bool
    {
        $routeName = $request->route()?->getName();
        return $routeName === 'maintenance.status';
    }
}