<?php

namespace App\Observers;

use App\Actions\Servers\GenerateServerShowCache;
use App\Models\Server;
use Senses\TaggedCache\Facades\TaggedCache;

use App\Events\Servers\ServerCreated;
use App\Events\Servers\ServerDeleted;
use App\Events\Servers\ServerUpdated;

class ServerObserver
{
    public function created(Server $server)
    {
        //app(GenerateServerShowCache::class)->onQueue('low')->execute($server->id);
        broadcast_safely(new ServerCreated($server));
    }

    public function updated(Server $server)
    {
        TaggedCache::forgetWithTag($server->cacheKey);
        //app(GenerateServerShowCache::class)->onQueue('low')->execute($server->id);
        broadcast_safely(new ServerUpdated($server));
    }

    public function locked(Server $server)
    {
        TaggedCache::forgetWithTag($server->cacheKey);
        //app(GenerateServerShowCache::class)->onQueue('low')->execute($server->id);
        broadcast_safely(new ServerUpdated($server));
    }

    public function unlocked(Server $server)
    {
        TaggedCache::forgetWithTag($server->cacheKey);
        //app(GenerateServerShowCache::class)->onQueue('low')->execute($server->id);
        broadcast_safely(new ServerUpdated($server));
    }

    public function deleted(Server $server)
    {
        TaggedCache::forgetWithTag($server->cacheKey);
        broadcast_safely(new ServerDeleted($server));
    }

    public function restored(Server $server)
    {
        TaggedCache::forgetWithTag($server->cacheKey);
        //app(GenerateServerShowCache::class)->onQueue('low')->execute($server->id);
        broadcast_safely(new ServerUpdated($server));
    }

    public function forceDeleted(Server $server)
    {
        TaggedCache::forgetWithTag($server->cacheKey);
        broadcast_safely(new ServerDeleted($server));
    }
}

//Generated 01-11-2023 11:27:41
