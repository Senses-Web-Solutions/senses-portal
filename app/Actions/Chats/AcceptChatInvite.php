<?php

namespace App\Actions\Chats;

use App\Actions\ActionLogs\CreateActionLog;
use App\Models\Chat;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Spatie\QueueableAction\QueueableAction;

class AcceptChatInvite
{
    use QueueableAction;

    public function execute(Chat|int $chat, User|int $user = null)
    {

        if (is_int($chat)) {
            $chat = Chat::findOrFail($chat);
        }

        if (is_int($user)) {
            $user = User::findOrFail($user);
        } else if (is_null($user)) {
            $user = Auth::user();
        }

        $chat->invitedAgents()->detach($user->id);
        app(CreateActionLog::class)->onQueue()->execute($chat, 'accepted-invite', []);

        return $chat;
    }
}
