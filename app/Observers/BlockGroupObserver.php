<?php

namespace App\Observers;

use App\Actions\BlockGroups\GenerateBlockGroupShowCache;
use App\Models\BlockGroup;
use Senses\TaggedCache\Facades\TaggedCache;

use App\Events\BlockGroups\BlockGroupCreated;
use App\Events\BlockGroups\BlockGroupDeleted;
use App\Events\BlockGroups\BlockGroupUpdated;

class BlockGroupObserver
{
    public function created(BlockGroup $blockGroup)
    {
        //app(GenerateBlockGroupShowCache::class)->onQueue('low')->execute($blockGroup->id);
        broadcast_safely(new BlockGroupCreated($blockGroup));
    }

    public function updated(BlockGroup $blockGroup)
    {
        TaggedCache::forgetWithTag($blockGroup->cacheKey);
        //app(GenerateBlockGroupShowCache::class)->onQueue('low')->execute($blockGroup->id);
        broadcast_safely(new BlockGroupUpdated($blockGroup));
    }

    public function locked(BlockGroup $blockGroup)
    {
        TaggedCache::forgetWithTag($blockGroup->cacheKey);
        //app(GenerateBlockGroupShowCache::class)->onQueue('low')->execute($blockGroup->id);
        broadcast_safely(new BlockGroupUpdated($blockGroup));
    }

    public function unlocked(BlockGroup $blockGroup)
    {
        TaggedCache::forgetWithTag($blockGroup->cacheKey);
        //app(GenerateBlockGroupShowCache::class)->onQueue('low')->execute($blockGroup->id);
        broadcast_safely(new BlockGroupUpdated($blockGroup));
    }

    public function deleted(BlockGroup $blockGroup)
    {
        TaggedCache::forgetWithTag($blockGroup->cacheKey);
        broadcast_safely(new BlockGroupDeleted($blockGroup));
    }

    public function restored(BlockGroup $blockGroup)
    {
        TaggedCache::forgetWithTag($blockGroup->cacheKey);
        //app(GenerateBlockGroupShowCache::class)->onQueue('low')->execute($blockGroup->id);
        broadcast_safely(new BlockGroupUpdated($blockGroup));
    }

    public function forceDeleted(BlockGroup $blockGroup)
    {
        TaggedCache::forgetWithTag($blockGroup->cacheKey);
        broadcast_safely(new BlockGroupDeleted($blockGroup));
    }
}

//Generated 16-10-2023 10:39:10
