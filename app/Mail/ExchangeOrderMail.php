<?php

namespace App\Mail;

use App\Models\Admin\Master\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ExchangeOrderMail extends Mailable
{
    use Queueable, SerializesModels;

    public $exchangeOrder;
    public $originalOrder;

    /**
     * Create a new message instance.
     */
    public function __construct(Order $exchangeOrder, Order $originalOrder)
    {
        $this->exchangeOrder = $exchangeOrder;
        $this->originalOrder = $originalOrder;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Exchange Order Generated - ' . $this->exchangeOrder->order_number . ' - The Skool Store',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.exchange-order',
            with: [
                'exchangeOrder' => $this->exchangeOrder,
                'originalOrder' => $this->originalOrder,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}


