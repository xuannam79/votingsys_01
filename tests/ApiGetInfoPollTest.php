<?php

use App\Models\Poll;
use App\Models\User;
use App\Models\Link;

class ApiGetInfoPollTest extends TestCase
{
    /*
     * test get poll success
     *
     * @param string token of link
     *
     * @expect status code 200
     * */
    public function testGetPollSuccess()
    {
        $user = factory(User::class)->create();
        $poll = factory(Poll::class)->create([
            'user_id' => $user->id,
            'status' => config('settings.status.open'),
        ]);
        $link = Link::create([
            'token' => str_random(10),
            'poll_id' => $poll->id,
            'link_admin' => config('settings.link_poll.vote'),
        ]);

        $this->get('api/v1/link/poll-info/' . $link->token);
        $this->seeStatusCode(API_RESPONSE_CODE_OK);
    }

    /*
     * test link not exist
     *
     * @param string token of link
     *
     * @expect status code 422
     * because link not exist
     * */
    public function testLinkNotExist()
    {
        $this->get('api/v1/link/poll-info/' . str_random(10));
        $this->seeJson([
            'status' => API_RESPONSE_CODE_UNPROCESSABLE,
            'error' => true,
            'messages' => [trans('polls.message.not_found_polls')]
        ]);
    }

    /*
     * test closed poll
     *
     * @param string token of link
     *
     * @expect status code 422
     * because poll was closed
     * */
    public function testClosedPoll()
    {
        $user = factory(User::class)->create();
        $poll = factory(Poll::class)->create([
            'user_id' => $user->id,
            'status' => config('settings.status.close'),
        ]);
        $link = Link::create([
            'token' => str_random(10),
            'poll_id' => $poll->id,
            'link_admin' => config('settings.link_poll.vote'),
        ]);

        $this->get('api/v1/link/poll-info/' . $link->token);
        $this->seeJson([
            'status' => API_RESPONSE_CODE_UNPROCESSABLE,
            'error' => true,
            'messages' => [trans('polls.message_poll_closed')]
        ]);
    }
}
