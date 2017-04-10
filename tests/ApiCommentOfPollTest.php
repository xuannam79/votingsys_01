<?php

use App\Models\Poll;

class ApiCommentOfPollTest extends TestCase
{
    /*
     * unit test comment success
     *
     * @param int idPoll
     * @param string name
     * @param string content
     *
     * @expect response status 200, error false
     * */
    public function testCommentSuccess()
    {
        $poll = factory(Poll::class)->create();

        $this->post('api/v1/poll/comment', [
            'idPoll' => $poll->id,
            'name' => str_random(10),
            'content' => str_random(10),
        ]);
        $this->assertResponseStatus(API_RESPONSE_CODE_OK);
        $this->seeJson([
            'error' => false,
            'messages' => [
                trans('polls.message_client.success_comment')
            ]
        ]);
    }

    /*
     * unit test poll not exist
     *
     * @param int idPoll
     * @param string name
     * @param string content
     *
     * @expect response status 404, error true
     * */
    public function testPollNotExist()
    {
        $this->post('api/v1/poll/comment', [
            'idPoll' => random_int(1000000000, 2000000000),
            'name' => str_random(10),
            'content' => str_random(10),
        ]);
        $this->assertResponseStatus(API_RESPONSE_CODE_NOT_FOUND);
        $this->seeJson([
            'error' => true,
            'messages' => [
                trans('polls.message.not_found_polls')
            ]
        ]);
    }
}
