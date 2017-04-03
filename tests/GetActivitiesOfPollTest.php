<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Models\Link;
use App\Models\User;
use App\Models\Poll;
use App\Models\Activity;

class GetActivitiesOfPollTest extends TestCase
{
    /*
     * Unit test get activities success
     *
     * @param token
     *
     * @result success
     * */
    public function testGetActivitiesSuccess()
    {
        $user = factory(User::class)->create();
        $userVote = factory(User::class)->create();

        $poll = factory(Poll::class)->create([
            'user_id' => $user->id,
        ]);

        $link = Link::create([
            'poll_id' => $poll->id,
            'token' => str_random(10),
            'link_admin' => 0,
        ]);

        $activity = Activity::create([
            'poll_id' => $poll->id,
            'user_id' => $userVote->id,
            'type' => 1,
            'name' => str_random(10),
        ]);

        $this->get('api/v1/showActivity?token=' . $link->token);
        $this->seeStatusCode(API_RESPONSE_CODE_OK);
    }

    /*
     * Unit test get activities not token
     *
     * @param
     *
     * @result check unprocessable
     * due to not token
     * */
    public function testGetActivitiesNotToken()
    {
        $this->get('api/v1/showActivity');
        $this->seeJson([
            'status' => API_RESPONSE_CODE_UNPROCESSABLE,
            'error' => true,
            'messages' => [
                trans('polls.message.not_param_token')
            ],
        ]);
    }

    /*
     * Unit test link not exist
     *
     * @param token
     *
     * @result unprocessable
     * because link not exist with param token
     * */
    public function testLinkNotExist()
    {
        $this->get('api/v1/showActivity?token=' . str_random(10));
        $this->seeJson([
            'status' => API_RESPONSE_CODE_UNPROCESSABLE,
            'error' => true,
            'messages' => [
                trans('activity.message.not_found_link')
            ],
        ]);
    }
}
