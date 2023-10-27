<?php

namespace App\Http\Requests\ServerMetrics;

use App\Rules\Colour;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class CreateServerMetricRequest extends FormRequest
{
    public function authorize(): bool
    {
		// if (getCurrentUser()?->can('create-server-metric')) {
		// 	return true;
		// }

        return true;
    }

    public function rules()
    {
        $rules = [
			// 'server_id' => 'required|integer|exists:servers,id',
			// 'company_id' => 'required|integer|exists:companies,id',
			// 'timestamp' => 'required',
			// 'uptime' => 'required',
			// 'logged_at' => 'required',
			// 'cpu_cores' => 'nullable|integer',
			// 'cpu_threads' => 'nullable|integer',
			// 'cpu_use' => 'nullable',
			// 'cpu_idle' => 'nullable',
			// 'load_1' => 'nullable',
			// 'load_5' => 'nullable',
			// 'load_15' => 'nullable',
			// 'ram_free' => 'nullable|integer',
			// 'ram_used' => 'nullable|integer',
			// 'disk_free' => 'nullable|integer',
			// 'disk_used' => 'nullable|integer',
			// 'swap_free' => 'nullable|integer',
			// 'swap_used' => 'nullable|integer',
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

//Generated 27-10-2023 10:55:27
