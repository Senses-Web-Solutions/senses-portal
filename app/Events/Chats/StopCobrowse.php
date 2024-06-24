<?php

namespace App\Events\Chats;

use App\Models\Chat;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class StopCobrowse implements ShouldBroadcastNow
{
    private Chat $chat;

    public function __construct(Chat|int $chat)
    {
        if (is_int($chat)) {
            $chat = Chat::findOrFail($chat);
        }

        $this->chat = $chat;
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
