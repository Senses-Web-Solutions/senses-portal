<?php

namespace App\Http\Requests\Servers;

use App\Rules\Colour;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class CreateServerRequest extends FormRequest
{
    public function authorize(): bool
    {
		if (getCurrentUser()?->can('create-block-group')) {
			return true;
		}

        return false;
    }

    public function rules()
    {
        $rules = [
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

//Generated 16-10-2023 10:39:10
