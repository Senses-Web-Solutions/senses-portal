<?php

namespace App\Events\BlockGroups;

use App\Models\BlockGroup;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class BlockGroupDeleted implements ShouldBroadcastNow
{
    public BlockGroup $blockGroup;

    public function __construct(BlockGroup $blockGroup)
    {
        $this->blockGroup = $blockGroup;
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
