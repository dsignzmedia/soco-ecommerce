<?php

namespace App\Mail;

use App\Models\Admin\Master\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RefundProcessedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $order;
    public $refundAmount;
    public $refundId;

    /**
     * Create a new message instance.
     */
    public function __construct(Order $order, $refundAmount, $refundId = null)
    {
        $this->order = $order;
        $this->refundAmount = $refundAmount;
        $this->refundId = $refundId;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Refund Processed - Order ' . $this->order->order_number . ' - The Skool Store',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.refund-processed',
            with: [
                'order' => $this->order,
                'refundAmount' => $this->refundAmount,
                'refundId' => $this->refundId,
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


