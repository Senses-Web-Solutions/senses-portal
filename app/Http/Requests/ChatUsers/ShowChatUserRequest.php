<?php

namespace App\Http\Requests\ChatUsers;

use Illuminate\Foundation\Http\FormRequest;

class ShowChatUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        if (getCurrentUser()?->can('show-chat-user')) {
            return true;
        }

        return false;
    }

    public function rules()
    {
        $rules = [];

        return $rules;
    }

    public function messages()
    {
        $messages = [];

        return $messages;
    }
}

//Generated 27-10-2023 10:55:45
