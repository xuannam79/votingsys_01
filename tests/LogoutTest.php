<?php

use App\Models\User;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class LogoutTest extends TestCase
{
    public function testUserViewHistoryPoll()
    {
        $user = factory(User::class)->create();
        $this->visit('/login')
            ->type($user->email, 'email')
            ->type('password', 'password')
            ->press(trans('label.login'))
            ->click(trans('label.logout'))
            ->seePageIs('/');
    }
}
