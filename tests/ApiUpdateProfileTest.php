<?php

use App\Models\User;
use Illuminate\Http\UploadedFile;

class ApiUpdateProfileTest extends TestCase
{
    /*
     * update function success
     *
     * @params string email
     * @params string name
     * @params string password
     * @params int gender
     * @params string chatwork_id
     * @params file avatar
     *
     * @expect success update
     */
    public function testUpdateProfileSuccess()
    {
        $user = factory(User::class)->create();
        $name = config('settings.avatar_default');
        $path = public_path() . '/' . config('settings.avatar_path') . '/' . $name;
        $file = new UploadedFile($path, $name, filesize($path), null, null, true);

        $this->post('api/v1/updateProfile', [
            'email' => str_random(10) . '@gmail.com',
            'name' => str_random(10),
            'password' => bcrypt(str_random(10)),
            'gender' => null,
            'chatwork_id' => str_random(10),
            'avatar' => $file,
        ], [
            'HTTP_Authorization' => 'Bearer ' . $user->createToken('myToken')->accessToken
        ]);

        $this->seeStatusCode(API_RESPONSE_CODE_OK);
    }

    /*
    * update profile since not login
    *
    * @params string email
    * @params string name
    * @params string password
    * @params int gender
    * @params string chatwork_id
    *
    * @expect return code 302
    */
    public function testSinceNotLogin()
    {
        $this->post('api/v1/updateProfile', [
            'email' => str_random(10) . '@gmail.com',
            'name' => str_random(10),
            'password' => bcrypt(str_random(10)),
            'gender' => null,
            'chatwork_id' => str_random(10),
        ]);

        $this->seeStatusCode(API_RESPONSE_CODE_MOVED_TEMPORARILY);
    }

    /*
    * update update not password
    *
    * @params string email
    * @params string name
    * @params string password
    * @params int gender
    * @params string chatwork_id
    *
    * @expect success update
    */
    public function testUpdateNotPassword()
    {
        $user = factory(User::class)->create();

        $this->post('api/v1/updateProfile', [
            'email' => str_random(10) . '@gmail.com',
            'name' => str_random(10),
            'gender' => null,
            'chatwork_id' => str_random(10),
        ], [
            'HTTP_Authorization' => 'Bearer ' . $user->createToken('myToken')->accessToken
        ]);

        $this->seeStatusCode(API_RESPONSE_CODE_OK);
    }
}
