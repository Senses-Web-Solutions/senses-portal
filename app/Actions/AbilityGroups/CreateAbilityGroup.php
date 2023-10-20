<?php

namespace App\Actions\AbilityGroups;

use App\Models\AbilityGroup;
use Spatie\QueueableAction\QueueableAction;
use App\Actions\AbilityGroups\SyncAbilityGroupRoleAbilities;

class CreateAbilityGroup
{
    use QueueableAction;

    public function execute(array $data)
    {
        $abilityGroup = new AbilityGroup();     
        $abilityGroup->fill($data);
        $abilityGroup->save();
        $abilityGroup->abilities()->sync($data['ability_ids']);

        return $abilityGroup;
    }
}