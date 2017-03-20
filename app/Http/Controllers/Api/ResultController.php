<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\ApiController;
use App\RepositoriesApi\Contracts\LinkRepositoryInterface;
use App\RepositoriesApi\Contracts\PollRepositoryInterface;

class ResultController extends ApiController
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

    public function show($token)
    {
        $link = $this->linkRepository->findBy('token', $token)->first();

        if (!$link || !$link->poll) {
            return $this->falseJson(API_RESPONSE_CODE_UNPROCESSABLE, trans('polls.message.not_found_polls'));
        }

        $poll = $link->poll->withoutAppends();

        $results = $this->pollRepository->resultsVoted($poll);

        if (!$results) {
            return $this->falseJson(API_RESPONSE_CODE_UNPROCESSABLE, trans('polls.message_client.error_occurs'));
        }

        return $this->trueJson($results);
    }

    public function resultDetail($token)
    {
        $link = $this->linkRepository->findBy('token', $token)->first();

        if (!$link || !$link->poll) {
            return $this->falseJson(API_RESPONSE_CODE_UNPROCESSABLE, trans('polls.message.not_found_polls'));
        }

        $poll = $link->poll->withoutAppends();

        return $this->trueJson($this->pollRepository->getResultDetail($poll));
    }
}
