<?php

namespace App\Http\Requests\Chats;

use Illuminate\Foundation\Http\FormRequest;

class TypingRequest extends FormRequest
{
    public function authorize(): bool
    {
        if (getCurrentUser()?->can('create-message')) {
            return true;
        }

        return false;
    }

    public function rules()
    {
        $rules = [
            'chat_id' => 'required|integer|exists:chats,id',
            'name' => 'required|string|max:255',
            'from_agent' => 'required|boolean',
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
