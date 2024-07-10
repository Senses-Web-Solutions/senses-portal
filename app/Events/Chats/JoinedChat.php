<?php

namespace App\Events\Chats;

use App\Models\Chat;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class JoinedChat implements ShouldBroadcastNow
{
    private Chat $chat;
    private User $user;
    public int $id;
    public string $full_name;

    public function __construct(User $user, Chat $chat)
    {
        $this->chat = $chat;
        $this->user = $user;
        $this->id = $user->id;
        $this->full_name = $user->full_name;
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
