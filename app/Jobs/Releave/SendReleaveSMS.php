<?php

namespace App\Jobs\Releave;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Actions\SMS\SendSMS;
use DateTime;

class SendReleaveSMS implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    protected $leave;

    public function __construct($leave)
    {
        $this->leave = $leave;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $startDate = new DateTime($this->leave->start_date);
        $endDate = new DateTime($this->leave->end_date);

        $startDateFormatted = $startDate->format('jS F');
        $endDateFormatted = $endDate->format('jS F Y');

        $msg = "Hi " . $this->leave->releave->name . ",";
        $msg .= " Please be informed that you will be the relieving officer for " . $this->leave->employees->name;
        $msg .= " during his/her upcoming leave from " . $startDateFormatted . " to " . $endDateFormatted . ". ";
        $msg .= "Kindly ensure a smooth transition and handle his/her responsibilities in his/her absence.";
        $msg .= " Thank you for your support! Delft Imaging Team.";

        $phone = $this->leave->employees->phone;

        SendSMS::sendSMS($phone, $msg);
    }
}
