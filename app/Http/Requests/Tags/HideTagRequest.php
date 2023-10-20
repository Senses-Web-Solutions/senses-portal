<?php

namespace App\Http\Requests\Tags;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class HideTagRequest extends FormRequest
{
    public function authorize(): bool
    {
		if (getCurrentUser()?->can('hide-tag')) {
			return true;
		}

        return false;
    }

    public function rules()
    {
        $rules = [];

        return $rules; //todo swap for the below when finished
        // return app('App\Interfaces\Validation')->tagRules($rules, 'hide');
    }

    public function messages()
    {
        $messages = [];

        return $messages; //todo swap for the below when finished
        // return app('App\Interfaces\Validation')->tagMessages($messages, 'hide');
    }
}

//Generated 09-10-2023 10:18:19
