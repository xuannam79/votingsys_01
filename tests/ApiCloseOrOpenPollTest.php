<?php

use App\Models\User;
use App\Models\Poll;

class ApiCloseOrOpenPoll extends TestCase
{
    /*
     * unit test close poll success
     *
     * @param pollId
     *
     * @expect response status 200, error false
     * @expect response message Poll closed successfully
     * */
    public function testClosePollSuccess()
    {
        $user = User::whereEmail(config('mail.from.address'))->first();

        if (!$user) {
            $user = factory(User::class)->create([
                'email' => config('mail.from.address'),
            ]);
        }

        $poll = factory(Poll::class)->create([
            'email' => $user->email,
            'user_id' => $user->id,
            'status' => config('settings.status.open'),
        ]);

        $this->delete('api/v1/poll/' . $poll->id);
        $this->assertResponseStatus(API_RESPONSE_CODE_OK)
            ->seeJson([
                'error' => false,
                'messages' => [
                    trans('polls.close_poll_successfully')
                ]
            ]);
    }

    /*
     * unit test open poll success
     *
     * @param pollId
     *
     * @expect response status 200, error false
     * @expect response message Reopen poll sucessfully
     * */
    public function testOpenPollSuccess()
    {
        $user = User::whereEmail(config('mail.from.address'))->first();

        if (!$user) {
            $user = factory(User::class)->create([
                'email' => config('mail.from.address'),
            ]);
        }

        $poll = factory(Poll::class)->create([
            'email' => $user->email,
            'user_id' => $user->id,
            'status' => config('settings.status.close'),
        ]);

        $this->delete('api/v1/poll/' . $poll->id);
        $this->assertResponseStatus(API_RESPONSE_CODE_OK)
            ->seeJson([
                'error' => false,
                'messages' => [
                    trans('polls.reopen_poll_successfully')
                ]
            ]);
    }

    /*
     * unit test poll not exist
     *
     * @param pollId
     *
     * @expect response status 404, error false
     * @expect response message not found poll
     * */
    public function testPollNotExist()
    {
        $this->delete('api/v1/poll/' . random_int(1000000000, 2000000000));

        $this->assertResponseStatus(API_RESPONSE_CODE_NOT_FOUND)
            ->seeJson([
                'error' => true,
                'messages' => [
                    trans('polls.message.not_found_polls')
                ]
            ]);
    }
}
