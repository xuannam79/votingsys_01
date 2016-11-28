<?php

namespace App\Http\Controllers\User;

use Input;
use Illuminate\Http\Request;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Repositories\User\UserRepositoryInterface;
use App\Http\Requests\RegisterRequest;

class UsersController extends Controller
{

    protected $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function index()
    {
        $currentUser = auth()->user();

        return view('user.profile', compact('currentUser'));
    }

    public function store(RegisterRequest $request)
    {
        $inputs = $request->only('name', 'email', 'password', 'avatar', 'gender');

        return $this->userRepository->register($inputs);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update(UpdateUserRequest $request, $id)
    {
        try {
            $data = $request->only(['email', 'name', 'password', 'avatar', 'gender']);
            $this->userRepository->update($data, $id);
        } catch (Exception $e) {
            return view('user.poll.create')->withErrors(trans('message.update_error'));
        }

        return redirect()->to(url('/'))->withMessage(trans('user.update_profile_successfully'));
    }
}
