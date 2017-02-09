<?php

namespace App\Http\Controllers\Api\User;

use App\Repositories\User\UserRepositoryInterface;
use App\Http\Controllers\Api\ApiController;

class UsersController extends ApiController
{

    protected $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function show($id)
    {
        $user = $this->userRepository->find($id);

        if (!$user) {
            return $this->false_json(\constants::API_RESPONSE_CODE_NOT_FOUND, trans('messages.error.not_found'));
        }

        return $this->true_json($user);
    }
}
