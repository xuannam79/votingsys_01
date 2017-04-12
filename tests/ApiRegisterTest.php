<?php

use Illuminate\Http\UploadedFile;

class ApiRegisterTest extends TestCase
{
    /*
     * unit test register success
     *
     * @param string name
     * @param string email
     * @param string chatwork_id
     * @param int gender
     * @param string password
     * @param string password_confirmation
     * @param file avatar
     *
     * @expect response status 200
     * */
    public function testRegiterSuccess()
    {
        $password = str_random(10);
        $this->call('POST', '/api/v1/register', [
            'name' => str_random(20),
            'email' => 'phuocbv1802@gmail.com',
            'chatwork_id' => str_random(10),
            'gender' => config('settings.gender_constant.other'),
            'password' => $password,
            'password_confirmation' => $password,
        ], [], [
            'avatar' => new UploadedFile(public_path(config('settings.image_default_path')),
                config('settings.avatar_default'), 'image/jpeg', null, null, true),
        ]);

        $this->assertResponseStatus(API_RESPONSE_CODE_OK);
    }

    /*
     * unit test email not valid
     *
     * @param string name
     * @param string email
     * @param string chatwork_id
     * @param int gender
     * @param string password
     * @param string password_confirmation
     * @param file avatar
     *
     * @expect response status 302
     * because email correct not format
     * */
    public function testEmailNotValid()
    {
        $password = str_random(10);
        $this->call('POST', '/api/v1/register', [
            'name' => str_random(20),
            'email' => str_random(20),
            'chatwork_id' => str_random(10),
            'password' => $password,
            'password_confirmation' => $password,
            'gender' => config('settings.gender_constant.other'),
        ], [], [
            'avatar' => new UploadedFile(public_path(config('settings.image_default_path')),
                config('settings.avatar_default'), 'image/jpeg', null, null, true),
        ]);

        $this->assertResponseStatus(API_RESPONSE_CODE_MOVED_TEMPORARILY);
    }

    /*
    * unit test email not exist
    *
    * @param string name
    * @param string chatwork_id
    * @param int gender
    * @param string password
    * @param string password_confirmation
    * @param file avatar
    *
    * @expect response status 302
    * */
    public function testNameNotExist()
    {
        $password = str_random(10);
        $this->call('POST', '/api/v1/register', [
            'email' => 'phuocbv1802@gmail.com',
            'chatwork_id' => str_random(10),
            'password' => $password,
            'password_confirmation' => $password,
            'gender' => config('settings.gender_constant.other'),
        ], [], [
            'avatar' => new UploadedFile(public_path(config('settings.image_default_path')),
                config('settings.avatar_default'), 'image/jpeg', null, null, true),
        ]);

        $this->assertResponseStatus(API_RESPONSE_CODE_MOVED_TEMPORARILY);
    }

    /*
    * unit test password not exist
    *
    * @param string name
    * @param string email
    * @param string chatwork_id
    * @param int gender
    * @param file avatar
    *
    * @expect response status 302
    * */
    public function testPasswordNotExist()
    {
        $this->call('POST', '/api/v1/register', [
            'name' => str_random(20),
            'email' => 'phuocbv1802@gmail.com',
            'chatwork_id' => str_random(10),
            'gender' => config('settings.gender_constant.other'),
        ], [], [
            'avatar' => new UploadedFile(public_path(config('settings.image_default_path')),
                config('settings.avatar_default'), 'image/jpeg', null, null, true),
        ]);

        $this->assertResponseStatus(API_RESPONSE_CODE_MOVED_TEMPORARILY);
    }

    /*
    * unit test email not exist
    *
    * @param string name
    * @param string email
    * @param string chatwork_id
    * @param int gender
    * @param string password
    * @param string password_confirmation
    * @param file avatar
    *
    * @expect response status 302
    * */
    public function testEmailNotExist()
    {
        $password = str_random(10);
        $this->call('POST', '/api/v1/register', [
            'name' => str_random(20),
            'email' => str_random(10) . '@gmail.com',
            'chatwork_id' => str_random(10),
            'gender' => config('settings.gender_constant.other'),
            'password' => $password,
            'password_confirmation' => $password,
        ], [], [
            'avatar' => new UploadedFile(public_path(config('settings.image_default_path')),
                config('settings.avatar_default'), 'image/jpeg', null, null, true),
        ]);

        $this->assertResponseStatus(API_RESPONSE_CODE_INTER_SERVER_ERROR);
    }
}
