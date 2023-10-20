<?php

namespace App\Http\Requests\Files;

use App\Rules\Colour;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UpdateFileRequest extends FormRequest
{
    public function authorize(): bool
    {
		if (getCurrentUser()?->can('update-file')) {
			return true;
		}

        return false;
    }

    public function rules()
    {
        $rules = [
			'name' => 'required|string|max:255',
			'stored_name' => 'required|string|max:255',
			'path' => 'required|string|max:255',
			'folder' => 'required|string|max:255',
			'mime_type' => 'required|string|max:255',
			'extension' => 'required|string|max:255',
			'size' => 'required|integer',
			'disk' => 'nullable|string|max:255',
			'preview_path' => 'required|string|max:255',
			'preview_disk' => 'required|string|max:255',
			'print_path' => 'required|string|max:255',
			'print_disk' => 'required|string|max:255',
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
