<?php

namespace App\Http\Requests\ChatUsers;

use Illuminate\Foundation\Http\FormRequest;

class CreateChatUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        if (getCurrentUser()?->can('create-chat-user')) {
            return true;
        }

        return false;
    }

    public function rules()
    {
        $rules = [
            'company_id' => 'required|integer|exists:companies,id',
            'system' => 'required|string|max:255',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'full_name' => 'required|string|max:255',
            'email' => 'nullable|string|max:255',
            'external_id' => 'nullable|string|max:255',
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
