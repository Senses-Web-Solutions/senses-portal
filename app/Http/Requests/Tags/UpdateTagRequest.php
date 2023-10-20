<?php

namespace App\Http\Requests\Tags;

use App\Rules\Colour;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Validation\Rule;

class UpdateTagRequest extends FormRequest
{
    public function authorize(): bool
    {
        if (getCurrentUser()?->can('update-tag')) {
            return true;
        }

        return false;
    }

    public function rules()
    {
        $rules = [
            'title' => ['required', 'string', 'max:255', Rule::unique('tags', 'title')->ignore($this->input('title'), 'title')],
            'slug' => 'required|string|max:255',
            'colour' => ['required', new Colour],
            'tag_group_ids' => ['required', 'array', 'min:1'],
            'tag_group_ids.*' => ['exists:tag_groups,id'],
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

//Generated 13-10-2021 11:49:44
