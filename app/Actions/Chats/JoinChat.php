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
        $chat->historicalAgents()->syncWithoutDetaching([$user->id]);

        // If the chat's answered_at is null, then set it to the current time
        if (is_null($chat->answered_at)) {
            $chat->answered_at = now();
        }

        $chat->save();

        app(ReadChat::class)->onQueue()->execute($chat);
        app(CreateActionLog::class)->onQueue()->execute($chat, 'joined', []);

        return $chat;
    }
}
