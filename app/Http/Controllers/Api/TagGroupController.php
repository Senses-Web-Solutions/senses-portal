<?php

namespace App\Http\Controllers\Api;

use App\Actions\TagGroups\CreateTagGroup;
use App\Actions\TagGroups\DeleteTagGroup;
use App\Actions\TagGroups\GenerateTagGroupShowCache;
use App\Actions\TagGroups\UpdateTagGroup;
use App\Http\Controllers\Api\Controller;
use App\Http\Requests\TagGroups\CreateTagGroupRequest;
use App\Http\Requests\TagGroups\DeleteTagGroupRequest;
use App\Http\Requests\TagGroups\ListTagGroupRequest;
use App\Http\Requests\TagGroups\ShowTagGroupRequest;
use App\Http\Requests\TagGroups\UpdateTagGroupRequest;
use App\Models\TagGroup;
use App\Support\QueryBuilder;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * @group TagGroup
 *
 * APIs for managing tag-groups
 */
class TagGroupController extends Controller
{
    use ApiResponse;

    /**
     * index()
     *
     * Reads and returns a collection of all tag-groups.
     * <aside><ul><li>list-tag-group</li></ul></aside>
     */
    public function index(ListTagGroupRequest $request)
    {
        return QueryBuilder::for(TagGroup::class)->list();
    }

    /**
     * show()
     *
     * Reads and returns a tag-group.
     * <aside><ul><li>show-tag-group</li></ul></aside>
     * @urlParam tag-group integer TagGroup ID. Example: 1
     */
    public function show(ShowTagGroupRequest $request, int $id, GenerateTagGroupShowCache $generateTagGroupShowCache)
    {
        return $generateTagGroupShowCache->execute($id);
    }

    /**
     * store()
     *
     * Creates, saves and returns a tag-group.
     * <aside><ul><li>create-tag-group</li></ul></aside>
     */
    public function store(CreateTagGroupRequest $request, CreateTagGroup $createTagGroup)
    {
        return $this->respond($createTagGroup->execute($request->all()));
    }

    /**
     * update()
     *
     * Updates, saves and returns a tag-group.
     * <aside><ul><li>update-tag-group</li></ul></aside>
     * @urlParam tag-group integer TagGroup ID. Example: 1
     */
    public function update(UpdateTagGroupRequest $request, int $id, UpdateTagGroup $updateTagGroup)
    {
        return $this->respond($updateTagGroup->execute($id, $request->all()));
    }

    /**
     * destroy()
     *
     * Deletes a tag-group.
     * <aside><ul><li>delete-tag-group</li></ul></aside>
     * @urlParam tag-group integer TagGroup ID. Example: 1
     */
    public function destroy(DeleteTagGroupRequest $request, int $id, DeleteTagGroup $deleteTagGroup)
    {
        return $this->respondDeleted($deleteTagGroup->execute($id));
    }

}

//Generated 09-10-2023 10:26:55
