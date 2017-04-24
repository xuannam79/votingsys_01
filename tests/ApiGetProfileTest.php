<?php

use App\Models\User;

class ApiGetProfileTest extends TestCase
{
    /*
     * unit test not login
     *
     * @expect status coce 302
     * */
    public function testNotLogin()
    {
        $this->get('api/v1/getProfile');
        $this->assertResponseStatus(API_RESPONSE_CODE_MOVED_TEMPORARILY);
    }

    /*
     * unit test get profile success
     *
     * @expect status code 200
     * @expect error false
     * */
    public function testGetProfileSuccess()
    {
        $user = factory(User::class)->create();
        $this->get('api/v1/getProfile', [
            'HTTP_Authorization' => 'Bearer ' . $user->createToken('myToken')->accessToken
        ]);
        $this->assertResponseStatus(API_RESPONSE_CODE_OK);
        $this->seeJson([
            'error' => false,
        ]);
    }
}
