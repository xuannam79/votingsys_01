<?php


class ApiResetPasswordTest extends TestCase
{
    public function testResetPasswordSuccess()
    {
        $this->post('api/v1/password/reset', [
            'email' => 'buivanphuoc1802@gmail.com',
        ]);
        $this->seeStatusCode(API_RESPONSE_CODE_OK);
    }

    public function testEmailNotExist()
    {
        $this->post('api/v1/password/reset', [
            'email' => str_random(3) . '@gmail.com',
        ]);
        $this->seeJson([
            'status' => API_RESPONSE_CODE_UNPROCESSABLE,
            'error' => true,
        ]);
    }

    public function testEmaiValida()
    {
        $this->post('api/v1/password/reset', [
            'email' => str_random(10),
        ]);
        $this->seeJson([
            'status' => API_RESPONSE_CODE_UNPROCESSABLE,
            'error' => true,
        ]);
    }
}
