<?php

namespace App\Http\Controllers\Api\Auth;

use Illuminate\Http\Request;
use App\RepositoriesApi\UserRepositoryEloquent;
use App\Http\Requests\Api\UserCreateRequest;
use App\Http\Controllers\Api\ApiController;

class RegisterController extends ApiController
{
    protected $userRepository;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(UserRepositoryEloquent $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  App\Http\Requests\Api\UserCreateRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    protected function create(UserCreateRequest $request)
    {
        $input = $request->all();

        $user = $this->userRepository->createUser($input);

        if (empty($user)) {
            return $this->falseJson(API_RESPONSE_CODE_INTER_SERVER_ERROR, trans('user.message.create_fail'));
        }

        return $this->trueJson($user, trans('user.message.create_success'));
    }
}
