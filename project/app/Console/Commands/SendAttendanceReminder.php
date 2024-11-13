<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Mail\EmailReminder;
use App\Mail\EmailCheckoutReminder;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

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

    
    public function handle()
    {
        // Lấy tất cả người dùng có `reminder_time` đúng với thời gian hiện tại
        $currentTime = Carbon::now()->format('H:i');
        $users = User::where('reminder_time', $currentTime)->get();

        foreach ($users as $user) {
            Mail::to($user->email)->send(new EmailReminder($user, $user->reminder_time));
            $this->info("Email sent to {$user->email} at {$user->reminder_time}");
        }
        $checkoutTime = '09:23';
        if ($currentTime === $checkoutTime) {
            $checkoutUsers = User::all();  // Lấy tất cả người dùng để gửi nhắc nhở checkout
            foreach ($checkoutUsers as $user) {
                Mail::to($user->email)->send(new EmailCheckoutReminder($user, 'checkout'));
                $this->info("Check-out email sent to {$user->email} at $checkoutTime");
            }
        }
        return Command::SUCCESS;
    }
}
