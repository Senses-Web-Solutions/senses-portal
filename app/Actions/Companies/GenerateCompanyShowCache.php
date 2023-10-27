<?php

namespace App\Actions\Companies;

use App\Models\Company;
use App\Support\QueryBuilder;
use App\Traits\ApiResponse;
use Senses\TaggedCache\Facades\TaggedCache;
use Spatie\QueueableAction\QueueableAction;

class GenerateCompanyShowCache
{
    use QueueableAction, ApiResponse;

    public $queue = 'low';

    public function execute(int $id)
    {
        return TaggedCache::responseForever('company-' . $id, function () use ($id) {
            return $this->respond(Company::findOrFail($id));
        });
    }
}

//Generated 27-10-2023 10:55:45
