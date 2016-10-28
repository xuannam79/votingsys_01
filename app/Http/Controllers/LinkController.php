<?php

namespace App\Http\Controllers;

use App\Models\Link;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Repositories\Link\LinkRepositoryInterface;
use App\Repositories\Poll\PollRepositoryInterface;
use App\Repositories\Vote\VoteRepositoryInterface;

class LinkController extends Controller
{
    protected $linkRepository;
    protected $pollRepository;
    protected $voteRepository;

    public function __construct(
        LinkRepositoryInterface $linkRepository,
        PollRepositoryInterface $pollRepository,
        VoteRepositoryInterface $voteRepository
    ) {
        $this->linkRepository = $linkRepository;
        $this->pollRepository = $pollRepository;
        $this->voteRepository = $voteRepository;
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
    public function show($token)
    {
        $link = $this->linkRepository->getPollByToken($token);

        if (! $link) {
            return view('errors.show_errors')->with('message', trans('polls.poll_not_found'));
        }

        if (! $link->poll->status) {
            return view('errors.show_errors')->with('message', trans('polls.message_poll_closed'));
        }

        if (! $link->link_admin) {
            $linkUser = url('link') . '/' . $link->token;
            $poll = $link->poll;
            $voteLimit = null;
            $isRequiredEmail = false;

            $isHideResult = false;
            $voteLimit = null;

            if ($poll->settings) {
                foreach ($poll->settings as $setting) {
                    if ($setting->key == config('settings.hide_result')) {
                        $isHideResult = true;
                    }

                    if ($setting->key == config('settings.set_limit')) {
                        $voteLimit = $setting->value;
                    }
                }

                if ($voteLimit && $poll->countParticipants() >= $voteLimit) {
                    return view('errors.show_errors')->with('message', trans('polls.message_poll_limit'));
                }
            }

            return view('user.poll.details', compact('poll', 'isHideResult', 'isRequiredEmail', 'linkUser'));
        }

        return false;
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
