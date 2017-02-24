<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class InviteParticipant extends Mailable
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
        $data = [
            'linkVote' => $this->poll->getUserLink(),
            'poll' => $this->poll,
            'password' => $this->poll->getPassword(),
        ];

        return $this->view(config('settings.view.participant_mail'), $data)
            ->subject(trans('label.mail.participant_vote.subject'));
    }
}
