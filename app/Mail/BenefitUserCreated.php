<?php

namespace App\Mail;

use App\Models\BenefitUser;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class BenefitUserCreated extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public BenefitUser $newBenefitUser;
    public Collection | null $bancoHoras;
    public Collection | null $miViernes;

    /**
     * Create a new message instance.
     */
    public function __construct(
        protected $data
    ) {
        $this->newBenefitUser = $data[0];
        $this->bancoHoras = $data[1];
        $this->miViernes = $data[2];
        $this->afterCommit();
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Nuevo Beneficio Registrado',
            replyTo: 'juan.soto@flamingo.com.co'
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.newBenefitUserCreated',
            with: [
                'newBenefitUser' => $this->newBenefitUser,
                'bancoHoras' => $this->bancoHoras,
                'miViernes' => $this->miViernes
            ]
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

    public function failed($error)
    {
        Log::error($error);
    }
}
