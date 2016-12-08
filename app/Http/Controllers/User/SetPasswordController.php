<?php

namespace App\Http\Controllers\User;

use Session;
use App\Models\Setting;
use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\Poll\PollRepositoryInterface;

class SetPasswordController extends Controller
{
    protected $pollRepository;

    public function __construct(PollRepositoryInterface $pollRepository)
    {
        $this->pollRepository = $pollRepository;
    }

    public function store(Request $request)
    {
        $inputs = $request->only('poll_id', 'password', 'token');
        $poll = $this->pollRepository->find($inputs['poll_id']);

        if (! $poll) {
            return view('errors.404');
        }

        $password = Setting::where('poll_id', $inputs['poll_id'])->where('key', config('settings.setting.set_password'))->first()->value;
        $url = url('/link') . '/' . $inputs['token'];

        if ($password && $password == $inputs['password']) {
            Session::put('isInputPassword', true);
        } else {
            Session::put('isInputPassword', false);
        }

        return redirect()->to($url);
    }
}
