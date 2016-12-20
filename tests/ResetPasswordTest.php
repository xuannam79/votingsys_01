<?php

use App\Models\User;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ResetPasswordTest extends TestCase
{
    public function testEmailInvalid()
    {
        $this->visit('/password/reset')
            ->type(str_random(10), 'email')
            ->press(trans('passwords.send_password_reset_link'))
            ->seePageIs('password/reset');
    }

    public function testEmailValid()
    {
        $user = factory(User::class)->create();
        $this->visit('/password/reset')
            ->type($user->email, 'email')
            ->press(trans('passwords.send_password_reset_link'))
            ->seePageIs('password/reset');
    }
}
