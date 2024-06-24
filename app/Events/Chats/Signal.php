<?php

namespace App\Events\Chats;

use App\Models\Chat;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class Signal implements ShouldBroadcastNow
{
    private Chat $chat;
    public bool $fromAgent;
    public array $data;

    public function __construct(Chat|int $chat, bool $fromAgent, array $data)
    {
        if (is_int($chat)) {
            $chat = Chat::findOrFail($chat);
        }

        $this->chat = $chat;
        $this->fromAgent = $fromAgent;
        $this->data = $data;
    }

    public function broadcastWhen()
    {
        return true;
    }

    public function broadcastOn()
    {
        return [
            new PrivateChannel('companies.' . $this->chat->company_id . '.chat'),
            new Channel('chats.' . $this->chat->id . '.message'),
        ];
    }
}
