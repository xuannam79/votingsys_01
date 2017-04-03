<?php

use App\Models\Link;
use App\Models\Poll;
use App\Models\User;
use App\Services\PassportService;

class EditLinkPoll extends TestCase
{
    public $passportService;

    public function __construct()
    {
        $this->passportService = new PassportService();
    }

    /*
     * Unit test api update link success
     * */
    public function testUpdateLinkPollSuccess()
    {
        $user = User::create([
            'email' => str_random(15) . '@gmail.com',
            'name' => str_random(10),
            'password' => str_random(10),
            'is_active' => config('settings.is_active'),
        ]);

        $poll = factory(Poll::class)->create([
            'user_id' => $user->id,
        ]);

        $linkUser = Link::create([
            'link_admin' => config('settings.link_poll.vote'),
            'poll_id' => $poll->id,
            'token' => str_random(10),
        ]);

        $linkAdmin = Link::create([
            'link_admin' => config('settings.link_poll.admin'),
            'poll_id' => $poll->id,
            'token' => str_random(10),
        ]);

        $this->patch('api/v1/updateLink', [
            'oldLinkUser' => $linkUser->token,
            'oldLinkAdmin' => $linkAdmin->token,
            'newLinkUser' => str_random(11),
            'newLinkAdmin' => str_random(11),
        ], [
            'HTTP_Authorization' => 'Bearer ' . $this->passportService->getTokenByUser($user)
        ]);

        $this->seeStatusCode(API_RESPONSE_CODE_OK);
    }

    /*
     * Unit test api update same old link
     **/
    public function testSameOldLink()
    {
        $user = User::create([
            'email' => str_random(15) . '@gmail.com',
            'name' => str_random(10),
            'password' => str_random(10),
            'is_active' => config('settings.is_active'),
        ]);

        $poll = factory(Poll::class)->create([
            'user_id' => $user->id,
        ]);

        $linkUser = Link::create([
            'link_admin' => config('settings.link_poll.vote'),
            'poll_id' => $poll->id,
            'token' => str_random(10),
        ]);

        $linkAdmin = Link::create([
            'link_admin' => config('settings.link_poll.admin'),
            'poll_id' => $poll->id,
            'token' => str_random(10),
        ]);

        $this->patch('api/v1/updateLink', [
            'oldLinkUser' => $linkUser->token,
            'oldLinkAdmin' => $linkAdmin->token,
            'newLinkUser' => $linkUser->token,
            'newLinkAdmin' => str_random(11),
        ], [
            'HTTP_Authorization' => 'Bearer ' . $this->passportService->getTokenByUser($user)
        ]);

        $this->seeJson([
            'status' => API_RESPONSE_CODE_UNPROCESSABLE,
            'error' => true,
            'messages' => [trans('link.message.old_link_and_new_link_of_user_not_different')]
        ]);
    }

    /*
     * Unit test api update same new link
     **/
    public function testSameNewLink()
    {
        $user = User::create([
            'email' => str_random(15) . '@gmail.com',
            'name' => str_random(10),
            'password' => str_random(10),
            'is_active' => config('settings.is_active'),
        ]);

        $poll = factory(Poll::class)->create([
            'user_id' => $user->id,
        ]);

        $linkUser = Link::create([
            'link_admin' => config('settings.link_poll.vote'),
            'poll_id' => $poll->id,
            'token' => str_random(10),
        ]);

        $linkAdmin = Link::create([
            'link_admin' => config('settings.link_poll.admin'),
            'poll_id' => $poll->id,
            'token' => str_random(10),
        ]);

        $this->patch('api/v1/updateLink', [
            'oldLinkUser' => $linkUser->token,
            'oldLinkAdmin' => $linkAdmin->token,
            'newLinkUser' => str_random(11),
            'newLinkAdmin' => $linkAdmin->token,
        ], [
            'HTTP_Authorization' => 'Bearer ' . $this->passportService->getTokenByUser($user)
        ]);

        $this->seeJson([
            'status' => API_RESPONSE_CODE_UNPROCESSABLE,
            'error' => true,
            'messages' => [trans('link.message.old_link_and_new_link_of_admin_not_different')]
        ]);
    }
}
