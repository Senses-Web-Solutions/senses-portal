<?php

namespace App\Http\Controllers\Api;

use App\Actions\ServerMetrics\CreateServerMetric;
use App\Actions\ServerMetrics\DeleteServerMetric;
use App\Actions\ServerMetrics\GenerateServerMetricShowCache;
use App\Actions\ServerMetrics\UpdateServerMetric;
use App\Http\Controllers\Api\Controller;
use App\Http\Requests\ServerMetrics\CreateServerMetricRequest;
use App\Http\Requests\ServerMetrics\DeleteServerMetricRequest;
use App\Http\Requests\ServerMetrics\ListServerMetricRequest;
use App\Http\Requests\ServerMetrics\ShowServerMetricRequest;
use App\Http\Requests\ServerMetrics\UpdateServerMetricRequest;
use App\Models\ServerMetric;
use App\Support\QueryBuilder;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * @group ServerMetric
 *
 * APIs for managing server-metrics
 */
class ServerMetricController extends Controller
{
    use ApiResponse;

    /**
     * index()
     *
     * Reads and returns a collection of all server-metrics.
     * <aside><ul><li>list-server-metric</li></ul></aside>
     */
    public function index(ListServerMetricRequest $request)
    {
        return QueryBuilder::for(ServerMetric::class)->list();
    }

    /**
     * show()
     *
     * Reads and returns a server-metric.
     * <aside><ul><li>show-server-metric</li></ul></aside>
     * @urlParam server-metric integer ServerMetric ID. Example: 1
     */
    public function show(ShowServerMetricRequest $request, int $id, GenerateServerMetricShowCache $generateServerMetricShowCache)
    {
        return $generateServerMetricShowCache->execute($id);
    }

    /**
     * store()
     *
     * Creates, saves and returns a server-metric.
     * <aside><ul><li>create-server-metric</li></ul></aside>
     */
    public function store(CreateServerMetricRequest $request, CreateServerMetric $createServerMetric)
    {
        logger($request->all());
        return $this->respond($createServerMetric->execute($request->all()));
    }

    /**
     * update()
     *
     * Updates, saves and returns a server-metric.
     * <aside><ul><li>update-server-metric</li></ul></aside>
     * @urlParam server-metric integer ServerMetric ID. Example: 1
     */
    public function update(UpdateServerMetricRequest $request, int $id, UpdateServerMetric $updateServerMetric)
    {
        return $this->respond($updateServerMetric->execute($id, $request->all()));
    }

    /**
     * destroy()
     *
     * Deletes a server-metric.
     * <aside><ul><li>delete-server-metric</li></ul></aside>
     * @urlParam server-metric integer ServerMetric ID. Example: 1
     */
    public function destroy(DeleteServerMetricRequest $request, int $id, DeleteServerMetric $deleteServerMetric)
    {
        return $this->respondDeleted($deleteServerMetric->execute($id));
    }

	/**
	* companyServerMetrics()
	*
	* Lists server metrics based on their company.
	* <aside><ul><li>list-server-metric</li></ul></aside>
	* @urlParam companies integer[] Company IDs Example: [1,2,3]
	*/
	public function companyServerMetrics(string $companyIDs)
	{
		$companyIDs = explode(',', $companyIDs);
		abort(501, 'Company server metrics not implemented');
		//return $this->respond(QueryBuilder::for(ServerMetric::class)->whereIn('company_id', $companyIDs)->list());
	}

	/**
	* serverServerMetrics()
	*
	* Lists server metrics based on their server.
	* <aside><ul><li>list-server-metric</li></ul></aside>
	* @urlParam servers integer[] Server IDs Example: [1,2,3]
	*/
	public function serverServerMetrics(string $serverIDs)
	{
		$serverIDs = explode(',', $serverIDs);
		abort(501, 'Server server metrics not implemented');
		//return $this->respond(QueryBuilder::for(ServerMetric::class)->whereIn('server_id', $serverIDs)->list());
	}

}

//Generated 27-10-2023 10:55:27
