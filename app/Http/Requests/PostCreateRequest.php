<?php

namespace App\Http\Requests;

class PostCreateRequest extends PostRequest
{
    public function rules(): array
    {
        $rules = parent::rules();

        return array_merge($rules, [
            'title' => ['required'],
        ]);
    }

    public function authorize(): bool
    {
        return true;
    }
}
