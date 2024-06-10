<?php

namespace App\Http\Requests\AllowedChatSites;

use Illuminate\Foundation\Http\FormRequest;

class ShowAllowedChatSiteRequest extends FormRequest
{
    public function authorize(): bool
    {
        if (getCurrentUser()?->can('show-allowed-chat-site')) {
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
