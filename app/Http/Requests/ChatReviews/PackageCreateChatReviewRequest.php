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

        // Early exit if no referrer URL is provided
        if (!$referrerUrl) {
            logger('No referrer URL provided');
            return false;
        }

        // Parse the referrer URL to get just the protocol and domain name
        $parsedUrl = parse_url($referrerUrl);
        if (!isset(
            $parsedUrl['scheme'],
            $parsedUrl['host']
        )) {
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

        if ($allowedChatSiteExists) {
            return true;
        } 

        return false;
    }

    public function rules()
    {
        $rules = [
            'chat_id' => 'required|integer|exists:chats,id',
            'chat_user_uuid' => 'required|string|max:255',
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
