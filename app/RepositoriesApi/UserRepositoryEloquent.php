<?php

namespace App\RepositoriesApi;

use App\Models\User;
use App\RepositoriesApi\Contracts\UserRepositoryInterface;
use App\Mail\RegisterUser;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Exception;

class UserRepositoryEloquent extends AbstractRepositoryEloquent implements UserRepositoryInterface
{
    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    /**
     * Create User
     * @param  array  $input
     * @return boolean
     */
    public function createUser($input = [])
    {
        $fileName = isset($input['avatar'])
            ? uploadImage($input['avatar'], config('settings.avatar_path'))
            : config('settings.avatar_default');

        $user = [
            'name' => $input['name'],
            'email' => $input['email'],
            'chatwork_id' => isset($input['chatwork_id']) ? $input['chatwork_id'] : null,
            'avatar' => $fileName,
            'gender' => isset($input['gender']) ? $input['gender'] : null,
            'password' => $input['password'],
            'is_active' => false,
            'token_verification' => str_random(20),
        ];

        DB::beginTransaction();
        try {
            $createUser = $this->create($user);
            DB::commit();

            // Send email to active user
            Mail::to($input['email'])->queue(new RegisterUser($createUser));

            return $createUser;
        } catch (Exception $e) {
            DB::rollback();

            return false;
        }
    }
}
