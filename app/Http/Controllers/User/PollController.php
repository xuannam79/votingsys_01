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
}
