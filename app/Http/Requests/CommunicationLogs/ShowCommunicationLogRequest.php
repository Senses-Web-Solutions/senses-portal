<?php

namespace App\Http\Requests\CommunicationLogs;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ShowCommunicationLogRequest extends FormRequest
{
    public function authorize(): bool
    {
		if (getCurrentUser()?->can('show-communication-log')) {
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

//Generated 04-11-2023 16:09:50
