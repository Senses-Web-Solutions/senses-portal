<?php

namespace App\Actions\Chats;

use App\Models\Chat;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Spatie\QueueableAction\QueueableAction;

class RefuseChatInvite
{
    use QueueableAction;

    public function execute(Chat $chat, User|int $user = null)
    {

        if (is_int($user)) {
            $user = User::findOrFail($user);
        } else if (is_null($user)) {
            $user = Auth::user();
        }

        $chat->invitedAgents()->detach($user->id);

        event(new \App\Events\Chats\ChatUpdated($chat));

        return $chat;
    }
}
