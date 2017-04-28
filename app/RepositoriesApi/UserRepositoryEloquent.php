<?php

namespace App\RepositoriesApi;

use App\Models\User;
use App\RepositoriesApi\Contracts\UserRepositoryInterface;
use App\Mail\RegisterUser;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Exception;
use Input;

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

    public function updateUser($inputs = [], $avatar = null, $id)
    {
        $currentUser = $this->model->find($id);

        if (!empty($inputs['email']) && $inputs['email'] != $currentUser->email) {
            $inputs['token_verification'] = str_random(20);
            $inputs['is_active'] = false;
        }

        if (!$inputs['gender']) {
            $inputs['gender'] = null;
        }

        $oldImage = $currentUser->avatar;

        if ($avatar) {
            $inputs['avatar'] = uploadImage($avatar, config('settings.avatar_path'), $oldImage);
        } else {
            $inputs['avatar'] = $oldImage;
        }

        DB::beginTransaction();
        try {
            $user = $this->model->find($id);
            $user->update($inputs);
            DB::commit();

            Mail::to($inputs['email'])->queue(new RegisterUser($user));

            return $user;
        } catch (Exception $e) {
            DB::rollBack();

            return false;
        }
    }

    public function changePassword($inputs = [], $currentUser)
    {
        if (!empty($inputs['password'])) {
            $inputs['password'] = bcrypt($inputs['password']);
        } else {
            $inputs['password'] = $currentUser->password;
        }

        DB::beginTransaction();
        try {
            $currentUser->update($inputs);
            DB::commit();

            return true;
        } catch (Exception $e) {
            DB::rollBack();

            return false;
        }
    }
}
