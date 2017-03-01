<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class DeleteVotedPoll extends Mailable
{
    use Queueable, SerializesModels;

    protected $link;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($linkAdmin)
    {
        $this->link = $linkAdmin;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('layouts.delete_all_participant_mail', ['link' => $this->link])
            ->subject(trans('label.mail.delete_participant.subject'));
    }
}
