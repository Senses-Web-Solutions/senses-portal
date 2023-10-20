<?php

namespace App\Http\Requests\Abilities;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ListAbilityRequest extends FormRequest
{

    public function authorize(): bool
    {

		if(getCurrentUser()?->can('list-ability')){
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