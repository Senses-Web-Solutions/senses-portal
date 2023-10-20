<?php

namespace App\Observers;

use App\Actions\Files\GenerateFileShowCache;
use App\Models\File;
use Senses\TaggedCache\Facades\TaggedCache;

use App\Events\Files\FileCreated;
use App\Events\Files\FileDeleted;
use App\Events\Files\FileUpdated;

class FileObserver
{
    public function created(File $file)
    {
        //app(GenerateFileShowCache::class)->onQueue('low')->execute($file->id);
        broadcast_safely(new FileCreated($file));
    }

    public function updated(File $file)
    {
        TaggedCache::forgetWithTag($file->cacheKey);
        //app(GenerateFileShowCache::class)->onQueue('low')->execute($file->id);
        broadcast_safely(new FileUpdated($file));
    }

    public function locked(File $file)
    {
        TaggedCache::forgetWithTag($file->cacheKey);
        //app(GenerateFileShowCache::class)->onQueue('low')->execute($file->id);
        broadcast_safely(new FileUpdated($file));
    }

    public function unlocked(File $file)
    {
        TaggedCache::forgetWithTag($file->cacheKey);
        //app(GenerateFileShowCache::class)->onQueue('low')->execute($file->id);
        broadcast_safely(new FileUpdated($file));
    }

    public function deleted(File $file)
    {
        TaggedCache::forgetWithTag($file->cacheKey);
        broadcast_safely(new FileDeleted($file));
    }

    public function restored(File $file)
    {
        TaggedCache::forgetWithTag($file->cacheKey);
        //app(GenerateFileShowCache::class)->onQueue('low')->execute($file->id);
        broadcast_safely(new FileUpdated($file));
    }

    public function forceDeleted(File $file)
    {
        TaggedCache::forgetWithTag($file->cacheKey);
        broadcast_safely(new FileDeleted($file));
    }
}

//Generated 09-10-2023 13:46:51
