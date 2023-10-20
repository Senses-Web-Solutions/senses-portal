<?php

namespace App\Http\Requests\AbilityGroups;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ListAbilityGroupRequest extends FormRequest
{

    public function authorize(): bool
    {

		if(getCurrentUser()?->can('list-ability-group')){
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