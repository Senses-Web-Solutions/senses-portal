<?php

namespace App\Observers;

use App\Models\Chat;
use Senses\TaggedCache\Facades\TaggedCache;

use App\Events\Chats\ChatCreated;
use App\Events\Chats\ChatDeleted;
use App\Events\Chats\ChatUpdated;

class ChatObserver
{
    public function created(Chat $chat)
    {
        broadcast_safely(new ChatCreated($chat));
    }

    public function updated(Chat $chat)
    {
        TaggedCache::forgetWithTag($chat->cacheKey);
        broadcast_safely(new ChatUpdated($chat));
    }

    public function locked(Chat $chat)
    {
        TaggedCache::forgetWithTag($chat->cacheKey);
        broadcast_safely(new ChatUpdated($chat));
    }

    public function unlocked(Chat $chat)
    {
        TaggedCache::forgetWithTag($chat->cacheKey);
        broadcast_safely(new ChatUpdated($chat));
    }

    public function deleted(Chat $chat)
    {
        TaggedCache::forgetWithTag($chat->cacheKey);
        broadcast_safely(new ChatDeleted($chat));
    }

    public function restored(Chat $chat)
    {
        TaggedCache::forgetWithTag($chat->cacheKey);
        broadcast_safely(new ChatUpdated($chat));
    }

    public function forceDeleted(Chat $chat)
    {
        TaggedCache::forgetWithTag($chat->cacheKey);
        broadcast_safely(new ChatDeleted($chat));
    }
}

//Generated 27-10-2023 10:55:45
