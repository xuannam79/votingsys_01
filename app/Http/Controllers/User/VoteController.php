<?php

namespace App\Http\Controllers\User;

use DB;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Repositories\Vote\VoteRepositoryInterface;
use App\Repositories\Activity\ActivityRepositoryInterface;
use App\Repositories\Poll\PollRepositoryInterface;

class VoteController extends Controller
{

    protected $voteRepository;
    protected $activityRepository;
    protected $pollRepository;

    public function __construct(
        VoteRepositoryInterface $voteRepository,
        ActivityRepositoryInterface $activityRepository,
        PollRepositoryInterface $pollRepository
    ) {
        $this->voteRepository = $voteRepository;
        $this->activityRepository = $activityRepository;
        $this->pollRepository = $pollRepository;
    }

    public function store(Request $request)
    {
        $inputs = $request->only('option', 'input', 'poll_id', 'isRequiredEmail');
        $poll = $this->pollRepository->findPollById($inputs['poll_id']);

        if (auth()->check()) {
            $userId = auth()->user()->id;
            foreach ($inputs['option'] as $option) {
                $votes[] = [
                    'user_id' => $userId,
                    'option_id' => $option,
                ];
            }
            try {
                DB::beginTransaction();
                $this->voteRepository->insert($votes);
                $activity = [
                    'poll_id' => $inputs['poll_id'],
                    'type' => config('settings.activity.participated'),
                    'user_id' => $userId,
                ];
                $this->activityRepository->create($activity);
                DB::commit();
            } catch (Exception $e) {
                DB::rollBack();
                throw $e;
            }
        }

        return redirect()->to($poll->getUserLink())->with('message', trans('polls.vote_successfully'));
    }
}
