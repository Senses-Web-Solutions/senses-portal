<?php

namespace App\Http\Requests\Abilities;

use Illuminate\Foundation\Http\FormRequest;

class ReseedAbilitiesRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return getCurrentUser()->isAn('senses');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            //
        ];
    }
}
