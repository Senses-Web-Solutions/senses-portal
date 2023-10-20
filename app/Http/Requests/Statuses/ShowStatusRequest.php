<?php

namespace App\Http\Requests\Statuses;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ShowStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
		if (getCurrentUser()?->can('show-status')) {
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

//Generated 09-10-2023 12:35:29
