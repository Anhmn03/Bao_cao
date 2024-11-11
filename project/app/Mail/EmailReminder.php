<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;

class EmailReminder extends Mailable
{
    use Queueable, SerializesModels;

    private User $user;
    private User $reminderTime;
    /**
     * Create a new message instance.
     */
    public function __construct(User $user)
{
    $this->user = $user; // Gán đúng đối tượng $user
    // $this->reminderTime = $reminderTime;
}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Email nhắc nhở chấm công ',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'fe_email.email_reminder',
            with: [
                'user' => $this->user,  // Thêm dấu phẩy ở đây
                // 'reminder_time' => $this->user->reminder_time,
                
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
}
