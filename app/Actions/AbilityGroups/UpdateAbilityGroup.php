<?php

namespace App\Actions\AbilityGroups;

use App\Models\AbilityGroup;
use Spatie\QueueableAction\QueueableAction;
use App\Actions\AbilityGroups\SyncAbilityGroupRoleAbilities;

class UpdateAbilityGroup
{
    use QueueableAction;

    public function execute(int $id, array $data)
    {
        $abilityGroup = AbilityGroup::find($id);      
        $abilityGroup->fill($data);
        
        $abilityGroup->save();

        $abilityGroup->abilities()->sync($data['ability_ids']);

        app(SyncAbilityGroupRoleAbilities::class)->execute($abilityGroup);

        return $abilityGroup;
    }
}