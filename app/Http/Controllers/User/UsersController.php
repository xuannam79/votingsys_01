<?php

namespace App\Http\Controllers\User;

use Input;
use Illuminate\Http\Request;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Repositories\User\UserRepositoryInterface;

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
            return view('home')->withError(trans('message.update_error'));
        }

        return redirect()->action('HomeController@index');
    }
}
