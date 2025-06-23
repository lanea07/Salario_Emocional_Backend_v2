<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class NotifyNewBenefitRequestToLeader extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $benefitUser;

    /**
     * Create a new message instance.
     */
    public function __construct(
        protected $data
    ) {
        $this->benefitUser = $data[0];
        $this->afterCommit();
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Uno de tus colaboradores ha solicitado un beneficio',
            replyTo: 'juan.soto@flamingo.com.co'
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.notifyNewBenefitRequestToLeader',
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
