<?php

namespace App\Actions\AbilityGroups;

use App\Traits\ApiResponse;
use App\Models\AbilityGroup;
use App\Support\QueryBuilder;
use Senses\TaggedCache\Facades\TaggedCache;
use Spatie\QueueableAction\QueueableAction;

class GenerateAbilityGroupShowCache
{
    use QueueableAction, ApiResponse;

    public function execute(int $id)
    {
        return TaggedCache::responseForever('ability-group-' . $id, function () use ($id) {
            $abilityGroup = AbilityGroup::findOrFail($id);
            $abilityGroup->load('abilities', 'creator', 'updater');
            return $this->respond($abilityGroup);
        });
    }
}

//Generated 11-11-2021 08:27:41
