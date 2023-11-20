<?php

namespace App\Events\ServerMetrics;

use App\Models\ServerMetric;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class ServerMetricUpdated implements ShouldBroadcastNow
{
    public ServerMetric $serverMetric;

    public function __construct(ServerMetric $serverMetric)
    {
        $this->serverMetric = $serverMetric;
    }

    public function broadcastWhen()
    {
        return true;
    }

    public function broadcastOn()
    {
        return [
            new PrivateChannel('server-metrics.' . $this->serverMetric->id . '.main'),
            new PrivateChannel('servers.' . $this->serverMetric->server_id . '.server-metrics')
        ];
    }
}
