<?php

namespace App\Repositories\Participant;

use DB;
use App\Models\Poll;
use App\Models\Participant;
use App\Repositories\BaseRepository;
use App\Repositories\Participant\ParticipantRepositoryInterface;

class ParticipantRepository extends BaseRepository implements ParticipantRepositoryInterface
{
    protected $poll;

    public function __construct(Participant $participant, Poll $poll)
    {
        $this->model = $participant;
        $this->poll = $poll;
    }

    public function deleteAllParticipants($pollId, $participantVoteRepository, $voteRepository)
    {
        $options = $this->poll->find($pollId)->options;
        foreach ($options as $option) {
            foreach ($option->participantVotes as $participantVote) {
                $participantVoteIds[] = [
                    'id' => $participantVote->id,
                ];

                if ($participantVote->participant) {
                    $participantIds[] = [
                        'id' => $participantVote->participant->id,
                    ];
                }
            }
            foreach ($option->votes as $vote) {
                $voteIds[] = [
                    'id' => $vote->id,
                ];
            }
        }
        try {
            DB::beginTransaction();

            if (isset($participantVoteIds)) {
                $participantVoteRepository->delete($participantVoteIds);
            }

            if (isset($participantIds)) {
                $this->delete($participantIds);
            }

            if (isset($voteIds)) {
                $voteRepository->delete($voteIds);
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
