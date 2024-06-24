<?php

namespace App\Actions\Chats;

use App\Actions\ActionLogs\CreateActionLog;
use App\Models\Chat;
use App\Models\Status;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Spatie\QueueableAction\QueueableAction;

class LeaveChat
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

        $chat->agents()->detach($user->id);

        if ($chat->agents->count() === 0) {
            $unassignedStatus = Status::where('slug', 'unassigned')->first();
            $chat->status()->associate($unassignedStatus);
        }

        $chat->save();

        $chat->load('agents', 'invitedAgents', 'actionLogs.user');

        app(CreateActionLog::class)->onQueue()->execute($chat, 'left', []);

        event(new \App\Events\Chats\ChatUpdated($chat));

        return $chat;
    }
}
