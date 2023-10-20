<?php

namespace App\Http\Controllers\Api;

use App\Models\Ability;
use App\Traits\ApiResponse;
use App\Support\QueryBuilder;
use App\Http\Controllers\Api\Controller;
use App\Http\Requests\Abilities\ListAbilityRequest;
use App\Http\Requests\Abilities\ReseedAbilitiesRequest;
use Illuminate\Support\Facades\Artisan;

/**
 * @group Ability
 *
 * APIs for managing abilities
 */
class AbilityController extends Controller
{

    use ApiResponse;

    /**
     * index()
     *
     * Reads and returns a collection of all abilities
     * <aside><ul><li>list-ability</li></ul></aside>
     */
    public function index(ListAbilityRequest $request)
    {
        return QueryBuilder::for(Ability::class)->list();
    }

    /**
     * reseed()
     *
     * Reseeds all abilities
     * <aside><ul><li>create-ability</li></ul></aside>
     */
    // @todo change to senses role check
    public function reseed(ReseedAbilitiesRequest $request)
    {
        // throw new \Exception();
        // return 5;
        return Artisan::call('role:refresh ');
    }

    /**
     * roleAbilities()
     *
     * Reads and returns a collection of all abilities for a role
     * <aside><ul><li>list-ability</li></ul></aside>
     * @urlParam roles integer Role ID Example: 1
     */
    public function roleAbilities(ListAbilityRequest $request, $roleID)
    {
        return QueryBuilder::for(Ability::whereRelation('roles', 'roles.id', '=', $roleID))->list();
    }

    /**
     * abilityGroupAbilities()
     *
     * Reads and returns a collection of all abilities for a ability group
     * <aside><ul><li>list-ability</li></ul></aside>
     * @urlParam roles integer Ability Group ID Example: 1
     */
    public function abilityGroupAbilities(ListAbilityRequest $request, $abiltiyGroupID)
    {
        return QueryBuilder::for(Ability::whereRelation('abilityGroups', 'ability_groups.id', '=', $abiltiyGroupID))->list();
    }

}

