<?php

namespace App\Console\Commands;

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
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $user = User::all();

        foreach ($user as $users) {
            Mail::to($users->email)->send(new EmailReminder($users));

            $this->info('Email sent to ' . $users->email);
        }
    }
}
