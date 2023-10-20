<?php

namespace App\Actions\AbilityGroups;

use App\Models\AbilityGroup;
use Spatie\QueueableAction\QueueableAction;
use App\Actions\AbilityGroups\SyncAbilityGroupRoleAbilities;

class DeleteAbilityGroup
{
    use QueueableAction;

    public function execute($id)
    {
        $abilityGroup = AbilityGroup::findOrFail($id);
        

        return $abilityGroup;
    }
}