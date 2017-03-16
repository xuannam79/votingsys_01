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
        //get MAC address of Client
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

        $inputs = $request->only('option', 'nameVote', 'emailVote', 'pollId', 'optionImage', 'optionText', 'newOption');
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
        }

        if ($voteLimit && $poll->countParticipants() >= $voteLimit) {
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
        if ($isRequiredName && !$inputs['nameVote']) {
            flash(trans('polls.message_validate_name'), config('settings.notification.danger'));

            return back();
        }

        // check setting's poll have require input email
        if ($isRequiredEmail && !$inputs['emailVote']) {
            flash(trans('polls.message_required_email'), config('settings.notification.danger'));

            return back();
        }

        // check setting's poll have require input name and email
        if ($isRequiredEmailName && !$inputs['emailVote'] && !$inputs['nameVote']) {
            flash(trans('polls.message_validate_name_and_email'), config('settings.notification.danger'));

            return back();
        }

        // Check same email when vote if have setting not the same email
        if ($isNotSameEmail && $this->pollRepository->checkIfEmailVoterExist($inputs)) {
            flash(trans('polls.message_client.email_exists'), config('settings.notification.danger'));

            return back();
        }

        if ($poll->multiple == trans('polls.label.single_choice') && count($inputs['option']) > 1) {
            flash(trans('polls.only_one_voted'), config('settings.notification.danger'));

            return back();
        }

        $inputs['optionText'] = array_filter($inputs['optionText'], function ($v) {
            return $v != '';
        });

        if ($isAllowAddOption && !$inputs['optionText'] && !count($inputs['option'])) {
            flash(trans('polls.message_client.option_required'), config('settings.notification.danger'));

            return back();
        }

        //Add New Option and Get Id New Option
        if ($isAllowAddOption && $inputs['newOption']) {
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

        //get data of poll
        $voteIds = $this->pollRepository->getVoteIds($poll->id);
        $votes = $this->voteRepository->getVoteWithOptionsByVoteId($voteIds);
        $participantVoteIds = $this->pollRepository->getParticipantVoteIds($poll->id);
        $participantVotes = $this->participantVoteRepository->getVoteWithOptionsByVoteId($participantVoteIds);
        $mergedParticipantVotes = $votes->toBase()->merge($participantVotes->toBase());

        // Show result options
        $optionDates = $this->pollRepository->showOptionDate($poll);

        if ($mergedParticipantVotes->count()) {
            foreach ($mergedParticipantVotes as $mergedParticipantVote) {
                $createdAt[] = $mergedParticipantVote->first()->created_at;
            }

            $sortedParticipantVotes = collect($createdAt)->sort();
            $resultParticipantVotes = collect();
            foreach ($sortedParticipantVotes as $sortedParticipantVote) {
                foreach ($mergedParticipantVotes as $mergedParticipantVote) {
                    foreach ($mergedParticipantVote as $participantVote) {
                        if ($participantVote->created_at == $sortedParticipantVote) {
                            $resultParticipantVotes->push($mergedParticipantVote);
                            break;
                        }

                    }
                }
            }
            $mergedParticipantVotes = $resultParticipantVotes;
        }

        $isHaveImages = false;

        foreach ($poll->options as $option) {
            if ($option->image) {
                $isHaveImages = true;
                break;
            }
        }

        $numberOfVote = config('settings.default_value');
        $html = view('user.poll.vote_details_layouts', compact('optionDates'))->render();

         //data for draw chart
        $optionRateBarChart = [];
        $totalVote = config('settings.default_value');

        foreach ($poll->options as $option) {
            $totalVote += $option->countVotes();
        }

        if ($totalVote) {
            foreach ($poll->options as $option) {
                $countOption = $option->countVotes();
                if ($countOption > 0) {
                    if ($isHaveImages) {
                        $optionRateBarChart[] = ['<img src="' . $option->showImage() .'" class="image-option-poll">' . '<span class="name-option-poll">' . $option->name . '</span>', $countOption];
                    } else {
                        $optionRateBarChart[] = ['<p>' . $option->name . '</p>', $countOption];
                    }
                }
            }
        } else {
            $optionRateBarChart = null;
        }

        $optionRateBarChart = json_encode($optionRateBarChart);

        $optionRatePieChart = json_encode($this->pollRepository->getDataToDrawPieChart($poll, $isHaveImages));

        $chartNameData = json_encode($this->pollRepository->getNameOptionToDrawChart($poll, $isHaveImages));
        $fontSize = $this->pollRepository->getSizeChart($poll)['fontSize'];

        //get data result to sort number of vote
        $dataTableResult = $this->pollRepository->getDataTableResult($poll);

        //sort option and count vote by number of vote
        $dataTableResult = array_values(array_reverse(array_sort($dataTableResult, function($value)
        {
            return $value['numberOfVote'];
        })));

        //use socket.io
        $redis = LRedis::connection();
        $redis->publish('votes', json_encode([
            'result' => $poll->countVotesWithOption(),
            'poll_id' => $poll->id,
            'count_participant' => $mergedParticipantVotes->count(),
            'success' => true,
            'html' => $html,
            'horizontalOption' => view('.user.poll.option_horizontal',
                compact('settingsPoll', 'poll', 'isHaveImages', 'isLimit'))->render(),
            'verticalOption' => view('.user.poll.option_vertical',
                compact('settingsPoll', 'poll', 'isHaveImages', 'isLimit'))->render(),
            'html_result_vote' => view('user.poll.result_vote_layouts', compact('dataTableResult', 'isHaveImages'))->render(),
            'html_pie_bar_manage_chart' => view('user.poll.pie_bar_manage_chart_layouts')->render(),
            'html_pie_bar_chart' => view('user.poll.pie_bar_chart_layouts')->render(),
            'htmlPieChart' => view('user.poll.piechart_layouts', [
                'optionRatePieChart' => $optionRatePieChart,
                'isHaveImages' => $isHaveImages,
            ])->render(),
            'htmlBarChart' => view('user.poll.barchart_layouts', [
                'optionRateBarChart' => $optionRateBarChart,
                'chartNameData' => $chartNameData,
                'fontSize' => $fontSize,
            ])->render(),
        ]));

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
            $status = $this->pollRepository->checkIfEmailVoterExist($request->all());

            return response()->json(['status' => $status]);
        }
    }

    public function update(Request $request, $id)
    {
        $input = $request->only(['optionImage', 'optionText', 'optionDeleteImage']);
        if ($this->pollRepository->editVoted($id, $input)) {
            //get data of poll for socket
            $poll = $this->pollRepository->findPollById($id);
            $voteIds = $this->pollRepository->getVoteIds($poll->id);
            $votes = $this->voteRepository->getVoteWithOptionsByVoteId($voteIds);
            $participantVoteIds = $this->pollRepository->getParticipantVoteIds($poll->id);
            $participantVotes = $this->participantVoteRepository->getVoteWithOptionsByVoteId($participantVoteIds);
            $mergedParticipantVotes = $votes->toBase()->merge($participantVotes->toBase());
            $settingsPoll = $this->pollRepository->getSettingsPoll($id);

            // Show result options
            $optionDates = $this->pollRepository->showOptionDate($poll);

            if ($mergedParticipantVotes->count()) {
                foreach ($mergedParticipantVotes as $mergedParticipantVote) {
                    $createdAt[] = $mergedParticipantVote->first()->created_at;
                }

                $sortedParticipantVotes = collect($createdAt)->sort();
                $resultParticipantVotes = collect();
                foreach ($sortedParticipantVotes as $sortedParticipantVote) {
                    foreach ($mergedParticipantVotes as $mergedParticipantVote) {
                        foreach ($mergedParticipantVote as $participantVote) {
                            if ($participantVote->created_at == $sortedParticipantVote) {
                                $resultParticipantVotes->push($mergedParticipantVote);
                                break;
                            }
                        }
                    }
                }

                $mergedParticipantVotes = $resultParticipantVotes;
            }

            $isHaveImages = false;

            foreach ($poll->options as $option) {
                if ($option->image) {
                    $isHaveImages = true;
                    break;
                }
            }

            $numberOfVote = config('settings.default_value');
            $html = view('user.poll.vote_details_layouts', compact('optionDates'))->render();

             //data for draw chart
            $optionRateBarChart = [];
            $totalVote = config('settings.default_value');

            foreach ($poll->options as $option) {
                $totalVote += $option->countVotes();
            }

            if ($totalVote) {
                foreach ($poll->options as $option) {
                    $countOption = $option->countVotes();
                    if ($countOption) {
                        if ($isHaveImages) {
                            $optionRateBarChart[] = ['<img src="' . $option->showImage() .'" class="image-option-poll">' . '<span class="name-option-poll">' . $option->name . '</span>', $countOption];
                        } else {
                            $optionRateBarChart[] = ['<p>' . $option->name . '</p>', $countOption];
                        }
                    }
                }
            } else {
                $optionRateBarChart = null;
            }

            $optionRateBarChart = json_encode($optionRateBarChart);

            $optionRatePieChart = json_encode($this->pollRepository->getDataToDrawPieChart($poll, $isHaveImages));

            $chartNameData = json_encode($this->pollRepository->getNameOptionToDrawChart($poll, $isHaveImages));
            $fontSize = $this->pollRepository->getSizeChart($poll)['fontSize'];

            //get data result to sort number of vote
            $dataTableResult = $this->pollRepository->getDataTableResult($poll);

            //sort option and count vote by number of vote
            $dataTableResult = array_values(array_reverse(array_sort($dataTableResult, function ($value) {
                return $value['numberOfVote'];
            })));

            // Get result option horizontal
            $isTimeOut = false;
            $isHaveImages = false;
            $isLimit = false;
            $voteLimit = $settingsPoll[config('settings.setting.set_limit')]['value'];
            //count number of vote
            $countParticipantsVoted = $mergedParticipantVotes->count();

            foreach ($poll->options as $option) {
                if ($option->image) {
                    $isHaveImages = true;

                    break;
                }
            }

            if ($voteLimit && $countParticipantsVoted >= $voteLimit) {
                $isLimit = true;
            }

            //use socket.io
            $redis = LRedis::connection();
            $redis->publish('votes', json_encode([
                'result' => $poll->countVotesWithOption(),
                'poll_id' => $poll->id,
                'count_participant' => $mergedParticipantVotes->count(),
                'success' => true,
                'html' => $html,
                'horizontalOption' => view('.user.poll.option_horizontal', compact('settingsPoll', 'poll', 'isHaveImages', 'isLimit'))->render(),
                'verticalOption' => view('.user.poll.option_vertical', compact('settingsPoll', 'poll', 'isHaveImages', 'isLimit'))->render(),
                'html_result_vote' => view('user.poll.result_vote_layouts', compact('dataTableResult', 'isHaveImages'))->render(),
                'html_pie_bar_manage_chart' => view('user.poll.pie_bar_manage_chart_layouts')->render(),
                'html_pie_bar_chart' => view('user.poll.pie_bar_chart_layouts')->render(),
                'htmlPieChart' => view('user.poll.piechart_layouts', [
                    'optionRatePieChart' => $optionRatePieChart,
                    'isHaveImages' => $isHaveImages,
                ])->render(),
                'htmlBarChart' => view('user.poll.barchart_layouts', [
                    'optionRateBarChart' => $optionRateBarChart,
                    'chartNameData' => $chartNameData,
                    'fontSize' => $fontSize,
                ])->render(),
            ]));

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
}
