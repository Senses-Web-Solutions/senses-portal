<?php

namespace App\Actions\Chats;

use App\Models\Chat;
use App\Models\Status;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Spatie\QueueableAction\QueueableAction;

class ChatInvite
{
    use QueueableAction;

    public function execute(Chat $chat, User|int $user = null)
    {

        $status = Status::firstWhere('slug', 'agent-invited');

        if (is_int($user)) {
            $user = User::findOrFail($user);
        } else if (is_null($user)) {
            $user = Auth::user();
        }

        $chat->status()->associate($status);
        $chat->invitedAgents()->syncWithoutDetaching([$user->id]);

        $chat->load('agents', 'invitedAgents');

        event(new \App\Events\Chats\ChatUpdated($chat));

        return $chat;
    }
}
