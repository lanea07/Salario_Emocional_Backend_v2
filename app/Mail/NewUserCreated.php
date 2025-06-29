<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class NewUserCreated extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public User $user;
    public string $password;


    /**
     * Create a new message instance.
     */
    public function __construct(
        protected $data
    ) {
        $this->user = $data[0];
        $this->password = $data[1];
        $this->afterCommit();
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Bienvenido(a) a tu Salario Emocional',
            replyTo: 'juancamilo.soto@outlook.com'
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.newUserCreated',
            with: [
                'user' => $this->user,
                'password' => $this->password
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
