<?php

namespace App\Http\Requests\Pages;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ListPageRequest extends FormRequest
{
    public function authorize(): bool
    {
		if (getCurrentUser()?->can('list-page')) {
			return true;
		}

        return false;
    }

    public function rules()
    {
        $rules = [];

        return $rules; //todo swap for the below when finished
        // return app('App\Interfaces\Validation')->pageRules($rules, 'list');
    }

    public function messages()
    {
        $messages = [];

        return $messages; //todo swap for the below when finished
        // return app('App\Interfaces\Validation')->pageMessages($messages, 'list');
    }
}

//Generated 10-10-2023 14:43:35
