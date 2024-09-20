<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ServiceRequisitionNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $officeProperty;
    public $settings;

    /**
     * Create a new message instance.
     */
    public function __construct($officeProperty, $settings)
    {
        $this->officeProperty = $officeProperty;
        $this->settings = $settings;
    }

    public function build()
    {
        return $this->from($this->settings['mail_from_address'], $this->settings['mail_from_name'])
            ->subject('New Service Requisition Request')
            ->view('email.service_requisition');
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
