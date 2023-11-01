<?php

namespace App\Http\Requests\Servers;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class HideServerRequest extends FormRequest
{
    public function authorize(): bool
    {
		if (getCurrentUser()?->can('hide-server')) {
			return true;
		}

        return false;
    }

    public function rules()
    {
        $rules = [];

        return $rules; //todo swap for the below when finished
        // return app('App\Interfaces\Validation')->serverRules($rules, 'hide');
    }

    public function messages()
    {
        $messages = [];

        return $messages; //todo swap for the below when finished
        // return app('App\Interfaces\Validation')->serverMessages($messages, 'hide');
    }
}

//Generated 01-11-2023 11:27:41
