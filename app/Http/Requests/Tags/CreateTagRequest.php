<?php

namespace App\Http\Requests\Tags;

use App\Rules\Colour;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Validation\Rule;

class CreateTagRequest extends FormRequest
{
    public function authorize(): bool
    {
        if (getCurrentUser()?->can('create-tag')) {
            return true;
        }

        return false;
    }

    public function rules()
    {
        $rules = [
            'title' => 'required|string|max:255|unique:tags,title',
            'colour' => ['required', new Colour],
            'tag_group_ids' => ['required_without:tag_group_slugs', 'array', 'min:1'],
            'tag_group_ids.*' => ['exists:tag_groups,id'],
            'tag_group_slugs' => ['required_without:tag_group_ids', 'array', 'min:1'],
            'tag_group_slugs.*' => ['exists:tag_groups,slug'],
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
