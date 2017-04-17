<?php

use App\Models\Poll;
use App\Models\Link;
use App\Models\User;
use App\Models\Setting;
use App\Models\Comment;
use App\Models\Option;
use App\Models\Vote;
use App\Models\Participant;
use App\Models\ParticipantVote;

class ApiShowPollTest extends TestCase
{
    /*
     * unit test link null
     *
     * @param string token
     *
     * @expect status code 422
     * @expect error true
     * */
    public function testLinkNull()
    {
        $this->get('api/v1/link/' . str_random(20));
        $this->assertResponseStatus(API_RESPONSE_CODE_UNPROCESSABLE);
        $this->seeJson([
            'error' => true,
            'messages' => [
                trans('polls.message.not_found_polls'),
            ],
        ]);
    }

    /*
     * unit test closed poll
     *
     * @param string token
     *
     * @expect status code 422
     * @expect error true
     * */
    public function testClosedPoll()
    {
        $poll = factory(Poll::class)->create([
            'status' => config('settings.status.close'),
        ]);
        $link = Link::create([
            'poll_id' => $poll->id,
            'token' => str_random(10),
            'link_admin' => config('settings.link_poll.vote'),
        ]);
        $this->get('api/v1/link/' . $link->token);
        $this->assertResponseStatus(API_RESPONSE_CODE_UNPROCESSABLE);
        $this->seeJson([
            'error' => true,
            'messages' => [
                trans('polls.message_poll_closed'),
            ]
        ]);
    }

    /*
     * unit test show poll success
     *
     * @param string token
     *
     * @expect status code 200
     * @expect error false
     * */
    public function testShowPollSuccess()
    {
        $user = factory(User::class)->create([
            'email' => str_random(10) . '@gmail.com',
        ]);
        $userComment = factory(User::class)->create([
            'email' => str_random(10) . '@gmail.com',
        ]);
        $poll = factory(Poll::class)->create([
            'user_id' => $user->id,
            'status' => config('settings.status.open'),
        ]);
        factory(Setting::class)->create([
            'poll_id' => $poll->id,
        ]);
        factory(Comment::class)->create([
            'user_id' => $userComment->id,
            'poll_id' => $poll->id,
        ]);
        $optionOne = factory(Option::class)->create([
            'poll_id' => $poll->id,
        ]);
        $optionTwo = factory(Option::class)->create([
            'poll_id' => $poll->id,
        ]);
        Vote::create([
            'user_id' => $userComment->id,
            'option_id' => $optionOne->id,
        ]);
        $participant = Participant::create([
            'user_id' => $userComment->id,
            'ip_address' => str_random(10),
            'name' => str_random(10),
            'email' => str_random(10) . '@gmail.com',
        ]);
        ParticipantVote::create([
            'option_id' => $optionTwo->id,
            'participant_id' => $participant->id,
        ]);
        $link = Link::create([
            'poll_id' => $poll->id,
            'token' => str_random(10),
            'link_admin' => config('settings.link_poll.vote'),
        ]);
        $this->get('api/v1/link/' . $link->token);
        $this->assertResponseStatus(API_RESPONSE_CODE_OK);
        $this->seeJson([
            'error' => false,
        ]);
    }
}
