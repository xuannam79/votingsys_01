<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\ApiController;
use App\RepositoriesApi\Contracts\PollRepositoryInterface;
use App\Http\Requests\Api\PollRequest;
use Validator;
use Auth;

class PollController extends ApiController
{
    protected $pollRepository;

    public function __construct(PollRepositoryInterface $pollRepository)
    {
        parent::__construct();
        $this->pollRepository = $pollRepository;
    }

    /**
     * get info poll with links.
     *
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function getPollDetail($id)
    {
        $poll = $this->pollRepository->getPollWithLinks($id);

        if (empty($poll)) {
            return $this->falseJson(API_RESPONSE_CODE_NOT_FOUND, trans('messages.error.not_found'));
        }

        return $this->trueJson(['poll' => $poll]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PollRequest $request)
    {
        $input = $request->only([
            'title',
            'location',
            'description',
            'name',
            'email',
            'multiple',
            'date_close',
            'optionText',
            'optionImage',
            'setting',
            'value',
            'setting_child',
            'member',
        ]);

        if ($data = $this->pollRepository->storePoll($input)) {
            return $this->trueJson($data, trans('polls.message.create_success'));
        }

        return $this->falseJson(API_RESPONSE_CODE_INTER_SERVER_ERROR, trans('polls.message.create_fail'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $button = $request->btn_edit;

        $poll = $this->pollRepository->find($id);

        if ($poll) {
            // Load eager loading
            $poll->load('options', 'settings', 'user', 'activities', 'links')->withoutAppends();

            if ($button == config('settings.btn_edit_poll.save_info')) {
                // Validate edit information poll
                $pollValidate = new PollRequest;
                $validator = Validator::make($request->all(), $pollValidate->rules(), $pollValidate->messages());

                // If fails to send message
                if ($validator->fails()) {
                    return $pollValidate->formatErrors($validator);
                }

                // Save information poll
                if ($this->pollRepository->editPoll($poll, $request->all())) {
                    return $this->trueJson($poll, trans('polls.message.update_poll_info_success'));
                }

                return $this->falseJson(API_RESPONSE_CODE_UNPROCESSABLE, trans('polls.message.update_poll_info_fail'));
            } elseif ($button == config('settings.btn_edit_poll.save_option')) {
                $input = $request->only(['optionImage', 'optionText']);

                // Save options of poll
                if ($this->pollRepository->editOption($poll, $input)) {
                    return $this->trueJson($poll->load('options'), trans('polls.message.update_option_success'));
                }

                return $this->falseJson(API_RESPONSE_CODE_UNPROCESSABLE, trans('polls.message.update_option_fail'));
            } else {
                $input = $request->only([
                    'setting',
                    'value',
                    'setting_child',
                ]);

                // Save settings of poll
                if ($this->pollRepository->addSetting($poll, $input)) {
                    return $this->trueJson($poll->load('settings'), trans('polls.message.update_setting_success'));
                }

                return $this->falseJson(API_RESPONSE_CODE_UNPROCESSABLE, trans('polls.message.update_setting_fail'));
            }
        }

        // Not found poll
        return $this->falseJson(API_RESPONSE_CODE_NOT_FOUND, trans('polls.message.not_found_polls'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $poll = $this->pollRepository->find($id);

        if (!$poll) {
            return $this->falseJson(API_RESPONSE_CODE_NOT_FOUND, trans('polls.message.not_found_polls'));
        }

        if (!$this->pollRepository->closeOrOpen($poll)) {
            return $this->falseJson(API_RESPONSE_CODE_INTER_SERVER_ERROR, trans('polls.close_poll_fail'));
        }

        if ($poll->status) {
            return $this->trueJson(null, trans('polls.reopen_poll_successfully'));
        }

        return $this->trueJson(null, trans('polls.close_poll_successfully'));
    }

    public function getPollsOfUser()
    {
        if (empty($this->currentUser)) {
            return $this->falseJson(API_RESPONSE_CODE_NOT_FOUND, trans('polls.message.not_found_user'));
        }

        $polls = $this->pollRepository->getPollsOfUser($this->currentUser->id);

        if (!$polls) {
            return $this->falseJson(API_RESPONSE_CODE_UNPROCESSABLE, trans('polls.message.polls_of_user_fail'));
        }

        return $this->trueJson($polls);
    }

    public function duplicatePoll(Request $request)
    {
        $input = $request->only(
            'title', 'location', 'description', 'name', 'email', 'chatwork_id', 'type', 'closingTime',
            'optionText', 'optionImage', 'oldImage', 'optionOldImage',
            'setting', 'value', 'setting_child',
            'member'
        );
        $input['page'] = 'duplicate';
        $data = $this->pollRepository->store($input);

        if (!$data) {
            return $this->falseJson(API_RESPONSE_CODE_UNPROCESSABLE, trans('polls.message.duplicate_poll_error'));
        }

        return $this->trueJson($data);
    }
}
