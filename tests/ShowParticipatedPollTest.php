<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Models\User;

class ShowParticipatedPollTest extends TestCase
{
    /*
     * unit test api show participated poll success
     *
     * @param access_token
     *
     * @expect list participated poll
     * */
    public function testShowParticipatedPollSuccess()
    {
        $user = factory(User::class)->create();

        $this->get('api/v1/getParticipatedPolls', [
            'HTTP_Authorization' => 'Bearer ' . $user->createToken('myToken')->accessToken
        ]);

        $this->seeStatusCode(API_RESPONSE_CODE_OK);
    }

    /*
     * unit test api show participated poll error
     *
     * @param
     *
     * @result false
     * */
    public function testShowParticipatedPollError()
    {
        $this->get('api/v1/getParticipatedPolls');
        $this->seeStatusCode(API_RESPONSE_CODE_MOVED_TEMPORARILY);
    }
}
