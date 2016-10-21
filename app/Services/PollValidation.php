<?php

namespace App\Validation;

use App\Models\Link;
use Illuminate\Support\Facades\Validator;
use Request;

class PollValidator extends Validator
{

    public function option($attribute, $value, $parameters, $validator)
    {
        $data = Request::file($parameters[0]);

        foreach ($value as $optionContent) {
            if ($optionContent) {
                return true;
            }
        }

        return !is_null($data);
    }

    public function email($attribute, $value, $parameters, $validator)
    {
        $data = Request::input($parameters[0]);

        if ($data == config('settings.participant.invite_all')) {
            return true;
        }

        foreach ($value as $input) {
            if ($input) {
                return true;
            }
        }

        return false;
    }

    public function setting($attribute, $value, $parameters, $validator)
    {
        $data = Request::input($parameters[0]);

        if ($attribute == config('settings.input_setting.link') ||
            $attribute == config('settings.input_setting.password') ||
            $attribute == config('settings.input_setting.limit')) {
            if ($value && ! $data) {
                return false;
            }
        }

        if ($attribute == config('settings.input_setting.limit') && !is_numeric($data)) {
                return false;
        }

        if ($attribute == config('settings.input_setting.link') &&
            Link::where('token', $data)->count()) {
            return false;
        }

        return true;
    }
}
