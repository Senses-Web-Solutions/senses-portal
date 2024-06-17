<?php

namespace App\Actions\Chats;

use App\Models\Chat;
use App\Models\Status;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Spatie\QueueableAction\QueueableAction;
use App\Actions\Chats\AgentReadsChat;

class AcceptChat
{
    use QueueableAction;

    public function execute(Chat $chat, User|int $user = null)
    {

        if (is_int($user)) {
            $user = User::findOrFail($user);
        } else if (is_null($user)) {
            $user = Auth::user();
        }

        $assignedStatus = Status::where('slug', 'assigned')->first();
        $chat->status()->associate($assignedStatus);
        $chat->agents()->syncWithoutDetaching([$user->id]);

        $chat->save();

        $chat->load('agents');

        app(AgentReadsChat::class)->onQueue()->execute($chat);

        return $chat;
    }
}
