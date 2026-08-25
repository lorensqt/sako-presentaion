<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LoanReturnedMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $borrowerName;
    public string $loanTypeName;
    public float $requestedAmount;
    public string $remarks;
    public string $loanId;

    /**
     * Create a new message instance.
     */
    public function __construct(string $borrowerName, string $loanTypeName, float $requestedAmount, string $remarks, string $loanId)
    {
        $this->borrowerName = $borrowerName;
        $this->loanTypeName = $loanTypeName;
        $this->requestedAmount = $requestedAmount;
        $this->remarks = $remarks;
        $this->loanId = $loanId;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Revision Required: Your Loan Application LN-' . $this->loanId . ' Has Been Returned',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.loan-returned',
        );
    }
}
