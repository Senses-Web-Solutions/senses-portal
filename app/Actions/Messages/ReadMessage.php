<?php

namespace App\Actions\Messages;

use App\Events\Messages\MessageRead;
use App\Models\Message;
use App\Models\Status;
use Spatie\QueueableAction\QueueableAction;

class ReadMessage
{
    use QueueableAction;

    public function execute(Message|int $message)
    {
        $message = $message instanceof Message ? $message : Message::findOrFail($message);
        
        $status = Status::where('slug', 'read')->first();
        $message->status()->associate($status);
        $message->read_at = now();
        $message->save();

        return $message;
    }
}
