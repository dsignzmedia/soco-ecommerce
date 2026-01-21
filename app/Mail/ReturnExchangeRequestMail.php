<?php

namespace App\Mail;

use App\Models\Admin\Master\ReturnExchangeRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReturnExchangeRequestMail extends Mailable
{
    use Queueable, SerializesModels;

    public $returnRequest;
    public $status;

    /**
     * Create a new message instance.
     */
    public function __construct(ReturnExchangeRequest $returnRequest, $status = 'submitted')
    {
        $this->returnRequest = $returnRequest;
        $this->status = $status;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subjects = [
            'submitted' => 'Exchange Request Submitted',
            'approved' => 'Exchange Request Approved',
            'rejected' => 'Exchange Request Rejected',
            'received' => 'Item Received',
            'completed' => 'Exchange Request Completed',
        ];

        $subject = $subjects[$this->status] ?? 'Exchange Request Update';

        return new Envelope(
            subject: $subject . ' - Order ' . $this->returnRequest->order->order_number . ' - The Skool Store',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.return-exchange-request',
            with: [
                'returnRequest' => $this->returnRequest,
                'status' => $this->status,
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


