<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RejectionNotification extends Mailable
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
            subject: 'Email Từ Chối Đơn Xin Nghỉ',
        );
    }

    /**
     * Get the message content definition.
     */
    public function build()
    {
        return $this->subject('Đơn xin nghỉ phép của bạn đã bị từ chối')
            ->view('fe_eamil.reject_leave')
            ->with([
                'leaveRequest' => $this->leaveRequest,
            ]);
    }
     public function content(): Content
    {
        return new Content(
            view: 'fe_email.reject_leave',
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
