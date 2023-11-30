<?php

namespace App\Actions\Servers;

use App\Actions\ApiTokens\CreateApiToken;
use App\Events\Servers\ServerValidated;
use App\Models\Server;
use Spatie\QueueableAction\QueueableAction;

class ValidateServer
{
    use QueueableAction;

    public function execute(array $data)
    {
        if (isset($data['server_id'])) {
            $server = Server::find($data['server_id']);
        }

        broadcast_safely(new ServerValidated($server));

        $server->verified_at = now();

        $server->save();

        return $server;
    }
}

//Generated 01-11-2023 11:27:41
