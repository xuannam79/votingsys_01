<?php

namespace App\Http\Controllers\Api;

use App\RepositoriesApi\Contracts\ActivityRepositoryInterface;
use App\RepositoriesApi\Contracts\LinkRepositoryInterface;
use Illuminate\Http\Request;

class ActivityController extends ApiController
{
    protected $activityRepository;
    protected $linkRepository;

    public function __construct(
        ActivityRepositoryInterface $activityRepository,
        LinkRepositoryInterface $linkRepository
    ) {
        $this->activityRepository = $activityRepository;
        $this->linkRepository = $linkRepository;
    }

    public function showActivity(Request $request)
    {
        $link = $this->linkRepository->findBy('token', $request->only('token'))->first();

        if (!$link) {
            return $this->falseJson(API_RESPONSE_CODE_UNPROCESSABLE, trans('activity.message.not_found_link'));
        }

        $activities = $this->activityRepository->getActivityByPollId($link->poll_id);

        if (!$activities) {
            return $this->falseJson(API_RESPONSE_CODE_UNPROCESSABLE, trans('activity.message.not_found_activities'));
        }

        return $this->trueJson($activities);
    }
}
