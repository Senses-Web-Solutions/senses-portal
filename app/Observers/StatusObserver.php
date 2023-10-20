<?php

namespace App\Observers;

use App\Actions\Statuses\GenerateStatusShowCache;
use App\Models\Status;
use Senses\TaggedCache\Facades\TaggedCache;

use App\Events\Statuses\StatusCreated;
use App\Events\Statuses\StatusDeleted;
use App\Events\Statuses\StatusUpdated;

class StatusObserver
{
    public function created(Status $status)
    {
        //app(GenerateStatusShowCache::class)->onQueue('low')->execute($status->id);
        broadcast_safely(new StatusCreated($status));
    }

    public function updated(Status $status)
    {
        TaggedCache::forgetWithTag($status->cacheKey);
        //app(GenerateStatusShowCache::class)->onQueue('low')->execute($status->id);
        broadcast_safely(new StatusUpdated($status));
    }

    public function locked(Status $status)
    {
        TaggedCache::forgetWithTag($status->cacheKey);
        //app(GenerateStatusShowCache::class)->onQueue('low')->execute($status->id);
        broadcast_safely(new StatusUpdated($status));
    }

    public function unlocked(Status $status)
    {
        TaggedCache::forgetWithTag($status->cacheKey);
        //app(GenerateStatusShowCache::class)->onQueue('low')->execute($status->id);
        broadcast_safely(new StatusUpdated($status));
    }

    public function deleted(Status $status)
    {
        TaggedCache::forgetWithTag($status->cacheKey);
        broadcast_safely(new StatusDeleted($status));
    }

    public function restored(Status $status)
    {
        TaggedCache::forgetWithTag($status->cacheKey);
        //app(GenerateStatusShowCache::class)->onQueue('low')->execute($status->id);
        broadcast_safely(new StatusUpdated($status));
    }

    public function forceDeleted(Status $status)
    {
        TaggedCache::forgetWithTag($status->cacheKey);
        broadcast_safely(new StatusDeleted($status));
    }
}

//Generated 09-10-2023 12:35:29
