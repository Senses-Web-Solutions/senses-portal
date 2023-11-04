<?php

namespace App\Http\Requests\Revenues;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class LockRevenueRequest extends FormRequest
{
    public function authorize(): bool
    {
		if (getCurrentUser()?->can('lock-revenue')) {
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

//Generated 04-11-2023 16:09:26
