<?php

namespace App\Http\Requests\ChatReviews;

use Illuminate\Foundation\Http\FormRequest;

class CreateChatReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        if (getCurrentUser()?->can('create-chat-review')) {
            return true;
        }

        return false;
    }

    public function rules()
    {
        $rules = [
            'chat_id' => 'required|integer|exists:chats,id',
            'knowledge' => 'required|integer|between:1,5',
            'friendliness' => 'required|integer|between:1,5',
            'responsiveness' => 'required|integer|between:1,5',
            'comment' => 'nullable|string',
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
