<?php

namespace App\Observers;

use App\Actions\ServerMetrics\GenerateServerMetricShowCache;
use App\Models\ServerMetric;
use Senses\TaggedCache\Facades\TaggedCache;

use App\Events\ServerMetrics\ServerMetricCreated;
use App\Events\ServerMetrics\ServerMetricDeleted;
use App\Events\ServerMetrics\ServerMetricUpdated;

class ServerMetricObserver
{
    public function created(ServerMetric $serverMetric)
    {
        //app(GenerateServerMetricShowCache::class)->onQueue('low')->execute($serverMetric->id);
        broadcast_safely(new ServerMetricCreated($serverMetric));
    }

    public function updated(ServerMetric $serverMetric)
    {
        TaggedCache::forgetWithTag($serverMetric->cacheKey);
        //app(GenerateServerMetricShowCache::class)->onQueue('low')->execute($serverMetric->id);
        broadcast_safely(new ServerMetricUpdated($serverMetric));
    }

    public function locked(ServerMetric $serverMetric)
    {
        TaggedCache::forgetWithTag($serverMetric->cacheKey);
        //app(GenerateServerMetricShowCache::class)->onQueue('low')->execute($serverMetric->id);
        broadcast_safely(new ServerMetricUpdated($serverMetric));
    }

    public function unlocked(ServerMetric $serverMetric)
    {
        TaggedCache::forgetWithTag($serverMetric->cacheKey);
        //app(GenerateServerMetricShowCache::class)->onQueue('low')->execute($serverMetric->id);
        broadcast_safely(new ServerMetricUpdated($serverMetric));
    }

    public function deleted(ServerMetric $serverMetric)
    {
        TaggedCache::forgetWithTag($serverMetric->cacheKey);
        broadcast_safely(new ServerMetricDeleted($serverMetric));
    }

    public function restored(ServerMetric $serverMetric)
    {
        TaggedCache::forgetWithTag($serverMetric->cacheKey);
        //app(GenerateServerMetricShowCache::class)->onQueue('low')->execute($serverMetric->id);
        broadcast_safely(new ServerMetricUpdated($serverMetric));
    }

    public function forceDeleted(ServerMetric $serverMetric)
    {
        TaggedCache::forgetWithTag($serverMetric->cacheKey);
        broadcast_safely(new ServerMetricDeleted($serverMetric));
    }
}

//Generated 01-11-2023 11:22:36
