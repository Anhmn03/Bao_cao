<?php

namespace App\Console\Commands;

use App\Http\Controllers\Salary_caculate;
use Illuminate\Console\Command;

class RUnSalaryCalculation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'salary:work';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Chạy tính lương cho toàn bộ nhân viên vào cuối ngày';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Tính lương cho nhân viên');
        $Salary_caculate = new Salary_caculate();
        $Salary_caculate->calculateSalariesForAllEmployees();
        $this->info('Tính lương cho nhân viên hoàn tất');
    }
}
