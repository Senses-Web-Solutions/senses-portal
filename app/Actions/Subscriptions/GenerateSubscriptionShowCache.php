<?php

namespace App\Actions\Subscriptions;

use App\Models\Subscription;
use App\Support\QueryBuilder;
use App\Traits\ApiResponse;
use Senses\TaggedCache\Facades\TaggedCache;
use Spatie\QueueableAction\QueueableAction;

class GenerateSubscriptionShowCache
{
    use QueueableAction, ApiResponse;

    public $queue = 'low';

    public function execute(int $id)
    {
        return TaggedCache::responseForever('subscription-' . $id, function () use ($id) {
            return $this->respond(Subscription::findOrFail($id));
        });
    }
}

//Generated 04-11-2023 16:09:38
