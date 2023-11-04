<?php

namespace App\Http\Controllers\Api;

use App\Actions\Revenues\CreateRevenue;
use App\Actions\Revenues\DeleteRevenue;
use App\Actions\Revenues\GenerateRevenueShowCache;
use App\Actions\Revenues\UpdateRevenue;
use App\Http\Controllers\Api\Controller;
use App\Http\Requests\Revenues\CreateRevenueRequest;
use App\Http\Requests\Revenues\DeleteRevenueRequest;
use App\Http\Requests\Revenues\ListRevenueRequest;
use App\Http\Requests\Revenues\ShowRevenueRequest;
use App\Http\Requests\Revenues\UpdateRevenueRequest;
use App\Models\Revenue;
use App\Support\QueryBuilder;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * @group Revenue
 *
 * APIs for managing revenues
 */
class RevenueController extends Controller
{
    use ApiResponse;

    /**
     * index()
     *
     * Reads and returns a collection of all revenues.
     * <aside><ul><li>list-revenue</li></ul></aside>
     */
    public function index(ListRevenueRequest $request)
    {
        return QueryBuilder::for(Revenue::class)->list();
    }

    /**
     * show()
     *
     * Reads and returns a revenue.
     * <aside><ul><li>show-revenue</li></ul></aside>
     * @urlParam revenue integer Revenue ID. Example: 1
     */
    public function show(ShowRevenueRequest $request, int $id, GenerateRevenueShowCache $generateRevenueShowCache)
    {
        return $generateRevenueShowCache->execute($id);
    }

    /**
     * store()
     *
     * Creates, saves and returns a revenue.
     * <aside><ul><li>create-revenue</li></ul></aside>
     */
    public function store(CreateRevenueRequest $request, CreateRevenue $createRevenue)
    {
        return $this->respond($createRevenue->execute($request->all()));
    }

    /**
     * update()
     *
     * Updates, saves and returns a revenue.
     * <aside><ul><li>update-revenue</li></ul></aside>
     * @urlParam revenue integer Revenue ID. Example: 1
     */
    public function update(UpdateRevenueRequest $request, int $id, UpdateRevenue $updateRevenue)
    {
        return $this->respond($updateRevenue->execute($id, $request->all()));
    }

    /**
     * destroy()
     *
     * Deletes a revenue.
     * <aside><ul><li>delete-revenue</li></ul></aside>
     * @urlParam revenue integer Revenue ID. Example: 1
     */
    public function destroy(DeleteRevenueRequest $request, int $id, DeleteRevenue $deleteRevenue)
    {
        return $this->respondDeleted($deleteRevenue->execute($id));
    }

	/**
	* companyRevenues()
	*
	* Lists revenues based on their company.
	* <aside><ul><li>list-revenue</li></ul></aside>
	* @urlParam companies integer[] Company IDs Example: [1,2,3]
	*/
	public function companyRevenues(string $companyIDs)
	{
		$companyIDs = explode(',', $companyIDs);
		abort(501, 'Company revenues not implemented');
		//return $this->respond(QueryBuilder::for(Revenue::class)->whereIn('company_id', $companyIDs)->list());
	}

}

//Generated 04-11-2023 16:09:26
