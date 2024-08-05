<?php

namespace App\Http\Requests\Chats;

use App\Models\AllowedChatSite;
use App\Models\ChatUser;
use Illuminate\Foundation\Http\FormRequest;

class PackageCreateChatRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Get the referrer URL
        $referrerUrl = $this->headers->get('referer');

        // Early exit if no referrer URL is provided
        if (!$referrerUrl) {
            logger('No referrer URL provided');
            return false;
        }

        // Parse the referrer URL to get just the protocol and domain name
        $parsedUrl = parse_url($referrerUrl);
        if (!isset($parsedUrl['scheme'], $parsedUrl['host'])) {
            logger('Invalid referrer URL');
            return false;
        }
        $protocolAndDomain = $parsedUrl['scheme'] . '://' . $parsedUrl['host'];

        // Get chat user UUID from request
        $chatUserUuid = $this->input('chat_user_uuid');

        // Attempt to directly check for an allowed chat site using a single query
        $allowedChatSiteExists = AllowedChatSite::join('chat_users', 'allowed_chat_sites.company_id', '=', 'chat_users.company_id')
        ->where('chat_users.uuid', $chatUserUuid)
        ->where('allowed_chat_sites.url', $protocolAndDomain)
        ->exists();

        return $allowedChatSiteExists;
    }

    public function rules()
    {
        $rules = [
            'chat_user_uuid' => 'required|string|max:255',
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
