<?php

namespace App\Actions\CommunicationLogs;

use App\Models\CommunicationLog;
use App\Support\QueryBuilder;
use App\Traits\ApiResponse;
use Senses\TaggedCache\Facades\TaggedCache;
use Spatie\QueueableAction\QueueableAction;

class GenerateCommunicationLogShowCache
{
    use QueueableAction, ApiResponse;

    public $queue = 'low';

    public function execute(int $id)
    {
        return TaggedCache::responseForever('communication-log-' . $id, function () use ($id) {
            return $this->respond(CommunicationLog::findOrFail($id));
        });
    }
}

//Generated 04-11-2023 16:09:50
