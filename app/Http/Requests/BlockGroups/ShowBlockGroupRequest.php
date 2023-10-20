<?php

namespace App\Http\Requests\BlockGroups;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ShowBlockGroupRequest extends FormRequest
{
    public function authorize(): bool
    {
		if (getCurrentUser()?->can('show-block-group')) {
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

//Generated 16-10-2023 10:39:10
