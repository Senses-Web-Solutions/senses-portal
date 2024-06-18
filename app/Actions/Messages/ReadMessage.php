<?php

namespace App\Actions\Messages;

use App\Events\Messages\MessageRead;
use App\Models\Message;
use App\Models\Status;
use Spatie\QueueableAction\QueueableAction;

class ReadMessage
{
    use QueueableAction;

    public function execute(Message|int $message, int $userId)
    {
        $message = $message instanceof Message ? $message : Message::findOrFail($message);
        
        $status = Status::where('slug', 'read')->first();
        $message->status()->associate($status);
        $message->read_at = now();

        $readBy = json_decode($message->read_by, true);
        $message->read_by = $readBy ? array_unique(array_merge($readBy, [$userId])) : [$userId];

        $message->save();

        return $message;
    }
}
