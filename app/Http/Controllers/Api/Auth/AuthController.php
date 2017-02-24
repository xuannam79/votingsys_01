<?php

namespace App\Http\Controllers\Api\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Api\LoginRequest;
use App\RepositoriesApi\UserRepositoryEloquent;
use Illuminate\Support\Facades\Auth;
use App\Services\PassportService;

class AuthController extends ApiController
{
    protected $userRepository;

    public function __construct(UserRepositoryEloquent $userRepository)
    {
        parent::__construct();
        $this->userRepository = $userRepository;
    }

    /**
     * Login
     * @param  App\Http\Requests\Api\LoginRequest  $request
     * @param  App\Services\PassportService $passport
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(LoginRequest $request, PassportService $passport)
    {
        $dataAttempt = $request->only('email', 'password');
        $user = $this->userRepository->findBy('email', $dataAttempt['email'])->first();

        if (! $user) {
            return $this->falseJson(API_RESPONSE_CODE_UNPROCESSABLE, trans('user.login_fail'));
        }

        if ($user->is_active == config('settings.is_active')) {
            if (Auth::guard('web')->attempt($dataAttempt)) {
                return $this->trueJson($passport->passwordGrantToken($dataAttempt));
            }

            return $this->falseJson(API_RESPONSE_CODE_UNPROCESSABLE, trans('user.login_fail'));
        }

        return $this->falseJson(API_RESPONSE_CODE_UNPROCESSABLE, trans('user.account_unactive'));
    }

    /**
     * Log Out
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logOut()
    {
        if ($this->currentUser) {
            $this->currentUser->token()->revoke();

            return $this->trueJson([], ['message' => trans('user.logout_success')]);
        }

        return $this->falseJson(API_RESPONSE_CODE_UNPROCESSABLE, trans('user.logout_fail'));
    }
}
