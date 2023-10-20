<?php

namespace App\Http\Requests\StatusGroups;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ShowStatusGroupRequest extends FormRequest
{
    public function authorize(): bool
    {
		if (getCurrentUser()?->can('show-status-group')) {
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

//Generated 09-10-2023 12:05:02
