<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Employee;
use Carbon\Carbon;

class SendBirthdaySMS extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sms:send-birthday';
    protected $description = 'Send SMS to employees whose birthdays are today';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = Carbon::now()->format('m-d');

        // Fetch employees whose birthday is today
        $employees = Employee::whereRaw('DATE_FORMAT(dob, "%m-%d") = ?', [$today])->get();
        $employeesNonBD = Employee::whereRaw('DATE_FORMAT(dob, "%m-%d") != ?', [$today])->get();

        $companyName = "Delft Imaging";
        $messageTemplate = "Happy Birthday, {employee_name}! From all of us at {company_name}, we appreciate ";
        $messageTemplate .= "your hard work and dedication as our {employee_designation}. Wishing you a fantastic day filled ";
        $messageTemplate .= "with joy and success! - {company_name} Team.";

        $messageTemplateNonBD = "Today is {employee_name}'s birthday! Join us in wishing our {employee_designation} a fantastic day.";
        $messageTemplateNonBD .= "Feel free to send him your best wishes! - {company_name} Team.";

        foreach ($employees as $employee) {
            $message = str_replace(
                ['{employee_name}', '{company_name}', '{employee_designation}'],
                [$employee->name, $companyName, $employee->designation->name ?? ''],
                $messageTemplate
            );

            $birthdayDetails[] = [
                'name' => $employee->name,
                'designation' => $employee->designation->name ?? ''
            ];

            \App\Actions\SMS\SendSMS::sendSMS($employee->phone, $message);
        }

        foreach ($employeesNonBD as $empl) {
            foreach ($birthdayDetails as $details) {
                $message = str_replace(
                    ['{employee_name}', '{company_name}', '{employee_designation}'],
                    [$details['name'], $companyName, $details['designation']],
                    $messageTemplateNonBD
                );
            }

            // Send SMS
            \App\Actions\SMS\SendSMS::sendSMS($empl->phone, $message);
        }

        $this->info('Birthday SMS sent to all employees whose birthdays are today.');
    }
}
