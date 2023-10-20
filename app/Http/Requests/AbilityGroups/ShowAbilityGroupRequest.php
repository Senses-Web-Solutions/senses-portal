<?php

namespace App\Http\Requests\AbilityGroups;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ShowAbilityGroupRequest extends FormRequest
{

    public function authorize(): bool
    {

		if(getCurrentUser()?->can('show-ability-group')){
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

//Generated 11-11-2021 08:27:41
