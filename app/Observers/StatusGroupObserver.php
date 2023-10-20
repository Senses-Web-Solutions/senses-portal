<?php

namespace App\Observers;

use App\Actions\StatusGroups\GenerateStatusGroupShowCache;
use App\Models\StatusGroup;
use Senses\TaggedCache\Facades\TaggedCache;

use App\Events\StatusGroups\StatusGroupCreated;
use App\Events\StatusGroups\StatusGroupDeleted;
use App\Events\StatusGroups\StatusGroupUpdated;

class StatusGroupObserver
{
    public function created(StatusGroup $statusGroup)
    {
        //app(GenerateStatusGroupShowCache::class)->onQueue('low')->execute($statusGroup->id);
        broadcast_safely(new StatusGroupCreated($statusGroup));
    }

    public function updated(StatusGroup $statusGroup)
    {
        TaggedCache::forgetWithTag($statusGroup->cacheKey);
        //app(GenerateStatusGroupShowCache::class)->onQueue('low')->execute($statusGroup->id);
        broadcast_safely(new StatusGroupUpdated($statusGroup));
    }

    public function locked(StatusGroup $statusGroup)
    {
        TaggedCache::forgetWithTag($statusGroup->cacheKey);
        //app(GenerateStatusGroupShowCache::class)->onQueue('low')->execute($statusGroup->id);
        broadcast_safely(new StatusGroupUpdated($statusGroup));
    }

    public function unlocked(StatusGroup $statusGroup)
    {
        TaggedCache::forgetWithTag($statusGroup->cacheKey);
        //app(GenerateStatusGroupShowCache::class)->onQueue('low')->execute($statusGroup->id);
        broadcast_safely(new StatusGroupUpdated($statusGroup));
    }

    public function deleted(StatusGroup $statusGroup)
    {
        TaggedCache::forgetWithTag($statusGroup->cacheKey);
        broadcast_safely(new StatusGroupDeleted($statusGroup));
    }

    public function restored(StatusGroup $statusGroup)
    {
        TaggedCache::forgetWithTag($statusGroup->cacheKey);
        //app(GenerateStatusGroupShowCache::class)->onQueue('low')->execute($statusGroup->id);
        broadcast_safely(new StatusGroupUpdated($statusGroup));
    }

    public function forceDeleted(StatusGroup $statusGroup)
    {
        TaggedCache::forgetWithTag($statusGroup->cacheKey);
        broadcast_safely(new StatusGroupDeleted($statusGroup));
    }
}

//Generated 09-10-2023 12:05:02
