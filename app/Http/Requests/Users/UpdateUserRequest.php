<?php

namespace App\Http\Requests\Users;

use App\Rules\Colour;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
		if (getCurrentUser()?->can('update-user')) {
			return true;
		}

        return false;
    }

    public function rules()
    {
        $rules = [
			'first_name' => 'required|string|max:255',
			'last_name' => 'required|string|max:255',
			'email' => ['required', 'string', 'max:255', Rule::unique('users','email')->ignore($this->input('email'), 'email')],
			'email_verified_at' => 'nullable',
			'password' => 'nullable|string|max:255',
        ];

        return $rules;
    }

    public function bodyParameters(): array
    {
        return [];
    }

    public function messages()
    {
        $messages = [];

        return $messages;
    }
}

//Generated 10-10-2023 10:05:12
