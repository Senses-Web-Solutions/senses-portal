<?php

namespace App\Http\Controllers\Api;

use App\Actions\Statuses\CreateStatus;
use App\Actions\Statuses\DeleteStatus;
use App\Actions\Statuses\GenerateStatusShowCache;
use App\Actions\Statuses\UpdateStatus;
use App\Http\Controllers\Api\Controller;
use App\Http\Requests\Statuses\CreateStatusRequest;
use App\Http\Requests\Statuses\DeleteStatusRequest;
use App\Http\Requests\Statuses\ListStatusRequest;
use App\Http\Requests\Statuses\ShowStatusRequest;
use App\Http\Requests\Statuses\UpdateStatusRequest;
use App\Models\Status;
use App\Support\QueryBuilder;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * @group Status
 *
 * APIs for managing statuses
 */
class StatusController extends Controller
{
    use ApiResponse;

    /**
     * index()
     *
     * Reads and returns a collection of all statuses.
     * <aside><ul><li>list-status</li></ul></aside>
     */
    public function index(ListStatusRequest $request)
    {
        return QueryBuilder::for(Status::class)->list();
    }

    /**
     * show()
     *
     * Reads and returns a status.
     * <aside><ul><li>show-status</li></ul></aside>
     * @urlParam status integer Status ID. Example: 1
     */
    public function show(ShowStatusRequest $request, int $id, GenerateStatusShowCache $generateStatusShowCache)
    {
        return $generateStatusShowCache->execute($id);
    }

    /**
     * store()
     *
     * Creates, saves and returns a status.
     * <aside><ul><li>create-status</li></ul></aside>
     */
    public function store(CreateStatusRequest $request, CreateStatus $createStatus)
    {
        return $this->respond($createStatus->execute($request->all()));
    }

    /**
     * update()
     *
     * Updates, saves and returns a status.
     * <aside><ul><li>update-status</li></ul></aside>
     * @urlParam status integer Status ID. Example: 1
     */
    public function update(UpdateStatusRequest $request, int $id, UpdateStatus $updateStatus)
    {
        return $this->respond($updateStatus->execute($id, $request->all()));
    }

    /**
     * destroy()
     *
     * Deletes a status.
     * <aside><ul><li>delete-status</li></ul></aside>
     * @urlParam status integer Status ID. Example: 1
     */
    public function destroy(DeleteStatusRequest $request, int $id, DeleteStatus $deleteStatus)
    {
        return $this->respondDeleted($deleteStatus->execute($id));
    }

	/**
	* statusGroupStatuses()
	*
	* Lists statuses based on their status group.
	* <aside><ul><li>list-status</li></ul></aside>
	* @urlParam status-groups integer[] Status Group IDs Example: [1,2,3]
	*/
	public function statusGroupStatuses(string $statusGroupIDs)
	{
		$statusGroupIDs = explode(',', $statusGroupIDs);
		return $this->respond(QueryBuilder::for(Status::class)->whereHas('statusGroups', function ($q) use ($statusGroupIDs) {$q->whereIn('status_groups.id', $statusGroupIDs);})->list());
	}

}

//Generated 09-10-2023 12:35:29
