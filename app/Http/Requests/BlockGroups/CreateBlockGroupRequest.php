<?php

namespace App\Http\Requests\BlockGroups;

use App\Rules\Colour;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class CreateBlockGroupRequest extends FormRequest
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
			'display_name' => 'required|string|max:255',
			'name' => 'required|string|max:255',
			'builder_category_id' => 'nullable|integer|exists:builder_categories,id',
			'description' => 'nullable|string|max:255',
			'block_group_types' => 'nullable',
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
