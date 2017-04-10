<?php

use App\Models\Link;
use App\Models\Poll;

class ApiShowVoteDetailTest extends TestCase
{
    /*
     * unit test show vote detail success
     *
     * @param token
     *
     * @expect response status 200
     * */
    public function testShowVotedetailSuccess()
    {
        $poll = factory(Poll::class)->create();
        $link = Link::create([
            'poll_id' => $poll->id,
            'token' => str_random(10),
            'link_admin' => config('settings.link_poll.vote'),
        ]);

        $this->get('api/v1/poll/result-detail/' . $link->token);
        $this->assertResponseStatus(API_RESPONSE_CODE_OK);
    }

    /*
     * unit test link not exist
     *
     * @param token
     *
     * @expect response status 422, error true
     * */
    public function testLinkNotExist()
    {
        $this->get('api/v1/poll/result-detail/' . str_random(10));
        $this->assertResponseStatus(API_RESPONSE_CODE_UNPROCESSABLE);
        $this->seeJson([
            'error' => true,
            'messages' => [
                trans('polls.message.not_found_polls')
            ]
        ]);
    }

    /*
     * unit test poll not exist
     *
     * @param token
     *
     * @expect response status 422, error true
     * */
    public function testPollNotExist()
    {
        $link = Link::create([
            'poll_id' => random_int(10000000, 20000000),
            'token' => str_random(10),
            'link_admin' => config('settings.link_poll.vote'),
        ]);

        $this->get('api/v1/poll/result-detail/' . $link->token);
        $this->assertResponseStatus(API_RESPONSE_CODE_UNPROCESSABLE);
        $this->seeJson([
            'error' => true,
            'messages' => [
                trans('polls.message.not_found_polls')
            ]
        ]);
    }
}
