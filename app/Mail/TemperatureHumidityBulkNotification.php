<?php

declare(strict_types=1);

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

final class TemperatureHumidityBulkNotification extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public int $count,
        public string $notificationType // 'review' or 'acknowledge'
    ) {}

    public function envelope(): Envelope
    {
        $subject = $this->notificationType === 'review' 
            ? "📊 {$this->count} Temperature & Humidity Records Ready for Review"
            : "✅ {$this->count} Temperature & Humidity Records Ready for Acknowledgment";

        return new Envelope(
            subject: $subject,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.temperature-humidity-bulk-notification',
            with: [
                'count' => $this->count,
                'notificationType' => $this->notificationType,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
