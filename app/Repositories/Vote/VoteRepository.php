<?php

namespace App\Repositories\Vote;

use App\Models\Vote;
use App\Models\Activity;
use App\Repositories\BaseRepository;
use App\Repositories\Vote\VoteRepositoryInterface;

class VoteRepository extends BaseRepository implements VoteRepositoryInterface
{
    public function __construct(Vote $vote)
    {
        $this->model = $vote;
    }

    public function getListPollIdOfCurrentUser()
    {
        $currentUserId = auth()->user()->id;
        $listPollIds = [];
        $votes = $this->model->where('user_id', $currentUserId)->with('option.poll')->get();

        foreach ($votes as $vote) {
            if ($vote->option->poll) {
                $listPollIds[] = $vote->option->poll->id;
            }
        }

        return array_unique($listPollIds);
    }
}
