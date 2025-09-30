<?php

// This controller is not in use yet, AdminController is handling organization settings for now.
// It can be used in the future to separate concerns and keep the codebase organized.

namespace App\Http\Controllers;

use App\Services\OrganizationSettingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class OrganizationSettingController extends Controller
{
    protected OrganizationSettingService $settingService;

    public function __construct(OrganizationSettingService $settingService)
    {
        $this->settingService = $settingService;
    }

    public function getMaintenanceStatus(): JsonResponse
    {
        try {
            $isEnabled = $this->settingService->isEnabled('maintenance_mode');

            return response()->json([
                'success' => true,
                'data' => [
                    'enabled' => $isEnabled
                ],
                'message' => 'Maintenance status retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve maintenance status',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getRegistrationStatus(): JsonResponse
    {
        try {
            $isEnabled = $this->settingService->isEnabled('registration_enabled');

            return response()->json([
                'success' => true,
                'data' => [
                    'enabled' => $isEnabled
                ],
                'message' => 'Registration status retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve registration status',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getMarketplaceStatus(): JsonResponse
    {
        try {
            $isEnabled = $this->settingService->isEnabled('marketplace_enabled');

            return response()->json([
                'success' => true,
                'data' => [
                    'enabled' => $isEnabled
                ],
                'message' => 'Marketplace status retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve marketplace status',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getNewsletterStatus(): JsonResponse
    {
        try {
            $isEnabled = $this->settingService->isEnabled('newsletter_enabled');

            return response()->json([
                'success' => true,
                'data' => [
                    'enabled' => $isEnabled
                ],
                'message' => 'Newsletter status retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve newsletter status',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getGalleryStatus(): JsonResponse
    {
        try {
            $isEnabled = $this->settingService->isEnabled('gallery_enabled');

            return response()->json([
                'success' => true,
                'data' => [
                    'enabled' => $isEnabled
                ],
                'message' => 'Gallery status retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve gallery status',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getMarketInsightsStatus(): JsonResponse
    {
        try {
            $isEnabled = $this->settingService->isEnabled('market_insights_enabled');

            return response()->json([
                'success' => true,
                'data' => [
                    'enabled' => $isEnabled
                ],
                'message' => 'Market insights status retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve market insights status',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getCommunityStatus(): JsonResponse
    {
        try {
            $isEnabled = $this->settingService->isEnabled('community_forum_enabled');

            return response()->json([
                'success' => true,
                'data' => [
                    'enabled' => $isEnabled
                ],
                'message' => 'Community status retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve community status',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getOrganizationSettings(): JsonResponse
    {
        try {
            $settings = $this->settingService->getAllSettings();
            return response()->json([
                'success' => true,
                'data' => $settings,
                'message' => 'Organization settings retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve organization settings',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function createOrganizationSetting(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'key' => 'required|string|max:255|unique:organization_settings,key',
                'value' => 'nullable',
                'type' => 'required|in:security,email,platform,content,payment,organization,business',
                'description' => 'nullable|string|max:500',
                'is_sensitive' => 'boolean',
            ]);

            $setting = $this->settingService->createSetting($validated);

            return response()->json([
                'success' => true,
                'data' => $setting,
                'message' => 'Organization setting created successfully'
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create organization setting',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function updateOrganizationSetting(Request $request, string $setting): JsonResponse
    {
        try {
            $validated = $request->validate([
                'value' => 'nullable',
                'type' => 'sometimes|required|in:security,email,platform,content,payment,organization,business',
                'description' => 'nullable|string|max:500',
                'is_sensitive' => 'boolean',
            ]);

            $updatedSetting = $this->settingService->updateSetting($setting, $validated);

            return response()->json([
                'success' => true,
                'data' => $updatedSetting,
                'message' => 'Organization setting updated successfully'
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update organization setting',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function deleteOrganizationSetting(string $setting): JsonResponse
    {
        try {
            $this->settingService->deleteSetting($setting);

            return response()->json([
                'success' => true,
                'message' => 'Organization setting deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete organization setting',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getOrganizationSettingValue(string $key): JsonResponse
    {
        try {
            $value = $this->settingService->getSettingValue($key);

            return response()->json([
                'success' => true,
                'data' => [
                    'key' => $key,
                    'value' => $value
                ],
                'message' => 'Organization setting value retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve organization setting value',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getOrganizationSettingsByType(string $type): JsonResponse
    {
        try {
            $settings = $this->settingService->getSettingsByType($type);

            return response()->json([
                'success' => true,
                'data' => $settings,
                'message' => 'Organization settings retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve organization settings by type',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function bulkUpdateOrganizationSettings(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'settings' => 'required|array',
                'settings.*.key' => 'required|string|max:255',
                'settings.*.value' => 'nullable',
                'settings.*.type' => 'required|in:security,email,platform,content,payment,organization,business',
                'settings.*.description' => 'nullable|string|max:500',
                'settings.*.is_sensitive' => 'boolean',
            ]);

            $results = $this->settingService->bulkUpdateSettings($validated['settings']);

            return response()->json([
                'success' => true,
                'data' => $results,
                'message' => 'Organization settings updated successfully'
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to bulk update organization settings',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}