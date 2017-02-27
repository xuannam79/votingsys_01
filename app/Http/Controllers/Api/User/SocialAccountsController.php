<?php

namespace App\Http\Controllers\Api\User;

use App\RepositoriesApi\Contracts\SocialAccountRepositoryInterface;
use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Api\SocialRequest;
use App\RepositoriesApi\Contracts\UserRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use App\Services\PassportService;
use App\SocialAccountService;
use Illuminate\Http\Request;
use App\Services\FacebookService;
use Socialite;

class SocialAccountsController extends ApiController
{

    protected $socialAccountRepository;
    protected $userRepository;

    public function __construct(
        SocialAccountRepositoryInterface $socialAccountRepository,
        UserRepositoryInterface $userRepository)
    {
        $this->socialAccountRepository = $socialAccountRepository;
        $this->userRepository = $userRepository;
    }

    public function loginSocial(Request $request, PassportService $passportService)
    {
        $data = $request->only('token', 'provider');

        if (!in_array($data['provider'], config('settings.provider'))) {
            return $this->falseJson(API_RESPONSE_CODE_UNPROCESSABLE, trans('user.not_found_provider'));
        }

        if ($data['token'] && $data['provider']) {
            $user = $this->socialAccountRepository->createOrGetUser(
                Socialite::driver($data['provider']), $data);

            if ($user) {
                return $this->trueJson([
                    'user' => $user,
                    'token' => $passportService->getTokenByUser($user)
                ]);
            }

            return $this->falseJson(API_RESPONSE_CODE_UNPROCESSABLE, trans('user.login_fail'));
        }

        return $this->falseJson(API_RESPONSE_CODE_BAD_REQUEST, trans('user.not_enough_info'));
    }
}
