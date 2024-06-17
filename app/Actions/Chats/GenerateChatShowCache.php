<?php

namespace App\Actions\Chats;

use App\Models\Chat;
use App\Traits\ApiResponse;
use Senses\TaggedCache\Facades\TaggedCache;
use Spatie\QueueableAction\QueueableAction;

class GenerateChatShowCache
{
    use QueueableAction, ApiResponse;

    public $queue = 'low';

    public function execute(int $id)
    {
        return TaggedCache::responseForever('chat-' . $id, function () use ($id) {
            return $this->respond(Chat::with(['messages.status', 'messages.files', 'agents'])->findOrFail($id)->append(['last_message', 'unread_messages_count', 'unread_messages']));
        });
    }
}

//Generated 27-10-2023 10:55:45
