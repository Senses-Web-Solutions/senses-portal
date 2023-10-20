<?php

namespace App\Events\Pages;

use App\Models\Page;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class PageDeleted implements ShouldBroadcastNow
{
    public Page $page;

    public function __construct(Page $page)
    {
        $this->page = $page;
    }

    public function broadcastWhen()
    {
        return true;
    }

    public function broadcastOn()
    {
        return [new PrivateChannel('pages.' . $this->page->id . '.main')];
    }
}
