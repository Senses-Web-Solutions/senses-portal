<?php

namespace App\Observers;

use App\Actions\CommunicationLogs\GenerateCommunicationLogShowCache;
use App\Models\CommunicationLog;
use Senses\TaggedCache\Facades\TaggedCache;

use App\Events\CommunicationLogs\CommunicationLogCreated;
use App\Events\CommunicationLogs\CommunicationLogDeleted;
use App\Events\CommunicationLogs\CommunicationLogUpdated;

class CommunicationLogObserver
{
    public function created(CommunicationLog $communicationLog)
    {
        //app(GenerateCommunicationLogShowCache::class)->onQueue('low')->execute($communicationLog->id);
        broadcast_safely(new CommunicationLogCreated($communicationLog));
    }

    public function updated(CommunicationLog $communicationLog)
    {
        TaggedCache::forgetWithTag($communicationLog->cacheKey);
        //app(GenerateCommunicationLogShowCache::class)->onQueue('low')->execute($communicationLog->id);
        broadcast_safely(new CommunicationLogUpdated($communicationLog));
    }

    public function locked(CommunicationLog $communicationLog)
    {
        TaggedCache::forgetWithTag($communicationLog->cacheKey);
        //app(GenerateCommunicationLogShowCache::class)->onQueue('low')->execute($communicationLog->id);
        broadcast_safely(new CommunicationLogUpdated($communicationLog));
    }

    public function unlocked(CommunicationLog $communicationLog)
    {
        TaggedCache::forgetWithTag($communicationLog->cacheKey);
        //app(GenerateCommunicationLogShowCache::class)->onQueue('low')->execute($communicationLog->id);
        broadcast_safely(new CommunicationLogUpdated($communicationLog));
    }

    public function deleted(CommunicationLog $communicationLog)
    {
        TaggedCache::forgetWithTag($communicationLog->cacheKey);
        broadcast_safely(new CommunicationLogDeleted($communicationLog));
    }

    public function restored(CommunicationLog $communicationLog)
    {
        TaggedCache::forgetWithTag($communicationLog->cacheKey);
        //app(GenerateCommunicationLogShowCache::class)->onQueue('low')->execute($communicationLog->id);
        broadcast_safely(new CommunicationLogUpdated($communicationLog));
    }

    public function forceDeleted(CommunicationLog $communicationLog)
    {
        TaggedCache::forgetWithTag($communicationLog->cacheKey);
        broadcast_safely(new CommunicationLogDeleted($communicationLog));
    }
}

//Generated 04-11-2023 16:09:50
