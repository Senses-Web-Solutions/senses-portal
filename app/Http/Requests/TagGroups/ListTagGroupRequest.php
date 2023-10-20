<?php

namespace App\Http\Requests\TagGroups;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ListTagGroupRequest extends FormRequest
{
    public function authorize(): bool
    {
		if (getCurrentUser()?->can('list-tag-group')) {
			return true;
		}

        return false;
    }

    public function rules()
    {
        $rules = [];

        return $rules; //todo swap for the below when finished
        // return app('App\Interfaces\Validation')->tagGroupRules($rules, 'list');
    }

    public function messages()
    {
        $messages = [];

        return $messages; //todo swap for the below when finished
        // return app('App\Interfaces\Validation')->tagGroupMessages($messages, 'list');
    }
}

//Generated 09-10-2023 10:26:55
