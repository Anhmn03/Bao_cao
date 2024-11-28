<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AcceptLeaveRequestMail extends Mailable
{
    use Queueable, SerializesModels;
    public $leaveRequest;

    /**
     * Create a new message instance.
     */
    public function __construct($leaveRequest)
    {
        $this->leaveRequest = $leaveRequest;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Chấp Nhận Đơn Xin Nghỉ',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'fe_email.accept_leave',
        );
    }
    public function build()
    {
        return $this->subject('Đơn xin nghỉ phép của bạn đã được chấp nhận')
            ->view('fe_email.accpet_leave')
            ->with([
                'leaveRequest' => $this->leaveRequest,
            ]);
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
