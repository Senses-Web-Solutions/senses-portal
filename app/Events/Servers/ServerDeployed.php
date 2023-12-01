<?php

namespace App\Events\Servers;

use App\Models\Server;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class ServerDeployed implements ShouldBroadcastNow
{
    public Server $server;
    public $data;

    public function __construct(Server $server, $data)
    {
        $this->server = $server;
        $this->data = $data;
    }

    public function broadcastWhen()
    {
        return true;
    }

    public function broadcastOn()
    {
        return [
            new PrivateChannel('servers.' . $this->server->id . '.deploy'),
        ];
    }
}
