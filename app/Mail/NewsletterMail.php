<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Newsletter;
use App\Models\NewsletterSubscriber;

class NewsletterMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public Newsletter $newsletter;
    public NewsletterSubscriber $subscriber;

    /**
     * Create a new message instance.
     */
    public function __construct(Newsletter $newsletter, NewsletterSubscriber $subscriber)
    {
        $this->newsletter = $newsletter;
        $this->subscriber = $subscriber;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->newsletter->subject,
            to: [$this->subscriber->email],
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.newsletter',
            with: [
                'newsletter' => $this->newsletter,
                'subscriber' => $this->subscriber,
                'unsubscribeUrl' => $this->generateUnsubscribeUrl(),
            ],
        );
    }

    /**
     * Generate unsubscribe URL for the subscriber.
     */
    private function generateUnsubscribeUrl(): string
    {
        $token = sha1($this->subscriber->email . $this->subscriber->id . config('app.key'));
        $frontendUrl = env('FRONTEND_URL', 'http://localhost:3000');
        return $frontendUrl . '/newsletter/unsubscribe?email=' . urlencode($this->subscriber->email) . '&token=' . $token;
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}