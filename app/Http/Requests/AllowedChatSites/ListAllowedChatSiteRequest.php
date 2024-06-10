<?php

namespace App\Http\Requests\AllowedChatSites;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ListAllowedChatSiteRequest extends FormRequest
{
    public function authorize(): bool
    {
        if (getCurrentUser()?->can('list-allowed-chat-site')) {
            return true;
        }

        return false;
    }

    public function rules()
    {
        $rules = [];

        return $rules; //todo swap for the below when finished
        // return app('App\Interfaces\Validation')->companyRules($rules, 'list');
    }

    public function messages()
    {
        $messages = [];

        return $messages; //todo swap for the below when finished
        // return app('App\Interfaces\Validation')->companyMessages($messages, 'list');
    }
}

//Generated 27-10-2023 10:55:45
