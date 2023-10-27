<?php

namespace App\Http\Requests\ServerMetrics;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class DeleteServerMetricRequest extends FormRequest
{
    public function authorize(): bool
    {
		if (getCurrentUser()?->can('delete-server-metric')) {
			return true;
		}

        return false;
    }

    public function rules()
    {
        $rules = [];

        return $rules;
    }

    public function messages()
    {
        $messages = [];

        return $messages;
    }
}

//Generated 27-10-2023 10:55:27
