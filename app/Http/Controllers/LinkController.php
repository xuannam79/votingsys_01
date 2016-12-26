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
        $link = $this->linkRepository->getPollByToken($token);

        if (! $link) {
            return view('errors.404');
        }

        $linkUser = url('link') . '/' . $link->token;
        $numberOfVote = config('settings.default_value');
        $voteLimit = null;
        $isRequiredEmail = false;
        $isRequiredName = false;
        $isRequiredNameAndEmail = false;
        $isLimit = false;
        $isHideResult = false;
        $isTimeOut = false;
        $poll = $link->poll;
        $totalVote = config('settings.default_value');

        //get information vote poll
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

        //count number of vote
        $countParticipantsVoted = $mergedParticipantVotes->count();

        // check option have image?
        $isHaveImages = false;

        $totalVote = [];
        foreach ($poll->options as $option) {
            $totalVote[$option->id] = $option->countVotes();

            if ($option->image) {
                $isHaveImages = true;
            }
        }

        $optionRateBarChart = [];

        if (array_sum($totalVote)) {
            foreach ($poll->options as $option) {
                $countOption = $totalVote[$option->id];

                if ($countOption > 0) {
                    $optionRateBarChart[] = [$option->name, $countOption];
                }
            }
        } else {
            $optionRateBarChart = null;
        }

        $optionRateBarChart = json_encode($optionRateBarChart);
        $dataTableResult = $this->pollRepository->getDataTableResult($poll);

        //sort option and count vote by number of vote
        $dataTableResult = array_values(array_reverse(array_sort($dataTableResult, function($value)
        {
            return $value['numberOfVote'];
        })));

        if (! $link->link_admin) {
            if ($link->poll->isClosed()) {
                return view('errors.show_errors')->with('message', trans('polls.message_poll_closed'))->with('pollId', $poll->id);
            }

            //check time close vote when time out
            if (Carbon::now()->toAtomString() > Carbon::parse($poll->date_close)->toAtomString()) {
                $isTimeOut = true;
            }

            $requiredPassword = null;

            //get all settings of poll
            $listSettings = [];
            if ($poll->settings) {
                foreach ($poll->settings as $setting) {
                    $listSettings[] = $setting->key;

                    if ($setting->key == config('settings.setting.set_limit')) {
                        $voteLimit = $setting->value;
                    }

                    if ($setting->key == config('settings.setting.set_password')) {
                        $requiredPassword = $setting->value;
                    }
                }

                if (collect($listSettings)->contains(config('settings.setting.required_name'))) {
                    $isRequiredName = true;
                }

                if (collect($listSettings)->contains(config('settings.setting.required_name_and_email'))) {
                    $isRequiredNameAndEmail = true;
                }

                if (collect($listSettings)->contains(config('settings.setting.required_email'))) {
                    $isRequiredEmail = true;
                }

                if (collect($listSettings)->contains(config('settings.setting.hide_result'))) {
                    $isHideResult = true;
                }

                if ($voteLimit && $countParticipantsVoted >= $voteLimit) {
                    $isLimit = true;
                }
            }

            if(! Session::has('isInputPassword')) {
                if ($requiredPassword) {

                    return view('user.poll.input_password', compact('poll', 'requiredPassword', 'token'));
                }
            } elseif (! Session::get('isInputPassword')) {
                Session::forget('isInputPassword');

                return view('user.poll.input_password', compact('poll', 'requiredPassword', 'token'))->withErrors(trans('polls.incorrect_password'));
            }

            Session::forget('isInputPassword');

            $isUserVoted = false;

            if (auth()->check()) {
                $isUserVoted = $this->pollRepository->checkUserVoted($poll->id, $this->voteRepository);
            }

            return view('user.poll.details', compact(
                'poll', 'numberOfVote', 'linkUser',
                'isRequiredEmail', 'isRequiredName', 'isRequiredNameAndEmail',
                'isHideResult', 'isLimit', 'requiredPassword',
                'isUserVoted', 'isTimeOut', 'optionRateBarChart',
                'dataTableResult', 'mergedParticipantVotes',
                'countParticipantsVoted', 'isHaveImages'
            ));
        } else {
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
            $settings = $this->pollRepository->showSetting($poll->settings);

            return view('user.poll.manage_poll', compact(
                'poll', 'tokenLinkUser', 'tokenLinkAdmin', 'numberOfVote',
                'linkUser', 'mergedParticipantVotes', 'isHaveImages',
                'settings', 'data', 'page', 'statistic', 'dataTableResult',
                'optionRateBarChart', 'optionRatePieChart', 'countParticipantsVoted'
            ));
        }
    }
}
