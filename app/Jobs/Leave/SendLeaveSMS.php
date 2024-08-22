<?php

namespace App\Jobs\Leave;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Actions\SMS\SendSMS;
use DateTime;

class SendLeaveSMS implements ShouldQueue
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

        $msg = "Hi " . $this->leave->employees->name . ",";
        $msg .= " Your leave request has been approved from " . $startDateFormatted . " to " . $endDateFormatted . ". ";
        $msg .= $this->leave->releave->name . " will be your relieving officer during your absence.";
        $msg .= " Please ensure a smooth handover before your leave begins.";
        $msg .= "Enjoy your time off! Delft Imaging Team.";

        $phone = $this->leave->employees->phone;

        SendSMS::sendSMS($phone, $msg);
    }
}
