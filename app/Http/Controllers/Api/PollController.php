<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\ApiController;
use App\RepositoriesApi\Contracts\PollRepositoryInterface;
use App\Http\Requests\Api\PollRequest;
use Validator;

class PollController extends ApiController
{
    protected $pollRepository;

    public function __construct(PollRepositoryInterface $pollRepository)
    {
        $this->pollRepository = $pollRepository;
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
            return $this->trueJson($data);
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
            $poll->load('options', 'settings', 'user', 'activities');

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
                    return $this->trueJson(null, ['message' => trans('polls.message.update_poll_info_success')]);
                }

                return $this->falseJson(API_RESPONSE_CODE_UNPROCESSABLE, trans('polls.message.update_poll_info_fail'));
            } elseif ($button == config('settings.btn_edit_poll.save_option')) {
                $input = $request->only(['optionImage', 'optionText']);

                // Save options of poll
                if ($this->pollRepository->editOption($poll, $input)) {
                    return $this->trueJson(null, ['message' => trans('polls.message.update_option_success')]);
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
                    return $this->trueJson(null, ['message' => trans('polls.message.update_setting_success')]);
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
        //
    }
}
