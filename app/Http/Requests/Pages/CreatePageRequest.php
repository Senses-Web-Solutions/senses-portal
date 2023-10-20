<?php

namespace App\Http\Requests\Pages;

use App\Rules\Colour;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class CreatePageRequest extends FormRequest
{
    public function authorize(): bool
    {
		if (getCurrentUser()?->can('create-page')) {
			return true;
		}

        return false;
    }

    public function rules()
    {
        $rules = [
            'title' => 'required|max:255',
            'slug' => 'nullable|unique:pages,slug',
            'type' => 'required',
            'featured' => 'required|boolean',
            'show_last_updated' => 'required|boolean',
			'status_id' => 'required|integer|exists:statuses,id',
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

//Generated 10-10-2023 14:43:35
