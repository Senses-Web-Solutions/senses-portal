<?php

namespace App\Actions\ApiTokens;

use App\Models\ApiToken;
use Illuminate\Support\Facades\Hash;
use Spatie\QueueableAction\QueueableAction;

class CreateApiToken
{
    use QueueableAction;

    public function execute(array $data)
    {
        $apiToken = new ApiToken($data);

        if (isset($data['server_id'])) {
            $apiToken->server()->associate($data['server_id']);
        }

        $token = bin2hex(random_bytes(16));

        $apiToken->preview = substr($token, 0, 4);
        $apiToken->hash = Hash::make($token);

        $apiToken->save();

        // Doing this means that the full token is only sent once and not saved.
        $apiToken['token'] = $token;

        return $apiToken;
    }
}

//Generated 01-11-2023 11:27:41
