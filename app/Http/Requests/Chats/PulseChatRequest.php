<?php

namespace App\Http\Requests\Chats;

use App\Models\AllowedChatSite;
use App\Models\ChatUser;
use Illuminate\Foundation\Http\FormRequest;

class PulseChatRequest extends FormRequest
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
            'chat_id' => 'required|integer',
            'x' => 'required|numeric',
            'y' => 'required|numeric',
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
