<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class FeedbackTest extends TestCase
{
    /*
     * Unit test api feedback success
     *
     * @param name
     * @param email
     * @param feedback
     *
     * */
    public function testFeedbackSuccess()
    {
        $this->post('api/v1/feedback', [
            'name' => str_random(10),
            'email' => 'buivanphuoc1802@gmail.com',
            'feedback' => str_random(10),
        ]);
        $this->seeStatusCode(API_RESPONSE_CODE_OK);
    }

    /*
     * Unit test api feedback error
     *
     * @param name
     * @param email
     * @param feedback
     *
     * */
    public function testFeedbackError()
    {
        $this->post('/api/v1/feedback', [
            'name' => str_random(10),
            'email' => str_random(10) . '@gmail.com',
            'feedback' => str_random(10),
        ]);
        $this->seeStatusCode(API_RESPONSE_CODE_UNPROCESSABLE);
    }
}
