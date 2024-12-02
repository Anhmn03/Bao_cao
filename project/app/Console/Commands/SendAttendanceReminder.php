<?php

namespace App\Console\Commands;

use App\Http\Controllers\Salary_caculate;
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
        $currentDayOfWeek = Carbon::now()->dayOfWeek; // Lấy ngày trong tuần (0: Chủ nhật, 6: Thứ 7)
        
        // Kiểm tra xem có phải là ngày cuối tuần không (Thứ 7 hoặc Chủ nhật)
        if ($currentDayOfWeek !== 0 && $currentDayOfWeek !== 6) {
            // Gửi email nhắc nhở theo giờ
            $users = User::where('reminder_time', $currentTime)->get();
        
            foreach ($users as $user) {
                Mail::to($user->email)->send(new EmailReminder($user, $user->reminder_time));
                $this->info("Email sent to {$user->email} at {$user->reminder_time}");
            }
        
            // Kiểm tra thời gian nhắc nhở chấm công và gửi email nếu đúng thời gian
            $reminderTimeCheckout = DB::table('settings')->where('key', 'reminder_timeCheckout')->value('value');
            
            if ($currentTime === $reminderTimeCheckout) {
                $checkoutReminderUsers = User::all();
                foreach ($checkoutReminderUsers as $user) {
                    Mail::to($user->email)->send(new EmailCheckoutReminder($user, 'reminder_checkout'));
                    $this->info("Reminder checkout email sent to {$user->email} at $reminderTimeCheckout");
                }
            }
        } else {
            $this->info("No reminder emails sent today as it's a weekend.");
        }
        
        $currentTime = Carbon::now()->format('H:i');

        // Lấy thời gian tính lương từ bảng settings
        $salaryCalculationTime = DB::table('settings')->where('key', 'salary_calculation_time')->value('value');
    
        // Kiểm tra nếu thời gian hiện tại trùng với thời gian tính lương
        if ($currentTime === $salaryCalculationTime) {
            // Khởi tạo controller của bạn
            $salaryCalculate = new Salary_caculate();
            $salaryCalculate->calculateSalariesForAllEmployees();
            $this->info("Salary calculated at $salaryCalculationTime");
        }

        return Command::SUCCESS;
    }
}