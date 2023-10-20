<?php

namespace App\Http\Requests\AbilityGroups;

use App\Rules\Colour;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Validation\Rule;

class CreateAbilityGroupRequest extends FormRequest
{

    public function authorize(): bool
    {

		if(getCurrentUser()?->can('create-ability-group')){
			return true;
		}

        return false;
    }

    public function rules()
    {
        $rules = [
			'title' => 'required|string|max:255',
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

//Generated 11-11-2021 08:27:41
