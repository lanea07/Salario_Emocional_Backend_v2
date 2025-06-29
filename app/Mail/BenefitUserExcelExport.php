<?php

namespace App\Mail;

use App\Exports\BenefitUserExport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class BenefitUserExcelExport extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    protected array $data;

    /**
     * Create a new message instance.
     */
    public function __construct(array $data)
    {
        $this->data = $data;
        $this->afterCommit();
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Resumen de Beneficios',
            replyTo: 'juancamilo.soto@outlook.com'
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.excelExportMail',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [
            Attachment::fromData(
                fn() =>
                Excel::raw(new BenefitUserExport($this->data), \Maatwebsite\Excel\Excel::XLSX),
                'Beneficios.xlsx'
            )->withMime('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')

        ];
    }

    public function failed($error)
    {
        Log::error($error);
    }
}
