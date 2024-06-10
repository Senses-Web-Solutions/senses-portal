<?php

namespace App\Actions\AllowedChatSites;

use App\Models\AllowedChatSite;
use App\Traits\ApiResponse;
use Senses\TaggedCache\Facades\TaggedCache;
use Spatie\QueueableAction\QueueableAction;

class GenerateAllowedChatSiteShowCache
{
    use QueueableAction, ApiResponse;

    public $queue = 'low';

    public function execute(int $id)
    {
        return TaggedCache::responseForever('allowed-chat-site-' . $id, function () use ($id) {
            return $this->respond(AllowedChatSite::findOrFail($id));
        });
    }
}

//Generated 27-10-2023 10:55:45
