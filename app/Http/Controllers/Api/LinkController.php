<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\ApiController;
use App\RepositoriesApi\Contracts\LinkRepositoryInterface;

class LinkController extends ApiController
{
    protected $linkRepository;

    public function __construct(LinkRepositoryInterface $linkRepository)
    {
        $this->linkRepository = $linkRepository;
    }

    public function show($token)
    {
        $link = $this->linkRepository->findBy('token', $token)->first();

        if (!$token || !$link) {
            return $this->falseJson(API_RESPONSE_CODE_UNPROCESSABLE, trans('polls.message.not_found_polls'));
        }

        $poll = $link->poll->withoutAppends();

        if (!$poll->status) {
            return $this->falseJson(API_RESPONSE_CODE_UNPROCESSABLE, trans('polls.message_poll_closed'));
        }

        $poll->load('user', 'settings', 'options.participants', 'options.users', 'comments', 'links');

        $data = [
            'poll' => $poll,
            'countParticipant' => $poll->countParticipants(),
            'countComments' => $poll->comments()->count(),
            'result_voted' => $poll->countVotesWithOption(),
        ];

        return $this->trueJson($data);
    }

    public function update(Request $request)
    {
        $data = $request->only([
            'oldLinkUser',
            'oldLinkAdmin',
            'newLinkUser',
            'newLinkAdmin',
        ]);

        if ($data['oldLinkUser'] == $data['newLinkUser']) {
            return $this->falseJson(
                API_RESPONSE_CODE_UNPROCESSABLE,
                trans('link.message.old_link_and_new_link_of_user_not_different')
            );
        }

        if ($data['oldLinkAdmin'] == $data['newLinkAdmin']) {
            return $this->falseJson(
                API_RESPONSE_CODE_UNPROCESSABLE,
                trans('link.message.old_link_and_new_link_of_admin_not_different')
            );
        }

        if ($data['newLinkUser'] && $this->linkRepository->findBy('token', $data['newLinkUser'])->first()) {
            return $this->falseJson(API_RESPONSE_CODE_UNPROCESSABLE, trans('link.message.new_link_user_exist'));
        }

        if ($data['newLinkAdmin'] && $this->linkRepository->findBy('token', $data['newLinkAdmin'])->first()) {
            return $this->falseJson(API_RESPONSE_CODE_UNPROCESSABLE, trans('link.message.new_link_admin_exist'));
        }

        $arrayLinks = [];

        if ($data['oldLinkUser']) {
            $arrayLinks['oldLinkUser'] = $this->linkRepository->findWhere([
                'token' => $data['oldLinkUser'],
                'link_admin' => config('settings.link_poll.vote'),
            ])->first();
        }

        if ($data['oldLinkAdmin']) {
            $arrayLinks['oldLinkAdmin'] = $this->linkRepository->findWhere([
                'token' => $data['oldLinkAdmin'],
                'link_admin' => config('settings.link_poll.admin'),
            ])->first();
        }

        if (!$arrayLinks['oldLinkUser'] || !$arrayLinks['oldLinkAdmin']) {
            return $this->falseJson(
                API_RESPONSE_CODE_UNPROCESSABLE,
                trans('settings.message.link_user_or_admin_not_exist')
            );
        }

        if ($arrayLinks['oldLinkUser']->poll->poll_id != $arrayLinks['oldLinkAdmin']->poll->poll_id) {
            return $this->falseJson(API_RESPONSE_CODE_UNPROCESSABLE, trans('link.message.two_link_are_not_one_poll'));
        }

        if ($arrayLinks['oldLinkUser']->poll->user_id != auth()->user()->id) {
            return $this->falseJson(API_RESPONSE_CODE_UNPROCESSABLE, trans('link.message.poll_is_not_current_user'));
        }

        if (!$this->linkRepository->updateLinkUserAndAdmin($arrayLinks, $data)) {
            return $this->falseJson(API_RESPONSE_CODE_UNPROCESSABLE, trans('link.message.update_link_not_success'));
        }

        return $this->trueJson([trans('link.message.update_link_success')]);
    }

    public function checkLinkExist(Request $request)
    {
        $link = $this->linkRepository->findBy('token', $request->only('token'));

        if ($link->count()) {
            return $this->falseJson(API_RESPONSE_CODE_UNPROCESSABLE, trans('polls.message.link_exists'));
        }

        return $this->trueJson(true, trans('polls.message.link_valid'));
    }

    public function checkLinkOfAdmin(Request $request)
    {
        $data = $request->only('token');

        if (!$data['token']) {
            return $this->falseJson(API_RESPONSE_CODE_UNPROCESSABLE, trans('polls.message.not_param_token'));
        }

        $link = $this->linkRepository->findBy('token', $data)->first();

        if (!$link) {
            return $this->falseJson(API_RESPONSE_CODE_UNPROCESSABLE, trans('activity.message.not_found_link'));
        }

        if ($link->link_admin != config('settings.link_poll.admin')) {
            return $this->falseJson(API_RESPONSE_CODE_UNPROCESSABLE, trans('link.message.not_link_admin'));
        }

        return $this->trueJson(true);
    }

    public function getInfo($token)
    {
        $link = $this->linkRepository->findBy('token', $token)->first();

        if (!$link) {
            return $this->falseJson(API_RESPONSE_CODE_UNPROCESSABLE, trans('polls.message.not_found_polls'));
        }

        $poll = $link->poll->withoutAppends()->load('user', 'settings', 'options', 'links');

        if (!$poll->status) {
            return $this->falseJson(API_RESPONSE_CODE_UNPROCESSABLE, trans('polls.message_poll_closed'));
        }

        $poll->options->each(function ($option) use ($poll) {
            $option->name = "$poll->title; $option->name";
        });

        return $this->trueJson(['poll' => $poll]);
    }
}
