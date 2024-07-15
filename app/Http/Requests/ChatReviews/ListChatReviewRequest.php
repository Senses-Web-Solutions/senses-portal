<?php

namespace App\Http\Requests\ChatReviews;

use Illuminate\Foundation\Http\FormRequest;

class ListChatReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        if (getCurrentUser()?->can('list-chat-review')) {
            return true;
        }

        return false;
    }

    public function rules()
    {
        $rules = [];

        return $rules; //todo swap for the below when finished
    }

    public function messages()
    {
        $messages = [];

        return $messages; //todo swap for the below when finished
    }
}

//Generated 27-10-2023 10:55:45
