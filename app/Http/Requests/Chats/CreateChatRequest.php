<?php

namespace App\Http\Requests\Chats;

use App\Models\AllowedChatSite;
use App\Models\ChatUser;
use Illuminate\Foundation\Http\FormRequest;

class CreateChatRequest extends FormRequest
{
    public function authorize(): bool
    {
        if (getCurrentUser()?->can('create-chat')) {
            // Get chat user from request
            $chatUser = ChatUser::where('uuid', $this->chat_user_uuid)->first();

            // Look if the system has the same company as the chatUser
            $allowedChatSite = AllowedChatSite::where('company_id', $chatUser->company_id)->where('url', $this->system)->first();

            if ($allowedChatSite) {
                return true;
            }
        }

        return false;
    }

    public function rules()
    {
        $rules = [
            'system' => 'required|string|max:255',
            'language' => 'nullable|string|max:50',
            'timezone' => 'nullable|string|max:50',
            'device_resolution' => 'nullable|string|max:50',
            'tab_resolution' => 'nullable|string|max:50',
            'browser' => 'nullable|string|max:50',
            'browser_version' => 'nullable|string|max:50',
            'os' => 'nullable|string|max:50',
            'os_version' => 'nullable|string|max:50',
            'device' => 'nullable|string|max:50',
            'meta' => 'nullable|array|max:255',
            'chat_user_uuid' => 'required|string|max:255',
            'message' => 'required|array|max:255',
            'message.content' => 'required|string|max:255',
            'message.chat_user_uuid' => 'required_if:message.from_agent,false|string|max:255',
            'message.from_agent' => 'required|boolean',
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
