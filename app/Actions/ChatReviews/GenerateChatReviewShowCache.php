<?php

namespace App\Actions\ChatReviews;

use App\Models\ChatReview;
use App\Traits\ApiResponse;
use Senses\TaggedCache\Facades\TaggedCache;
use Spatie\QueueableAction\QueueableAction;

class GenerateChatReviewShowCache
{
    use QueueableAction, ApiResponse;

    public $queue = 'low';

    public function execute(int $id)
    {
        return TaggedCache::responseForever('chat-review-' . $id, function () use ($id) {
            return $this->respond(ChatReview::findOrFail($id)->append(['agents']));
        });
    }
}

//Generated 27-10-2023 10:55:45
