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
			'title' => 'required|string|max:255',
			'hostname' => 'required|string|max:255',
			'ip_address' => 'required|string|max:255',
			'os' => 'required|string|max:255',
			'architecture' => 'nullable|string|max:255',
			'cpu_cores' => 'nullable|integer',
			'cpu_threads' => 'nullable|integer',
			'distro' => 'nullable|string|max:255',
			'distro_version' => 'nullable|string|max:255',
			'kernel' => 'nullable|string|max:255',
			'kernel_version' => 'nullable|string|max:255',
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

//Generated 01-11-2023 11:27:41
