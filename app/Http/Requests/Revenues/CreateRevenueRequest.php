<?php

namespace App\Http\Requests\Revenues;

use App\Rules\Colour;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class CreateRevenueRequest extends FormRequest
{
    public function authorize(): bool
    {
		if (getCurrentUser()?->can('create-revenue')) {
			return true;
		}

        return false;
    }

    public function rules()
    {
        $rules = [
			'company_id' => 'required|integer|exists:companies,id',
			'revenue_date' => 'required',
			'reference' => 'required|string|max:255',
			'description' => 'nullable|string',
			'amount' => 'required|numeric',
			'quantity' => 'required',
			'vat' => 'required',
			'sub_total' => 'required|numeric',
			'vat_total' => 'required|numeric',
			'total' => 'required|numeric',
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

//Generated 04-11-2023 16:09:26
