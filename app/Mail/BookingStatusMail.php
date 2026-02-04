<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * AC Chill - Booking Status Update Mail
 */
class BookingStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    public $booking;
    public $status;
    public $customerName;

    /**
     * Create a new message instance.
     */
    public function __construct($booking, $status, $customerName)
    {
        $this->booking = $booking;
        $this->status = $status;
        $this->customerName = $customerName;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'AC Chill - Booking Status Update',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.booking-status',
            with: [
                'booking' => $this->booking,
                'status' => $this->status,
                'customerName' => $this->customerName,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}
