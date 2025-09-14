<?php

namespace Database\Seeders;

use App\Models\Newsletter;
use App\Models\NewsletterSubscriber;
use App\Models\NewsletterRecipient;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class NewsletterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create subscribers
        $subscribers = [
            [
                'email' => 'john.adebayo@email.com',
                'name' => 'John Adebayo',
                'subscribed_at' => Carbon::parse('2024-11-15T10:00:00Z'),
                'status' => 'active',
            ],
            [
                'email' => 'sarah.okafor@email.com',
                'name' => 'Sarah Okafor',
                'subscribed_at' => Carbon::parse('2024-10-22T09:15:00Z'),
                'status' => 'active',
            ],
            [
                'email' => 'michael.chen@email.com',
                'name' => 'Michael Chen',
                'subscribed_at' => Carbon::parse('2024-12-01T11:20:00Z'),
                'status' => 'unsubscribed',
            ],
        ];

        foreach ($subscribers as $subscriberData) {
            NewsletterSubscriber::create($subscriberData);
        }

        // Create newsletters
        $newsletters = [
            [
                'subject' => 'Weekly Market Update - December 2024',
                'content' => 'This is the weekly market update for December 2024...',
                'html_content' => '<p>This is the weekly market update for December 2024...</p>',
                'status' => 'sent',
                'sent_at' => Carbon::parse('2024-12-15T10:00:00Z'),
                'created_at' => Carbon::parse('2024-12-15T09:00:00Z'),
            ],
            [
                'subject' => 'Gold Price Alert - Significant Changes',
                'content' => 'Alert: Significant changes in gold prices...',
                'html_content' => '<p>Alert: Significant changes in gold prices...</p>',
                'status' => 'sent',
                'sent_at' => Carbon::parse('2024-12-12T14:30:00Z'),
                'created_at' => Carbon::parse('2024-12-12T13:00:00Z'),
            ],
            [
                'subject' => 'New Mining Regulations Update',
                'content' => 'Update on new mining regulations...',
                'html_content' => '<p>Update on new mining regulations...</p>',
                'status' => 'draft',
                'created_at' => Carbon::parse('2024-12-10T10:00:00Z'),
            ],
            [
                'subject' => 'Monthly Industry Report - November',
                'content' => 'Monthly industry report for November...',
                'html_content' => '<p>Monthly industry report for November...</p>',
                'status' => 'scheduled',
                'scheduled_for' => Carbon::parse('2024-12-20T09:00:00Z'),
                'created_at' => Carbon::parse('2024-12-18T10:00:00Z'),
            ],
        ];

        foreach ($newsletters as $newsletterData) {
            $newsletter = Newsletter::create($newsletterData);

            // Create recipients for sent newsletters
            if ($newsletter->status === 'sent') {
                $activeSubscribers = NewsletterSubscriber::where('status', 'active')->get();
                foreach ($activeSubscribers as $subscriber) {
                    NewsletterRecipient::create([
                        'newsletter_id' => $newsletter->id,
                        'subscriber_id' => $subscriber->id,
                        'sent_at' => $newsletter->sent_at,
                        'opened_at' => rand(0, 1) ? $newsletter->sent_at->addHours(rand(1, 24)) : null,
                        'clicked_at' => rand(0, 1) && rand(0, 1) ? $newsletter->sent_at->addHours(rand(1, 48)) : null,
                        'status' => 'sent',
                    ]);
                }
            }
        }
    }
}
