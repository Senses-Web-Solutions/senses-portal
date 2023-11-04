<?php

namespace App\Http\Requests\Subscriptions;

use App\Rules\Colour;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UpdateSubscriptionRequest extends FormRequest
{
    public function authorize(): bool
    {
		if (getCurrentUser()?->can('update-subscription')) {
			return true;
		}

        return false;
    }

    public function rules()
    {
        $rules = [
			'company_id' => 'required|integer|exists:companies,id',
			'type' => 'required|string|max:255',
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

//Generated 04-11-2023 16:09:38
