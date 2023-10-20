<?php

namespace App\Http\Requests\UserSettings;

use App\Models\User;
use App\Rules\Locked;
use App\Models\UserSetting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;

class DeleteUserSettingRequest extends FormRequest
{

    public function authorize(): bool
    {
        $currentUser = getCurrentUser();
        if ($currentUser->can('delete-user-setting')) {
            return true;
        }

        $userSetting = UserSetting::where('setting', $this->route('setting'))->where('user_id', $currentUser->id)->firstOrFail(['id', 'user_id']);
        $user = User::findOrFail($userSetting->user_id, ['id', 'company_id', 'line_manager_id', 'secondary_line_manager_id']);

        if (
            (isset($user) && $currentUser->can('delete-own-user-setting') && $currentUser->id === $userSetting->user_id) ||

            (isset($user) && $currentUser->can('delete-managed-user-setting') && $currentUser->id === $user->line_manager_id) ||
            (isset($user) && $currentUser->can('delete-managed-user-setting') && $currentUser->id === $user->secondary_line_manager_id) ||

            (isset($user) && $currentUser->can('delete-company-user-setting') && $currentUser->company_id === $user->company_id)
        ) {
            return true;
        }

        return false;
    }

    public function prepareForValidation()
    {
        $validator = validator(['id' => $this->route('user_setting')], ['id' => [new Locked(UserSetting::class)]]);
        if ($validator->fails()) {
            $this->failedValidation($validator);
        }
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
