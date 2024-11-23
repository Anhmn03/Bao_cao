<?php

namespace App\Console\Commands;

use App\Http\Controllers\Salary_caculate;
use Illuminate\Console\Command;

class CalculateSalary extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:calculate-salary';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $salaryCalculate = new Salary_caculate();
        $salaryCalculate->calculateSalariesForAllEmployees();
        $this->info('Lương đã được tính cho tất cả nhân viên!');

    }
}
