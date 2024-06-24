<?php

namespace App\Http\Requests\Chats;

use App\Models\AllowedChatSite;
use Illuminate\Foundation\Http\FormRequest;

class StartChatRequest extends FormRequest
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
            'company_id' => 'required|integer|exists:companies,id',
            'meta' => 'nullable|array|max:255',
            'message' => 'required|array|max:255',
            'name' => 'required|string|max:255',
            'message.content' => 'required|string|max:255',
            'message.author' => 'required|string|max:255',
            'message.sent_at' => 'required|date_format:Y-m-d H:i:s',
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
