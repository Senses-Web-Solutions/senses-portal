<?php

namespace App\Actions\Servers;

use App\Actions\ApiTokens\CreateApiToken;
use App\Models\Server;
use Illuminate\Support\Facades\Hash;
use Spatie\QueueableAction\QueueableAction;

class AuthenticateServer
{
    use QueueableAction;

    public function execute($bearerToken)
    {
        foreach (Server::has('apiTokens')->get() as $server) {
            foreach ($server->apiTokens as $token) {
                $hasNoExpiry = $token->expires_at == null;
                $hasValidExpiry = $token->expires_at >= now();
                $hasValidHash = Hash::check($bearerToken, $token->hash);

                if (($hasNoExpiry || $hasValidExpiry) && $hasValidHash) {
                    return $server;
                }
            }
        }

        return null;
    }
}

//Generated 01-11-2023 11:27:41
