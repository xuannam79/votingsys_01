<?php

namespace App\Repositories\ParticipantVote;

use App\Models\ParticipantVote;
use App\Models\Activity;
use App\Repositories\BaseRepository;
use App\Repositories\ParticipantVote\ParticipantVoteRepositoryInterface;

class ParticipantVoteRepository extends BaseRepository implements ParticipantVoteRepositoryInterface
{
    public function __construct(ParticipantVote $participantVote)
    {
        $this->model = $participantVote;
    }

    public function getVoteWithOptionsByVoteId($participantVoteIds)
    {
        return $this->model->whereIn('id', $participantVoteIds)->with('participant', 'option')->get()->groupBy('participant_id');
    }
}
