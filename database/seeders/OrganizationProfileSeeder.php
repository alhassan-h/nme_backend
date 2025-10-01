<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\OrganizationProfile;
use App\Services\CloudinaryService;
use Illuminate\Support\Facades\Log;

class OrganizationProfileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $profileData = [
            [
                'key' => 'organization_name',
                'value' => 'Nigerian Mining Exchange',
                'type' => 'string',
                'description' => 'Organization name displayed on the platform',
                'is_public' => true,
                'sort_order' => 1,
            ],
            [
                'key' => 'tagline',
                'value' => 'Connecting Nigerian mining stakeholders with global markets',
                'type' => 'string',
                'description' => 'Short tagline for the organization',
                'is_public' => true,
                'sort_order' => 2,
            ],
            [
                'key' => 'description',
                'value' => 'A comprehensive platform for Nigerian mining industry stakeholders to connect, trade, and collaborate with global markets. We provide a secure marketplace for mineral resources, market insights, and networking opportunities.',
                'type' => 'string',
                'description' => 'Detailed description of the organization',
                'is_public' => true,
                'sort_order' => 3,
            ],
            [
                'key' => 'contact_email',
                'value' => 'admin@nme.com',
                'type' => 'string',
                'description' => 'Primary contact email address',
                'is_public' => true,
                'sort_order' => 4,
            ],
            [
                'key' => 'support_phone',
                'value' => '+234 123 456 7890',
                'type' => 'string',
                'description' => 'Support phone number',
                'is_public' => true,
                'sort_order' => 5,
            ],
            [
                'key' => 'address',
                'value' => 'Lagos, Nigeria',
                'type' => 'string',
                'description' => 'Organization address',
                'is_public' => true,
                'sort_order' => 6,
            ],
            [
                'key' => 'website',
                'value' => 'https://nigerianminingexchange.com',
                'type' => 'string',
                'description' => 'Organization website URL',
                'is_public' => true,
                'sort_order' => 7,
            ],
            [
                'key' => 'social_links',
                'value' => json_encode([
                    ['platform' => 'linkedin', 'url' => 'https://linkedin.com/company/nigerian-mining-exchange'],
                    ['platform' => 'twitter', 'url' => 'https://twitter.com/NMEMining'],
                    ['platform' => 'facebook', 'url' => 'https://facebook.com/NigerianMiningExchange']
                ]),
                'type' => 'json',
                'description' => 'Social media links and profiles',
                'is_public' => true,
                'sort_order' => 8,
            ],
            [
                'key' => 'emails',
                'value' => json_encode([
                    ['admin' => 'admin@nme.com'],
                    ['support' => 'support@nme.com'],
                    ['info' => 'info@nme.com'],
                    ['sales' => 'sales@nme.com'],
                    ['contact' => 'contact@nme.com']
                ]),
                'type' => 'json',
                'description' => 'List of organization email addresses',
                'is_public' => false,
                'sort_order' => 9,
            ],
            [
                'key' => 'phones',
                'value' => json_encode([
                    ['call' => '+234 123 456 7890'],
                    ['whatsapp' => '+234 987 654 3210']
                ]),
                'type' => 'json',
                'description' => 'List of organization phone numbers',
                'is_public' => true,
                'sort_order' => 10,
            ],
            [
                'key' => 'founded_year',
                'value' => 2024,
                'type' => 'integer',
                'description' => 'Year the organization was founded',
                'is_public' => true,
                'sort_order' => 11,
            ],
            [
                'key' => 'industry',
                'value' => 'Mining & Natural Resources',
                'type' => 'string',
                'description' => 'Primary industry focus',
                'is_public' => true,
                'sort_order' => 12,
            ],
            [
                'key' => 'mission',
                'value' => 'To create a transparent and efficient marketplace for Nigerian mineral resources',
                'type' => 'string',
                'description' => 'Organization mission statement',
                'is_public' => true,
                'sort_order' => 13,
            ],
            [
                'key' => 'default_currency',
                'value' => 'NGN',
                'type' => 'string',
                'description' => 'Default currency for transactions',
                'is_public' => false,
                'sort_order' => 14,
            ],
            [
                'key' => 'homepage_footer_powered_by_name',
                'value' => 'STOTAN LLP LTD',
                'type' => 'string',
                'description' => 'Name of the entity powering the site',
                'is_public' => true,
                'sort_order' => 15,
            ],
            [
                'key' => 'homepage_footer_powered_by_logo',
                'value' => 'images/organization/stotan-logo.jpg',
                'type' => 'image',
                'description' => 'URL or path to the logo image for the powering entity',
                'is_public' => true,
                'sort_order' => 16,
            ],
            [
                'key' => 'homepage_footer_powered_by_url',
                'value' => 'https://stotan.com',
                'type' => 'string',
                'description' => 'URL to link to when the powered by section is clicked',
                'is_public' => true,
                'sort_order' => 17,
            ],
            [
                'key' => 'homepage_footer_developed_by_name',
                'value' => 'DevStudio',
                'type' => 'string',
                'description' => 'Name of the entity that developed the site',
                'is_public' => true,
                'sort_order' => 18,
            ],
            [
                'key' => 'homepage_footer_developed_by_logo',
                'value' => 'images/organization/devstudio-logo.png',
                'type' => 'image',
                'description' => 'URL or path to the logo image for the developing entity',
                'is_public' => true,
                'sort_order' => 19,
            ],
            [
                'key' => 'homepage_footer_developed_by_url',
                'value' => 'https://devstudio.example.com',
                'type' => 'string',
                'description' => 'URL to link to when the developed by section is clicked',
                'is_public' => true,
                'sort_order' => 20,
            ],
            [
                'key' => 'logo',
                'value' => 'images/organization/logo.png',
                'type' => 'image',
                'description' => 'URL or path to the logo image for the organization',
                'is_public' => true,
                'sort_order' => 21,
            ],
            [
                'key' => 'favicon',
                'value' => 'images/organization/favicon.png',
                'type' => 'image',
                'description' => 'URL or path to the favicon image for the organization',
                'is_public' => true,
                'sort_order' => 22,
            ],
        ];

        foreach ($profileData as $data) {
            if ($data['type'] === 'image' && isset($data['value'])) {
                $filename = basename($data['value']); // e.g., 'stotan-logo.jpg'
                $uploadedUrl = $this->uploadOrganizationImage($filename);
                if ($uploadedUrl) {
                    $data['value'] = $uploadedUrl;
                } else {
                    Log::warning('Failed to upload organization image: ' . $filename);
                }
            }
            OrganizationProfile::updateOrCreate(
                ['key' => $data['key']],
                $data
            );
        }
    }

    private function uploadOrganizationImage($filename)
    {
        $cloudinary = app(CloudinaryService::class);
        try {
            $imagePath = resource_path('defaults/images/organization/' . $filename);
            if (file_exists($imagePath)) {
                $result = $cloudinary->upload($imagePath, ['folder' => 'organization']);
                return $result['secure_url'];
            } else {
                Log::warning('Organization image not found: ' . $imagePath);
                return null;
            }
        } catch (\Exception $e) {
            Log::error('Failed to upload organization image ' . $filename . ': ' . $e->getMessage());
            return null;
        }
    }
}
