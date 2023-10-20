<?php

namespace App\Http\Requests\TagGroups;

use App\Rules\Colour;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UpdateTagGroupRequest extends FormRequest
{
    public function authorize(): bool
    {
		if (getCurrentUser()?->can('update-tag-group')) {
			return true;
		}

        return false;
    }

    public function rules()
    {
        $rules = [
			'title' => 'required|string|max:255',
			'slug' => 'required|string|max:255',
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

//Generated 09-10-2023 10:26:55
