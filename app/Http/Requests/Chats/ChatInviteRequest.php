<?php

namespace App\Http\Requests\Chats;

use App\Models\Chat;
use Illuminate\Foundation\Http\FormRequest;

class ChatInviteRequest extends FormRequest
{
    public function authorize(): bool
    {
        if (getCurrentUser()?->can('create-chat')) {
            return true;
        }

        return false;
    }

    public function rules()
    {
        $requestAgentIDs = array_column(request('agents'), 'id');
        $rules = [
            'chat_id' => [
                'required',
                'integer',
                'exists:chats,id',
                function ($attribute, $value, $fail) use ($requestAgentIDs) {
                    $chat = Chat::find($value);
                    $agentIds = $chat->invitedAgents->pluck('id')->toArray() ?? [];
                    $intersect = array_intersect($agentIds, $requestAgentIDs);
                    if (!empty($intersect)) {
                        $fail('One or more agents have already been invited to this chat.');
                    }
                },
            ],
            'agents' => 'required|array',
            'agents.*.id' => 'required|integer|exists:users,id',
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
