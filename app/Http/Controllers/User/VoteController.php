<?php

namespace App\Http\Controllers\User;

use DB;
use Session;
use Carbon\Carbon;
use App\Models\Option;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Repositories\Vote\VoteRepositoryInterface;
use App\Repositories\Activity\ActivityRepositoryInterface;
use App\Repositories\Poll\PollRepositoryInterface;
use App\Repositories\ParticipantVote\ParticipantVoteRepositoryInterface;
use App\Repositories\Participant\ParticipantRepositoryInterface;
use App\Repositories\User\UserRepositoryInterface;

class VoteController extends Controller
{

    protected $voteRepository;
    protected $activityRepository;
    protected $pollRepository;
    protected $participantVoteRepository;
    protected $participantRepository;
    protected $userRepository;

    public function __construct(
        VoteRepositoryInterface $voteRepository,
        ActivityRepositoryInterface $activityRepository,
        PollRepositoryInterface $pollRepository,
        ParticipantVoteRepositoryInterface $participantVoteRepository,
        ParticipantRepositoryInterface $participantRepository,
        UserRepositoryInterface $userRepository
    ) {
        $this->voteRepository = $voteRepository;
        $this->activityRepository = $activityRepository;
        $this->pollRepository = $pollRepository;
        $this->participantVoteRepository = $participantVoteRepository;
        $this->participantRepository = $participantRepository;
        $this->userRepository = $userRepository;
    }

    public function store(Request $request)
    {
        //get MAC address of Client
        $client  = @$_SERVER['HTTP_CLIENT_IP'];
        $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
        $remote  = $_SERVER['REMOTE_ADDR'];

        if (filter_var($client, FILTER_VALIDATE_IP)) {
            $ip = $client;
        } elseif (filter_var($forward, FILTER_VALIDATE_IP)) {
            $ip = $forward;
        } else {
            $ip = $remote;
        }

        $inputs = $request->only('option', 'nameVote', 'emailVote', 'pollId', 'isRequiredEmail');
        $poll = $this->pollRepository->findPollById($inputs['pollId']);
        $isRequiredEmail = $inputs['isRequiredEmail'];
        $now = Carbon::now();

        //check time close poll
        if (Carbon::now()->format('d/m/Y h:i') > Carbon::parse($poll->date_close)->format('d/m/Y h:i')) {
            $poll->status = false;
            $poll->save();

            return view('errors.show_errors')->with('message', trans('polls.message_poll_closed'));
        }

        //user vote poll
        if (auth()->check()) {
            $currentUser = auth()->user();
            $participantInformation = [
                'user_id' => $currentUser->id,
            ];

            $isChanged = false;

            if ($inputs['nameVote'] != $currentUser->name || $inputs['emailVote'] != $currentUser->email) {
                $participantInformation['name'] = $inputs['nameVote'];
                $participantInformation['email'] = $inputs['emailVote'];
                $isChanged = true;
            }

            if (! $inputs['nameVote'] && ! $inputs['emailVote']) {
                $participantInformation['name'] = trans('polls.no_name');
            }

            if (! $isChanged) {
                foreach ($inputs['option'] as $option) {
                    $votes[] = [
                        'user_id' => $currentUser->id,
                        'option_id' => $option,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
            } else {
                $participant = $this->participantRepository->create($participantInformation);
                foreach ($inputs['option'] as $option) {
                    $participantVotes[] = [
                        'participant_id' => $participant->id,
                        'option_id' => $option,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
            }

            try {
                DB::beginTransaction();

                $activity = [
                    'poll_id' => $inputs['pollId'],
                    'type' => config('settings.activity.participated'),
                    'user_id' => $currentUser->id,
                ];

                if ($isChanged) {
                    $this->participantVoteRepository->insert($participantVotes);

                    if ($isRequiredEmail) {
                        if ($inputs['nameVote']) {
                            $activity['name'] = $inputs['nameVote'] . ' (' . $inputs['emailVote'] . ') ';
                        } else {
                            $activity['name'] = $inputs['emailVote'];
                        }
                    } else {
                        if ($inputs['nameVote'] && $inputs['emailVote']) {
                            $activity['name'] = $inputs['nameVote'] . ' (' . $inputs['emailVote'] . ') ';
                        } elseif (! $inputs['nameVote'] && $inputs['emailVote']){
                            $activity['name'] = $inputs['emailVote'];
                        } elseif ($inputs['nameVote'] && ! $inputs['emailVote']){
                            $activity['name'] = $inputs['nameVote'];
                        } else {
                            $activity['name'] = trans('polls.no_name');
                        }
                    }
                } else {
                    $this->voteRepository->insert($votes);
                }

                $this->activityRepository->create($activity);
                DB::commit();
            } catch (Exception $e) {
                DB::rollBack();
                throw $e;
            }
        } else {
            $participantInformation = [
                'ip_address' => $ip,
            ];

            if (! $inputs['nameVote'] && ! $inputs['emailVote']) {
                $participantInformation['name'] = trans('polls.no_name');
            } else {
                $participantInformation['email'] = $inputs['emailVote'];
                $participantInformation['name'] = $inputs['nameVote'];
            }

            $participant = $this->participantRepository->create($participantInformation);
            foreach ($inputs['option'] as $option) {
                $participantVotes[] = [
                    'participant_id' => $participant->id,
                    'option_id' => $option,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
            try {
                DB::beginTransaction();
                $this->participantVoteRepository->insert($participantVotes);
                $activity = [
                    'poll_id' => $inputs['pollId'],
                    'type' => config('settings.activity.participated'),
                ];

                if ($isRequiredEmail) {
                    if ($inputs['nameVote']) {
                        $activity['name'] = $inputs['nameVote'] . ' (' . $inputs['emailVote'] . ') ';
                    } else {
                        $activity['name'] = $inputs['emailVote'];
                    }
                } else {
                    if ($inputs['nameVote'] && $inputs['emailVote']) {
                        $activity['name'] = $inputs['nameVote'] . ' (' . $inputs['emailVote'] . ') ';
                    } elseif (! $inputs['nameVote'] && $inputs['emailVote']){
                        $activity['name'] = $inputs['emailVote'];
                    } elseif ($inputs['nameVote'] && ! $inputs['emailVote']){
                        $activity['name'] = $inputs['nameVote'];
                    } else {
                        $activity['name'] = trans('polls.no_name');
                    }
                }

                $this->activityRepository->create($activity);
                DB::commit();
            } catch (Exception $e) {
                DB::rollBack();
                throw $e;
            }
        }

        Session::put('isVotedSuccess', true);

        return redirect()->to($poll->getUserLink())->with('message', trans('polls.vote_successfully'));
    }

    public function destroy($id, Request $request)
    {
        $inputs = $request->only('poll_id');
        $poll = $this->pollRepository->findPollById($inputs['poll_id']);
        $voteIds = $this->pollRepository->getVoteIds($inputs['poll_id']);

        if ($voteIds) {
            $this->voteRepository->deleteVote($voteIds);
        }

        $participantVoteIds = $this->pollRepository->getParticipantVoteIds($inputs['poll_id']);

        if ($participantVoteIds) {
            $this->participantVoteRepository->delete($participantVoteIds);
            $this->participantRepository->delete($id);
        }

        return redirect()->to($poll->getUserLink())->with('message', trans('polls.remove_vote_successfully'));
    }
}
