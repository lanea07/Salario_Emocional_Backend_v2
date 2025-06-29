<?php

namespace App\Mail;

use App\Enums\BenefitDecisionEnum;
use App\Services\BenefitUserService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class BenefitDecision extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public $benefitUser
    ) {
        $this->afterCommit();
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Decisión tomada en tu beneficio solicitado',
            replyTo: 'juancamilo.soto@outlook.com'
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.benefitDecision',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return $this->benefitUser->is_approved === BenefitDecisionEnum::APPROVED
            ? [
                Attachment::fromData(
                    fn() =>
                    BenefitUserService::generateICS($this->benefitUser),
                    'invite.ics'
                )
            ]
            : [];
    }

    public function failed($error)
    {
        Log::error($error);
    }
}
