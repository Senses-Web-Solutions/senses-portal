<?php

namespace App\Http\Requests\UserSettings;

use App\Models\User;
use App\Models\UserSetting;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ShowUserSettingRequest extends FormRequest
{

    public function authorize(): bool
    {
        $currentUser = getCurrentUser();
        if ($currentUser->can('show-user-setting')) {
            return true;
        }

        if (getActualCurrentUser()->can('show-user-setting')) {
            return true;
        }

        if($currentUser->can('show-own-user-setting') && $currentUser->id == $this->route('user')) {
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
