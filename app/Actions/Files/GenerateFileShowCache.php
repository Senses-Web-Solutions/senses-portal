<?php

namespace App\Actions\Files;

use App\Models\File;
use App\Support\QueryBuilder;
use App\Traits\ApiResponse;
use Senses\TaggedCache\Facades\TaggedCache;
use Spatie\QueueableAction\QueueableAction;

class GenerateFileShowCache
{
    use QueueableAction, ApiResponse;

    public $queue = 'low';

    public function execute(int $id)
    {
        return TaggedCache::responseForever('file-' . $id, function () use ($id) {
            return $this->respond(File::findOrFail($id));
        });
    }
}

//Generated 09-10-2023 13:46:51
