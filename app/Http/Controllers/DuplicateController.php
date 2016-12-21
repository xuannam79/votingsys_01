<?php
namespace App\Http\Controllers;

use App\Http\Requests\PollRequest;
use App\Models\Poll;
use App\Repositories\Poll\PollRepositoryInterface;
use Illuminate\Http\Request;

class DuplicateController extends Controller
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
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = $request->only(
            'title', 'location', 'description', 'name', 'email', 'chatwork_id', 'type', 'closingTime',
            'optionText', 'optionImage', 'oldImage', 'optionOldImage',
            'setting', 'value', 'setting_child',
            'member'
        );
        $input['page'] = 'duplicate';
        $data = $this->pollRepository->store($input);

        if ($data) {
            return redirect()->to(
                url(config('settings.link_poll.result_create') . $data['poll']->id . '/' . $data['link']['administration'])
            );
        }

        $message = trans('polls.message.duplicate_fail');

        return redirect()->route('duplicate.show', ['id' => $request->id])->with('message', $message);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = $this->pollRepository->getDataPollSystem();
        $poll = Poll::with('user', 'options', 'settings')->find($id);
        $setting = $poll->settings->pluck('value', 'key')->toArray();
        $page = 'duplicate';

        return view('user.poll.duplicate', compact('poll', 'data', 'setting', 'page'));
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
