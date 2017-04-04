<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Models\User;

class ShowClosedPollTest extends TestCase
{
    /*
     * unit test api show closed poll success
     *
     * @param access_token
     *
     * @expect list closed poll
     * */
    public function testShowClosedPollSuccess()
    {
        $user = factory(User::class)->create();

        $this->get('api/v1/getClosedPolls', [
            'HTTP_Authorization' => 'Bearer ' . $user->createToken('myToken')->accessToken
        ]);

        $this->seeStatusCode(API_RESPONSE_CODE_OK);
    }

    /*
     * unit test api show closed poll error
     *
     * @param
     *
     * @expect false
     * */
    public function testShowClosedPollError()
    {
        $this->get('api/v1/getClosedPolls');
        $this->seeStatusCode(API_RESPONSE_CODE_MOVED_TEMPORARILY);
    }
}
