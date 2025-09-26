<?php

namespace Database\Seeders;

use App\Models\OrganizationSetting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrganizationSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // Security Settings
            [
                'key' => 'two_factor_auth_for_admin',
                'value' => '0',
                'type' => 'security',
                'description' => 'Enable two-factor authentication for admin accounts',
                'is_sensitive' => false,
            ],
            [
                'key' => 'session_timeout',
                'value' => '30',
                'type' => 'security',
                'description' => 'Session timeout in minutes',
                'is_sensitive' => false,
            ],
            [
                'key' => 'password_min_length',
                'value' => '8',
                'type' => 'security',
                'description' => 'Minimum password length requirement',
                'is_sensitive' => false,
            ],
            [
                'key' => 'login_attempts',
                'value' => '5',
                'type' => 'security',
                'description' => 'Maximum number of login attempts before lockout',
                'is_sensitive' => false,
            ],

            // Email Settings
            [
                'key' => 'email_notifications',
                'value' => 'true',
                'type' => 'email',
                'description' => 'Enable system email notifications',
                'is_sensitive' => false,
            ],
            [
                'key' => 'newsletter_enabled',
                'value' => 'true',
                'type' => 'email',
                'description' => 'Enable newsletter subscription system',
                'is_sensitive' => false,
            ],

            // Platform Settings
            [
                'key' => 'maintenance_mode',
                'value' => 'false',
                'type' => 'platform',
                'description' => 'Put platform in maintenance mode',
                'is_sensitive' => false,
            ],
            [
                'key' => 'registration_enabled',
                'value' => 'true',
                'type' => 'platform',
                'description' => 'Allow new user registrations',
                'is_sensitive' => false,
            ],
            [
                'key' => 'marketplace_enabled',
                'value' => 'true',
                'type' => 'platform',
                'description' => 'Enable marketplace functionality',
                'is_sensitive' => false,
            ],
            [
                'key' => 'gallery_enabled',
                'value' => 'true',
                'type' => 'platform',
                'description' => 'Enable image gallery features',
                'is_sensitive' => false,
            ],
            [
                'key' => 'default_currency',
                'value' => 'NGN',
                'type' => 'platform',
                'description' => 'Default currency for transactions',
                'is_sensitive' => false,
            ],

            // Content Settings
            [
                'key' => 'max_file_size',
                'value' => '10',
                'type' => 'content',
                'description' => 'Maximum file upload size in MB',
                'is_sensitive' => false,
            ],
            // [
            //     'key' => 'allowed_file_types',
            //     'value' => '["jpg","png","pdf","doc","xls"]',
            //     'type' => 'content',
            //     'description' => 'Allowed file types for uploads',
            //     'is_sensitive' => false,
            // ],
            [
                'key' => 'content_moderation',
                'value' => 'true',
                'type' => 'content',
                'description' => 'Enable automatic content moderation',
                'is_sensitive' => false,
            ],
            [
                'key' => 'auto_approve_listings',
                'value' => 'false',
                'type' => 'content',
                'description' => 'Automatically approve new listings',
                'is_sensitive' => false,
            ],
            [
                'key' => 'auto_approve_gallery_images',
                'value' => 'false',
                'type' => 'content',
                'description' => 'Automatically approve new gallery images',
                'is_sensitive' => false,
            ],

            // Payment Settings
            [
                'key' => 'commission_rate_percentage',
                'value' => '2.5',
                'type' => 'payment',
                'description' => 'Commission rate as percentage',
                'is_sensitive' => false,
            ],
            [
                'key' => 'payment_methods_enabled',
                'value' => '["bank_transfer","card"]',
                'type' => 'payment',
                'description' => 'Enabled payment methods',
                'is_sensitive' => false,
            ],
            [
                'key' => 'escrow_system_enabled',
                'value' => 'true',
                'type' => 'payment',
                'description' => 'Enable secure payment escrow',
                'is_sensitive' => false,
            ],
            [
                'key' => 'stripe_publishable_key',
                'value' => 'pk_test_your_stripe_publishable_key',
                'type' => 'payment',
                'description' => 'Stripe publishable key for frontend',
                'is_sensitive' => false,
            ],
            [
                'key' => 'stripe_secret_key',
                'value' => 'sk_test_your_stripe_secret_key',
                'type' => 'payment',
                'description' => 'Stripe secret key for backend',
                'is_sensitive' => true,
            ],

        ];

        foreach ($settings as $setting) {
            OrganizationSetting::create($setting);
        }
    }
}
