<?php

namespace App\Observers;

use App\Models\Message;
use Senses\TaggedCache\Facades\TaggedCache;

use App\Events\Messages\MessageCreated;
use App\Events\Messages\MessageDeleted;
use App\Events\Messages\MessageRead;
use App\Events\Messages\MessageUpdated;

class MessageObserver
{
    public function created(Message $message)
    {
        TaggedCache::forgetWithTag($message->chat->cacheKey);
        broadcast_safely(new MessageCreated($message));
    }

    public function updated(Message $message)
    {
        TaggedCache::forgetWithTag($message->cacheKey);
        TaggedCache::forgetWithTag($message->chat->cacheKey);
        broadcast_safely(new MessageUpdated($message));

        if ($message->wasChanged('read')) {
            broadcast_safely(new MessageRead($message));
        }
    }

    public function locked(Message $message)
    {
        TaggedCache::forgetWithTag($message->cacheKey);
        broadcast_safely(new MessageUpdated($message));
    }

    public function unlocked(Message $message)
    {
        TaggedCache::forgetWithTag($message->cacheKey);
        broadcast_safely(new MessageUpdated($message));
    }

    public function deleted(Message $message)
    {
        TaggedCache::forgetWithTag($message->cacheKey);
        broadcast_safely(new MessageDeleted($message));
    }

    public function restored(Message $message)
    {
        TaggedCache::forgetWithTag($message->cacheKey);
        broadcast_safely(new MessageUpdated($message));
    }

    public function forceDeleted(Message $message)
    {
        TaggedCache::forgetWithTag($message->cacheKey);
        broadcast_safely(new MessageDeleted($message));
    }
}

//Generated 27-10-2023 10:55:45
