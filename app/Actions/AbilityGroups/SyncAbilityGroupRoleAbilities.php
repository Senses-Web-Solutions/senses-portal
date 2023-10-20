<?php

namespace App\Actions\AbilityGroups;

use App\Models\Ability;
use App\Models\AbilityGroup;
use Spatie\QueueableAction\QueueableAction;

class SyncAbilityGroupRoleAbilities
{
    use QueueableAction;

    public function execute(AbilityGroup $abilityGroup)
    {
        foreach($abilityGroup->roles as $role) {

            //ensures abilites removed are removed, and any groups which have the same ability doesn't end up lost.
            $abilityGroupIDs = $role->abilityGroups()->pluck('id');
            $abilities = Ability::whereHas('abilityGroups', function($q) use(&$abilityGroupIDs) {
                $q->whereIn('id', $abilityGroupIDs);
            })->pluck('id');

            $role->abilities()->sync($abilities);
        }
    }
}