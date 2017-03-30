<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Models\User;

class GetPollsOfUser extends TestCase
{
    public function testGetPollsSuccess()
    {
        $user = factory(User::class)->create();

        $this->get('api/v1/getPollsOfUser', [
            'HTTP_Authorization' => 'Bearer ' . $user->createToken('myToken')->accessToken
        ]);

        $this->seeStatusCode(API_RESPONSE_CODE_OK);
    }

    public function testGetPollsError()
    {
        $this->get('api/v1/getPollsOfUser');
        $this->seeStatusCode(API_RESPONSE_CODE_MOVED_TEMPORARILY);
    }
}
