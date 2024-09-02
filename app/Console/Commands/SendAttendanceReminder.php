<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Employee;
use App\Models\AttendanceEmployee;
use App\Models\Leave;
use Carbon\Carbon;
use App\Actions\SMS\SendSMS;

class SendAttendanceReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attendance:reminder';
    protected $description = 'Send a reminder to employees to clock in their attendance every morning at 8:00 AM';

    /**
     * The console command description.
     *
     * @var string
     */

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = Carbon::today();

        // If today is Saturday or Sunday, adjust to the next Monday
        if ($today->isWeekend()) {
            $today->next(Carbon::MONDAY);
        }

        // Format the date as needed, for example:
        $todayFormatted = $today->format('Y-m-d');

        // Fetch employees who are on leave today
        $employeesOnLeave = Leave::whereDate('start_date', '<=', $todayFormatted)
            ->whereDate('end_date', '>=', $todayFormatted)
            ->pluck('employee_id')
            ->toArray();

        // Fetch employees who have already clocked in to$todayFormatted
        $employeesClockedIn = AttendanceEmployee::whereDate('created_at', $todayFormatted)
            ->pluck('employee_id')
            ->toArray();

        // Fetch employees who are not on leave and haven't clocked in yet
        $employeesToRemind = Employee::whereNotIn('id', array_merge($employeesOnLeave, $employeesClockedIn))
            ->get();

        $companyName = "Delft Imaging";
        $messageTemplate = "Please remember to clock in by 8:00 AM every day to ensure your attendance is recorded. ";
        $messageTemplate .= "Let’s stay on track and keep up the great work! {company_name} Team.";

        // Send SMS reminders
        foreach ($employeesToRemind as $employee) {
            $message = str_replace(['{company_name}'], [$companyName], $messageTemplate);

            SendSMS::sendSMS($employee->phone, $message);
        }

        $this->info('Attendance reminders sent successfully.');
    }
}
