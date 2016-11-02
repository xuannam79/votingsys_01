<?php

namespace App\Http\Controllers;

use App\Http\Requests\PollRequest;
use App\Repositories\Poll\PollRepositoryInterface;
use Illuminate\Http\Request;

class PollController extends Controller
{
    private $pollRepository;

    public function __construct(PollRepositoryInterface $pollRepository)
    {
        $this->pollRepository = $pollRepository;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $settingConfig = config('settings.setting');
        $settingTrans = trans('polls.label.setting');
        $dataJson = json_encode([
            'message' => [
                'numberOfOptions' => config('settings.length_poll.option'),
                'length' => [
                    'title' => config('settings.length_poll.title'),
                    'description' => config('settings.length_poll.description'),
                    'name' => config('settings.length_poll.name'),
                    'email' => config('settings.length_poll.email'),
                    'link' => config('settings.length_poll.link'),
                    'limit' => config('settings.length_poll.number_limit'),
                    'password' => config('settings.length_poll.password_poll'),
                ],
                'config' => [
                    'invite_all' => config('settings.participant.invite_all'),
                    'invite_people' => config('settings.participant.invite_people'),
                ],
                'setting' => [
                    'link' => $settingConfig['custom_link'],
                    'limit' => $settingConfig['set_limit'],
                    'password' => $settingConfig['set_password'],
                ],
                'validate' => [
                    'required' => trans('polls.validate_client.required'),
                    'max' => trans('polls.validate_client.max'),
                    'email' => trans('polls.validate_client.email'),
                    'number' => trans('polls.validate_client.number'),
                    'choose' => trans('polls.validate_client.choose'),
                    'option_empty' => trans('polls.validate_client.option_empty'),
                    'option_required' => trans('polls.validate_client.option_required'),
                    'participant_empty' => trans('polls.validate_client.participant_empty'),
                    'character' => trans('polls.validate_client.character'),
                    'email_exists' => trans('polls.message.email_exists'),
                    'email_valid' => trans('polls.message.email_valid'),
                    'link_exists' => trans('polls.message.link_exists'),
                    'link_valid' => trans('polls.message.link_valid'),
                ],
            ],
            'view' => [
                'option' => view('layouts.poll_option')->render(),
                'email' => view('layouts.poll_email')->render(),
            ],
            'oldInput' => session("_old_input"),
        ]);

        $dataView = [
            'setting' => [
                $settingConfig['required_email'] => $settingTrans['required_email'],
                $settingConfig['add_answer'] => $settingTrans['add_answer'],
                $settingConfig['hide_result'] => $settingTrans['hide_result'],
                $settingConfig['custom_link'] => $settingTrans['custom_link'],
                $settingConfig['set_limit'] => $settingTrans['set_limit'],
                $settingConfig['set_password'] => $settingTrans['set_password'],
            ],
        ];
        return view('user.poll.create', compact('dataJson', 'dataView'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  PollRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(PollRequest $request)
    {
        $input = $request->only(
            'title', 'location', 'description', 'name', 'email', 'chatwork_id', 'type',
            'optionText', 'optionImage',
            'setting', 'value',
            'participant', 'member'
        );

        if ($this->pollRepository->store($input)) {
            $message = trans('polls.message.create_success');
        } else {
            $message = trans('polls.message.create_fail');
        }

        return redirect()->route('poll.create')->with('message', $message);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
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
