<?php

namespace App\Actions\ChatUsers;

use App\Models\ChatUser;
use App\Traits\ApiResponse;
use Senses\TaggedCache\Facades\TaggedCache;
use Spatie\QueueableAction\QueueableAction;

class GenerateChatUserShowCache
{
    use QueueableAction, ApiResponse;

    public $queue = 'low';

    public function execute(int $id)
    {

        return TaggedCache::responseForever('chat-user-' . $id, function () use ($id) {
            return $this->respond(ChatUser::findOrFail($id));
        });
    }
}

//Generated 27-10-2023 10:55:45
