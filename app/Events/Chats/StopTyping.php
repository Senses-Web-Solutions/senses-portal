<?php

namespace App\Events\Chats;

use App\Models\Chat;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class StopTyping implements ShouldBroadcastNow
{
    public Chat $chat;
    public string $name;
    public bool $from_agent;

    public function __construct(Chat|int $chat, string $name, bool $fromAgent)
    {
        if (is_int($chat)) {
            $chat = Chat::findOrFail($chat);
        }

        $this->chat = $chat;
        $this->name = $name;
        $this->from_agent = $fromAgent;
    }

    public function broadcastWhen()
    {
        return true;
    }

    public function broadcastOn()
    {
        return [
            new PrivateChannel('companies.' . $this->chat->company_id . '.message'),
            new Channel('chats.' . $this->chat->id . '.message'),
        ];
    }
}
