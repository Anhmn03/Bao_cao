<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class JustificationRejected extends Mailable
{
    use Queueable, SerializesModels;
    public $user;
    public $justification;
    public $rejectionReason;
    public $userJustification;
    public $userDate;
    public $userDepartment;
    /**
     * Create a new message instance.
     */
    public function __construct($user, $justification, $rejectionReason, $userJustification, $userDate, $userDepartment)
    {
        $this->user = $user;
        $this->justification = $justification;
        $this->rejectionReason = $rejectionReason;
        $this->userJustification = $userJustification;
        $this->userDate = $userDate;
        $this->userDepartment = $userDepartment;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Quản lý chấm công',
        );
    }
    public function build()
    {
        return $this->subject('Lý do giải trình bị từ chối')
                    ->view('fe_email.justificationRejected');
    }
    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'fe_email.justificationRejected',
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
