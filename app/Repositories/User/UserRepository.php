<?php

namespace App\Repositories\User;

use Auth;
use App\Models\User;
use Input;
use App\Repositories\BaseRepository;
use App\Repositories\User\UserRepositoryInterface;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    public function __construct(User $user)
    {
        $this->model = $user;
    }

    public function checkEmailExist($email)
    {
        return $this->model->where('email', $email)->count();
    }

    public function createUserSocial($data)
    {
        return $this->model->create($data);
    }

    public function getUserWithEmail($providerUser)
    {
        return $this->model->whereEmail($providerUser->getEmail())->first();
    }

    public function register(array $data)
    {
        if (isset($data['avatar'])) {
            $fileName = $this->uploadAvatar();
        } else {
            $fileName =  config('settings.avatar_default');
        }

        $user = [
            'name' => $data['name'],
            'email' => $data['email'],
            'avatar' => $fileName,
            'gender' => $data['gender'],
            'password' => $data['password'],
        ];

        $createUser = User::create($user);

        if (!$createUser) {
            throw new Exception('message.create_error');
        }

        return $createUser;
    }

    public function update($inputs, $id)
    {
        try {
            $currentUser = $this->model->find($id);

            if (!empty($inputs['password'])) {
                $inputs['password'] = bcrypt($inputs['password']);
            } else {
                unset($inputs['password']);
            }

            $oldImage = $currentUser->avatar;

            if (isset($inputs['avatar'])) {
                $inputs['avatar'] = $this->uploadAvatar($oldImage);
            } else {
                $inputs['avatar'] = $oldImage;
            }

            $data = $this->model->where('id', $id)->update($inputs);
        } catch (Exception $e) {
            return view('user.home')->withError(trans('message.update_error'));
        }

        return $data;
    }

    public function uploadAvatar($oldImage = null)
    {
        $file = Input::file('avatar');
        $fileName = uniqid(rand(), true) . '.' . $file->getClientOriginalExtension();
        $file->move(public_path(config('settings.avatar_path')), $fileName);

        if (!empty($oldImage) && file_exists($oldImage)) {
            File::delete($oldImage);
        }

        return $fileName;
    }
}
