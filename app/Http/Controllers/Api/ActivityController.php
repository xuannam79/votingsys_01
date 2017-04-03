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
        $data = $request->only('token');

        if (!$data['token']) {
            return $this->falseJson(API_RESPONSE_CODE_UNPROCESSABLE, trans('polls.message.not_param_token'));
        }

        $link = $this->linkRepository->findBy('token', $data)->first();

        if (!$link) {
            return $this->falseJson(API_RESPONSE_CODE_UNPROCESSABLE, trans('activity.message.not_found_link'));
        }

        $activities = $this->activityRepository->getActivityByPollId($link->poll_id);

        if (!$activities) {
            return $this->falseJson(API_RESPONSE_CODE_UNPROCESSABLE, trans('activity.message.not_found_activities'));
        }

        $poll = $link->poll->withoutAppends()->load('user');

        return $this->trueJson([
            'poll' => $poll,
            'activities' => $activities,
        ]);
    }
}
