<?php

namespace App\Http\Requests\Users;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ListChatInviteUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        $companyID = request()->query('company');
        $chatID = request()->query('chat');

        if ($companyID && $chatID) {
            $user = getCurrentUser();
            if ($user && $user->can('list-user') && $user->company_id == $companyID) {
                return true;
            }
        }

        return false;
    }

    public function rules()
    {
        $rules = [];

        return $rules; //todo swap for the below when finished
        // return app('App\Interfaces\Validation')->userRules($rules, 'list');
    }

    public function messages()
    {
        $messages = [];

        return $messages; //todo swap for the below when finished
        // return app('App\Interfaces\Validation')->userMessages($messages, 'list');
    }
}

//Generated 10-10-2023 10:05:12
