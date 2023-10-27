<?php

namespace App\Http\Requests\Servers;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ListServerRequest extends FormRequest
{
    public function authorize(): bool
    {
		if (getCurrentUser()?->can('list-server')) {
			return true;
		}

        return false;
    }

    public function rules()
    {
        $rules = [];

        return $rules; //todo swap for the below when finished
        // return app('App\Interfaces\Validation')->serverRules($rules, 'list');
    }

    public function messages()
    {
        $messages = [];

        return $messages; //todo swap for the below when finished
        // return app('App\Interfaces\Validation')->serverMessages($messages, 'list');
    }
}

//Generated 27-10-2023 10:53:42
