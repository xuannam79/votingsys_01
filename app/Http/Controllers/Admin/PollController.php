<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Poll;
use App\Repositories\Poll\PollRepositoryInterface;
use Illuminate\Http\Request;
use App\Http\Requests\PollRequest;

class PollController extends Controller
{
    private $pollRepository;

    public function __construct(PollRepositoryInterface $pollRepository)
    {
        $this->pollRepository = $pollRepository;
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
        $data = json_encode([
            'message' => [
                'numberOfOptions' => config('settings.length_poll.option'),
                'config' => [
                    'invite_all' => config('settings.participant.invite_all'),
                    'invite_people' => config('settings.participant.invite_people'),
                ],
                'link_exists' => trans('polls.message.link_exists'),
                'link_valid' => trans('polls.message.link_valid'),
                'submit_form' => trans('polls.message.submit_form'),
            ],
            'view' => [
                'option' => view('layouts.poll_option')->render(),
                'email' => view('layouts.poll_email')->render(),
            ],
            'oldInput' => session('_old_input'),
        ]);

        return view('admins.poll.create', compact('data'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  PollRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PollRequest $request)
    {
        $input = $request->only(
            'email', 'title', 'description', 'location', 'type',
            'option', 'optionImage',
            'required_email', 'add_answer', 'hide_result',
            'custom_link', 'link',
            'set_limit', 'limit',
            'set_password', 'password_poll',
            'invite', 'email_poll'
        );
        $message = $this->pollRepository->store($input);

        return redirect()->route('admin.poll.index')->with('message', $message);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
