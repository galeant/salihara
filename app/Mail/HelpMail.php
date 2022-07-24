<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class HelpMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */

    public $str_subject, $str_name, $str_email, $str_message;
    public function __construct($payload, $str_subject)
    {
        $this->str_subject = $str_subject;
        $this->str_name = $payload->name;
        $this->str_email = $payload->email;
        $this->str_message = $payload->message;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->subject($this->str_subject)
            ->view('email.help');
    }
}
