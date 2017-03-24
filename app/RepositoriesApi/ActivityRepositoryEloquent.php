<?php

namespace App\RepositoriesApi;

use App\Models\Activity;
use App\RepositoriesApi\Contracts\ActivityRepositoryInterface;

class ActivityRepositoryEloquent extends AbstractRepositoryEloquent implements ActivityRepositoryInterface
{
    public function __construct(Activity $model)
    {
        parent::__construct($model);
    }

    public function getActivityByPollId($pollId)
    {
        $activities = $this->model->where('poll_id', $pollId)->get();

        return $activities->map(function ($activity) {
            return $activity->load('user');
        });
    }
}
