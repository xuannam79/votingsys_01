<?php

namespace App\Http\Controllers\User;

use Mail;
use LRedis;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Requests\PollGeneralRequest;
use App\Http\Controllers\Controller;
use App\Repositories\Poll\PollRepositoryInterface;
use App\Repositories\Vote\VoteRepositoryInterface;
use App\Repositories\Activity\ActivityRepositoryInterface;

class PollController extends Controller
{
    protected $pollRepository;
    protected $voteRepository;
    protected $activityRepository;

    public function __construct(
        PollRepositoryInterface $pollRepository,
        VoteRepositoryInterface $voteRepository,
        ActivityRepositoryInterface $activityRepository
    ) {
        $this->pollRepository = $pollRepository;
        $this->voteRepository = $voteRepository;
        $this->activityRepository = $activityRepository;
    }

    public function index()
    {
        try {
            if (auth()->check()) {
                $initiatedPolls = $this->pollRepository->getInitiatedPolls();
                $participatedPolls = $this->pollRepository->getParticipatedPolls($this->voteRepository);
                $closedPolls = $this->pollRepository->getClosedPolls();

                return view('user.poll.list_polls', compact('initiatedPolls', 'participatedPolls', 'closedPolls'));
            }
        } catch (\Exception $ex) {
            return redirect()->to(url('/'));
        }

        return redirect()->to(url('/'));
    }

    public function edit($id)
    {
        $poll = $this->pollRepository->findClosedPoll($id);

        if (! $poll) {
            return view('errors.show_errors')->with('message', trans('polls.reopen_poll_fail'));
        }

        $emails = $poll->email;

        if ($poll->user_id) {
            $emails = $poll->user->email;
        }

        if ($emails) {
            Mail::queue('layouts.open_poll_mail', [
                'link' => $poll->getAdminLink(),
            ], function ($message) use ($emails) {
                $message->to($emails)->subject(trans('label.mail.open_poll.subject'));
            });

            if (count(Mail::failures()) == config('settings.default_value')) {
                $poll->status = true;
                $poll->date_close = null;
                $poll->save();
            }
        }

        $poll->status = true;
        $poll->save();

        //insert activity
        $activity = [
            'poll_id' => $poll->id,
            'type' => config('settings.activity.reopen_poll'),
        ];
        $this->activityRepository->create($activity);

        //use socket.io
        $redis = LRedis::connection();
        $redis->publish('reopenPoll', json_encode([
            'success' => true,
            'poll_id' => $poll->id,
            'link_user' => $poll->getUserLink(),
        ]));

        return redirect()->to($poll->getAdminLink())->with('messages', trans('polls.reopen_poll_successfully'));
    }

    public function update($id, Request $request)
    {
        $defaultResult = [
            'success' => false,
            'is_exist' => false,
        ];

        if ($request->ajax()) {
            $inputs = $request->only('token_input', 'is_link_admin');
            $poll = $this->pollRepository->find($id);

            if (! $poll) {
                return response()->json($defaultResult);
            }

            if (! $inputs['is_link_admin']) {
                foreach ($poll->links as $link) {
                    if (! $link->link_admin) {
                        return $link->editToken($inputs['token_input']);
                    }
                }
            } else {
                foreach ($poll->links as $link) {
                    if ($link->link_admin) {
                        return $link->editToken($inputs['token_input']);
                    }
                }
            }
        }

        return response()->json($defaultResult);
    }

    public function destroy($id)
    {
        $poll = $this->pollRepository->find($id);

        if (! $poll) {
            return view('errors.show_errors')->with('message', trans('polls.close_poll_fail'));
        }

        $emails = $poll->email;

        if ($poll->user_id) {
            $emails = $poll->user->email;
        }

        if ($emails) {
            Mail::queue('layouts.close_poll_mail', [
                'link' => $poll->getAdminLink(),
            ], function ($message) use ($emails) {
                $message->to($emails)->subject(trans('label.mail.close_poll.subject'));
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
