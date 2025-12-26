<?php

namespace App\Mail;

use App\Models\Admin\Master\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaymentStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    public $order;
    public $previousStatus;

    /**
     * Create a new message instance.
     */
    public function __construct(Order $order, $previousStatus = null)
    {
        $this->order = $order;
        $this->previousStatus = $previousStatus;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $statusLabels = [
            'paid' => 'Payment Confirmed',
            'pending' => 'Payment Pending',
            'failed' => 'Payment Failed',
            'refunded' => 'Payment Refunded',
        ];

        $statusLabel = $statusLabels[$this->order->payment_status] ?? ucfirst($this->order->payment_status);

        return new Envelope(
            subject: $statusLabel . ' - Order ' . $this->order->order_number . ' - The Skool Store',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.payment-status',
            with: [
                'order' => $this->order,
                'previousStatus' => $this->previousStatus,
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


