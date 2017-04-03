<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Models\Link;

class CheckLinkExistTest extends TestCase
{
    /*
     * Unit test check link success
     *
     * @param token
     *
     * @result check success
     * due to token not exist
     * */
    public function testCheckLinkSuccess()
    {
        $this->get('api/v1/checkLinkExist?token=' . str_random(10));
        $this->seeStatusCode(API_RESPONSE_CODE_OK);
    }

    /*
     * Unit test check link exist
     *
     * @param token
     *
     * @result check unprocessable
     * due to token exist
     * */
    public function testCheckLinkExist()
    {
        $link = Link::create([
            'token' => str_random(10),
            'poll_id' => 1,
            'link_admin' => 0,
        ]);
        $this->get('api/v1/checkLinkExist?token=' . $link->token);
        $this->seeJson([
            'status' => API_RESPONSE_CODE_UNPROCESSABLE,
            'error' => true,
            'messages' => [
                trans('polls.message.link_exists')
            ],
        ]);
    }

    /*
     * Unit test check link not token
     *
     * @param
     *
     * @result check unprocessable
     * due to not token
     * */
    public function testCheckLinkNotToken()
    {
        $this->get('api/v1/checkLinkExist');
        $this->seeJson([
            'status' => API_RESPONSE_CODE_UNPROCESSABLE,
            'error' => true,
            'messages' => [
                trans('polls.message.not_param_token')
            ],
        ]);
    }
}
