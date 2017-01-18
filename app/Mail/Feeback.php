<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class Feeback extends Mailable
{
    use Queueable, SerializesModels;

    protected $dataRequest = [];

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(array $dataRequest)
    {
        $this->dataRequest = $dataRequest;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $dataRequest = $this->dataRequest;

        $address = $dataRequest['email'];
        $name = $dataRequest['name'];
        $subject = trans('label.mail.feedback.subject');

        return $this->view('layouts.mail_feedback', compact('dataRequest'))
            ->from($address, $name)
            ->replyTo($address, $name)
            ->subject($subject);
    }
}
