<?php

namespace App\Http\Requests\Api;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ApiParentRequest extends FormRequest
{


    protected function failedValidation(Validator $validator)
    {
        $errorKeys = $validator->errors()->keys();
        throw new HttpResponseException(response()->json([
            'key' => $errorKeys[0],
            'message' => $validator->errors()->first(),
            'errors' => $validator->errors(),
            'status' => false,
            'code' => 422
        ], 422));
    }
}
