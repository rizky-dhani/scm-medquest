<?php

namespace App\Mail;

use App\Models\TemperatureDeviation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TemperatureDeviationNotification extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public TemperatureDeviation $temperatureDeviation,
        public bool $isHighPriority = false
    ) {}

    public function envelope(): Envelope
    {
        $priorityLabel = $this->isHighPriority ? 'HIGH PRIORITY - ' : '';
        $subject = $priorityLabel . "Temperature Deviation Alert";

        return new Envelope(
            subject: $subject,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.temperature-deviation-notification',
            with: [
                'temperatureDeviation' => $this->temperatureDeviation,
                'isHighPriority' => $this->isHighPriority,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
