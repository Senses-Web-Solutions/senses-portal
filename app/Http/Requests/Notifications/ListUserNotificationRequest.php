<?php

namespace App\Http\Requests\Notifications;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ListUserNotificationRequest extends FormRequest
{

    public function authorize(): bool
    {
        if(getCurrentUser()?->can('list-notification')){
			return true;
		}

		if(getCurrentUser()?->can('list-own-notification') && getCurrentUser()->id == $this->user){
		    //changed to this to follow own/managed/company permissions convention,
            // left the below 'list-user-notification' check in case abilities have not been regenerated
			return true;
		}

		if(getCurrentUser()?->can('list-user-notification') && getCurrentUser()->id == $this->user){
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
