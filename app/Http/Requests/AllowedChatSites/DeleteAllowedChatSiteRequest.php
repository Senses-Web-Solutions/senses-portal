<?php

namespace App\Http\Requests\AllowedChatSites;

use Illuminate\Foundation\Http\FormRequest;

class DeleteAllowedChatSiteRequest extends FormRequest
{
    public function authorize(): bool
    {
        if (getCurrentUser()?->can('delete-allowed-chat-site')) {
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
