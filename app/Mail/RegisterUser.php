<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class RegisterUser extends Mailable
{
    use Queueable, SerializesModels;

    protected $user;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user)
    {
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $data = [
            'name' => $this->user->name,
            'link' => action('LinkController@index', [
                'userId' => $this->user->id,
                'tokenRegister' => $this->user->token_verification,
            ]),
        ];

        return $this->view('layouts.register_mail', $data)
            ->subject(trans('label.mail.register.subject'));
    }
}
