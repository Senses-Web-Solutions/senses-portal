<?php

namespace App\Http\Requests\Statuses;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class HideStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
		if (getCurrentUser()?->can('hide-status')) {
			return true;
		}

        return false;
    }

    public function rules()
    {
        $rules = [];

        return $rules; //todo swap for the below when finished
        // return app('App\Interfaces\Validation')->statusRules($rules, 'hide');
    }

    public function messages()
    {
        $messages = [];

        return $messages; //todo swap for the below when finished
        // return app('App\Interfaces\Validation')->statusMessages($messages, 'hide');
    }
}

//Generated 09-10-2023 12:35:29
