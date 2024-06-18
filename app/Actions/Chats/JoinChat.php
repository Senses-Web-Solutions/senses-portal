<?php

namespace App\Actions\Chats;

use App\Actions\ActionLogs\CreateActionLog;
use App\Models\Chat;
use App\Models\Status;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Spatie\QueueableAction\QueueableAction;
use App\Actions\Chats\ReadChat;

class JoinChat
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

        // If the chat does not have a status slug of assigned, then assign it
        if ($chat->status->slug !== 'assigned') {
            $assignedStatus = Status::where('slug', 'assigned')->first();
            $chat->status()->associate($assignedStatus);
        }

        $chat->agents()->syncWithoutDetaching([$user->id]);
        $chat->save();

        $chat->load('agents', 'invitedAgents', 'actionLogs.user');

        app(ReadChat::class)->onQueue()->execute($chat);
        app(CreateActionLog::class)->execute($chat, 'joined', []);

        event(new \App\Events\Chats\ChatUpdated($chat));

        return $chat;
    }
}
