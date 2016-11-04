<?php

namespace App\Http\Controllers;

use App\Models\Link;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Repositories\Link\LinkRepositoryInterface;
use App\Repositories\Poll\PollRepositoryInterface;
use App\Repositories\Vote\VoteRepositoryInterface;
use App\Repositories\ParticipantVote\ParticipantVoteRepositoryInterface;

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
    public function index()
    {
        //
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
        $token = $request->value;
        $links = Link::where('token', $token)->get();

        if (! $links->count()) {
            return [
                'success' => false,
            ];
        }

        return [
            'success' => true,
            'link' => $links,
        ];
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
            return view('errors.show_errors')->with('message', trans('polls.poll_not_found'));
        }

        if (! $link->poll->status) {
            return view('errors.show_errors')->with('message', trans('polls.message_poll_closed'));
        }

        $linkUser = url('link') . '/' . $link->token;
        $numberOfVote = config('settings.default_value');
        $voteLimit = null;
        $isRequiredEmail = false;
        $isHideResult = false;
        $poll = $link->poll;

        if ($poll->settings) {
            foreach ($poll->settings as $setting) {
                if ($setting->key == config('settings.setting.set_limit')) {
                    $voteLimit = $setting->value;
                }

                $isRequiredEmail = ($setting->key == config('settings.setting.required_email'));
                $isHideResult = ($setting->key == config('settings.setting.hide_result'));
            }

            if ($voteLimit && $poll->countParticipants() >= $voteLimit) {
                return view('errors.show_errors')->with('message', trans('polls.message_poll_limit'));
            }
        }

        if (! $link->link_admin) {


            $isRequiredEmail = $poll->settings->whereIn('key', [config('settings.setting.required_email')])->count() != config('settings.default_value');
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
                        if (isset($item->participant) && $item->participant->ip_address == $request->ip()) {
                            $isParticipantVoted = true;
                            break;
                        }
                    }
                }
            }

            return view('user.poll.details', compact('poll', 'isRequiredEmail', 'isUserVoted', 'isHideResult', 'numberOfVote', 'linkUser', 'mergedParticipantVotes', 'isParticipantVoted', 'requiredPassword'));
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

            return view('user.poll.manage_poll', compact('poll', 'tokenLinkUser', 'tokenLinkAdmin', 'isRequiredEmail', 'isUserVoted', 'isHideResult', 'numberOfVote', 'linkUser', 'mergedParticipantVotes', 'isParticipantVoted'));
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
