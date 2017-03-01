<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\ApiController;
use App\RepositoriesApi\Contracts\LinkRepositoryInterface;
use App\RepositoriesApi\Contracts\PollRepositoryInterface;

class ParticipantController extends ApiController
{
    protected $linkRepository;
    protected $pollRepository;

    public function __construct(
        LinkRepositoryInterface $linkRepository,
        PollRepositoryInterface $pollRepository
    ) {
        $this->linkRepository = $linkRepository;
        $this->pollRepository = $pollRepository;
    }

    public function deleteAll($token)
    {
        $link = $this->linkRepository->findBy('token', $token)->first();

        if (!$link || !isset($link->poll)) {
            return $this->falseJson(API_RESPONSE_CODE_NOT_FOUND, trans('messages.error.not_found'));
        }

        $poll = $link->poll->load('options.votes', 'options.participants', 'links');

        if ($this->pollRepository->resetVoted($poll)) {
            return $this->trueJson(null, ['message' => trans('polls.delete_all_participants_successfully')]);
        }

        return $this->falseJson(API_RESPONSE_CODE_UNPROCESSABLE, trans('polls.link_not_found'));
    }
}
