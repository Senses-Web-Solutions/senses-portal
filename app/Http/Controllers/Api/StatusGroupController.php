<?php

namespace App\Http\Controllers\Api;

use App\Actions\StatusGroups\CreateStatusGroup;
use App\Actions\StatusGroups\DeleteStatusGroup;
use App\Actions\StatusGroups\GenerateStatusGroupShowCache;
use App\Actions\StatusGroups\UpdateStatusGroup;
use App\Http\Controllers\Api\Controller;
use App\Http\Requests\StatusGroups\CreateStatusGroupRequest;
use App\Http\Requests\StatusGroups\DeleteStatusGroupRequest;
use App\Http\Requests\StatusGroups\ListStatusGroupRequest;
use App\Http\Requests\StatusGroups\ShowStatusGroupRequest;
use App\Http\Requests\StatusGroups\UpdateStatusGroupRequest;
use App\Models\StatusGroup;
use App\Support\QueryBuilder;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * @group StatusGroup
 *
 * APIs for managing status-groups
 */
class StatusGroupController extends Controller
{
    use ApiResponse;

    /**
     * index()
     *
     * Reads and returns a collection of all status-groups.
     * <aside><ul><li>list-status-group</li></ul></aside>
     */
    public function index(ListStatusGroupRequest $request)
    {
        return QueryBuilder::for(StatusGroup::class)->list();
    }

    /**
     * show()
     *
     * Reads and returns a status-group.
     * <aside><ul><li>show-status-group</li></ul></aside>
     * @urlParam status-group integer StatusGroup ID. Example: 1
     */
    public function show(ShowStatusGroupRequest $request, int $id, GenerateStatusGroupShowCache $generateStatusGroupShowCache)
    {
        return $generateStatusGroupShowCache->execute($id);
    }

    /**
     * store()
     *
     * Creates, saves and returns a status-group.
     * <aside><ul><li>create-status-group</li></ul></aside>
     */
    public function store(CreateStatusGroupRequest $request, CreateStatusGroup $createStatusGroup)
    {
        return $this->respond($createStatusGroup->execute($request->all()));
    }

    /**
     * update()
     *
     * Updates, saves and returns a status-group.
     * <aside><ul><li>update-status-group</li></ul></aside>
     * @urlParam status-group integer StatusGroup ID. Example: 1
     */
    public function update(UpdateStatusGroupRequest $request, int $id, UpdateStatusGroup $updateStatusGroup)
    {
        return $this->respond($updateStatusGroup->execute($id, $request->all()));
    }

    /**
     * destroy()
     *
     * Deletes a status-group.
     * <aside><ul><li>delete-status-group</li></ul></aside>
     * @urlParam status-group integer StatusGroup ID. Example: 1
     */
    public function destroy(DeleteStatusGroupRequest $request, int $id, DeleteStatusGroup $deleteStatusGroup)
    {
        return $this->respondDeleted($deleteStatusGroup->execute($id));
    }

}

//Generated 09-10-2023 12:05:02
