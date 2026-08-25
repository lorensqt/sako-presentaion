<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CoMakerRequestMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $borrowerName;
    public string $coMakerName;
    public string $loanTypeName;
    public float $requestedAmount;

    /**
     * Create a new message instance.
     */
    public function __construct(string $borrowerName, string $coMakerName, string $loanTypeName, float $requestedAmount)
    {
        $this->borrowerName = $borrowerName;
        $this->coMakerName = $coMakerName;
        $this->loanTypeName = $loanTypeName;
        $this->requestedAmount = $requestedAmount;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Endorsement Requested: Co-Maker for ' . $this->borrowerName . "'s Loan Application",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.comaker-request',
        );
    }
}
