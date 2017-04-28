<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Api\ChangePasswordRequest;
use App\RepositoriesApi\Contracts\UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;

class ChangePasswordController extends ApiController
{
    protected $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        parent::__construct();
        $this->userRepository = $userRepository;
    }

    public function changePassword(ChangePasswordRequest $request)
    {
        $inputs = $request->only('old_password', 'password');

        if (empty($this->currentUser)) {
            return $this->falseJson(API_RESPONSE_CODE_UNPROCESSABLE, trans('polls.message.not_found_user'));
        }

        if ($this->currentUser->socialAccounts()->count() > 0) {
            return $this->falseJson(API_RESPONSE_CODE_UNPROCESSABLE, trans('messages.error.is_social_account'));
        }

        if (!Hash::check($inputs['old_password'], $this->currentUser->password)) {
            return $this->falseJson(API_RESPONSE_CODE_UNPROCESSABLE, trans('messages.error.password_false'));
        }

        if (!$this->userRepository->changePassword($inputs, $this->currentUser)) {
            return $this->falseJson(API_RESPONSE_CODE_UNPROCESSABLE, trans('messages.error.reset_password_error'));
        }

        return $this->trueJson(true, trans('messages.success.reset_password'));
    }
}
