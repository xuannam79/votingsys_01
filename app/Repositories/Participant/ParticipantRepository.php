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

    public function updateOption($input)
    {
        if (!$input['option']) {
            return false;
        }

        DB::beginTransaction();
        try {
            $user = $this->getCurrentUser();

            if ($user && $user->id == $input['id'] && $input['vote_id']) {
                $this->getCurrentUser()
                    ->options()
                    ->wherePivot('id', $input['vote_id'])
                    ->sync($input['option']);
                DB::commit();

                return true;
            }

            $participant = $this->model->find($input['id']);

            $participant->options()->sync($input['option']);

            if (!$input['user_id']) {
                $input['name'] = $input['name'] ?: trans('polls.no_name');
                $input['email'] = $input['email'] ?: null;
                $input['user_id'] = $input['user_id'] ?: null;

                $participant->fill($input)->save();
            }

            DB::commit();

            return true;
        } catch (Exception $e) {
            DB::rollBack();

            return false;
        }
    }

    public function deleteVoter($input)
    {
        if (!$input['option'] || !$input['id']) {
            return false;
        }

        DB::beginTransaction();
        try {
            $user = $this->getCurrentUser();

            if ($user && $user->id == $input['id'] && $input['vote_id']) {
                $this->getCurrentUser()
                    ->options()
                    ->wherePivot('id', $input['vote_id'])
                    ->detach($input['option']);
                DB::commit();

                return true;
            }

            $participant = $this->model->find($input['id']);

            $participant->options()->detach();

            if (!$participant->delete()) {
                DB::rollBack();

                return false;
            }

            DB::commit();

            return true;
        } catch (Exception $e) {
            DB::rollBack();

            return false;
        }
    }
}
