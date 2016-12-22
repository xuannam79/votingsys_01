<?php

namespace App\Services;

use App\Models\Link;
use Illuminate\Support\Facades\Validator;
use Request;
use Carbon\Carbon;
use App\Models\User;

class PollValidator extends Validator
{

    public function option($attribute, $value, $parameters, $validator)
    {
        $data = Request::file($parameters[0]);

        foreach ($value as $option) {
            if ($option) {
                return true;
            }
        }

        return $data;
    }

    public function participant($attribute, $value, $parameters, $validator)
    {
        $data = Request::input($parameters[0]);

        if ($data == config('settings.participant.invite_all')) {
            return true;
        }

        $emails = explode(",", $value);

        foreach ($emails as $email) {
            if (! $email || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return false;
            }
        }

        return true;
    }

    public function setting($attribute, $value, $parameters, $validator)
    {
        $data = Request::input($parameters[0]);
        $settingConfig = config('settings.setting');
        $lengthConfig = config('settings.length_poll');

        if ($data) {
            foreach ($data as $setting) {
                if ($setting == $settingConfig['custom_link']) {
                    $token = $value['link'];

                    if (! ($token && strlen($token) <= $lengthConfig['link'])) {

                         return false;
                     }

                    $link = Link::where('token', $token)->count();

                    if ($link) {
                        return false;
                    }
                }

                if ($setting == $settingConfig['set_limit']) {
                    $numberLimit = $value['limit'];
                    if (! ($numberLimit
                        && is_numeric($numberLimit)
                        && strlen($numberLimit) <= $lengthConfig['number_limit']
                    )) {
                        return false;
                    }
                }

                 if ($setting == $settingConfig['set_password']) {
                     $passwordOfPoll = $value['password'];
                     if (! ($passwordOfPoll && strlen($passwordOfPoll) <= $lengthConfig['password_poll'])) {
                         return false;
                     }
                 }
            }
        }

        return true;
    }

    public function information($attribute, $value, $parameters, $validator)
    {
        if ($value) {
            return Carbon::now()->format('d/m/Y h:i') < Carbon::parse($value)->format('d/m/Y h:i');
        }

        return true;
    }

    public function updateEmail($attribute, $value, $parameters, $validator)
    {
        if (! auth()->check()) {
            return false;
        }

        if (auth()->user()->email == $value) {
            return true;
        }

        return User::where('email', $value)->count() == config('settings.default_value');
    }
}
