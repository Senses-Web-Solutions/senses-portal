<?php

namespace App\Http\Requests\Chats;

use Illuminate\Foundation\Http\FormRequest;

class CreateChatRequest extends FormRequest
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
            'company_id' => 'required|integer|exists:companies,id',
            'system' => 'required|string|max:255',
            'meta' => 'nullable|array|max:255',
            'chat_user_uuid' => 'required|string|max:255',
            'message' => 'required|array|max:255',
            'message.content' => 'required|string|max:255',
            'message.chat_user_uuid' => 'required_if:message.from_agent,false|string|max:255',
            'message.from_agent' => 'required|boolean',
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
