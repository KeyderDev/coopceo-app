<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ScheduleMail extends Mailable
{
    use Queueable, SerializesModels;

    public $schedules;

    /**
     * Create a new message instance.
     */
    public function __construct($schedules)
    {
        $this->schedules = $schedules;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this
            ->subject('Tu horario de trabajo')
            ->markdown('emails.schedule')
            ->with(['schedules' => $this->schedules]);
    }
}
