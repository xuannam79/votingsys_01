<?php

namespace App\Http\Controllers\User;

use Mail;
use LRedis;
use App\Models\Poll;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Repositories\Participant\ParticipantRepositoryInterface;
use App\Repositories\ParticipantVote\ParticipantVoteRepositoryInterface;
use App\Repositories\Vote\VoteRepositoryInterface;
use App\Repositories\Activity\ActivityRepositoryInterface;
use App\Repositories\Poll\PollRepositoryInterface;

class ParticipantController extends Controller
{
    protected $participantRepository;
    protected $participantVoteRepository;
    protected $voteRepository;
    protected $activityRepository;
    protected $pollRepository;

    public function __construct(
        ParticipantRepositoryInterface $participantRepository,
        ParticipantVoteRepositoryInterface $participantVoteRepository,
        VoteRepositoryInterface $voteRepository,
        ActivityRepositoryInterface $activityRepository,
        PollRepositoryInterface $pollRepository
    ) {
        $this->participantRepository = $participantRepository;
        $this->participantVoteRepository = $participantVoteRepository;
        $this->voteRepository = $voteRepository;
        $this->activityRepository = $activityRepository;
        $this->pollRepository = $pollRepository;
    }

    public function deleteAllParticipant(Request $request)
    {
        $inputs = $request->only('poll_id');
        $poll = Poll::find($inputs['poll_id']);
        $emails = $poll->email;

        if ($poll->user_id) {
            $emails = $poll->user->email;
        }

        $this->participantRepository->deleteAllParticipants($inputs['poll_id'], $this->participantVoteRepository, $this->voteRepository);

        //send email when admin delete all participant
        if ($emails) {
            Mail::queue('layouts.delete_all_participant_mail', [
                'link' => $poll->getAdminLink(),
            ], function ($message) use ($emails) {
                $message->to($emails)->subject(trans('label.mail.delete_participant.subject'));
            });
        }

        $activity = [
            'poll_id' => $inputs['poll_id'],
            'type' => config('settings.activity.all_participants_deleted'),
        ];
        $this->activityRepository->create($activity);

        //use socket.io
        $redis = LRedis::connection();
        $redis->publish('deleteParticipant', json_encode([
            'success' => true,
            'poll_id' => $poll->id,
            'result' => $poll->countVotesWithOption(),
            'modal_details_empty' => view('user.poll.modal_details_empty_layouts')->render(),
        ]));

        return redirect()->to($poll->getAdminLink())->with('messages', trans('polls.delete_all_participants_successfully'));
    }
}
