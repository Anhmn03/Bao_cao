<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Mail\EmailReminder;
use App\Mail\EmailCheckoutReminder;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;

class SendAttendanceReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:attendance-reminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Gửi email nhắc nhở đến tất cả người dùng vào thời gian đã định';

    public function __construct()
    {
        parent::__construct();
    }

    
    /**
     * Handle execution of the command to send attendance reminders.
     *
     * @return int
     */
    public function handle(): int
    {
        $currentTime = Carbon::now()->format('H:i');
        $users = User::where('reminder_time', $currentTime)->get();

        foreach ($users as $user) {
            Mail::to($user->email)->send(new EmailReminder($user, $user->reminder_time));
            $this->info("Email sent to {$user->email} at {$user->reminder_time}");
        }

        $reminderTimeCheckout = DB::table('settings')->where('key', 'reminder_timeCheckout')->value('value');
    
    
        // Send reminder emails if it's the reminder time for checkout
        if ($currentTime === $reminderTimeCheckout) {
            $checkoutReminderUsers = User::all();
            foreach ($checkoutReminderUsers as $user) {
                Mail::to($user->email)->send(new EmailCheckoutReminder($user, 'reminder_checkout'));
                $this->info("Reminder checkout email sent to {$user->email} at $reminderTimeCheckout");
            }
        }

        return Command::SUCCESS;
    }
}