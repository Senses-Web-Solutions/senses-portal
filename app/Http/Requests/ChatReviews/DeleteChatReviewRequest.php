<?php

namespace App\Http\Requests\ChatReviews;

use Illuminate\Foundation\Http\FormRequest;

class DeleteChatReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        if (getCurrentUser()?->can('delete-chat-review')) {
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

//Generated 27-10-2023 10:55:45
