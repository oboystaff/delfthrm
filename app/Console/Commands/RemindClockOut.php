<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Employee;
use App\Models\AttendanceEmployee;
use App\Models\Leave;
use DateTime;
use DateTimeZone;
use App\Actions\SMS\SendSMS;

class RemindClockOut extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'remind:clockout';
    protected $description = 'Send SMS reminders to employees to clock out at the end of the day';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Get today's date and current time in Y-m-d and H:i:s formats respectively
        $today = (new DateTime('now', new DateTimeZone('UTC')))->format('Y-m-d');

        // Fetch employees who are not on leave today
        $employeesOnLeave = Leave::where('start_date', '<=', $today)
            ->where('end_date', '>=', $today)
            ->pluck('employee_id');

        // Fetch employees who have already clocked out today
        $clockedOutEmployees = AttendanceEmployee::whereDate('clock_out', $today)
            ->pluck('employee_id');

        // Get employees who are not on leave and haven't clocked out yet
        $employeesToRemind = Employee::whereNotIn('id', $employeesOnLeave)
            ->whereNotIn('id', $clockedOutEmployees)
            ->get();

        $messageTemplate = "It's 5:00 PM! Please remember to clock out before you leave Office.";
        $messageTemplate .= "Have a great evening!";

        foreach ($employeesToRemind as $employee) {
            $message = $messageTemplate;

            $phone = $employee->phone;
            SendSMS::sendSMS($phone, $message);
        }

        // Log the message sent
        $this->info("Clockout reminders sent successfully.");
    }
}
