<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\OrganizationProfile;

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
        ];

        foreach ($profileData as $data) {
            OrganizationProfile::updateOrCreate(
                ['key' => $data['key']],
                $data
            );
        }
    }
}
