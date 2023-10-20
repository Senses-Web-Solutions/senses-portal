<?php

namespace App\Http\Requests\Files;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class LockFileRequest extends FormRequest
{
    public function authorize(): bool
    {
		if (getCurrentUser()?->can('lock-file')) {
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

//Generated 09-10-2023 13:46:51
