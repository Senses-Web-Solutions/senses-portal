<?php

namespace App\Events\StatusGroups;

use App\Models\StatusGroup;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class StatusGroupCreated implements ShouldBroadcastNow
{
    public StatusGroup $statusGroup;

    public function __construct(StatusGroup $statusGroup)
    {
        $this->statusGroup = $statusGroup;
    }

    public function broadcastWhen()
    {
        return true;
    }

    public function broadcastOn()
    {
        return [new PrivateChannel('status-groups.' . $this->statusGroup->id . '.main')];
    }
}
