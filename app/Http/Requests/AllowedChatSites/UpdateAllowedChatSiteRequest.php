<?php

namespace App\Http\Requests\AllowedChatSites;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAllowedChatSiteRequest extends FormRequest
{
    public function authorize(): bool
    {
        if (getCurrentUser()?->can('update-allowed-chat-site')) {
            return true;
        }

        return false;
    }

    public function rules()
    {
        $rules = [
            'company_id' => 'nullable|integer|exists:companies,id',
            'title' => 'required|string|max:255',
            'url' => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) {
                    if (!app()->environment('local')) {
                        if (substr($value, 0, 7) === 'http://') {
                            $fail($attribute . ' must not start with "http://".');
                        }
                        if (!preg_match('/^(https:\/\/)?(www\.)?[a-z0-9.-]+\.[a-z]{2,}$/', $value)) {
                            $fail($attribute . ' is not a valid URL.');
                        }
                    }
                },
            ],
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

//Generated 27-10-2023 10:55:45
