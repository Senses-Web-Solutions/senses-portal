<?php

namespace App\Http\Requests\CannedMessages;

use Illuminate\Foundation\Http\FormRequest;

class ListCannedMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        if (getCurrentUser()?->can('list-canned-message')) {
            return true;
        }

        return false;
    }

    public function rules()
    {
        $rules = [];

        return $rules;
    }

    public function bodyParameters(): array
    {
        return [];
    }

    public function messages()
    {
        $messages = [];

        return $messages;
    }
}

//Generated 27-10-2023 10:55:45
