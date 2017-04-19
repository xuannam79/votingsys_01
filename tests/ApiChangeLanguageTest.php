<?php

class ApiChangeLanguageTest extends TestCase
{
    /*
     * unit test language not exist
     *
     * @param string lang
     *
     * @expect status code 404
     * @expect error true
     * */
    public function testLanguageNotExist()
    {
        $this->post('api/v1/language', [
            'lang' => str_random(3),
        ]);
        $this->assertResponseStatus(API_RESPONSE_CODE_NOT_FOUND);
        $this->seeJson([
            'error' => true,
        ]);
    }

    /*
     * unit test change language success
     *
     * @param string lang
     *
     * @expect status code 200
     * @expect error false
     * */
    public function testChangeLanguageSuccess()
    {
        $this->post('api/v1/language', [
            'lang' => config('settings.languages')[0],
        ]);
        $this->assertResponseStatus(API_RESPONSE_CODE_OK);
        $this->seeJson([
            'error' => false,
        ]);
    }
}
