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
        $chat->load([
            'messages' => function ($query) {
                $query->select('messages.id', 'messages.chat_id', 'messages.from_agent', 'messages.content', 'messages.author_type', 'messages.author_id', 'messages.sent_at', 'messages.read_at', 'messages.read_by');
            },
            'messages.files',
            'status' => function ($query) {
                $query->select('statuses.id', 'statuses.title', 'statuses.slug', 'statuses.colour', 'statuses.text_colour');
            },
            'agents' => function ($query) {
                $query->select('users.id', 'users.full_name');
            },
            'invitedAgents' => function ($query) {
                $query->select('users.id', 'users.full_name');
            },
            'chatUser'
        ]);
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
