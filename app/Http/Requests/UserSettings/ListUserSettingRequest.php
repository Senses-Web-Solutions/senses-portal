<?php

namespace App\Http\Requests\UserSettings;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ListUserSettingRequest extends FormRequest
{

    public function authorize(): bool
    {

		if(getCurrentUser()?->can('list-user-setting')){
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

//Generated 07-09-2021 09:54:24
