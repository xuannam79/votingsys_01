<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Repositories\Poll\PollRepositoryInterface;
use Illuminate\Http\Request;

class CheckEmailController extends Controller
{
    private $pollRepository;

    public function __construct(PollRepositoryInterface $pollRepository)
    {
        $this->pollRepository = $pollRepository;
    }

    public function store(Request $request)
    {
        if ($request->ajax()) {
            $inputs = $request->only('poll', 'link', 'password');
            $this->pollRepository->sendMailAgain($inputs['poll'], $inputs['link'], $inputs['password']);

            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false]);
    }
}
