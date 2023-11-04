<?php

namespace App\Http\Requests\Subscriptions;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ListSubscriptionRequest extends FormRequest
{
    public function authorize(): bool
    {
		if (getCurrentUser()?->can('list-subscription')) {
			return true;
		}

        return false;
    }

    public function rules()
    {
        $rules = [];

        return $rules; //todo swap for the below when finished
        // return app('App\Interfaces\Validation')->subscriptionRules($rules, 'list');
    }

    public function messages()
    {
        $messages = [];

        return $messages; //todo swap for the below when finished
        // return app('App\Interfaces\Validation')->subscriptionMessages($messages, 'list');
    }
}

//Generated 04-11-2023 16:09:38
