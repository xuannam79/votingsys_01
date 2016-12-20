<?php

namespace App\Repositories\User;

use App\Models\Activity;
use App\Models\Comment;
use App\Models\Link;
use App\Models\Option;
use App\Models\ParticipantVote;
use App\Models\Setting;
use App\Models\Vote;
use Auth;
use App\Models\User;
use Input;
use App\Repositories\BaseRepository;
use App\Repositories\User\UserRepositoryInterface;
use DB;
use Mail;
use Flashy;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    public function __construct(User $user)
    {
        $this->model = $user;
    }

    public function checkEmailExist($email)
    {
        return $this->model->where('email', $email)->where('is_active', 1)->count();
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

        if ($data['gender'] == '') {
            $data['gender'] = null;
        }

        $user = [
            'name' => $data['name'],
            'email' => $data['email'],
            'chatwork_id' => isset($data['chatwork_id']) ? $data['chatwork_id'] : null,
            'avatar' => $fileName,
            'gender' => $data['gender'],
            'password' => $data['password'],
            'is_active' => false,
            'token_verification' => str_random(20),
        ];
        $createUser = User::create($user);

        //check email exist
        $emails = $data['email'];
        try {
            Mail::queue('layouts.register_mail', [
                'name' => $data['name'],
                'link' => url('/link/verification') . '/' . $createUser->id . '/' . $user['token_verification'],
            ], function ($message) use ($emails) {
                $message->to($emails)->subject(trans('label.mail.register.subject'));
            });
        } catch(Exception $ex) {
            return view('errors.show_errors')->with('message', trans('polls.register_with_mail_not_exist'));
        }

        if (!$createUser) {
            throw new Exception('message.create_error');
        }

        return redirect()->to(url('/login'))->withMessages(trans('user.register_account'));
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

            if ($inputs['gender'] == '') {
                $inputs['gender'] = null;
            }

            $oldImage = $currentUser->avatar;

            if (isset($inputs['avatar'])) {
                $inputs['avatar'] = $this->uploadAvatar($oldImage);
            } else {
                $inputs['avatar'] = $oldImage;
            }

            $data = $this->model->where('id', $id)->update($inputs);
        } catch (Exception $e) {
            throw new Exception(trans('user.message.update_fail'));
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

    public function delete($ids)
    {
        try {
            DB::beginTransaction();
            $user = User::with(
                'polls', 'participants', 'comments', 'socialAccounts', 'activities', 'votes'
            )->findOrFail($ids);

            foreach ($user->polls as $poll) {
                $options = Option::where('poll_id', $poll->id)->get();

                foreach ($options as $option) {
                    Vote::where('option_id', $option->id)->delete();
                    ParticipantVote::where('option_id', $option->id)->delete();

                    $option->delete();
                    Comment::where('poll_id', $poll->id)->delete();
                    Activity::where('poll_id', $poll->id)->delete();
                    Link::where('poll_id', $poll->id)->delete();
                    Setting::where('poll_id', $poll->id)->delete();
                }
            }

            foreach ($user->participants as $participant) {
                ParticipantVote::where('participant_id', $participant->id)->delete();
            }

            $user->polls()->delete();
            $user->participants()->delete();
            $user->comments()->delete();
            $user->socialAccounts()->delete();
            $user->activities()->delete();
            $user->votes()->delete();
            $user->delete();
            DB::commit();
            $message = trans('user.message.delete_success');
        } catch (Exception $ex) {
            DB::rollback();
            $message = trans('user.message.delete_fail');
        }

        return $message;
    }
}
