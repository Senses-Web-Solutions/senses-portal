<?php

namespace App\Http\Requests\Messages;

use App\Models\AllowedChatSite;
use Illuminate\Foundation\Http\FormRequest;

class ReadMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        if (getCurrentUser()?->can('show-message')) {
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
