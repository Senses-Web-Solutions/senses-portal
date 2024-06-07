<?php

namespace App\Http\Requests\Chats;

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

        // TODO: Setup a new table that contains company_id, key, and url
        if ($protocolAndDomain == 'http://127.0.0.1') {
            $keyValue = $this->input('key');
            logger($keyValue);
            if ($keyValue == '1234') {
                logger('Key is correct');
                return true;
            }
            logger('Key is incorrect');

            return false;
        }

        logger('Referrer URL is not correct');

        return false;
    }

    public function rules()
    {
        $rules = [
            'key' => 'required|string|max:255', // This key should be stored in the database and should be unique for each company
            'chat' => 'required|array',
            'chat.company_id' => 'required|integer|exists:companies,id',
            'chat.meta' => 'nullable|array|max:255',
            'chat.message' => 'required|array|max:255',
            'chat.message.content' => 'required|string|max:255',
            'chat.message.author' => 'required|string|max:255',
            'chat.message.sent_at' => 'required|date_format:Y-m-d H:i:s',
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
