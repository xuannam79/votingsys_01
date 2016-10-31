<?php

namespace App\Http\Controllers\Admin;

use App\Filter\PollsFilter;
use App\Http\Controllers\Controller;
use App\Models\Poll;
use App\Repositories\Poll\PollRepositoryInterface;
use Illuminate\Http\Request;
use App\Http\Requests\PollRequest;

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
    public function index(PollsFilter $filters)
    {
        $polls =  Poll::with('user')->filter($filters)->paginate(config('settings.length_poll.number_record'));
        $input = $filters->input();
        $linkFilter = $polls->appends($input)->links();
        $data = [
            'type' => [
                config('settings.search_all') => trans('polls.label.search_all'),
                config('settings.type.multiple_choice') => trans('polls.label.multiple_choice'),
                config('settings.type.single_choice') => trans('polls.label.single_choice'),
            ],
            'status' => [
                config('settings.search_all') => trans('polls.label.search_all'),
                config('settings.status.open') => trans('polls.label.opening'),
                config('settings.status.close') => trans('polls.label.closed'),
            ],
        ];
        return view('admins.poll.index', compact('polls', 'input', 'data', 'linkFilter'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data = json_encode([
            'message' => [
                'numberOfOptions' => config('settings.length_poll.option'),
                'config' => [
                    'invite_all' => config('settings.participant.invite_all'),
                    'invite_people' => config('settings.participant.invite_people'),
                ],
                'link_exists' => trans('polls.message.link_exists'),
                'link_valid' => trans('polls.message.link_valid'),
                'submit_form' => trans('polls.message.submit_form'),
            ],
            'view' => [
                'option' => view('layouts.poll_option')->render(),
                'email' => view('layouts.poll_email')->render(),
            ],
            'oldInput' => session('_old_input'),
        ]);

        return view('admins.poll.create', compact('data'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  PollRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PollRequest $request)
    {
        $input = $request->only(
            'email', 'title', 'description', 'location', 'type',
            'option', 'optionImage',
            'required_email', 'add_answer', 'hide_result',
            'custom_link', 'link',
            'set_limit', 'limit',
            'set_password', 'password_poll',
            'invite', 'email_poll'
        );
        $message = $this->pollRepository->store($input);

        return redirect()->route('admin.poll.index')->with('message', $message);
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
        $settings = [];
        $data = json_encode([
            'message' => [
                'numberOfOptions' => config('common.length_poll.option'),
                'config' => [
                    'invite_all' => config('common.participant.invite_all'),
                    'invite_people' => config('common.participant.invite_people'),
                ],
                'link_exists' => trans('polls.message.link_exists'),
                'link_valid' => trans('polls.message.link_valid'),
                'confirm_delete_option' => trans('polls.message.confirm_delete_option')
            ],
            'view' => [
                'option' => view('layouts.poll-option')->render(),
                'email' => view('layouts.poll-email')->render(),
            ],
            'oldInput' => session("_old_input"),
        ]);

        $poll = Poll::with('user', 'options', 'settings')->find($id);

        foreach ($poll->settings as $setting) {
            $settings[$setting->key] = $setting->value;
        }

        return view('admins.poll.edit', compact('poll', 'data', 'settings'));
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

        if ($button == trans('polls.button.change_poll_infor')) {
            $input = $request->only(
                'status', 'name', 'email', 'chatwork_id', 'title', 'location', 'description', 'type'
            );

            $message = $this->pollRepository->editInfor($input, $id);
        } elseif ($button == trans('polls.button.change_poll_option')) {
            $input = $request->only(
                'option', 'image', 'optionImage', 'optionText'
            );
            $message = $this->pollRepository->editPollOption($input, $id);
        } elseif ($button == trans('polls.button.change_poll_setting')) {
            $input = $request->only(
                'setting', 'value', 'participant'
            );

            $message = $this->pollRepository->editPollSetting($input, $id);
        }

        return redirect()->route('admin.poll.edit', $id)->with('message', $message);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $message = $this->pollRepository->delete($id);
        return redirect()->route('admin.poll.index')->with('message', $message);
    }
}
