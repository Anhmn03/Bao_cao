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
    public $user;
    public $justificationReason;
   


    /**
     * Create a new message instance.
     */
    public function __construct($user, $justificationReason)
    {
        $this->user = $user;
        $this->justificationReason = $justificationReason;
    }        public function build()
        {
            return $this->from($this->user->email) // Sử dụng email của người dùng làm người gửi
                        ->subject('Giải trình lý do')
                        ->view('fe_email/justification_submitted')
                        ->with([
                            'userName' => $this->user->name,
                            'justificationReason' => $this->justificationReason,
                        ]);
        }
    /**
     * Get the message envelope.
     */
        public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Giải trình lý do',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'fe_email/justification_submitted',
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
