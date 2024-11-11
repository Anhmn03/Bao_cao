<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class JustificationSubmitted extends Mailable
{
    use Queueable, SerializesModels;
    public $attendance;


    /**
     * Create a new message instance.
     */
    public function __construct($attendance)
    {
        $this->attendance = $attendance;
    }

    public function build()
    {
        return $this->subject('Lý do giải trình từ nhân viên')
                    ->view('fe_email.justification_submitted');
    }
    /**
     * Get the message envelope.
     */
        public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Justification Submitted',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'view.name',
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
}
