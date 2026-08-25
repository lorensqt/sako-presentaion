<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LoanReleasedMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $borrowerName;
    public string $loanTypeName;
    public float $releasedAmount;
    public int $termMonths;
    public string $loanId;

    /**
     * Create a new message instance.
     */
    public function __construct(string $borrowerName, string $loanTypeName, float $releasedAmount, int $termMonths, string $loanId)
    {
        $this->borrowerName = $borrowerName;
        $this->loanTypeName = $loanTypeName;
        $this->releasedAmount = $releasedAmount;
        $this->termMonths = $termMonths;
        $this->loanId = $loanId;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Congratulations! Your Loan Application LN-' . $this->loanId . ' Has Been Released',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.loan-released',
        );
    }
}
