<?php

namespace App\Actions\Users;

use App\Models\User;
use App\Support\QueryBuilder;
use App\Traits\ApiResponse;
use Senses\TaggedCache\Facades\TaggedCache;
use Spatie\QueueableAction\QueueableAction;

class GenerateUserShowCache
{
    use QueueableAction, ApiResponse;

    public $queue = 'low';

    public function execute(int $id)
    {
        return TaggedCache::responseForever('user-' . $id, function () use ($id) {
            return $this->respond(User::findOrFail($id));
        });
    }
}

//Generated 10-10-2023 10:05:12
