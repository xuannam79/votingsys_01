<?php

namespace App\Http\Controllers\User;

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
        if ($request->ajax()) {
            $inputs = $request->only('poll_id', 'password');
            $poll = $this->pollRepository->find($inputs['poll_id']);

            if (! $poll) {
                return response()->json(['success' => false]);
            }

            $password = Setting::where('poll_id', $inputs['poll_id'])->where('key', config('settings.setting.set_password'))->first()->value;

            if ($password && $password == $inputs['password']) {
                return response()->json(['success' => true]);
            }
        }

        return response()->json(['success' => false]);
    }
}
