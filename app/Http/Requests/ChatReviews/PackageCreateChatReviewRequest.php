<?php

namespace App\Http\Requests\ChatReviews;

use App\Models\AllowedChatSite;
use Illuminate\Foundation\Http\FormRequest;

class PackageCreateChatReviewRequest extends FormRequest
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

        if ($allowedChatSite) {
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
            'resolved' => 'required|boolean',
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
