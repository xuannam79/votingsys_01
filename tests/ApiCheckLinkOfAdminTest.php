<?php

use App\Models\Link;

class ApiCheckLinkOfAdminTest extends TestCase
{
    /*
     * unit test token null
     *
     * @param string token null
     *
     * @expect status code 422
     * @expect error true
     * */
    public function testTokenNull()
    {
        $this->post('api/v1/checkLinkOfAdmin');
        $this->assertResponseStatus(API_RESPONSE_CODE_UNPROCESSABLE);
        $this->seeJson([
            'error' => true,
            'messages' => [
                trans('polls.message.not_param_token'),
            ],
        ]);
    }

    /*
    * unit test link not exist
    *
    * @param string token
    *
    * @expect status code 422
    * @expect error true
    * */
    public function testLinkNotExist()
    {
        $this->post('api/v1/checkLinkOfAdmin', [
            'token' => str_random(15),
        ]);
        $this->assertResponseStatus(API_RESPONSE_CODE_UNPROCESSABLE);
        $this->seeJson([
            'error' => true,
            'messages' => [
                trans('activity.message.not_found_link'),
            ],
        ]);
    }

    /*
    * unit test link vote
    *
    * @param string token
    *
    * @expect status code 422
    * @expect error true
    * */
    public function testLinkVote()
    {
        $link = Link::create([
            'poll_id' => random_int(1000, 2000),
            'token' => str_random(10),
            'link_admin' => config('settings.link_poll.vote'),
        ]);
        $this->post('api/v1/checkLinkOfAdmin', [
            'token' => $link->token,
        ]);
        $this->assertResponseStatus(API_RESPONSE_CODE_UNPROCESSABLE);
        $this->seeJson([
            'error' => true,
            'messages' => [
                trans('link.message.not_link_admin'),
            ]
        ]);
    }

    /*
    * unit test link admin
    *
    * @param string token
    *
    * @expect status code 200
    * @expect error false
    * */
    public function testLinkAdmin()
    {
        $link = Link::create([
            'poll_id' => random_int(1000, 2000),
            'token' => str_random(10),
            'link_admin' => config('settings.link_poll.admin'),
        ]);
        $this->post('api/v1/checkLinkOfAdmin', [
            'token' => $link->token,
        ]);
        $this->assertResponseStatus(API_RESPONSE_CODE_OK);
        $this->seeJson([
            'error' => false,
        ]);
    }
}
