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

    public function edit($id)
    {
        $poll = $this->pollRepository->findClosedPoll($id);

        if (! $poll) {
            return view('errors.show_errors')->with('message', trans('polls.reopen_poll_fail'));
        }

        $poll->status = true;
        $poll->save();

        return redirect()->to($poll->getUserLink())->with('message', trans('polls.reopen_poll_successfully'));
    }

    public function destroy($id)
    {
        $poll = $this->pollRepository->find($id);

        if (! $poll) {
            return view('errors.show_errors')->with('message', trans('polls.close_poll_fail'));
        }

        $poll->status = false;
        $poll->save();

        return redirect()->action('User\PollController@index');
    }
}
