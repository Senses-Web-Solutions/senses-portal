<?php

namespace App\Http\Requests\CannedMessages;

use Illuminate\Foundation\Http\FormRequest;

class CreateCannedMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        if (getCurrentUser()?->can('create-canned-message')) {
            return true;
        }

        return false;
    }

    public function rules()
    {
        $rules = [
            'user_id' => 'required|integer|exists:users,id',
            'content' => 'required|string',
            'title' => 'required|string',
            'system' => 'required|string',
            'shortcut' => 'required|string',
        ];

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
