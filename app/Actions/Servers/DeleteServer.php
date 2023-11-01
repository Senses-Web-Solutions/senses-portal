<?php

namespace App\Actions\Servers;

use App\Models\Server;
use Spatie\QueueableAction\QueueableAction;

class DeleteServer
{
    use QueueableAction;

    public function execute(int $id)
    {
        $server = Server::findOrFail($id);

        $server->delete();

        return $server;
    }
}

//Generated 01-11-2023 11:27:41
