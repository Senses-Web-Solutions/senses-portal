<?php

namespace App\Events\Tags;

use App\Models\Tag;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class TagUpdated implements ShouldBroadcastNow
{
    public Tag $tag;

    public function __construct(Tag $tag)
    {
        $this->tag = $tag;
    }

    public function broadcastWhen()
    {
        return true;
    }

    public function broadcastOn()
    {
        return [new PrivateChannel('tags.' . $this->tag->id . '.main')];
    }
}
