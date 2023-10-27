<?php

namespace App\Http\Requests\ServerMetrics;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ListServerMetricRequest extends FormRequest
{
    public function authorize(): bool
    {
		if (getCurrentUser()?->can('list-server-metric')) {
			return true;
		}

        return false;
    }

    public function rules()
    {
        $rules = [];

        return $rules; //todo swap for the below when finished
        // return app('App\Interfaces\Validation')->serverMetricRules($rules, 'list');
    }

    public function messages()
    {
        $messages = [];

        return $messages; //todo swap for the below when finished
        // return app('App\Interfaces\Validation')->serverMetricMessages($messages, 'list');
    }
}

//Generated 27-10-2023 10:55:27
