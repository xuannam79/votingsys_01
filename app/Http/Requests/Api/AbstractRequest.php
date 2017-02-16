<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use constants;

abstract class AbstractRequest extends FormRequest
{
    /**
     * {@inheritdoc}
     */
    protected function formatErrors(Validator $validator)
    {
        return [
            'message' => [
                'status' => false,
                'code' => constants::API_RESPONSE_CODE_UNPROCESSABLE,
                'description' => $validator->errors()->all(),
            ],
        ];
    }
}
