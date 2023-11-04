<?php

namespace App\Events\Revenues;

use App\Models\Revenue;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class RevenueDeleted implements ShouldBroadcastNow
{
    public Revenue $revenue;

    public function __construct(Revenue $revenue)
    {
        $this->revenue = $revenue;
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
