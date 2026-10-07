<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WithdrawalRejectedMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $memberName;
    public string $referenceNo;
    public float $amount;
    public string $channel;
    public string $remarks;
    public ?string $adminName;
    public string $rejectedAt;

    /**
     * Create a new message instance.
     */
    public function __construct(
        string $memberName,
        string $referenceNo,
        float $amount,
        string $channel,
        string $remarks,
        ?string $adminName = null,
        ?string $rejectedAt = null
    ) {
        $this->memberName = $memberName;
        $this->referenceNo = $referenceNo;
        $this->amount = $amount;
        $this->channel = $channel;
        $this->remarks = $remarks;
        $this->adminName = $adminName;
        $this->rejectedAt = $rejectedAt ?? now()->format('M d, Y h:i A');
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Notice of Declined Withdrawal Request: ' . $this->referenceNo,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.withdrawal-rejected',
        );
    }
}
