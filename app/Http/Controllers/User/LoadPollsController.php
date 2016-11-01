<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Repositories\Poll\PollRepositoryInterface;
use App\Repositories\Vote\VoteRepositoryInterface;

class LoadPollsController extends Controller
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

    public function loadInitiatedPolls(Request $request)
    {
        if ($request->ajax()) {
            if (auth()->check()) {
                $initiatedPolls = $this->pollRepository->getInitiatedPolls();
                $result = [
                    'success' => true,
                ];

                if ($initiatedPolls->count()) {
                    $result['html'] = view('user.poll.list_polls_layouts', [
                        'polls' => $initiatedPolls,
                    ])->render();
                }

                return response()->json($result);
            }
        }

        return response()->json(['success' => false]);
    }

    public function loadParticipantedPolls(Request $request)
    {
        if ($request->ajax()) {
            if (auth()->check()) {
                $participatedPolls = $this->pollRepository->getParticipatedPolls($this->voteRepository);
                 $result = [
                    'success' => true,
                ];

                if ($participatedPolls->count()) {
                    $result['html'] = view('user.poll.list_polls_layouts', [
                        'polls' => $participatedPolls,
                    ])->render();
                }

                return response()->json($result);
            }
        }

        return response()->json(['success' => false]);
    }

    public function loadClosedPolls(Request $request)
    {
        if ($request->ajax()) {
            if (auth()->check()) {
                $closedPolls = $this->pollRepository->getClosedPolls();
                $result = [
                    'success' => true,
                ];

                if ($closedPolls->count()) {
                    $result['html'] = view('user.poll.list_opened_polls_layouts', [
                        'polls' => $closedPolls,
                    ])->render();
                }

                return response()->json($result);
            }
        }

        return response()->json(['success' => false]);
    }
}
