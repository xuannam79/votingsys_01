<?php

use App\Models\User;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class HistoryPollTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testViewHistoryPoll()
    {
        $user = factory(User::class)->create();
        $this->visit('/login')
            ->type($user->email, 'email')
            ->type('password', 'password')
            ->press(trans('label.login'))
            ->click(trans('polls.poll_history'))
            ->seePageIs('/user/poll');
    }
}
