<?php

namespace App\Actions\CannedMessages;

use App\Models\CannedMessage;
use App\Traits\ApiResponse;
use Senses\TaggedCache\Facades\TaggedCache;
use Spatie\QueueableAction\QueueableAction;

class GenerateCannedMessageShowCache
{
    use QueueableAction, ApiResponse;

    public $queue = 'low';

    public function execute(int $id)
    {
        return TaggedCache::responseForever('canned-message-' . $id, function () use ($id) {
            return $this->respond(CannedMessage::findOrFail($id));
        });
    }
}

//Generated 27-10-2023 10:55:45
