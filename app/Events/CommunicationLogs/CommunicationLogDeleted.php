<?php

namespace App\Events\CommunicationLogs;

use App\Models\CommunicationLog;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class CommunicationLogDeleted implements ShouldBroadcastNow
{
    public CommunicationLog $communicationLog;

    public function __construct(CommunicationLog $communicationLog)
    {
        $this->communicationLog = $communicationLog;
    }

    public function broadcastWhen()
    {
        return true;
    }

    public function broadcastOn()
    {
        return [];
    }
}
