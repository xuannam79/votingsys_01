<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class CloseOrReOpenPoll extends Mailable
{
    use Queueable, SerializesModels;

    protected $poll;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($poll)
    {
        $this->poll = $poll;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $this->poll->withoutAppends();

        $link = $this->poll->getAdminLink();

        $view = $this->poll->status ? 'layouts.open_poll_mail' : 'layouts.close_poll_mail';

        $subject = $this->poll->status ? trans('label.mail.open_poll.subject') : trans('label.mail.close_poll.subject');

        return $this->view($view, compact('link'))->subject($subject);
    }
}
