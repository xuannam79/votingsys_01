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
}
