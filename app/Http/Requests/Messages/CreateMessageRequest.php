<?php

namespace App\Http\Requests\Messages;

use Illuminate\Foundation\Http\FormRequest;

class CreateMessageRequest extends FormRequest
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
            'content' => 'required|string',
            'chat_user_uuid' => 'required_if:from_agent,false|string|max:255',
            'from_agent' => 'required|boolean',
            'meta' => 'nullable|array|max:255',
            'file_ids' => 'nullable|array',
            'file_ids.*' => 'integer|exists:files,id',
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
