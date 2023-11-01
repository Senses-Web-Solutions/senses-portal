<?php

namespace App\Actions\Servers;

use App\Models\Server;
use App\Support\QueryBuilder;
use App\Traits\ApiResponse;
use Senses\TaggedCache\Facades\TaggedCache;
use Spatie\QueueableAction\QueueableAction;

class GenerateServerShowCache
{
    use QueueableAction, ApiResponse;

    public $queue = 'low';

    public function execute(int $id)
    {
        return TaggedCache::responseForever('server-' . $id, function () use ($id) {
            return $this->respond(Server::findOrFail($id));
        });
    }
}

//Generated 01-11-2023 11:27:41
