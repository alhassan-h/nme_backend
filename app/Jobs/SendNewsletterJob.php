<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\NewsletterMail;
use App\Models\Newsletter;
use App\Models\NewsletterSubscriber;
use App\Models\NewsletterRecipient;
use Exception;

class SendNewsletterJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Newsletter $newsletter;
    public int $tries = 3;
    public int $timeout = 300; // 5 minutes

    /**
     * Create a new job instance.
     */
    public function __construct(Newsletter $newsletter)
    {
        $this->newsletter = $newsletter;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            Log::info("Starting newsletter send job for newsletter ID: {$this->newsletter->id}");

            // Get all active subscribers
            $subscribers = NewsletterSubscriber::where('status', 'active')->get();

            if ($subscribers->isEmpty()) {
                Log::warning("No active subscribers found for newsletter ID: {$this->newsletter->id}");
                $this->updateNewsletterStatus('sent');
                return;
            }

            $totalSent = 0;
            $totalFailed = 0;

            foreach ($subscribers as $subscriber) {
                try {
                    // Check if recipient already exists to avoid duplicates
                    $existingRecipient = NewsletterRecipient::where('newsletter_id', $this->newsletter->id)
                        ->where('subscriber_id', $subscriber->id)
                        ->whereNot('status', 'bounced')
                        ->first();

                    if ($existingRecipient) {
                        Log::info("Newsletter already sent to subscriber {$subscriber->email}");
                        continue;
                    }

                    // Send the email
                    Mail::to($subscriber->email)->send(new NewsletterMail($this->newsletter, $subscriber));

                    // Create recipient record
                    NewsletterRecipient::create([
                        'newsletter_id' => $this->newsletter->id,
                        'subscriber_id' => $subscriber->id,
                        'sent_at' => now(),
                        'status' => 'sent',
                    ]);

                    $totalSent++;
                    Log::info("Newsletter sent to: {$subscriber->email}");

                    // Add small delay to prevent overwhelming the mail server
                    sleep(1);

                } catch (Exception $e) {
                    Log::error("Failed to send newsletter to {$subscriber->email}: " . $e->getMessage());

                    // Create recipient record with failed status
                    NewsletterRecipient::create([
                        'newsletter_id' => $this->newsletter->id,
                        'subscriber_id' => $subscriber->id,
                        'sent_at' => now(),
                        'status' => 'bounced',
                    ]);

                    $totalFailed++;
                }
            }

            // Update newsletter status
            $this->updateNewsletterStatus('sent');

            Log::info("Newsletter send job completed. Sent: {$totalSent}, Failed: {$totalFailed}");

        } catch (Exception $e) {
            Log::error("Newsletter send job failed: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(Exception $exception): void
    {
        Log::error("Newsletter send job permanently failed for newsletter ID: {$this->newsletter->id}. Error: " . $exception->getMessage());

        // Update newsletter status to failed
        $this->updateNewsletterStatus('failed');
    }

    /**
     * Update the newsletter status.
     */
    private function updateNewsletterStatus(string $status): void
    {
        $this->newsletter->update([
            'status' => $status,
            'sent_at' => $status === 'sent' ? now() : null,
        ]);
    }
}