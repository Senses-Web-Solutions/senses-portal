<?php

namespace App\Events\Statuses;

use App\Models\Status;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class StatusUpdated implements ShouldBroadcastNow
{
    public Status $status;

    public function __construct(Status $status)
    {
        $this->status = $status;
    }

    public function broadcastWhen()
    {
        return true;
    }

    public function broadcastOn()
    {
        return [new PrivateChannel('statuses.' . $this->status->id . '.main')];
    }
}
