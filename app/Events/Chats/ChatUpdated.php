<?php

namespace App\Events\Chats;

use App\Models\Chat;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class ChatUpdated implements ShouldBroadcastNow
{
    public Chat $chat;

    public function __construct(Chat $chat)
    {
        $chat->load(['messages', 'status', 'agents', 'invitedAgents', 'actionLogs.user']);
        $chat->append(['last_message']);
        $this->chat = $chat;
    }

    public function broadcastWhen()
    {
        return true;
    }

    public function broadcastOn()
    {
        return [
            new PrivateChannel('companies.' . $this->chat->company_id . '.chat'),
        ];
    }
}
