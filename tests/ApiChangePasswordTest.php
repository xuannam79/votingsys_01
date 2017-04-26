<?php

use App\Models\User;
use App\Models\SocialAccount;

class ApiChangePasswordTest extends TestCase
{
    /*
     * unit test not login
     *
     * @param string old_password
     * @param string password
     * @param string password_confirmation
     *
     * @expect status code 302
     * */
    public function testNotLogin()
    {
        $newPassword = str_random(10);
        $this->post('api/v1/resetPassword', [
            'old_password' => str_random(10),
            'password' => $newPassword,
            'password_confirmation' => $newPassword,
        ]);
        $this->assertResponseStatus(API_RESPONSE_CODE_MOVED_TEMPORARILY);
    }

    /*
     * unit test password confirm
     *
     * @param string old_password
     * @param string password
     * @param string password_confirmation
     * @param string HTTP_Authorization
     *
     * @expect status code 302
     * */
    public function testPasswordConfirm()
    {
        $password = str_random(10);
        $user = factory(User::class)->create([
            'password' => $password
        ]);
        $this->post('api/v1/resetPassword', [
            'old_password' => $password,
            'password' => str_random(11),
            'password_confirmation' => str_random(10),
        ], [
            'HTTP_Authorization' => 'Bearer ' . $user->createToken('myToken')->accessToken
        ]);
        $this->assertResponseStatus(API_RESPONSE_CODE_MOVED_TEMPORARILY);
    }

    /*
     * unit test is social account
     *
     * @param string old_password
     * @param string password
     * @param string password_confirmation
     * @param string HTTP_Authorization
     *
     * @expect status code 422
     * @expect error true
     * */
    public function testIsSocialAccount()
    {
        $password = str_random(10);
        $newPassword = str_random(10);
        $user = factory(User::class)->create([
            'password' => $password
        ]);
        SocialAccount::create([
            'user_id' => $user->id,
            'provider_user_id' => random_int(10000000000, 20000000000),
            'provider' => str_random(5),
        ]);
        $this->post('api/v1/resetPassword', [
            'old_password' => $password,
            'password' => $newPassword,
            'password_confirmation' => $newPassword,
        ], [
            'HTTP_Authorization' => 'Bearer ' . $user->createToken('myToken')->accessToken
        ]);
        $this->assertResponseStatus(API_RESPONSE_CODE_UNPROCESSABLE);
        $this->seeJson([
            'error' => true,
            'messages' => [
                trans('messages.error.is_social_account'),
            ],
        ]);
    }

    /*
     * unit test old password false
     *
     * @param string old_password
     * @param string password
     * @param string password_confirmation
     * @param string HTTP_Authorization
     *
     * @expect status code 422
     * @expect error true
     * */
    public function testOldPasswordFalse()
    {
        $newPassword = str_random(10);
        $user = factory(User::class)->create([
            'password' => str_random(10)
        ]);
        $this->post('api/v1/resetPassword', [
            'old_password' => str_random(10),
            'password' => $newPassword,
            'password_confirmation' => $newPassword,
        ], [
            'HTTP_Authorization' => 'Bearer ' . $user->createToken('myToken')->accessToken
        ]);
        $this->assertResponseStatus(API_RESPONSE_CODE_UNPROCESSABLE);
        $this->seeJson([
            'error' => true,
            'messages' => [
                trans('messages.error.password_false'),
            ],
        ]);
    }

    /*
     * unit test change password success
     *
     * @param string old_password
     * @param string password
     * @param string password_confirmation
     * @param string HTTP_Authorization
     *
     * @expect status code 200
     * @expect error false
     * */
    public function testChangePasswordSuccess()
    {
        $password = str_random(10);
        $newPassword = str_random(10);
        $user = factory(User::class)->create([
            'password' => $password
        ]);
        $this->post('api/v1/resetPassword', [
            'old_password' => $password,
            'password' => $newPassword,
            'password_confirmation' => $newPassword,
        ], [
            'HTTP_Authorization' => 'Bearer ' . $user->createToken('myToken')->accessToken
        ]);
        $this->assertResponseStatus(API_RESPONSE_CODE_OK);
        $this->seeJson([
            'error' => false,
        ]);
    }
}
