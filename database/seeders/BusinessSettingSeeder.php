<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\BusinessSetting;

class BusinessSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // SMTP Configuration
            [
                'key' => 'smtp_host',
                'value' => 'smtp.gmail.com',
                'type' => 'string',
                'description' => 'SMTP server hostname',
                'is_sensitive' => false,
            ],
            [
                'key' => 'smtp_port',
                'value' => '587',
                'type' => 'string',
                'description' => 'SMTP server port',
                'is_sensitive' => false,
            ],
            [
                'key' => 'smtp_user',
                'value' => 'noreply@nme.com',
                'type' => 'string',
                'description' => 'SMTP authentication username',
                'is_sensitive' => false,
            ],
            [
                'key' => 'smtp_password',
                'value' => null, // To be set by admin
                'type' => 'encrypted',
                'description' => 'SMTP authentication password',
                'is_sensitive' => true,
            ],

            // Email Service (MailGun)
            [
                'key' => 'mailgun_api_key',
                'value' => null, // To be set by admin
                'type' => 'encrypted',
                'description' => 'MailGun API Key for email services',
                'is_sensitive' => true,
            ],
            [
                'key' => 'mailgun_domain',
                'value' => 'nme.com',
                'type' => 'string',
                'description' => 'MailGun sending domain',
                'is_sensitive' => false,
            ],
            [
                'key' => 'mailgun_endpoint',
                'value' => 'https://api.mailgun.net/v3',
                'type' => 'string',
                'description' => 'MailGun API endpoint',
                'is_sensitive' => false,
            ],

            // Payment Gateway (Stripe)
            [
                'key' => 'stripe_publishable_key',
                'value' => null, // To be set by admin
                'type' => 'string',
                'description' => 'Stripe publishable key for frontend',
                'is_sensitive' => false,
            ],
            [
                'key' => 'stripe_secret_key',
                'value' => null, // To be set by admin
                'type' => 'encrypted',
                'description' => 'Stripe secret key for backend',
                'is_sensitive' => true,
            ],
            [
                'key' => 'stripe_webhook_secret',
                'value' => null, // To be set by admin
                'type' => 'encrypted',
                'description' => 'Stripe webhook secret for verification',
                'is_sensitive' => true,
            ],

            // SMS Service (Twilio)
            [
                'key' => 'twilio_account_sid',
                'value' => null, // To be set by admin
                'type' => 'encrypted',
                'description' => 'Twilio Account SID',
                'is_sensitive' => true,
            ],
            [
                'key' => 'twilio_auth_token',
                'value' => null, // To be set by admin
                'type' => 'encrypted',
                'description' => 'Twilio Auth Token',
                'is_sensitive' => true,
            ],
            [
                'key' => 'twilio_phone_number',
                'value' => null, // To be set by admin
                'type' => 'string',
                'description' => 'Twilio phone number for sending SMS',
                'is_sensitive' => false,
            ],

            // File Storage (AWS S3)
            [
                'key' => 'aws_access_key_id',
                'value' => null, // To be set by admin
                'type' => 'encrypted',
                'description' => 'AWS Access Key ID for S3',
                'is_sensitive' => true,
            ],
            [
                'key' => 'aws_secret_access_key',
                'value' => null, // To be set by admin
                'type' => 'encrypted',
                'description' => 'AWS Secret Access Key for S3',
                'is_sensitive' => true,
            ],
            [
                'key' => 'aws_default_region',
                'value' => 'us-east-1',
                'type' => 'string',
                'description' => 'AWS default region',
                'is_sensitive' => false,
            ],
            [
                'key' => 'aws_bucket',
                'value' => 'nme-uploads',
                'type' => 'string',
                'description' => 'AWS S3 bucket name',
                'is_sensitive' => false,
            ],

            // Analytics (Google Analytics)
            [
                'key' => 'google_analytics_id',
                'value' => null, // To be set by admin
                'type' => 'string',
                'description' => 'Google Analytics Tracking ID',
                'is_sensitive' => false,
            ],

            // Social Media API Keys
            [
                'key' => 'facebook_app_id',
                'value' => null, // To be set by admin
                'type' => 'string',
                'description' => 'Facebook App ID for social login',
                'is_sensitive' => false,
            ],
            [
                'key' => 'facebook_app_secret',
                'value' => null, // To be set by admin
                'type' => 'encrypted',
                'description' => 'Facebook App Secret',
                'is_sensitive' => true,
            ],
            [
                'key' => 'google_client_id',
                'value' => null, // To be set by admin
                'type' => 'string',
                'description' => 'Google Client ID for social login',
                'is_sensitive' => false,
            ],
            [
                'key' => 'google_client_secret',
                'value' => null, // To be set by admin
                'type' => 'encrypted',
                'description' => 'Google Client Secret',
                'is_sensitive' => true,
            ],

            // Exchange Rate API
            [
                'key' => 'exchange_rate_api_key',
                'value' => null, // To be set by admin
                'type' => 'encrypted',
                'description' => 'API key for currency exchange rates',
                'is_sensitive' => true,
            ],

            // Push Notifications (Firebase)
            [
                'key' => 'firebase_server_key',
                'value' => null, // To be set by admin
                'type' => 'encrypted',
                'description' => 'Firebase server key for push notifications',
                'is_sensitive' => true,
            ],
        ];

        foreach ($settings as $setting) {
            BusinessSetting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
