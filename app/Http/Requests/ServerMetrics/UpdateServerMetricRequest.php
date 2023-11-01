<?php

namespace App\Http\Requests\ServerMetrics;

use App\Rules\Colour;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UpdateServerMetricRequest extends FormRequest
{
    public function authorize(): bool
    {
		if (getCurrentUser()?->can('update-server-metric')) {
			return true;
		}

        return false;
    }

    public function rules()
    {
        $rules = [
			'server_id' => 'required|integer|exists:servers,id',
			'company_id' => 'required|integer|exists:companies,id',
			'timestamp' => 'nullable|integer|min:0',
			'uptime' => 'nullable|integer|min:0',
			'cpu_use' => 'nullable',
			'cpu_us' => 'nullable',
			'cpu_sy' => 'nullable',
			'cpu_ni' => 'nullable',
			'cpu_id' => 'nullable',
			'cpu_wa' => 'nullable',
			'cpu_hi' => 'nullable',
			'cpu_si' => 'nullable',
			'cpu_st' => 'nullable',
			'load_1' => 'nullable',
			'load_5' => 'nullable',
			'load_15' => 'nullable',
			'ram_total' => 'nullable',
			'ram_free' => 'nullable',
			'ram_buffer' => 'nullable',
			'ram_cache' => 'nullable',
			'ram_used' => 'nullable',
			'swap_total' => 'nullable',
			'swap_free' => 'nullable',
			'swap_used' => 'nullable',
			'swap_cache' => 'nullable',
			'disk_total' => 'nullable|integer|min:0',
			'disk_free' => 'nullable|integer|min:0',
			'disk_used' => 'nullable|integer|min:0',
			'disk_read' => 'nullable|integer|min:0',
			'disk_write' => 'nullable|integer|min:0',
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

//Generated 01-11-2023 11:22:36
