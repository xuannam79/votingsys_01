<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\ApiController;
use App\RepositoriesApi\Contracts\PollRepositoryInterface;

class CheckMailController extends ApiController
{
    protected $pollRepository;

    public function __construct(PollRepositoryInterface $pollRepository)
    {
        $this->pollRepository = $pollRepository;
    }

    public function sendMail(Request $request)
    {
        $input = $request->only(['pollId']);

        $poll = $this->pollRepository->find($input['pollId']);

        if (!$poll) {
            return $this->falseJson(API_RESPONSE_CODE_NOT_FOUND, trans('polls.message.not_found_polls'));
        }

        $pollInfo = $this->pollRepository->sendMailAgain($poll);

        if (!$pollInfo) {
            return $this->falseJson(API_RESPONSE_CODE_NOT_FOUND, trans('polls.message.not_found_polls'));
        }

        return $this->trueJson($pollInfo, trans('polls.message_client.send_email_success'));
    }
}
