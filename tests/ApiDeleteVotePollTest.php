<?php

use App\Models\Link;
use App\Models\Poll;
use App\Models\Option;

class ApiDeleteVotePollTest extends TestCase
{
    /*
    * unit test delete vote poll success
    *
    * @param string token
    *
    * @expect response status 200
    * */
    public function testDeleteVotePollSuccess()
    {
        $poll = factory(Poll::class)->create();
        $linkVote = Link::create([
            'poll_id' => $poll->id,
            'token' => str_random(10),
            'link_admin' => config('settings.link_poll.vote'),
        ]);
        $linkAdmin = Link::create([
            'poll_id' => $poll->id,
            'token' => str_random(10),
            'link_admin' => config('settings.link_poll.admin'),
        ]);
        $option = Option::create([
            'name' => str_random(10),
            'image' => str_random(20),
            'poll_id' => $poll->id,
        ]);
        $this->delete('api/v1/poll/participants/' . $linkVote->token);
        $this->assertResponseStatus(API_RESPONSE_CODE_OK);
        $this->seeJson([
            'error' => false,
            'messages' => [
                trans('polls.delete_all_participants_successfully'),
            ]
        ]);
    }

    /*
     * unit test link not exist
     *
     * @param string token
     *
     * @expect response status 404
     * */
    public function testLinkNotExist()
    {
        $this->delete('api/v1/poll/participants/' . str_random(10));
        $this->assertResponseStatus(API_RESPONSE_CODE_NOT_FOUND);
        $this->seeJson([
            'error' => true,
            'messages' => [
                trans('messages.error.not_found'),
            ]
        ]);
    }

    /*
     * unit test poll not exist
     *
     * @param string token
     *
     * @expect response status 404
     * */
    public function testPollNotExist()
    {
        $link = Link::create([
            'poll_id' => random_int(1000000000, 2000000000),
            'token' => str_random(10),
        ]);
        $this->delete('api/v1/poll/participants/' . $link->token);
        $this->assertResponseStatus(API_RESPONSE_CODE_NOT_FOUND);
        $this->seeJson([
            'error' => true,
            'messages' => [
                trans('messages.error.not_found'),
            ]
        ]);
    }

    /*
     * unit test poll not option
     *
     * @param string token
     *
     * @expect response status 404
     * */
    public function testPollNotOption()
    {
        $poll = factory(Poll::class)->create();
        $link = Link::create([
            'token' => str_random(10),
            'poll_id' => $poll->id,
        ]);
        $this->delete('api/v1/poll/participants/' . $link->token);
        $this->assertResponseStatus(API_RESPONSE_CODE_UNPROCESSABLE);
        $this->seeJson([
            'error' => true,
            'messages' => [
                trans('polls.link_not_found'),
            ]
        ]);
    }
}
