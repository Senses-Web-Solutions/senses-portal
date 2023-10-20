<?php

namespace App\Http\Requests\Files;

use App\Rules\Colour;
use Illuminate\Validation\Rule;
use App\Rules\MorphRelationExists;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Database\Eloquent\Relations\Relation;

class CreateFileRequest extends FormRequest
{
    public function authorize(): bool
    {
		if (getCurrentUser()?->can('create-file')) {
			return true;
		}

        return false;
    }

    public function rules()
    {
        $rules = [
			'file' => 'required|file|max:200000',
            'public' => 'boolean|nullable',
            'app_visible' => 'boolean|nullable',
            'disk' => 'string|in:remote,local',
            'fileables' => 'array',
            'fileables.*.fileable_id' => [
                'required',
                new MorphRelationExists('type')
            ],
            'fileables.*.fileable_type' => [
                'required',
                Rule::in(array_keys(Relation::morphMap())),
            ],
            'pending'  => ['bool', 'nullable']
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

//Generated 09-10-2023 13:46:51
