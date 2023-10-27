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

//Generated 27-10-2023 10:53:42
