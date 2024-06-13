<?php

namespace App\Http\Requests\Messages;

use App\Models\AllowedChatSite;
use Illuminate\Foundation\Http\FormRequest;

class SensesChatCreateMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Get the referrer URL
        $referrerUrl = $this->headers->get('referer');

        // Parse the referrer URL to get just the protocol and domain name
        $parsedUrl = parse_url($referrerUrl);
        $protocolAndDomain = $parsedUrl['scheme'] . '://' . $parsedUrl['host'];

        // Find AllowedChatSite with the given protocol and domain
        $allowedChatSite = AllowedChatSite::where('url', $protocolAndDomain)->first();

        // TODO: Setup a new table that contains company_id, key, and url
        if ($allowedChatSite) {
            return true;
        }

        logger('Referrer URL is not correct');

        return false;
    }

    public function rules()
    {
        $rules = [
            'chat_id' => 'required|integer|exists:chats,id',
            'content' => 'required|string',
            'author' => 'required|string|max:255',
            'from_agent' => 'required|boolean',
            'meta' => 'nullable|array|max:255',
            'file_ids' => 'nullable|array',
            'file_ids.*' => 'integer|exists:files,id',
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
