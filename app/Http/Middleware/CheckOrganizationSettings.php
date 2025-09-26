<?php

namespace App\Http\Middleware;

use App\Services\OrganizationSettingService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckOrganizationSettings
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
    public function handle(Request $request, Closure $next, string $settingKey, string $operation = null): Response
    {
        // Special handling for maintenance mode
        if ($settingKey === 'maintenance_mode') {
            if ($this->settingService->isEnabled('maintenance_mode')) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => 'The platform is currently under maintenance. Please try again later.',
                        'code' => 'MAINTENANCE_MODE'
                    ], 503);
                }
                return response()->view('maintenance', [], 503);
            }
            // If maintenance mode is disabled, continue
            return $next($request);
        }

        // For other settings, check if enabled
        try {
            $this->settingService->checkAccess($settingKey, $operation);
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => $e->getMessage(),
                    'code' => 'FEATURE_DISABLED',
                    'setting' => $settingKey
                ], $e->getCode());
            }

            abort(403, $e->getMessage());
        }

        return $next($request);
    }
}
