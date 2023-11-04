<?php

namespace App\Http\Requests\Revenues;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ListRevenueRequest extends FormRequest
{
    public function authorize(): bool
    {
		if (getCurrentUser()?->can('list-revenue')) {
			return true;
		}

        return false;
    }

    public function rules()
    {
        $rules = [];

        return $rules; //todo swap for the below when finished
        // return app('App\Interfaces\Validation')->revenueRules($rules, 'list');
    }

    public function messages()
    {
        $messages = [];

        return $messages; //todo swap for the below when finished
        // return app('App\Interfaces\Validation')->revenueMessages($messages, 'list');
    }
}

//Generated 04-11-2023 16:09:26
