<?php

namespace App\Http\Requests\Chats;

use Illuminate\Foundation\Http\FormRequest;

class ChatInviteRequest extends FormRequest
{
    public function authorize(): bool
    {
        if (getCurrentUser()?->can('create-chat')) {
            return true;
        }

        return false;
    }

    public function rules()
    {
        $rules = [
            'chat_id' => 'required|integer|exists:chats,id',
            'agents' => 'required|array',
            'agents.*.id' => 'required|integer|exists:users,id',
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
