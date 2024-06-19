<?php

namespace App\Events\Chats;

use App\Models\Chat;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class Cobrowse implements ShouldBroadcastNow
{
    private Chat $chat;
    public string $html;
    public string $stylesheet;

    public function __construct(Chat|int $chat, string $html, string $stylesheet)
    {
        if (is_int($chat)) {
            $chat = Chat::findOrFail($chat);
        }

        $this->chat = $chat;
        $this->html = $html;
        $this->stylesheet = $stylesheet ?? '';
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
