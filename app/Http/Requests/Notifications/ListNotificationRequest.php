<?php

namespace App\Http\Requests\Notifications;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ListNotificationRequest extends FormRequest
{

    public function authorize(): bool
    {

		if(getCurrentUser()?->can('list-notification')){
			return true;
		}

        return false;
    }

    public function rules()
    {
        $rules = [];

        return $rules;
    }

    public function messages()
    {
        $messages = [];

        return $messages;
    }
}

//Generated 15-09-2021 14:33:59
