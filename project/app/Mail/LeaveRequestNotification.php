<?php

namespace App\Mail;

use App\Models\Leave_request;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LeaveRequestNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $leaveRequest;

    /**
     * Create a new message instance.
     */
    public function __construct(Leave_request $leaveRequest)
    {
        $this->leaveRequest = $leaveRequest;
    }
    public function build()
    {
        $senderName = $this->leaveRequest->user->name;
        $senderEmail = $this->leaveRequest->user->email;
        return $this->from($senderEmail, $senderName) // Sử dụng email của người dùng làm người gửi
            ->subject('Giải trình lý do')
            ->view('fe_email.leave_request_notification');
    }
    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Đơn xin nghỉ phép',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'fe_email.leave_request_notification',
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
