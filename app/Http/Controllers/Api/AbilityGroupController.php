<?php

namespace App\Http\Controllers\Api;

use App\Traits\ApiResponse;
use App\Models\AbilityGroup;
use App\Support\QueryBuilder;
use App\Http\Controllers\Api\Controller;
use App\Actions\AbilityGroups\CreateAbilityGroup;
use App\Actions\AbilityGroups\DeleteAbilityGroup;
use App\Actions\AbilityGroups\UpdateAbilityGroup;
use App\Actions\AbilityGroups\GenerateAbilityGroupShowCache;
use App\Http\Requests\AbilityGroups\ListAbilityGroupRequest;
use App\Http\Requests\AbilityGroups\ShowAbilityGroupRequest;
use App\Http\Requests\AbilityGroups\CreateAbilityGroupRequest;
use App\Http\Requests\AbilityGroups\DeleteAbilityGroupRequest;
use App\Http\Requests\AbilityGroups\UpdateAbilityGroupRequest;


/**
 * @group Ability Group
 *
 * APIs for managing ability-groups
 */
class AbilityGroupController extends Controller
{

    use ApiResponse;

    /**
     * index()
     *
     * Reads and returns a collection of all ability groups.
     * <aside><ul><li>list-ability-group</li></ul></aside>
     */
    public function index(ListAbilityGroupRequest $request)
    {
        return QueryBuilder::for(AbilityGroup::class)->list();
    }

    /**
     * show()
     *
     * Reads and returns a ability group.
     * <aside><ul><li>show-ability-group</li></ul></aside>
     * @urlParam abilitygroup integer Ability Group ID. Example: 1
     */
    public function show(ShowAbilityGroupRequest $request, int $id, GenerateAbilityGroupShowCache $generateAbilityGroupShowCache)
    {
        return $generateAbilityGroupShowCache->execute($id);
    }

    /**
     * store()
     *
     * Creates, saves and returns a abilitygroup.
     * <aside><ul><li>create-ability-group</li></ul></aside>
     */
    public function store(CreateAbilityGroupRequest $request, CreateAbilityGroup $createAbilityGroup)
    {
        return $this->respond($createAbilityGroup->execute($request->all()));
    }

    /**
     * update()
     *
     * Updates, saves and returns a abilitygroup.
     * <aside><ul><li>update-ability-group</li></ul></aside>
     * @urlParam abilitygroup integer Ability Group ID. Example: 1
     */
    public function update(UpdateAbilityGroupRequest $request, int $id, UpdateAbilityGroup $updateAbilityGroup)
    {
        return $this->respond($updateAbilityGroup->execute($id, $request->all()));
    }

    /**
     * destroy()
     *
     * Deletes a abilitygroup.
     * <aside><ul><li>delete-ability-group</li></ul></aside>
     * @urlParam abilitygroup integer Ability Group ID. Example: 1
     */
    public function destroy(DeleteAbilityGroupRequest $request, int $id, DeleteAbilityGroup $deleteAbilityGroup)
    {
        return $this->respondDeleted($deleteAbilityGroup->execute($id));
    }

    /**
	* roleAbilityGroups()
	*
	* Lists ability groups based on their role.
	* <aside><ul><li>list-task</li></ul></aside>
	* @urlParam companies integer Role ID Example: 1
	*/
    public function roleAbilityGroups(ListAbilityGroupRequest $request, $roleID) {
        return QueryBuilder::for(AbilityGroup::whereHas('roles', function($q) use($roleID){
            $q->where('id', $roleID);
        }))->list();
    }
}

