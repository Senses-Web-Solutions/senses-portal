<?php

namespace App\Events\Chats;

use App\Models\Chat;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class ChatCreated implements ShouldBroadcastNow
{
    public Chat $chat;

    public function __construct(Chat $chat)
    {
        $chat->load(['messages', 'status', 'agents', 'invitedAgents', 'actionLogs.user']);
        $chat->append(['last_message']);
        $chat->user_id = $chat->user_id ?? null;
        $chat->invited_user_id = $chat->invited_user_id ?? null;
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
