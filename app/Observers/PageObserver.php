<?php

namespace App\Observers;

use App\Actions\Pages\GeneratePageShowCache;
use App\Models\Page;
use Senses\TaggedCache\Facades\TaggedCache;

use App\Events\Pages\PageCreated;
use App\Events\Pages\PageDeleted;
use App\Events\Pages\PageUpdated;

class PageObserver
{
    public function created(Page $page)
    {
        //app(GeneratePageShowCache::class)->onQueue('low')->execute($page->id);
        broadcast_safely(new PageCreated($page));
    }

    public function updated(Page $page)
    {
        TaggedCache::forgetWithTag($page->cacheKey);
        //app(GeneratePageShowCache::class)->onQueue('low')->execute($page->id);
        broadcast_safely(new PageUpdated($page));
    }

    public function locked(Page $page)
    {
        TaggedCache::forgetWithTag($page->cacheKey);
        //app(GeneratePageShowCache::class)->onQueue('low')->execute($page->id);
        broadcast_safely(new PageUpdated($page));
    }

    public function unlocked(Page $page)
    {
        TaggedCache::forgetWithTag($page->cacheKey);
        //app(GeneratePageShowCache::class)->onQueue('low')->execute($page->id);
        broadcast_safely(new PageUpdated($page));
    }

    public function deleted(Page $page)
    {
        TaggedCache::forgetWithTag($page->cacheKey);
        broadcast_safely(new PageDeleted($page));
    }

    public function restored(Page $page)
    {
        TaggedCache::forgetWithTag($page->cacheKey);
        //app(GeneratePageShowCache::class)->onQueue('low')->execute($page->id);
        broadcast_safely(new PageUpdated($page));
    }

    public function forceDeleted(Page $page)
    {
        TaggedCache::forgetWithTag($page->cacheKey);
        broadcast_safely(new PageDeleted($page));
    }
}

//Generated 10-10-2023 14:43:35
