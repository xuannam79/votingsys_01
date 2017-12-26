<?php

namespace App\Http\Controllers\User;

use DB;
use LRedis;
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
use Exception;
use App\Http\Requests\VoteRequest;

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

    public function store(VoteRequest $request)
    {
        $client  = @$_SERVER['HTTP_CLIENT_IP'];
        $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
        $remote  = $_SERVER['REMOTE_ADDR'];

        if (filter_var($client, FILTER_VALIDATE_IP)) {
            $ip = $client;
        } else if (filter_var($forward, FILTER_VALIDATE_IP)) {
            $ip = $forward;
        } else {
            $ip = $remote;
        }

        $inputs = $request->only('option', 'nameVote', 'emailVote', 'pollId', 'optionImage', 'optionText', 'newOption', 'optionDescription');
        $poll = $this->pollRepository->findPollById($inputs['pollId']);
        $settingsPoll = $this->pollRepository->getSettingsPoll($poll->id);
        $isSetIp = false;
        $voteLimit = null;
        $isRequiredEmail = false;
        $isRequiredEmailName = false;
        $isRequiredName = false;
        $isNotSameEmail = false;
        $isLimit = false;
        $isAllowAddOption = false;
        $isDisableVoting = false;
        $isRequiredAuthWsm = false;

        $poll->load('options.users', 'options.participants');

        //get all settings of poll
        $listSettings = [];
        if ($poll->settings) {
            foreach ($poll->settings as $setting) {
                $listSettings[] = $setting->key;

                if ($setting->key == config('settings.setting.set_limit')) {
                    $voteLimit = $setting->value;
                }
            }

            if (collect($listSettings)->contains(config('settings.setting.is_set_ip'))) {
                $isSetIp = true;
            }

            if (collect($listSettings)->contains(config('settings.setting.required_email'))) {
                $isRequiredEmail = true;
            }

            if (collect($listSettings)->contains(config('settings.setting.required_name_and_email'))) {
                $isRequiredEmailName = true;
            }

            if (collect($listSettings)->contains(config('settings.setting.required_name'))) {
                $isRequiredName = true;
            }

            if (collect($listSettings)->contains(config('settings.setting.not_same_email'))) {
                $isNotSameEmail = true;
            }

            if (collect($listSettings)->contains(config('settings.setting.allow_add_option'))) {
                $isAllowAddOption = true;
            }

            if (collect($listSettings)->contains(config('settings.setting.required_auth_wsm'))) {
                $isRequiredAuthWsm = true;
            }

            if (collect($listSettings)->contains(config('settings.setting.disable_voting'))) {
                $isDisableVoting = true;
            }
        }

        // count voters
        $optionDates = $this->pollRepository->showOptionDate($poll);

        if ($voteLimit && $optionDates['participants']->count() >= $voteLimit) {
            $isLimit = true;
        }

        // Check condition to vote
        $now = Carbon::now();
        if ($isLimit || $poll->isClosed()
            || (!$inputs['option'] && !$inputs['newOption'])
            || Carbon::now()->toAtomString() > Carbon::parse($poll->date_close)->toAtomString()
            || strlen($inputs['nameVote']) >= config('settings.length_poll.name')
        ) {
            return redirect()->to($poll->getUserLink());
        }

        // check setting's poll have require input name
        if (($isRequiredAuthWsm || $isRequiredName) && !$inputs['nameVote']) {
            flash(trans('polls.message_validate_name'), config('settings.notification.danger'));

            return back();
        }

        // check setting's poll have require input email
        if (($isRequiredAuthWsm || $isRequiredEmail) && !$inputs['emailVote']) {
            flash(trans('polls.message_required_email'), config('settings.notification.danger'));

            return back();
        }

        // check setting's poll have require input name and email
        if (($isRequiredAuthWsm || $isRequiredEmailName) && !$inputs['emailVote'] && !$inputs['nameVote']) {
            flash(trans('polls.message_validate_name_and_email'), config('settings.notification.danger'));

            return back();
        }

        // Check same email when vote if have setting not the same email
        if (($isRequiredAuthWsm || $isNotSameEmail) && $this->pollRepository->checkIfEmailVoterExist($inputs)) {
            flash(trans('polls.message_client.email_exists'), config('settings.notification.danger'));

            return back();
        }

        // Check email of wsm that settings required
        if ($isRequiredAuthWsm && auth()->check() && (!auth()->user()->haveWsmAction() || auth()->user()->email != $inputs['emailVote'])) {
            flash(trans('polls.message_vote_one_time'), config('settings.notification.danger'));

            return back();
        }

        if ($poll->multiple == trans('polls.label.single_choice') && count($inputs['option']) > 1) {
            flash(trans('polls.only_one_voted'), config('settings.notification.danger'));

            return back();
        }

        if (!empty($inputs['optionText'])) {
            $inputs['optionText'] = array_filter($inputs['optionText'], function ($v) {
                return $v != '';
            });
        }

        if ($isAllowAddOption && empty($inputs['optionText']) && empty($inputs['option'])) {
            flash(trans('polls.message_client.option_required'), config('settings.notification.danger'));

            return back();
        }

        // Check have setting disable voting
        if ($isDisableVoting) {
            flash(trans('polls.invalid_option_voted'), config('settings.notification.danger'));

            return back();
        }

        //Add New Option and Get Id New Option
        if ($isAllowAddOption && !empty($inputs['newOption']) && !empty($inputs['optionText'])) {
            $newOption = $this->pollRepository->addOption($inputs, $inputs['pollId'], true);
            $idNewOption = $newOption[0]->id;
            $inputs['option'][] = $idNewOption;
        }

        //user vote poll
        if (auth()->check()) {
            $currentUser = auth()->user();
            $participantInformation = [
                'user_id' => $currentUser->id,
            ];
            $isChanged = false;

            if ($poll->multiple == trans('polls.label.multiple_choice')
                || $inputs['nameVote'] != $currentUser->name
                || $inputs['emailVote'] != $currentUser->email) {
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

        // Set Cookie
        if (isset($participant)) {
            $cookie = (array) $request->cookie('participant_id');

            array_push($cookie, $participant->id);

            \Cookie::queue('participant_id', $cookie, config('settings.cookie_expire'));
        }

        $redis = LRedis::connection();

        // update eagle loading voter
        $poll->load('options.users', 'options.participants');

        // Get view option
        $viewOptions = $this->pollRepository->getSocketOption($poll);

        // Get view chart
        $chart = $this->pollRepository->getSocketChart($poll);

        $dataSocket['success'] = true;

        $dataSocket = array_merge($viewOptions, $chart, $dataSocket);

        $redis->publish('votes', json_encode($dataSocket));

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

    public function ajaxCheckIfExistEmailVote(Request $request)
    {
        if ($request->ajax()) {
            $input = $request->only([
                'pollId',
                'emailVote',
                'emailIgnore',
            ]);

            $status = $this->pollRepository->checkIfEmailVoterExist($input);

            return response()->json(['status' => $status]);
        }
    }

    public function update(Request $request, $id)
    {
        $input = $request->only(['optionImage', 'optionText', 'optionDeleteImage']);
        if ($this->pollRepository->editVoted($id, $input)) {
            $poll = $this->pollRepository->findPollById($id);

            $poll->load('options.users', 'options.participants');

            $redis = LRedis::connection();

            // Get view option
            $viewOptions = $this->pollRepository->getSocketOption($poll);

            // Get view chart
            $chart = $this->pollRepository->getSocketChart($poll);

            $dataSocket['success'] = true;

            $dataSocket = array_merge($viewOptions, $chart, $dataSocket);

            $redis->publish('votes', json_encode($dataSocket));

            flash(trans('polls.message.update_option_success'), 'success');

            return back();
        }

        flash(trans('polls.message.update_option_fail'), 'error');

        return back();
    }

    public function getModalOptionVoters($idOption)
    {
        try {
            $option = Option::find($idOption)->load('users', 'participants');
            $voters = [];

            foreach ($option->users as $user) {
                $voters[] = [
                    'name' => $user->name,
                    'email' => $user->email,
                    'avatar' => $user->getAvatarPath(),
                ];
            }

            foreach ($option->participants as $participant) {
                $voters[] = [
                    'name' => $participant->name,
                    'email' => $participant->email,
                    'avatar' => asset(config('settings.image_default_path')),
                ];
            }

            return response()->json([
                'status' => true,
                'voters' => view('layouts.modal_voters', compact('voters'))->render(),
            ]);
        } catch (Exception $e) {
            throw new Exception(trans('message.find_error'));
        }
    }

    public function editVote(Request $request)
    {
        $input = $request->only('id', 'option', 'vote_id', 'poll_id', 'user_id', 'name', 'email');

        $status = $this->participantRepository->updateOption($input);

        if ($status) {
            $redis = LRedis::connection();

            // Get data for socket
            $poll = $this->pollRepository->find($input['poll_id']);

            $poll->load('options.users', 'options.participants');

            // Get view option
            $viewOptions = $this->pollRepository->getSocketOption($poll);

            // Get view chart
            $chart = $this->pollRepository->getSocketChart($poll);

            $dataSocket['success'] = true;

            $dataSocket = array_merge($viewOptions, $chart, $dataSocket);

            $redis->publish('votes', json_encode($dataSocket));
        }

        return json_encode(['status' => $status]);
    }

    public function deleteVote(Request $request)
    {
        $input = $request->only('id', 'option', 'vote_id', 'poll_id', 'name', 'email');

        $status = $this->participantRepository->deleteVoter($input);
        $response = ['status' => $status];

        if ($status) {
            $redis = LRedis::connection();

            // Get data for socket
            $poll = $this->pollRepository->find($input['poll_id']);

            $poll->load('options.users', 'options.participants', 'links', 'settings');

            // Get view option
            $viewOptions = $this->pollRepository->getSocketOption($poll);

            // Get view chart
            $chart = $this->pollRepository->getSocketChart($poll);

            $dataSocket['success'] = true;

            $dataSocket = array_merge($viewOptions, $chart, $dataSocket);

            $redis->publish('votes', json_encode($dataSocket));
        } else {
            $response['message'] = trans('polls.message_client.error_occurs');
        }

        return json_encode($response);
    }
}
