<?php

namespace App\Events\Chats;

use App\Models\Chat;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Support\Facades\Auth;

class Typing implements ShouldBroadcastNow
{
    private Chat $chat;
    public int $chat_id;
    public string $name;
    public bool $from_agent;

    public function __construct(Chat|int $chat, bool $fromAgent)
    {
        if (is_int($chat)) {
            $chat = Chat::findOrFail($chat);
        }

        if ($fromAgent) {
            $name = Auth::user()->full_name;
        } else {
            $name = $chat->chatUser->full_name;
        }

        $this->chat = $chat;
        $this->name = $name;
        $this->from_agent = $fromAgent;
        $this->chat_id = $chat->id;
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
