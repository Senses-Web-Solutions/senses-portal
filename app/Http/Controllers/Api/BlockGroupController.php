<?php

namespace App\Http\Controllers\Api;

use App\Actions\BlockGroups\CreateBlockGroup;
use App\Actions\BlockGroups\DeleteBlockGroup;
use App\Actions\BlockGroups\GenerateBlockGroupShowCache;
use App\Actions\BlockGroups\UpdateBlockGroup;
use App\Http\Controllers\Api\Controller;
use App\Http\Requests\BlockGroups\CreateBlockGroupRequest;
use App\Http\Requests\BlockGroups\DeleteBlockGroupRequest;
use App\Http\Requests\BlockGroups\ListBlockGroupRequest;
use App\Http\Requests\BlockGroups\ShowBlockGroupRequest;
use App\Http\Requests\BlockGroups\UpdateBlockGroupRequest;
use App\Models\BlockGroup;
use App\Support\QueryBuilder;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * @group BlockGroup
 *
 * APIs for managing block-groups
 */
class BlockGroupController extends Controller
{
    use ApiResponse;

    /**
     * index()
     *
     * Reads and returns a collection of all block-groups.
     * <aside><ul><li>list-block-group</li></ul></aside>
     */
    public function index(ListBlockGroupRequest $request)
    {
        return QueryBuilder::for(BlockGroup::class)->list();
    }

    /**
     * show()
     *
     * Reads and returns a block-group.
     * <aside><ul><li>show-block-group</li></ul></aside>
     * @urlParam block-group integer BlockGroup ID. Example: 1
     */
    public function show(ShowBlockGroupRequest $request, int $id, GenerateBlockGroupShowCache $generateBlockGroupShowCache)
    {
        return $generateBlockGroupShowCache->execute($id);
    }

    /**
     * store()
     *
     * Creates, saves and returns a block-group.
     * <aside><ul><li>create-block-group</li></ul></aside>
     */
    public function store(CreateBlockGroupRequest $request, CreateBlockGroup $createBlockGroup)
    {
        return $this->respond($createBlockGroup->execute($request->all()));
    }

    /**
     * update()
     *
     * Updates, saves and returns a block-group.
     * <aside><ul><li>update-block-group</li></ul></aside>
     * @urlParam block-group integer BlockGroup ID. Example: 1
     */
    public function update(UpdateBlockGroupRequest $request, int $id, UpdateBlockGroup $updateBlockGroup)
    {
        return $this->respond($updateBlockGroup->execute($id, $request->all()));
    }

    /**
     * destroy()
     *
     * Deletes a block-group.
     * <aside><ul><li>delete-block-group</li></ul></aside>
     * @urlParam block-group integer BlockGroup ID. Example: 1
     */
    public function destroy(DeleteBlockGroupRequest $request, int $id, DeleteBlockGroup $deleteBlockGroup)
    {
        return $this->respondDeleted($deleteBlockGroup->execute($id));
    }

}

//Generated 16-10-2023 10:39:10
