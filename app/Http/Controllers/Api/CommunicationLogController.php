<?php

namespace App\Http\Controllers\Api;

use App\Actions\CommunicationLogs\CreateCommunicationLog;
use App\Actions\CommunicationLogs\DeleteCommunicationLog;
use App\Actions\CommunicationLogs\GenerateCommunicationLogShowCache;
use App\Actions\CommunicationLogs\UpdateCommunicationLog;
use App\Http\Controllers\Api\Controller;
use App\Http\Requests\CommunicationLogs\CreateCommunicationLogRequest;
use App\Http\Requests\CommunicationLogs\DeleteCommunicationLogRequest;
use App\Http\Requests\CommunicationLogs\ListCommunicationLogRequest;
use App\Http\Requests\CommunicationLogs\ShowCommunicationLogRequest;
use App\Http\Requests\CommunicationLogs\UpdateCommunicationLogRequest;
use App\Models\CommunicationLog;
use App\Support\QueryBuilder;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * @group CommunicationLog
 *
 * APIs for managing communication-logs
 */
class CommunicationLogController extends Controller
{
    use ApiResponse;

    /**
     * index()
     *
     * Reads and returns a collection of all communication-logs.
     * <aside><ul><li>list-communication-log</li></ul></aside>
     */
    public function index(ListCommunicationLogRequest $request)
    {
        return QueryBuilder::for(CommunicationLog::class)->list();
    }

    /**
     * show()
     *
     * Reads and returns a communication-log.
     * <aside><ul><li>show-communication-log</li></ul></aside>
     * @urlParam communication-log integer CommunicationLog ID. Example: 1
     */
    public function show(ShowCommunicationLogRequest $request, int $id, GenerateCommunicationLogShowCache $generateCommunicationLogShowCache)
    {
        return $generateCommunicationLogShowCache->execute($id);
    }

    /**
     * store()
     *
     * Creates, saves and returns a communication-log.
     * <aside><ul><li>create-communication-log</li></ul></aside>
     */
    public function store(CreateCommunicationLogRequest $request, CreateCommunicationLog $createCommunicationLog)
    {
        return $this->respond($createCommunicationLog->execute($request->all()));
    }

    /**
     * update()
     *
     * Updates, saves and returns a communication-log.
     * <aside><ul><li>update-communication-log</li></ul></aside>
     * @urlParam communication-log integer CommunicationLog ID. Example: 1
     */
    public function update(UpdateCommunicationLogRequest $request, int $id, UpdateCommunicationLog $updateCommunicationLog)
    {
        return $this->respond($updateCommunicationLog->execute($id, $request->all()));
    }

    /**
     * destroy()
     *
     * Deletes a communication-log.
     * <aside><ul><li>delete-communication-log</li></ul></aside>
     * @urlParam communication-log integer CommunicationLog ID. Example: 1
     */
    public function destroy(DeleteCommunicationLogRequest $request, int $id, DeleteCommunicationLog $deleteCommunicationLog)
    {
        return $this->respondDeleted($deleteCommunicationLog->execute($id));
    }

	/**
	* companyCommunicationLogs()
	*
	* Lists communication logs based on their company.
	* <aside><ul><li>list-communication-log</li></ul></aside>
	* @urlParam companies integer[] Company IDs Example: [1,2,3]
	*/
	public function companyCommunicationLogs(string $companyIDs)
	{
		$companyIDs = explode(',', $companyIDs);
		abort(501, 'Company communication logs not implemented');
		//return $this->respond(QueryBuilder::for(CommunicationLog::class)->whereIn('company_id', $companyIDs)->list());
	}

}

//Generated 04-11-2023 16:09:50
