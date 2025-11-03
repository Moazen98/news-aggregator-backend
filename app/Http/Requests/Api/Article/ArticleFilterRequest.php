<?php

namespace App\Http\Requests\Api\Article;

use App\Http\Requests\Api\ApiParentRequest;
use Illuminate\Foundation\Http\FormRequest;

class ArticleFilterRequest extends ApiParentRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => 'required',
            'from' => [
                'nullable',
                'date',
            ],
            'to' => [
                'nullable',
                'date',
            ],
        ];
    }

    public function withValidator($validator)
    {
        $validator->sometimes('from', 'before_or_equal:to', function ($input) {
            return !empty($input->to);
        });

        $validator->sometimes('to', 'after_or_equal:from', function ($input) {
            return !empty($input->from);
        });
    }

    public function messages(): array
    {
        return [
            'type.required' => 'The api type field is required',
            'from.date'            => 'The from field must be a valid date.',
            'to.date'              => 'The to field must be a valid date.',
            'from.before_or_equal' => 'The from date must be before or equal to the to date.',
            'to.after_or_equal'    => 'The to date must be after or equal to the from date.',
        ];
    }
}
