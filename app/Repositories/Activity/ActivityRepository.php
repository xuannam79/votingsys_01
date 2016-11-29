<?php

namespace App\Repositories\Activity;

use App\Models\Activity;
use App\Repositories\BaseRepository;
use App\Repositories\Activity\ActivityRepositoryInterface;

class ActivityRepository extends BaseRepository implements ActivityRepositoryInterface
{
    public function __construct(Activity $activity)
    {
        $this->model = $activity;
    }

    public function getActivityByPollId($id)
    {
        return $this->model->where('poll_id', $id)->with('user', 'poll.options')->orderBy('id', 'DESC')->paginate(config('settings.activity_per_page'));
    }

    public function getOwnerOfPoll($id)
    {
        $currentPoll = $this->model->with('user')->find($id);

        return $currentPoll->user->name;
    }
}
