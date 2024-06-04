<?php

namespace App\Http\Requests\Chats;

use Illuminate\Foundation\Http\FormRequest;

class HideChatRequest extends FormRequest
{
    public function authorize(): bool
    {
        if (getCurrentUser()?->can('hide-chat')) {
            return true;
        }

        return false;
    }

    public function rules()
    {
        $rules = [];

        return $rules; //todo swap for the below when finished
        // return app('App\Interfaces\Validation')->companyRules($rules, 'hide');
    }

    public function messages()
    {
        $messages = [];

        return $messages; //todo swap for the below when finished
        // return app('App\Interfaces\Validation')->companyMessages($messages, 'hide');
    }
}

//Generated 27-10-2023 10:55:45
