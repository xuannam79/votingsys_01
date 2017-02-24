<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

abstract class AbstractRequest extends FormRequest
{
    /**
     * {@inheritdoc}
     */
    public function formatErrors(Validator $validator)
    {
        return [
            'error' => true,
            'status' => API_RESPONSE_CODE_UNPROCESSABLE,
            'messages' => $validator->errors()->all(),
        ];
    }
}
