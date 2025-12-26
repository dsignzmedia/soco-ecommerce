<?php

namespace App\Mail;

use App\Models\Admin\Master\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderStatusUpdateMail extends Mailable
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
            'order_placed' => 'Order Placed',
            'processing' => 'Order Processing',
            'packed' => 'Order Packed',
            'shipped' => 'Order Shipped',
            'delivered' => 'Order Delivered',
        ];

        $statusLabel = $statusLabels[$this->order->order_status] ?? ucfirst(str_replace('_', ' ', $this->order->order_status));

        return new Envelope(
            subject: 'Order Update: ' . $statusLabel . ' - ' . $this->order->order_number . ' - The Skool Store',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.order-status-update',
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


