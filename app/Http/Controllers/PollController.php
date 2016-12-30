<?php
namespace App\Http\Controllers;

use Mail;
use LRedis;
use App\Models\Poll;
use Illuminate\Http\Request;
use App\Http\Requests\PollEditRequest;
use App\Http\Requests\PollRequest;
use App\Repositories\Poll\PollRepositoryInterface;
use App\Repositories\Activity\ActivityRepositoryInterface;
use App\Repositories\Link\LinkRepositoryInterface;

class PollController extends Controller
{
    protected $pollRepository;
    protected $activityRepository;
    protected $linkRepository;

    public function __construct(
        PollRepositoryInterface $pollRepository,
        ActivityRepositoryInterface $activityRepository,
        LinkRepositoryInterface $linkRepository
    )
    {
        $this->pollRepository = $pollRepository;
        $this->activityRepository = $activityRepository;
        $this->linkRepository = $linkRepository;
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
        $data = $this->pollRepository->getDataPollSystem();

        return view('user.poll.create', compact('data'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = $request->only(
            'title', 'location', 'description', 'name', 'email', 'chatwork_id', 'type', 'closingTime',
            'optionText', 'optionImage',
            'setting', 'value', 'setting_child',
            'member'
        );
        $input['page'] = 'create';
        $data = $this->pollRepository->store($input);

        if ($data) {
            return redirect()->to(
                url(config('settings.link_poll.result_create') . $data['poll']->id . '/' . $data['link']['administration'])
            );
        }

        $message = trans('polls.message.create_fail');
        return redirect()->route('user-poll.create')->with('message', $message);

    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($token)
    {
        $link = $this->linkRepository->getPollByToken($token);

        if ($link) {
            $poll = $link->poll;
            $data = $this->pollRepository->getDataPollSystem();
            $setting = $poll->settings->pluck('value', 'key')->toArray();
            $page = 'edit';
            $totalVote = $poll->countParticipants();

            return view('user.poll.edit', compact('poll', 'data', 'setting', 'page', 'totalVote'));
        } else {
            return view('errors.404');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $button = $request->btn_edit;
        $poll = Poll::with('options')->findOrFail($id);

        if ($button == trans('polls.button.save_info')) {
            $input = $request->only(
                'name', 'email', 'chatwork_id', 'title', 'location', 'description', 'type'
            );
            $input['date_close'] = $request->closingTime;
            $message = $this->pollRepository->editInfor($input, $id);
        } elseif ($button == trans('polls.button.save_option')) {
            $input = $request->only(
                'option', 'image', 'optionImage', 'optionText'
            );
            $message = $this->pollRepository->editPollOption($input, $id);
        } else {
            $input = $request->only(
                'setting', 'value', 'setting_child'
            );
            $message = $this->pollRepository->editPollSetting($input, $id);
        }

        $poll = Poll::findOrFail($id);
        $redis = LRedis::connection();
        $redis->publish('editPoll', json_encode([
            'success' => true,
            'poll_id' => $poll->id,
            'link_user' => $poll->getUserLink(),
        ]));

        return redirect()->to($poll->getAdminLink())->with('message', $message);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $poll = $this->pollRepository->find($id);

        if (!$poll) {
            return view('errors.show_errors')->with('message', trans('polls.close_poll_fail'));
        }

        $email = $poll->email;

        if ($poll->user_id) {
            $email = $poll->user->email;
        }
        if ($email) {

            Mail::queue('layouts.close_poll_mail', [
                'link' => $poll->getAdminLink(),
            ], function ($message) use ($email) {
                $message->to($email)->subject(trans('label.mail.close_poll.subject'));
            });
            if (count(Mail::failures()) == config('settings.default_value')) {
                $poll->status = false;
                $poll->save();
            }
        }
        $poll->status = false;
        $poll->save();

        //insert activity
        $activity = [
            'poll_id' => $poll->id,
            'type' => config('settings.activity.close_poll'),
        ];
        $this->activityRepository->create($activity);

        //use socket.io
        $redis = LRedis::connection();
        $redis->publish('closePoll', json_encode([
            'success' => true,
            'poll_id' => $poll->id,
            'link_user' => $poll->getUserLink(),
        ]));

        return redirect()->to($poll->getAdminLink())->with('messages', trans('polls.close_poll_successfully'));
    }
}
