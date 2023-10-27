<?php

namespace App\Http\Requests\Servers;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class DeleteServerRequest extends FormRequest
{
    public function authorize(): bool
    {
		if (getCurrentUser()?->can('delete-server')) {
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

//Generated 27-10-2023 10:53:42
