<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\ApiController;
use App\RepositoriesApi\Contracts\PollRepositoryInterface;
use App\Http\Requests\Api\CommentRequest;

class CommentController extends ApiController
{
    protected $pollRepository;

    public function __construct(PollRepositoryInterface $pollRepository)
    {
        $this->pollRepository = $pollRepository;
    }

    public function store(CommentRequest $request)
    {
        $input = $request->only(['idPoll', 'name', 'content']);

        $poll = $this->pollRepository->find($input['idPoll']);

        if (!$poll) {
            return $this->falseJson(API_RESPONSE_CODE_NOT_FOUND, trans('polls.message.not_found_polls'));
        }

        $comment = $this->pollRepository->comment($poll, $input);

        if (!$comment) {
            return $this->falseJson(API_RESPONSE_CODE_UNPROCESSABLE, trans('polls.message_client.fail_comment'));
        }

        return $this->trueJson($comment, ['message' => trans('polls.message_client.success_comment')]);
    }
}
