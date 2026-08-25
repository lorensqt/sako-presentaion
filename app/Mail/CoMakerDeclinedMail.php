<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CoMakerDeclinedMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $borrowerName;
    public string $coMakerName;
    public string $loanTypeName;
    public float $requestedAmount;
    public ?string $remarks;

    /**
     * Create a new message instance.
     */
    public function __construct(string $borrowerName, string $coMakerName, string $loanTypeName, float $requestedAmount, ?string $remarks = null)
    {
        $this->borrowerName = $borrowerName;
        $this->coMakerName = $coMakerName;
        $this->loanTypeName = $loanTypeName;
        $this->requestedAmount = $requestedAmount;
        $this->remarks = $remarks;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Co-Maker Request Declined - ML Sako Loan Application',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.comaker-declined',
        );
    }
}
