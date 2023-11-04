<?php

namespace App\Http\Requests\CommunicationLogs;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ListCommunicationLogRequest extends FormRequest
{
    public function authorize(): bool
    {
		if (getCurrentUser()?->can('list-communication-log')) {
			return true;
		}

        return false;
    }

    public function rules()
    {
        $rules = [];

        return $rules; //todo swap for the below when finished
        // return app('App\Interfaces\Validation')->communicationLogRules($rules, 'list');
    }

    public function messages()
    {
        $messages = [];

        return $messages; //todo swap for the below when finished
        // return app('App\Interfaces\Validation')->communicationLogMessages($messages, 'list');
    }
}

//Generated 04-11-2023 16:09:50
