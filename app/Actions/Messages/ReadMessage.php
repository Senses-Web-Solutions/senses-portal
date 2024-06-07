<?php

namespace App\Actions\Messages;

use App\Models\Message;
use App\Models\Status;
use Spatie\QueueableAction\QueueableAction;

class ReadMessage
{
    use QueueableAction;

    public function execute(Message $message)
    {
        $status = Status::where('slug', 'read')->first();
        $message->status()->associate($status);
        $message->read_at = now();
        $message->save();

        return $message;
    }
}
