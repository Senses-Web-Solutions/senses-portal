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
        $select = [
            'id',
            'company_id',
            'status_id',
            'completed_at'
        ];

        $with = [
            'messages:id,chat_id,from_agent,content,sent_at,read_at,read_by,author_id,author_type',
            'agents:id,full_name,email',
            'status:id,title,slug,colour,text_colour',
        ];

        return TaggedCache::responseForever('chat-' . $id, function () use ($id, &$select, &$with) {
            $chat = Chat::query()
                ->select($select)
                ->with($with)
                ->findOrFail($id)
                ->append(['last_message', 'unread_messages']);
            return $this->respond($chat);
        });
    }
}

//Generated 27-10-2023 10:55:45
