<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Mail\EmailReminder;
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

    // public function handle()
    // {
    //     // Lấy tất cả người dùng có thời gian nhắc nhở được lưu
    //     $users = User::whereNotNull('reminder_time')->get();

    //     foreach ($users as $user) {
    //         $reminderTime = Carbon::parse($user->reminder_time)->format('H:i');
            
    //         // Kiểm tra nếu thời gian hiện tại đã trôi qua thời gian nhắc nhở của người dùng
    //         $now = Carbon::now();
    //         $reminderTimeCarbon = Carbon::parse($user->reminder_time);

    //         // Nếu thời gian hiện tại đã trôi qua thời gian nhắc nhở
    //         if ($now->greaterThanOrEqualTo($reminderTimeCarbon)) {
    //             // Gửi email cho người dùng
    //             Mail::to($user->email)->send(new EmailReminder($user, $reminderTime));
    //             $this->info("Email đã được gửi cho người dùng: " . $user->email);
    //         }
    //     }
    // }
    public function handle()
    {
        // Lấy tất cả người dùng có `reminder_time` đúng với thời gian hiện tại
        $currentTime = Carbon::now()->format('H:i');
        $users = User::where('reminder_time', $currentTime)->get();

        foreach ($users as $user) {
            Mail::to($user->email)->send(new EmailReminder($user, $user->reminder_time));
            $this->info("Email sent to {$user->email} at {$user->reminder_time}");
        }

        return Command::SUCCESS;
    }
}
