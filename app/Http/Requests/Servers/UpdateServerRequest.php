<?php

namespace App\Http\Requests\Servers;

use App\Rules\Colour;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UpdateServerRequest extends FormRequest
{
    public function authorize(): bool
    {
		if (getCurrentUser()?->can('update-server')) {
			return true;
		}

        return false;
    }

    public function rules()
    {
        $rules = [
			'company_id' => 'required|integer|exists:companies,id',
			'ip' => 'required|string|max:255',
			'os' => 'required|string|max:255',
			'priority' => 'required|integer',
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

//Generated 27-10-2023 10:53:42
