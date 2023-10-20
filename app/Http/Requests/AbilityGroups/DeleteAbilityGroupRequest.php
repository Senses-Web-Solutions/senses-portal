<?php

namespace App\Http\Requests\AbilityGroups;

use App\Models\AbilityGroup;
use App\Rules\Locked;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;

class DeleteAbilityGroupRequest extends FormRequest
{

    public function authorize(): bool
    {

		if(getCurrentUser()?->can('delete-ability-group')){
			return true;
		}

        return false;
    }

    public function prepareForValidation() {
        $validator = validator(['id' => $this->route('ability_group')], ['id' => [new Locked(AbilityGroup::class)]]);
        if($validator->fails()) {
            $this->failedValidation($validator);
        }
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
