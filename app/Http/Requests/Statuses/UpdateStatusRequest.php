<?php

namespace App\Http\Requests\Statuses;

use App\Rules\Colour;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UpdateStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
		if (getCurrentUser()?->can('update-status')) {
			return true;
		}

        return false;
    }

    public function rules()
    {
        $rules = [
			'title' => 'required|string|max:255',
			'slug' => 'required|string|max:255',
			'colour' => ['required', new Colour],
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

//Generated 09-10-2023 12:35:29
