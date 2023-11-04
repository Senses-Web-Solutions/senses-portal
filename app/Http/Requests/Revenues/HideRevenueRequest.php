<?php

namespace App\Http\Requests\Revenues;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class HideRevenueRequest extends FormRequest
{
    public function authorize(): bool
    {
		if (getCurrentUser()?->can('hide-revenue')) {
			return true;
		}

        return false;
    }

    public function rules()
    {
        $rules = [];

        return $rules; //todo swap for the below when finished
        // return app('App\Interfaces\Validation')->revenueRules($rules, 'hide');
    }

    public function messages()
    {
        $messages = [];

        return $messages; //todo swap for the below when finished
        // return app('App\Interfaces\Validation')->revenueMessages($messages, 'hide');
    }
}

//Generated 04-11-2023 16:09:26
