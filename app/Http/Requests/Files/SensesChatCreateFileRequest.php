<?php

namespace App\Http\Requests\Files;

use App\Models\AllowedChatSite;
use Illuminate\Validation\Rule;
use App\Rules\MorphRelationExists;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Database\Eloquent\Relations\Relation;

class SensesChatCreateFileRequest extends FormRequest
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
            'file' => 'required|file|max:200000',
            'public' => 'boolean|nullable',
            'app_visible' => 'boolean|nullable',
            'disk' => 'string|in:remote,local',
            'fileables' => 'array',
            'fileables.*.fileable_id' => [
                'required',
                new MorphRelationExists('type')
            ],
            'fileables.*.fileable_type' => [
                'required',
                Rule::in(array_keys(Relation::morphMap())),
            ],
            'pending'  => ['bool', 'nullable']
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
