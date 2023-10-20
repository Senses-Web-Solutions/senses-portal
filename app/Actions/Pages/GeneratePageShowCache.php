<?php

namespace App\Actions\Pages;

use App\Models\Page;
use App\Support\QueryBuilder;
use App\Traits\ApiResponse;
use Senses\TaggedCache\Facades\TaggedCache;
use Spatie\QueueableAction\QueueableAction;

class GeneratePageShowCache
{
    use QueueableAction, ApiResponse;

    public $queue = 'low';

    public function execute(int $id)
    {
        return TaggedCache::responseForever('page-' . $id, function () use ($id) {
            return $this->respond(Page::findOrFail($id)->append('content'));
        });
    }
}

//Generated 10-10-2023 14:43:35
