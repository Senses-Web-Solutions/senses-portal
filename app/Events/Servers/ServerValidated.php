<?php

namespace App\Events\Servers;

use App\Models\Server;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class ServerValidated implements ShouldBroadcastNow
{
    public Server $server;

    public function __construct(Server $server)
    {
        $this->server = $server;
    }

    public function broadcastWhen()
    {
        return true;
    }

    public function broadcastOn()
    {
        return [
            new PrivateChannel('servers.' . $this->server->id . '.main'),
        ];
    }
}
