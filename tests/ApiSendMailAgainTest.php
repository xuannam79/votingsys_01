<?php

use App\Models\User;
use App\Models\Poll;
use App\Models\Setting;
use App\Models\Link;

class ApiSendMailAgainTest extends TestCase
{
    /*
     * unit test poll null
     *
     * @param int pollId
     *
     * @expect status code 404
     * @expect error true
     * */
    public function testPollNull()
    {
        $this->post('api/v1/send-mail-again', [
            'pollId' => random_int(1000000000, 2000000000),
        ]);
        $this->assertResponseStatus(API_RESPONSE_CODE_NOT_FOUND);
        $this->seeJson([
            'error' => true,
            'messages' => [
                trans('polls.message.not_found_polls'),
            ],
        ]);
    }

    /*
     * unit test send email again success
     *
     * @param int pollId
     *
     * @expect status code 200
     * @expect error false
     * */
    public function testSendEmailAgainSuccess()
    {
        $user = factory(User::class)->create([
            'email' => str_random(20) . '@gmail.com',
        ]);
        $poll = factory(Poll::class)->create([
            'email' => $user->email,
        ]);
        Link::create([
            'poll_id' => $poll->id,
            'token' => str_random(10),
            'link_admin' => config('settings.link_poll.vote'),
        ]);
         Link::create([
            'poll_id' => $poll->id,
            'token' => str_random(10),
            'link_admin' => config('settings.link_poll.admin'),
        ]);
        factory(Setting::class)->create([
            'poll_id' => $poll->id,
            'key' => config('settings.setting.set_limit'),
        ]);
        factory(Setting::class)->create([
            'poll_id' => $poll->id,
            'key' => config('settings.setting.set_password'),
        ]);
        $this->post('api/v1/send-mail-again', [
            'pollId' => random_int(1000000000, 2000000000),
        ]);
        $this->post('api/v1/send-mail-again', [
            'pollId' => $poll->id,
        ]);
        $this->assertResponseStatus(API_RESPONSE_CODE_OK);
        $this->seeJson([
            'error' => false,
        ]);
    }
}
