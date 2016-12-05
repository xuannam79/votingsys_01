<?php

namespace App\Http\Controllers;

use Session;
use App\Models\Link;
use App\Models\ParticipantVote;
use App\Models\User;
use App\Models\Vote;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Repositories\Link\LinkRepositoryInterface;
use App\Repositories\Poll\PollRepositoryInterface;
use App\Repositories\Vote\VoteRepositoryInterface;
use App\Repositories\ParticipantVote\ParticipantVoteRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class LinkController extends Controller
{
    protected $linkRepository;
    protected $pollRepository;
    protected $voteRepository;
    protected $participantVoteRepository;

    public function __construct(
        LinkRepositoryInterface $linkRepository,
        PollRepositoryInterface $pollRepository,
        VoteRepositoryInterface $voteRepository,
        ParticipantVoteRepositoryInterface $participantVoteRepository
    ) {
        $this->linkRepository = $linkRepository;
        $this->pollRepository = $pollRepository;
        $this->voteRepository = $voteRepository;
        $this->participantVoteRepository = $participantVoteRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($userId, $tokenRegister)
    {
        $user = User::where('token_verification', $tokenRegister)->first();

        if (! $user) {
            return view('errors.show_errors')->with('message', trans('polls.link_not_found'));
        }

        if ($userId == $user->id) {
            $user->is_active = true;
            $user->token_verification = '';
            $user->save();

            if (! Auth::login($user)) {
                return redirect()->to(url('/'))->withMessage(trans('user.register_account_successfully'));
            } else {
                return redirect()->to(url('/'))->withMessage(trans('user.register_account_fail'));
            }
        }

        return view('errors.404');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $token = $request->token;
        $links = Link::where('token', $token)->get();

        if ($links->count()) {
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($token, Request $request)
    {
        //get MAC address
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

        $link = $this->linkRepository->getPollByToken($token);

        if (! $link) {
            return view('errors.404');
        }

        $linkUser = url('link') . '/' . $link->token;
        $numberOfVote = config('settings.default_value');
        $voteLimit = null;
        $isRequiredEmail = false;
        $isLimit = false;
        $isHideResult = false;
        $poll = $link->poll;
        $totalVote = config('settings.default_value');
        $isSetIp = false;

        foreach ($poll->options as $option) {
            $totalVote += $option->countVotes();
        }

        $optionRatePieChart = [];
        $optionRateBarChart = [];

        if ($totalVote) {
            foreach ($poll->options as $option) {
                $countOption = $option->countVotes();
                $optionRatePieChart[$option->name] = (int) ($countOption * 100 / $totalVote);
                if ($countOption > 0) {
                    $optionRateBarChart[] = [str_limit($option->name, 30), $countOption];
                }
            }
        } else {
            $optionRatePieChart = null;
            $optionRateBarChart = null;
        }

        if (! empty($optionRateBarChart)) {
            $optionRateBarChart = array_sort_recursive($optionRateBarChart);
        }

        $optionRateBarChart = json_encode($optionRateBarChart);
        $requiredPassword = null;
        $passwordSetting = $poll->settings->whereIn('key', [config('settings.setting.set_password')])->first();
        $isRequiredEmail = $poll->settings->whereIn('key', [config('settings.setting.required_email')])->count() != config('settings.default_value');
        $dataTableResult = $this->pollRepository->getDataTableResult($poll, $isRequiredEmail);

        //sort option and count vote by number of vote
        $dataTableResult = array_values(array_reverse(array_sort($dataTableResult, function($value)
        {
            return $value['numberOfVote'];
        })));

        if (! $link->link_admin) {
            if ($link->poll->isClosed()) {
                return view('errors.show_errors')->with('message', trans('polls.message_poll_closed'));
            }

            //check time close poll
            if (Carbon::now()->toAtomString() > Carbon::parse($poll->date_close)->toAtomString()) {
                $poll->status = false;
                $poll->save();

                return view('errors.show_errors')->with('message', trans('polls.message_poll_closed'));
            }

            if ($poll->settings) {
                foreach ($poll->settings as $setting) {
                    if ($setting->key == config('settings.setting.set_limit')) {
                        $voteLimit = $setting->value;
                    }

                    if ($setting->key == config('settings.setting.is_set_ip')) {
                        $isSetIp = true;
                    }

                    $isHideResult = ($setting->key == config('settings.setting.hide_result'));
                }
            }

            if ($voteLimit && $poll->countParticipants() >= $voteLimit) {
                $isLimit = true;
            }

            if(! Session::has('isInputPassword')) {
                if ($passwordSetting) {
                    $requiredPassword = $passwordSetting->value;

                    return view('user.poll.input_password', compact('poll', 'requiredPassword', 'token'));
                }
            } elseif (! Session::get('isInputPassword')) {
                $requiredPassword = $passwordSetting->value;
                Session::forget('isInputPassword');

                return view('user.poll.input_password', compact('poll', 'requiredPassword', 'token'))->withErrors(trans('polls.incorrect_password'));
            }

            Session::forget('isInputPassword');

            $isHideResult = $poll->settings->whereIn('key', [config('settings.setting.hide_result')])->count() != config('settings.default_value');
            $voteIds = $this->pollRepository->getVoteIds($poll->id);
            $votes = $this->voteRepository->getVoteWithOptionsByVoteId($voteIds);
            $participantVoteIds = $this->pollRepository->getParticipantVoteIds($poll->id);
            $participantVotes = $this->participantVoteRepository->getVoteWithOptionsByVoteId($participantVoteIds);
            $mergedParticipantVotes = $votes->toBase()->merge($participantVotes->toBase());

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

            $isUserVoted = false;
            $isParticipantVoted = false;

            if (auth()->check()) {
                $isUserVoted = $this->pollRepository->checkUserVoted($poll->id, $this->voteRepository);
            } else {
                foreach ($participantVotes as $participantVote) {
                    foreach($participantVote as $item) {
                        if (isset($item->participant) && $item->participant->ip_address == $ip) {
                            $isParticipantVoted = true;
                            break;
                        }
                    }
                }
            }

            return view('user.poll.details', compact(
                'poll', 'isRequiredEmail', 'isUserVoted', 'isHideResult', 'numberOfVote', 'linkUser', 'mergedParticipantVotes', 'isParticipantVoted', 'requiredPassword',
                'optionRatePieChart', 'isSetIp', 'optionRateBarChart', 'isLimit', 'dataTableResult'
            ));
        } else {
            $poll = $link->poll;
            $voteIds = $this->pollRepository->getVoteIds($poll->id);
            $votes = $this->voteRepository->getVoteWithOptionsByVoteId($voteIds);
            $participantVoteIds = $this->pollRepository->getParticipantVoteIds($poll->id);
            $participantVotes = $this->participantVoteRepository->getVoteWithOptionsByVoteId($participantVoteIds);
            $mergedParticipantVotes = $votes->toBase()->merge($participantVotes->toBase());

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

            foreach ($poll->links as $link) {
                if ($link->link_admin) {
                    $tokenLinkAdmin = $link->token;
                } else {
                    $tokenLinkUser = $link->token;
                }
            }

            //get data contain config or message return view and js
            $data = $this->pollRepository->getDataPollSystem();
            $page = 'manager';

            //statistic
            $statistic = [
                'total' => $this->pollRepository->getTotalVotePoll($poll),
                'firstTime' => $this->pollRepository->getTimeFirstVote($poll),
                'lastTime' => $this->pollRepository->getTimeLastVote($poll),
                'largestVote' => $this->pollRepository->getOptionLargestVote($poll),
                'leastVote' => $this->pollRepository->getOptionLeastVote($poll),
            ];

            $settings = $this->pollRepository->showSetting($poll->settings);

            return view('user.poll.manage_poll', compact(
                'poll', 'tokenLinkUser', 'tokenLinkAdmin',
                'isRequiredEmail', 'isUserVoted', 'isHideResult', 'numberOfVote',
                'linkUser', 'mergedParticipantVotes', 'isParticipantVoted',
                'settings', 'data', 'page', 'statistic', 'dataTableResult', 'optionRateBarChart', 'optionRatePieChart', 'isSetIp'
            ));
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
