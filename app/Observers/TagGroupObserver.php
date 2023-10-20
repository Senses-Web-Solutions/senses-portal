<?php

namespace App\Observers;

use App\Actions\TagGroups\GenerateTagGroupShowCache;
use App\Models\TagGroup;
use Senses\TaggedCache\Facades\TaggedCache;

use App\Events\TagGroups\TagGroupCreated;
use App\Events\TagGroups\TagGroupDeleted;
use App\Events\TagGroups\TagGroupUpdated;

class TagGroupObserver
{
    public function created(TagGroup $tagGroup)
    {
        //app(GenerateTagGroupShowCache::class)->onQueue('low')->execute($tagGroup->id);
        broadcast_safely(new TagGroupCreated($tagGroup));
    }

    public function updated(TagGroup $tagGroup)
    {
        TaggedCache::forgetWithTag($tagGroup->cacheKey);
        //app(GenerateTagGroupShowCache::class)->onQueue('low')->execute($tagGroup->id);
        broadcast_safely(new TagGroupUpdated($tagGroup));
    }

    public function locked(TagGroup $tagGroup)
    {
        TaggedCache::forgetWithTag($tagGroup->cacheKey);
        //app(GenerateTagGroupShowCache::class)->onQueue('low')->execute($tagGroup->id);
        broadcast_safely(new TagGroupUpdated($tagGroup));
    }

    public function unlocked(TagGroup $tagGroup)
    {
        TaggedCache::forgetWithTag($tagGroup->cacheKey);
        //app(GenerateTagGroupShowCache::class)->onQueue('low')->execute($tagGroup->id);
        broadcast_safely(new TagGroupUpdated($tagGroup));
    }

    public function deleted(TagGroup $tagGroup)
    {
        TaggedCache::forgetWithTag($tagGroup->cacheKey);
        broadcast_safely(new TagGroupDeleted($tagGroup));
    }

    public function restored(TagGroup $tagGroup)
    {
        TaggedCache::forgetWithTag($tagGroup->cacheKey);
        //app(GenerateTagGroupShowCache::class)->onQueue('low')->execute($tagGroup->id);
        broadcast_safely(new TagGroupUpdated($tagGroup));
    }

    public function forceDeleted(TagGroup $tagGroup)
    {
        TaggedCache::forgetWithTag($tagGroup->cacheKey);
        broadcast_safely(new TagGroupDeleted($tagGroup));
    }
}

//Generated 09-10-2023 10:26:55
