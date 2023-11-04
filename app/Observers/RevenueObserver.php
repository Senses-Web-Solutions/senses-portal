<?php

namespace App\Observers;

use App\Actions\Revenues\GenerateRevenueShowCache;
use App\Models\Revenue;
use Senses\TaggedCache\Facades\TaggedCache;

use App\Events\Revenues\RevenueCreated;
use App\Events\Revenues\RevenueDeleted;
use App\Events\Revenues\RevenueUpdated;

class RevenueObserver
{
    public function created(Revenue $revenue)
    {
        //app(GenerateRevenueShowCache::class)->onQueue('low')->execute($revenue->id);
        broadcast_safely(new RevenueCreated($revenue));
    }

    public function updated(Revenue $revenue)
    {
        TaggedCache::forgetWithTag($revenue->cacheKey);
        //app(GenerateRevenueShowCache::class)->onQueue('low')->execute($revenue->id);
        broadcast_safely(new RevenueUpdated($revenue));
    }

    public function locked(Revenue $revenue)
    {
        TaggedCache::forgetWithTag($revenue->cacheKey);
        //app(GenerateRevenueShowCache::class)->onQueue('low')->execute($revenue->id);
        broadcast_safely(new RevenueUpdated($revenue));
    }

    public function unlocked(Revenue $revenue)
    {
        TaggedCache::forgetWithTag($revenue->cacheKey);
        //app(GenerateRevenueShowCache::class)->onQueue('low')->execute($revenue->id);
        broadcast_safely(new RevenueUpdated($revenue));
    }

    public function deleted(Revenue $revenue)
    {
        TaggedCache::forgetWithTag($revenue->cacheKey);
        broadcast_safely(new RevenueDeleted($revenue));
    }

    public function restored(Revenue $revenue)
    {
        TaggedCache::forgetWithTag($revenue->cacheKey);
        //app(GenerateRevenueShowCache::class)->onQueue('low')->execute($revenue->id);
        broadcast_safely(new RevenueUpdated($revenue));
    }

    public function forceDeleted(Revenue $revenue)
    {
        TaggedCache::forgetWithTag($revenue->cacheKey);
        broadcast_safely(new RevenueDeleted($revenue));
    }
}

//Generated 04-11-2023 16:09:26
