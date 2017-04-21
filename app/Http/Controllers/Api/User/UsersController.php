<?php

namespace App\Http\Controllers\Api\User;

use App\RepositoriesApi\Contracts\UserRepositoryInterface;
use App\RepositoriesApi\Contracts\PollRepositoryInterface;
use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Api\UserEditRequest;
use App\Services\PassportService;
use Auth;
use Input;

class UsersController extends ApiController
{
    protected $userRepository;
    protected $pollRepository;
    protected $passportService;

    public function __construct(
        UserRepositoryInterface $userRepository,
        PollRepositoryInterface $pollRepository,
        PassportService $passportService
    ) {
        parent::__construct();
        $this->userRepository = $userRepository;
        $this->pollRepository = $pollRepository;
        $this->passportService = $passportService;
    }

    public function show($id)
    {
        $user = $this->userRepository->find($id);

        if (!$user) {
            return $this->falseJson(API_RESPONSE_CODE_NOT_FOUND, trans('messages.error.not_found'));
        }

        return $this->trueJson($user);
    }

    public function updateProfile(UserEditRequest $request)
    {
        if (empty($this->currentUser)) {
            return $this->falseJson(API_RESPONSE_CODE_NOT_FOUND, trans('messages.error.not_found'));
        }

        $data = $request->only([
            'email',
            'name',
            'password',
            'gender',
            'chatwork_id',
        ]);
        $result = $this->userRepository->updateUser($data, Input::file('avatar'), $this->currentUser->id);

        if (!$result) {
            return $this->falseJson(API_RESPONSE_CODE_UNPROCESSABLE, trans('messages.error.update_profile_error'));
        }

        $this->currentUser->token()->revoke();

        return $this->trueJson([
            'user' => $result,
            'token' => $result->is_active ? $this->passportService->getTokenByUser($result) : null,
        ], $result->is_active ? trans('user.update_profile_successfully') : trans('user.account_unactive'));
    }

    public function getProfile()
    {
        if (empty($this->currentUser)) {
            return $this->falseJson(API_RESPONSE_CODE_UNPROCESSABLE, trans('polls.message.not_found_user'));
        }

        $user = clone $this->currentUser;
        $user['countParticipatedPoll'] = $this->pollRepository->getParticipatedPolls($this->currentUser)->count();
        $user['countCreatedPoll'] = $this->currentUser->polls()->count();

        return $this->trueJson($user);
    }
}
