<?php

namespace App\Events\TagGroups;

use App\Models\TagGroup;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class TagGroupDeleted implements ShouldBroadcastNow
{
    public TagGroup $tagGroup;

    public function __construct(TagGroup $tagGroup)
    {
        $this->tagGroup = $tagGroup;
    }

    public function broadcastWhen()
    {
        return true;
    }

    public function broadcastOn()
    {
        return [new PrivateChannel('tag-groups.' . $this->tagGroup->id . '.main')];
    }
}
