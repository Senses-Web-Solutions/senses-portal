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

        // Check if $message->read_by is a string and decode it, otherwise handle it as an array or initialize as an empty array
        $readBy = is_string($message->read_by) ? json_decode($message->read_by, true) : (is_array($message->read_by) ? $message->read_by : []);

        // Merge $userId into the array, ensuring uniqueness
        $message->read_by = array_unique(array_merge($readBy, [$userId]));

        // Encode back to JSON string before saving, if needed
        $message->read_by = json_encode($message->read_by);

        $message->save();

        return $message;
    }
}
