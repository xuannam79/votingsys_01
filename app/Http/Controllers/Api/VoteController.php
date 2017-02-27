<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\ApiController;
use App\RepositoriesApi\Contracts\PollRepositoryInterface;
use App\Http\Requests\Api\VoteRequest;

class VoteController extends ApiController
{
    protected $pollRepository;

    public function __construct(PollRepositoryInterface $pollRepository)
    {
        $this->pollRepository = $pollRepository;
    }

    public function store(VoteRequest $request)
    {
        $input = $request->only('option', 'name', 'email', 'idPoll');

        $poll = $this->pollRepository->find($input['idPoll']);

        if (!$poll) {
            return $this->falseJson(API_RESPONSE_CODE_UNPROCESSABLE, trans('polls.message.not_found_polls'));
        }

        // Load eager loading
        $poll->load('options.users', 'options.participants', 'settings');

        $settings = $this->pollRepository->getSettingsPoll($poll);
        $config = config('settings.setting');

        // Check over voted poll
        if ($settings[$config['set_limit']]['value']
            && $settings[$config['set_limit']]['value'] < $poll->countParticipants()
        ) {
            return $this->falseJson(API_RESPONSE_CODE_UNPROCESSABLE, trans('polls.message_poll_limit'));
        }

        // Was poll closed ?
        if ($poll->isClosed()) {
            return $this->falseJson(API_RESPONSE_CODE_UNPROCESSABLE, trans('polls.message_poll_closed'));
        }

        // Time close poll to out
        if ($poll->isTimeOut()) {
            return $this->falseJson(API_RESPONSE_CODE_UNPROCESSABLE, trans('polls.message_poll_time_out'));
        }

        // check setting's poll have require input name
        if ($settings[$config['required_name']]['status'] && !$input['name']) {
            return $this->falseJson(API_RESPONSE_CODE_UNPROCESSABLE, trans('polls.message_validate_name'));
        }

        // check setting's poll have require input email
        if ($settings[$config['required_email']]['status'] && !$input['email']) {
            return $this->falseJson(API_RESPONSE_CODE_UNPROCESSABLE, trans('polls.message_required_email'));
        }

        // check setting's poll have require input name and email
        if ($settings[$config['required_name_and_email']]['status'] && !$input['email'] && !$input['name']) {
            return $this->falseJson(API_RESPONSE_CODE_UNPROCESSABLE, trans('polls.message_validate_name_and_email'));
        }

        // Save participant voted
        if ($this->pollRepository->vote($poll, $input)) {
            return $this->trueJson(null, ['message' => trans('polls.voted_poll')]);
        }

        return $this->falseJson(API_RESPONSE_CODE_INTER_SERVER_ERROR, trans('polls.message_client.error_occurs'));
    }
}
