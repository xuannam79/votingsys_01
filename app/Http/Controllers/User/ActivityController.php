<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Repositories\Activity\ActivityRepositoryInterface;
use App\Repositories\Poll\PollRepositoryInterface;

class ActivityController extends Controller
{
    protected $activityRepository;
    protected $pollRepository;

    public function __construct(
        ActivityRepositoryInterface $activityRepository,
        PollRepositoryInterface $pollRepository
    ) {
        $this->activityRepository = $activityRepository;
        $this->pollRepository = $pollRepository;
    }

    public function show($id)
    {
        $activities = $this->activityRepository->getActivityByPollId($id);

        if (! $activities) {
            return view('errors.show_errors')->with('message', trans('polls.activity_not_found'));
        }

        $poll = $this->pollRepository->findPollById($id);

        return view('user.poll.history', compact('activities', 'poll'));
    }
}
