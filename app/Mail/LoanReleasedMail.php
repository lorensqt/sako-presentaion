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
    public ?string $remarks;
    public ?string $releasingOfficerName;

    /**
     * Create a new message instance.
     */
    public function __construct(
        string $borrowerName,
        string $loanTypeName,
        float $releasedAmount,
        int $termMonths,
        string $loanId,
        ?string $remarks = null,
        ?string $releasingOfficerName = null
    ) {
        $this->borrowerName = $borrowerName;
        $this->loanTypeName = $loanTypeName;
        $this->releasedAmount = $releasedAmount;
        $this->termMonths = $termMonths;
        $this->loanId = $loanId;
        $this->remarks = $remarks;
        $this->releasingOfficerName = $releasingOfficerName;
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
