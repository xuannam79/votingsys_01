<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Requests\PollGeneralRequest;
use App\Http\Controllers\Controller;
use App\Repositories\Poll\PollRepositoryInterface;
use App\Repositories\Vote\VoteRepositoryInterface;

class PollController extends Controller
{
    protected $pollRepository;
    protected $voteRepository;

    public function __construct(
        PollRepositoryInterface $pollRepository,
        VoteRepositoryInterface $voteRepository
    ) {
        $this->pollRepository = $pollRepository;
        $this->voteRepository = $voteRepository;
    }

    public function index()
    {
        if (auth()->check()) {
            $initiatedPolls = $this->pollRepository->getInitiatedPolls();
            $participatedPolls = $this->pollRepository->getParticipatedPolls($this->voteRepository);
            $closedPolls = $this->pollRepository->getClosedPolls();
        }

        return view('user.poll.list_polls', compact('initiatedPolls', 'participatedPolls', 'closedPolls'));
    }

    public function show($id)
    {
        $poll = $this->pollRepository->find($id);

        if (!$poll) {
            return view('errors.show_errors')->with('message', trans('polls.poll_not_found'));
        }

        $isHideResult = false;
        $voteLimit = null;

        if ($poll->settings) {
            foreach ($poll->settings as $setting) {
                if ($setting->key == config('settings.hide_result')) {
                    $isHideResult = true;
                }

                if ($setting->key == config('settings.set_limit')) {
                    $voteLimit = $setting->value;
                }
            }

            if ($voteLimit && $poll->countParticipants() >= $voteLimit) {
                return view('errors.show_errors')->with('message', trans('polls.message_poll_limit'));
            }
        }

        return view('user.poll.details', compact('poll', 'isHideResult'));
    }
}
