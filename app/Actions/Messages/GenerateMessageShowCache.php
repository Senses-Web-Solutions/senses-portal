<?php

namespace App\Actions\Messages;

use App\Models\Message;
use App\Traits\ApiResponse;
use Senses\TaggedCache\Facades\TaggedCache;
use Spatie\QueueableAction\QueueableAction;

class GenerateMessageShowCache
{
    use QueueableAction, ApiResponse;

    public $queue = 'low';

    public function execute(int $id)
    {
        return TaggedCache::responseForever('message-' . $id, function () use ($id) {
            return $this->respond(Message::with(['status'])->findOrFail($id));
        });
    }
}

//Generated 27-10-2023 10:55:45
