<?php

namespace App\Observers;

use App\Actions\Tags\GenerateTagShowCache;
use App\Models\Tag;
use Senses\TaggedCache\Facades\TaggedCache;

use App\Events\Tags\TagCreated;
use App\Events\Tags\TagDeleted;
use App\Events\Tags\TagUpdated;

class TagObserver
{
    public function created(Tag $tag)
    {
        //app(GenerateTagShowCache::class)->onQueue('low')->execute($tag->id);
        broadcast_safely(new TagCreated($tag));
    }

    public function updated(Tag $tag)
    {
        TaggedCache::forgetWithTag($tag->cacheKey);
        //app(GenerateTagShowCache::class)->onQueue('low')->execute($tag->id);
        broadcast_safely(new TagUpdated($tag));
    }

    public function locked(Tag $tag)
    {
        TaggedCache::forgetWithTag($tag->cacheKey);
        //app(GenerateTagShowCache::class)->onQueue('low')->execute($tag->id);
        broadcast_safely(new TagUpdated($tag));
    }

    public function unlocked(Tag $tag)
    {
        TaggedCache::forgetWithTag($tag->cacheKey);
        //app(GenerateTagShowCache::class)->onQueue('low')->execute($tag->id);
        broadcast_safely(new TagUpdated($tag));
    }

    public function deleted(Tag $tag)
    {
        TaggedCache::forgetWithTag($tag->cacheKey);
        broadcast_safely(new TagDeleted($tag));
    }

    public function restored(Tag $tag)
    {
        TaggedCache::forgetWithTag($tag->cacheKey);
        //app(GenerateTagShowCache::class)->onQueue('low')->execute($tag->id);
        broadcast_safely(new TagUpdated($tag));
    }

    public function forceDeleted(Tag $tag)
    {
        TaggedCache::forgetWithTag($tag->cacheKey);
        broadcast_safely(new TagDeleted($tag));
    }
}

//Generated 09-10-2023 10:18:19
