<?php

namespace App\Events\Chats;

use App\Models\Chat;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class PulseChat implements ShouldBroadcastNow
{
    private Chat $chat;
    public float $x;
    public float $y;

    public function __construct(Chat $chat, float $x, float $y)
    {
        $this->chat = $chat;
        $this->x = $x;
        $this->y = $y;
    }

    public function broadcastWhen()
    {
        return true;
    }

    public function broadcastOn()
    {
        return [
            new Channel('chats.' . $this->chat->id . '.message'),
        ];
    }
}
